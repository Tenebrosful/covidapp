<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

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
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['repassword']) && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['dateNaissance'])) {
            $email = filter_var($_POST['email'], FILTER_SANITAZE_STRING);
            $nom = filter_var($_POST['nom'], FILTER_SANITAZE_STRING);
            $prenom = filter_var($_POST['prenom'], FILTER_SANITAZE_STRING);
            $dateNaissance = filter_var($_POST['dateNaissance'], FILTER_SANITAZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITAZE_STRING);
            $repassword = filter_var($_POST['repassword'], FILTER_SANITAZE_STRING);

            if ($password !== $repassword) {
                $_SESSION['message'] = "Les mots de passe ne correspondent pas !";
                return $response->withHeader('Location', $routeParser->urlFor('signup'))->withStatus(301);
            } else {
                $nouveauUtilisateur = new Utilisateur();
                $nouveauUtilisateur->email = $email;
                $nouveauUtilisateur->mdpCrypte = password_hash($password, PASSWORD_BCRYPT);
                $nouveauUtilisateur->nom = $nom;
                $nouveauUtilisateur->prenom = $prenom;
                $nouveauUtilisateur->dateNais = $dateNaissance;
                $nouveauUtilisateur->save();
                $_SESSION['message'] = "Votre compte a été créé ! Vous pouvez désormais vous connecter ! ^^";
                return $response->withHeader('Location', $routeParser->urlFor('signin'))->withStatus(301);
            }
        } else {
            $_SESSION['message'] = "Étrangement, toutes les informations requises n'ont pas été transmises ...";
            return $response->withHeader('Location', $routeParser->urlFor('signup'))->withStatus(301);
        }
    }
}
