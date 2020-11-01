<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use App\Domain\Groupe\Groupe;
use App\Domain\GroupeUtilisateur\GroupeUtilisateur;

/**
 * Action
 */
final class DeleteGroupAction
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
        $groupeASupprimer = Groupe::getById($args['groupid']);
        // On supprime les messages qui était dans le groupe
        $messagesASupprimer = $groupeASupprimer->messages();
        foreach ($messagesASupprimer as $messageASupprimer)
            $messageASupprimer->delete();
        // On supprime les liaisons avec cette groupe
        GroupeUtilisateur::getById($args['groupid'])->delete();
        // Et on supprime le groupe elle-même
        $groupeASupprimer->delete();
        return $response->withHeader('Location', $routeParser->urlFor('groupes'))->withStatus(301);
    }
}
