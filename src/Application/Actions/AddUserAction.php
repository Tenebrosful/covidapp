<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 */
final class AddUserAction
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
        if (isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['password']) && $_POST['password'] != '' && isset($_POST['repassword']) && $_POST['repassword'] != '') {
            $username = htmlentities($_POST['username']);
            $nom = htmlentities($_POST['nom']);
            $prenom = htmlentities($_POST['prenom']);
            $datenaissance = htmlentities($_POST['datenaissance']);
            $password = htmlentities($_POST['password']);
            $repassword = htmlentities($_POST['repassword']);
            if ($password !== $repassword) {
                $_SESSION['message'] = "Le mot de passe et sa confirmation sont différents ! Faites gaffe quand vous tapez !";
                return $response->withHeader('Location', 'signup')->withStatus(301);
            } else {
                $nouveauutilisateur = new Utilisateur();
                $nouveauutilisateur->email = $username;
                $nouveauutilisateur->mdpCrypte = password_hash($password, PASSWORD_BCRYPT);
                $nouveauutilisateur->nom = $nom;
                $nouveauutilisateur->prenom = $prenom;
                $nouveauutilisateur->dateNais = $datenaissance;
                $nouveauutilisateur->save();
                $_SESSION['message'] = "L'utilisateur a été rajouté ! Vous pouvez vous connecter ^^";
                return $response->withHeader('Location', 'signin')->withStatus(301);
            }
        }
    }
}
