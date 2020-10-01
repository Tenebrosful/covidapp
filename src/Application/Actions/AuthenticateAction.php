<?php

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Utilisateur\Utilisateur;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Action
 */
final class AuthenticateAction {
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
    ): ResponseInterface {
        session_start();
        if (isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['password']) && $_POST['password'] != '') {
            $username = htmlentities($_POST['username']);
            $password = htmlentities($_POST['password']);
            $utilisateur = new Utilisateur();
            $utilisateurvalide = false;
            $authentificationvalide = false;
            try {
                $donneesutilisateur = $utilisateur->where('email', $username)->firstOrFail();
                $utilisateurvalide = true;
                //if (password_verify($password, $donneesutilisateur->pluck('mdpCrypte')->toArray()[0]))
                if ($password === $donneesutilisateur->pluck('mdpCrypte')->toArray()[0])
                    $authentificationvalide = true;
            } catch (ModelNotFoundException $e) { }

            if ($authentificationvalide) {
                $_SESSION['username'] = $username;
                return $response->withHeader('Location', 'welcome')->withStatus(301);
            } else {
                if (!$utilisateurvalide)
                    $_SESSION['message'] = "Le nom d'utilisateur semble ne pas exister dans notre base de données !";
                else
                    $_SESSION['message'] = "Le nom d'utilisateur existe mais le mot de passe fournit ne correspond pas !";
                return $response->withHeader('Location', 'signin')->withStatus(301);
            }

        } else {
            $_SESSION['message'] = "Vous devez renseigner les 2 champs !";
            return $response->withHeader('Location', 'signin')->withStatus(301);
        }
    }
}
