<?php


namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class LoggedMiddleware implements Middleware
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!empty($_SESSION['user_id'])) {
            return $handler->handle($request);
        } else {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('signin', ['message' => "L'accès à cette page require d'être connecté !"]);

            return $handler->handle($request)->withHeader('Location', $url)->withStatus(302);
        }
    }
}