<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Action
 */
final class ChangePasswordAction
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

        if (!empty($_POST['oldPassword']) && !empty($_POST['password']) && !empty($_POST['repassword'])) {
            $utilisateur = Utilisateur::getById($_SESSION['user_id']);

            if (password_verify($_POST['oldPassword'], $utilisateur->mdpCrypte)) {
                $password = filter_var($_POST['password'], FILTER_SANITAZE_STRING);
                $repassword = filter_var($_POST['repassword'], FILTER_SANITAZE_STRING);
                if ($password !== $repassword) {
                    $_SESSION['message'] = "Les mots de passe ne correspondent pas !";
                    return $response->withHeader('Location', $routeParser->urlFor('formpassword'))->withStatus(301);
                } else {
                    $utilisateur->mdpCrypte = password_hash($password, PASSWORD_BCRYPT);
                    $utilisateur->save();
                    return $response->withHeader('Location', $routeParser->urlFor('welcome'))->withStatus(301);
                }
            } else {
                $_SESSION['message'] = "Votre ancien mot de passe est incorrect !";
                return $response->withHeader('Location', $routeParser->urlFor('formpassword'))->withStatus(301);
            }
        }
    }
}
