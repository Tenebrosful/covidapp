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
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = htmlentities($_POST['email']);
            $password = htmlentities($_POST['password']);

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            try {
                $donneesUtilisateur = Utilisateur::getByEmail($email);
            } catch (ModelNotFoundException $e) {
                $_SESSION['message'] = "La combinaison email/mot de passe est incorrect !";

                return $response->withHeader('Location', $routeParser->urlFor('signin'))->withStatus(301);
            }
            if (password_verify($password, $donneesUtilisateur->mdpCrypte)) {
                $_SESSION['user_id'] = $donneesUtilisateur->id;
                $_SESSION['email'] = $donneesUtilisateur->email;

                return $response->withHeader('Location', $routeParser->urlFor('welcome'))->withStatus(301);
            } else {
                $_SESSION['message'] = "La combinaison email/mot de passe est incorrect !";
                return $response->withHeader('Location', $routeParser->urlFor('signin'))->withStatus(301);
            }
        }
    }
}
