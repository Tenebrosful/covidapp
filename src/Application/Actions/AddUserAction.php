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
        if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['repassword']) && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['dateNaissance'])) {
            $email = htmlentities($_POST['email']);
            $nom = htmlentities($_POST['nom']);
            $prenom = htmlentities($_POST['prenom']);
            $dateNaissance = htmlentities($_POST['dateNaissance']);
            $password = htmlentities($_POST['password']);
            $repassword = htmlentities($_POST['repassword']);
            if ($password !== $repassword) {
                $_SESSION['message'] = "Les mots de passe ne correspondent pas !";
                return $response->withHeader('Location', 'signup')->withStatus(301);
            } else {
                $nouveauUtilisateur = new Utilisateur();
                $nouveauUtilisateur->email = $email;
                $nouveauUtilisateur->mdpCrypte = password_hash($password, PASSWORD_BCRYPT);
                $nouveauUtilisateur->nom = $nom;
                $nouveauUtilisateur->prenom = $prenom;
                $nouveauUtilisateur->dateNais = $dateNaissance;
                $nouveauUtilisateur->save();
                $_SESSION['message'] = "Votre compte a été créé ! Vous pouvez désormais vous connecter ! ^^";
                return $response->withHeader('Location', 'signin')->withStatus(301);
            }
        } else {
            $_SESSION['message'] = "Étrangement, toutes les informations requises n'ont pas été transmises ...";
            return $response->withHeader('Location', 'signup')->withStatus(301);
        }
    }
}
