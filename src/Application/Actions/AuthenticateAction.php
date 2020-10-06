<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Action
 */
final class AuthenticateAction
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
        if (isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['password']) && $_POST['password'] != '') {
            $username = htmlentities($_POST['username']);
            $password = htmlentities($_POST['password']);
            try {
                $donneesUtilisateur = Utilisateur::getByEmail($username);
                //if (password_verify($password, $donneesUtilisateur->pluck('mdpCrypte')->toArray()[0]))
            } catch (ModelNotFoundException $e) {
                $_SESSION['message'] = "La combinaison d'identifiant est incorrecte !";
                return $response->withHeader('Location', 'signin')->withStatus(301);
            }

            if ($password === $donneesUtilisateur->mdpCrypte) {
                $_SESSION['user_id'] = $donneesUtilisateur->id;
                $_SESSION['username'] = $donneesUtilisateur->email;
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $url = $routeParser->urlFor('welcome');
                return $response->withHeader('Location', $url)->withStatus(301);
            } else {

                $_SESSION['message'] = "La combinaison d'identifiant est incorrecte !";
                return $response->withHeader('Location', 'signin')->withStatus(301);
            }

        } else {
            $_SESSION['message'] = "Vous devez renseigner les 2 champs !";
            return $response->withHeader('Location', 'signin')->withStatus(301);
        }
    }
}
