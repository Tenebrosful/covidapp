<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Action
 */
final class CovidAction
{
    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @param array $args The route arguments
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $utilisateurAModifier = Utilisateur::getById($_SESSION["user_id"]);
        $utilisateurAModifier->covid = $args['statutcovid'];
        $utilisateurAModifier->save();
        return $response->withHeader('Location', $routeParser->urlFor('welcome'))->withStatus(301);
    }
}
