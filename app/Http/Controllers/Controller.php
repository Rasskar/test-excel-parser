<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      title="API Documentation",
 *      version="1.0.0",
 *      description="Документация для API",
 *      @OA\Contact(
 *          email="alekscygankov20@gmail.com"
 *      )
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="basicAuth",
 *      type="http",
 *      scheme="basic",
 *      description="Введите логин и пароль. Для теста добавлен пользователь: Username: admin@example.com, Password: password"
 * )
 *
 */
abstract class Controller
{
    //
}
