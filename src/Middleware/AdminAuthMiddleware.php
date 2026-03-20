<?php

declare(strict_types=1);

namespace Souboreu\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response as SlimResponse;

class AdminAuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if (empty($_SESSION['admin'])) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        return $handler->handle($request);
    }
}
