<?php


namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class LoggedMiddleware implements Middleware
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!empty($_SESSION['user_id'])) {
            return $handler->handle($request);
        } else {
            $_SESSION['message'] = "L'accès à cette page demande d'être connecté !";

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $response = new Response();
            return $response->withHeader('Location', $routeParser->urlFor('signin'))->withStatus(302);
        }
    }
}