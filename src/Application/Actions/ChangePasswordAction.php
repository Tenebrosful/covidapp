<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        if (isset($_POST['password']) && $_POST['password'] != '' && isset($_POST['repassword']) && $_POST['repassword'] != '') {
            $password = htmlentities($_POST['password']);
            $repassword = htmlentities($_POST['repassword']);
            if ($password !== $repassword) {
                $_SESSION['message'] = "Le mot de passe et sa confirmation sont différents ! Faites gaffe quand vous tapez !";
                return $response->withHeader('Location', 'signup')->withStatus(301);
            } else {
                $utilisateuramettreajour = Utilisateur::getByEmail($_SESSION['username']);
                $utilisateuramettreajour->mdpCrypte = password_hash($password, PASSWORD_BCRYPT);
                $utilisateuramettreajour->save();
                return $response->withHeader('Location', '/welcome')->withStatus(301);
            }
        }
    }
}
