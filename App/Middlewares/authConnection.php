<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthConnection
{
    public function validateConnection(Request $request, Response $response, callable $next): Response
    {
        $token = $request->getHeader('token') ?? null;

        if (empty($token)) {

            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            $response->getBody()->write('Required token');

            return $response;
        }

        if ($token[0] == getenv('TOKEN_ACCESS_APP')) {

            return $next($request, $response);
        } else {

            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            $response->getBody()->write('Invalid token');

            return $response;
        }
    }
}
