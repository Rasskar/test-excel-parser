<?php

namespace App\Modules\ExcelParserApi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExcelFileRequest extends FormRequest
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx']
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Вы должны передать файл.',
            'file.file' => 'Переданный объект должен быть файлом.',
            'file.mimes' => 'Файл должен быть в формате (.xlsx).'
        ];
    }
}
