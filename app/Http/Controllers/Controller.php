<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'Documentação da API de solicitações de viagem',
    title: 'Travel Requests API',
    contact: new OA\Contact(email: 'suporte@exemplo.com')
)]
#[OA\Server(
    url: '/api/v1',
    description: 'Servidor de Desenvolvimento'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    name: 'Authorization',
    in: 'header',
    bearerFormat: 'JWT',
    scheme: 'bearer',
)]
abstract class Controller
{
    //
}
