<?php
declare(strict_types=1);

use App\Application\Actions\AddGroupeAction;
use App\Application\Actions\AddMessageAction;
use App\Application\Actions\AddUserAction;
use App\Application\Actions\AuthenticateAction;
use App\Application\Actions\ChangePasswordAction;
use App\Application\Actions\DeleteUserAction;
use App\Application\Middleware\LoggedMiddleware;
use App\Domain\Groupe\Groupe;
use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/account', function (Group $group) {

        // Page d'inscription
        $group->get('/signup', function (Request $request, Response $response, $args) {
            $res = $this->get('view')->render($response, 'signup.html', [
                'message' => isset($_SESSION['message']) ? $_SESSION['message'] : '',
            ]);
            unset($_SESSION['message']);
            return $res;
        })->setName('signup');

        // Page de connexion
        $group->get('/signin', function (Request $request, Response $response, $args) {
            $res = $this->get('view')->render($response, 'signin.html', [
                'message' => isset($_SESSION['message']) ? $_SESSION['message'] : '',
            ]);
            unset($_SESSION['message']);
            return $res;
        })->setName('signin');

        // Action pour déconnecter l'utilisateur (Redirection vers la page de connexion)
        $group->get('/signout', function (Request $request, Response $response, $args) {
            session_destroy();
            return $response->withHeader('Location', 'signin')->withStatus(301);
        })->setName('signout')->add(LoggedMiddleware::class);

        // Action pour rajouter un utilisateur
        $group->post('/adduser', AddUserAction::class)->setName('adduser');

        // Action pour authentifier l'utilisateur
        $group->post('/authenticate', AuthenticateAction::class)->setName('authenticate');

        // Formulaire pour changer le mot de passe
        $group->get('/formpassword', function (Request $request, Response $response, $args) {
            $res = $this->get('view')->render($response, 'formpassword.html', [
                'message' => isset($_SESSION['message']) ? $_SESSION['message'] : ''
            ]);
            unset($_SESSION['message']);
            return $res;
        })->setName('formpassword')->add(LoggedMiddleware::class);

        // Action pour changer le mot de passe
        $group->post('/changepassword', ChangePasswordAction::class)->setName('changepassword')->add(LoggedMiddleware::class);

        // Action pour supprimer l'utilisateur
        $group->get('/deleteuser', DeleteUserAction::class)->setName('deleteuser')->add(LoggedMiddleware::class);
    });

    $app->group('', function (Group $group) {
        // Action pour souhaiter la bienvenue à l'utilisateur
        $group->get('/welcome', function (Request $request, Response $response, $args) {
            return $this->get('view')->render($response, 'welcome.html', [
                'email' => $_SESSION['email']
            ]);
        })->setName('welcome');

        // Action pour afficher la messagerie
        $group->get('/messagerie/{groupid}', function (Request $request, Response $response, $args) {
            // On récupère des messages crées dans ce groupe
            $messages = Groupe::getById($args['groupid'])->messages();

            /*
             * @Todo Amélioration pour réduire le nombre de requête SQL
             */
            foreach ($messages as $clemessage => $message) {
                $auteurMessage = Utilisateur::getById($message['id_user_auteur']);
                $messages[$clemessage]['nomprenomauteur'] = $auteurMessage->prenom . " " . $auteurMessage->nom;
            }
            return $this->get('view')->render($response, 'messagerie.html', [
                'idgroupe' => $args['groupid'],
                'idutilisateurcourant' => $_SESSION['user_id'],
                'messages' => $messages
            ]);
        })->setName('messagerie');

        // Action pour rajouter un message
        $group->post('/addmessage', AddMessageAction::class)->setName('addmessage');

        // Action pour afficher les groupes
        $group->get('/groupes', function (Request $request, Response $response, $args) {
            // Il faut récuperer la liste de groupes
            $groupes = Utilisateur::getById($_SESSION["user_id"])->groupes();
            // Et la liste des utilisateurs
            $utilisateurs = Utilisateur::all()->toArray();
            return $this->get('view')->render($response, 'groupes.html', [
                'groupes' => $groupes,
                'utilisateurs' => $utilisateurs
            ]);
        })->setName('groupes');

        // Action pour rajouter un groupe
        $group->post('/addgroup', AddGroupeAction::class)->setName('addgroup');

        $group->get('/group/{groupid}', function (Request $request, Response $response, $args) {
            $groupeAModifier = Groupe::getById($args['groupid']);
            $informationsGroupe = [$groupeAModifier->toArray()];
            $informationsGroupe[] = $groupeAModifier->membres();
            $response->getBody()->write(json_encode($informationsGroupe));
            return $response;
        })->setName('group');
        $group->get('/modifygroup', function (Request $request, Response $response, $args) {

            return $response;
        })->setName('modifygroup');
    })->add(LoggedMiddleware::class);

};
