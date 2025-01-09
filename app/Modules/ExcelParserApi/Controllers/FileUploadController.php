<?php

namespace App\Modules\ExcelParserApi\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ExcelParserApi\Jobs\ProcessExcelFileJob;
use App\Modules\ExcelParserApi\Requests\ExcelFileRequest;
use App\Modules\ExcelParserApi\Resources\FileUploadResource;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class FileUploadController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/excel-parser/upload",
     *      summary="Загрузить Excel-файл для обработки",
     *      description="Этот эндпоинт позволяет загрузить Excel-файл, который будет обработан. Возвращает сообщение об успешной загрузке и путь к файлу.",
     *      security={
     *          {"basicAuth": {}}
     *      },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"file"},
     *                  @OA\Property(
     *                      property="file",
     *                      type="string",
     *                      format="binary",
     *                      description="Excel-файл для загрузки"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешная загрузка",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="message", type="string", example="Файл успешно загружен и передан на обработку."),
     *                  @OA\Property(property="file_path", type="string", example="uploads/qBUmQeODzsIAqcG58husvHLUjEUO223J9PthYNUp.xlsx")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Ошибка при загрузке файла.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неавторизован",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Неавторизованный запрос.")
     *          )
     *      ),
     *      @OA\Response(
     *           response=500,
     *           description="Внутренняя ошибка сервера",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Ошибка: невозможно получить результат.")
     *           )
     *      )
     *  )
     *
     * @param ExcelFileRequest $request
     * @return FileUploadResource|JsonResponse
     */
    public function upload(ExcelFileRequest $request)
    {
        try {
            $filePath = $request->file('file')->store('uploads');

            ProcessExcelFileJob::dispatch($filePath);

            return new FileUploadResource((object) ['file_path' => $filePath]);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
