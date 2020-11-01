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
final class ModifyGroupAction
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
        // On change le nom du groupe
        $groupeAModifier = Groupe::getById($_POST['groupid']);
        $groupeAModifier->nom = filter_var($_POST['grouptitle'], FILTER_SANITIZE_STRING);
        $groupeAModifier->save();
        // On supprime tous les liaisons pour ce groupe
        GroupeUtilisateur::getById($_POST['groupid'])->delete();
        // On rajoute des liaisons comme pour le rajout du groupe
        foreach (explode(',', $_POST['users']) as $idUser) {
            $nouveauGroupeUtilisateur = new GroupeUtilisateur();
            $nouveauGroupeUtilisateur->id_groupe = $_POST['groupid'];
            $nouveauGroupeUtilisateur->id_user = $idUser;
            $nouveauGroupeUtilisateur->save();
        }
        return $response->withHeader('Location', $routeParser->urlFor('groupes'))->withStatus(301);
    }
}
