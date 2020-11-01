<?php


namespace App\Application\Actions;


use App\Domain\Groupe\Groupe;
use App\Domain\GroupeUtilisateur\GroupeUtilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class AddGroupAction
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
        if (!empty($_POST['grouptitle'])) {
            $nouveauGroupe = new Groupe();

            $nouveauGroupe->nom = filter_var($_POST['grouptitle'], FILTER_SANITIZE_STRING);

            $nouveauGroupe->save();

            foreach (explode(',', $_POST['users']) as $idUser) {
                $nouveauGroupeUtilisateur = new GroupeUtilisateur();

                $nouveauGroupeUtilisateur->id_groupe = $nouveauGroupe->id;
                $nouveauGroupeUtilisateur->id_user = $idUser;

                $nouveauGroupeUtilisateur->save();
            }
        }
        return $response->withHeader('Location', $routeParser->urlFor('groupes'))->withStatus(301);
    }
}