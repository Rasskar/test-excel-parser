<?php

namespace App\Modules\ExcelParserApi\Jobs;

use App\Models\Row;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelReader;

class ProcessExcelFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const ROWS_SIZE = 1000; // Количество обрабатываемых строк за цикл

    /**
     * @var int
     */
    public $tries = 2;

    /**
     * @var int
     */
    public $timeout = 3600;

    /**
     * @param string $filePath
     */
    public function __construct(
        protected string $filePath
    )
    {
    }

    /**
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            while (true) {
                $excelProcessingProgress = Redis::get('excel_processing_progress') ?? 0;

                $rows = SimpleExcelReader::create(storage_path('app/private/' . $this->filePath))
                    ->skip($excelProcessingProgress)
                    ->take(self::ROWS_SIZE)
                    ->getRows()
                    ->toArray();

                if (empty($rows)) {
                    break;
                }

                $validatedRows = [];

                foreach ($rows as $index => $row) {
                    $lineNumber = $excelProcessingProgress + $index + 2;

                    $validator = Validator::make($row, [
                        'id' => ['required', 'regex:/^\d+$/', 'integer', 'min:1', 'unique:rows,id'],
                        'name' => ['required', 'regex:/^[A-Za-z ]+$/'],
                        'date' => ['required', 'date', 'date_format:d.m.Y']
                    ], [
                        'id.required' => 'ID отсутствует',
                        'id.regex' => 'ID содержит недопустимые символы или пробелы',
                        'id.integer' => 'ID должен быть целым числом',
                        'id.unique' => 'ID уже существует в базе данных',
                        'id.min' => 'ID должен быть больше 0',
                        'name.required' => 'Имя отсутствует',
                        'name.regex' => 'Имя содержит недопустимые символы',
                        'date.required' => 'Дата отсутствует',
                        'date.date_format' => 'Дата должна быть в формате d.m.Y',
                    ]);

                    if ($validator->fails()) {
                        File::append(storage_path('logs/result.txt'), "{$lineNumber} - " . implode(', ', $validator->errors()->all()) . "\n");
                        continue;
                    }

                    $validatedRows[] = [
                        'id' => (int) $row['id'],
                        'name' => (string) $row['name'],
                        'date' => Carbon::createFromFormat('d.m.Y', $row['date'])->format('Y-m-d')
                    ];
                }

                if (!empty($validatedRows)) {
                    Row::insert($validatedRows);
                }

                Redis::set('excel_processing_progress', $excelProcessingProgress + count($rows));
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        } finally {
            Redis::del('excel_processing_progress');

            File::delete(storage_path('app/private/' . $this->filePath));
        }
    }
}
