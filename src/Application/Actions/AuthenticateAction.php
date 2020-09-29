<?php

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Utilisateur\Utilisateur;

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
        return $response;
        session_start();
        if (isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['password']) && $_POST['password'] != '') {
            $username = htmlentities($_POST['username']);
            $password = htmlentities($_POST['password']);
            
            /*require_once('utilisateurModel.php');
            $utilisateurmodel = new utilisateurModel();
            $donneesutilisateur = $utilisateurmodel->read($username);
            $utilisateurvalide = false;
            $authentificationvalide = false;
            if ($donneesutilisateur !== false) {
                $utilisateurvalide = true;
                if (password_verify($password, $donneesutilisateur['password']))
                    $authentificationvalide = true;
            }

            if ($authentificationvalide) {
                $_SESSION['username'] = $username;
                header('Location: welcome');
            } else {
                if (!$utilisateurvalide)
                    $_SESSION['message'] = "Le nom d'utilisateur semble ne pas exister dans notre base de données !";
                else
                    $_SESSION['message'] = "Le nom d'utilisateur existe mais le mot de passe fournit ne correspond pas !";
                header('Location: signin');
            }*/

        } else {
            session_destroy();
            return $response->withHeader('Location', 'signin')->withStatus(301);
        }

    }
}
