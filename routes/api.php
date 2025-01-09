<?php

use App\Modules\ExcelParserApi\Controllers\FileResultController;
use App\Modules\ExcelParserApi\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/excel-parser')
    ->middleware(['auth.basic'])
    ->group(function () {
        Route::post('upload', [FileUploadController::class, 'upload']);
        Route::get('result', [FileResultController::class, 'result']);
    });
