<?php

namespace App\Modules\ExcelParserApi\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Row;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use OpenApi\Annotations as OA;

class FileResultController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/excel-parser/result",
     *      summary="Получить результат обработки файла",
     *      description="Возвращает данные из обработанного файла, сгруппированные по дате. Каждая дата содержит массив объектов с `id` и `name`.",
     *      security={
     *          {"basicAuth": {}}
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "2024-05-29": {
     *                      {"id": 1, "name": "Пример 1"},
     *                      {"id": 2, "name": "Пример 2"}
     *                  },
     *                  "2024-05-30": {
     *                      {"id": 3, "name": "Пример 3"}
     *                  }
     *              },
     *              additionalProperties={
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="Уникальный идентификатор строки"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="Имя строки"
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *           response=401,
     *           description="Неавторизован",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Неавторизованный запрос.")
     *           )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Внутренняя ошибка сервера",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Ошибка: невозможно получить результат.")
     *          )
     *      )
     *  )
     *
     * @return JsonResponse|void
     */
    public function result()
    {
        try {
            $data = Row::all()
                ->groupBy(fn (Row $row) => $row->date->toDateString())
                ->map(function (Collection $group) {
                    return $group->map->only(['id', 'name']);
                });

            return response()->json($data->toArray());
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
