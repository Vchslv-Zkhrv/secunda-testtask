<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Test task API',
)]
#[OA\Components(
    securitySchemes: [
        new OA\SecurityScheme(
            securityScheme: 'http',
            type: 'apiKey',
            in: 'header',
            name: 'Authorization'
        )
    ]
)]
abstract class Controller
{
    //
}
