<?php

namespace App\Modules\ExcelParserApi\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileUploadResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'Файл успешно загружен и передан на обработку.',
            'file_path' => $this->file_path,
        ];
    }
}
