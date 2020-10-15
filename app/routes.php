<?php
declare(strict_types=1);

use App\Application\Actions\AddUserAction;
use App\Application\Actions\AuthenticateAction;
use App\Application\Actions\ChangePasswordAction;
use App\Application\Actions\DeleteUserAction;
use App\Application\Actions\Messages\MessageReadAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\LoggedMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Routing\RouteContext;
use App\Domain\Groupe\Groupe;
use App\Domain\Utilisateur\Utilisateur;
use App\Domain\GroupeUtilisateur\GroupeUtilisateur;
use App\Domain\Message\Message;
use App\Domain\Messagerie\Messagerie;

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
            $messages = Groupe::getById($args['groupid'])->messages()->toArray();
            foreach ($messages as $clemessage => $message) {
                $auteurMessage = Utilisateur::getById($message['id_user_auteur']);
                $messages[$clemessage]['nomprenomauteur'] = $auteurMessage->prenom." ".$auteurMessage->nom;
            }
            return $this->get('view')->render($response, 'messagerie.html', [
                'idgroupe' => $args['groupid'],
                'idutilisateurcourant' => $_SESSION['user_id'],
                'messages' => $messages
            ]);
        })->setName('messagerie');
        // Action pour rajouter un message
        $group->post('/addmessage', function (Request $request, Response $response, $args) {
            $nouveauMessage = new Message();
            $nouveauMessage->id_user_auteur = htmlentities($_POST['authorid']);
            $nouveauMessage->contenu = htmlentities($_POST['content']);
            $datetime = new DateTime('now');
            $nouveauMessage->date = $datetime->format('Y-m-d H:i:s');
            $nouveauMessage->save();
            $nouveauMessagerie = new Messagerie();
            $nouveauMessagerie->id_groupe = htmlentities($_POST['groupid']);
            $nouveauMessagerie->id_message = $nouveauMessage->id;
            $nouveauMessagerie->save();
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response->withHeader('Location', $routeParser->urlFor('messagerie', ['groupid' => htmlentities($_POST['groupid'])]))->withStatus(301);
        })->setName('addmessage');
        // Action pour afficher les groupes
        $group->get('/groupes', function (Request $request, Response $response, $args) {
            // Il faut récuperer la liste de groupes
            $groupes = Groupe::all()->toArray();
            // Et la liste des utilisateurs
            $utilisateurs = Utilisateur::all()->toArray();
            return $this->get('view')->render($response, 'groupes.html', [
                'groupes' => $groupes,
                'utilisateurs' => $utilisateurs
            ]);
        })->setName('groupes');
        // Action pour rajouter un groupe
        $group->post('/addgroup', function (Request $request, Response $response, $args) {
            $nouveauGroupe = new Groupe();
            $nouveauGroupe->nom = htmlentities($_POST['grouptitle']);
            $nouveauGroupe->save();
            foreach (explode(',', htmlentities($_POST['users'])) as $idUser) {
                $nouveauGroupeUtilisateur = new GroupeUtilisateur();
                $nouveauGroupeUtilisateur->id_groupe = $nouveauGroupe->id;
                $nouveauGroupeUtilisateur->id_user = $idUser;
                $nouveauGroupeUtilisateur->save();
            }
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response->withHeader('Location', $routeParser->urlFor('groupes'))->withStatus(301);
        })->setName('addgroup');
    })->add(LoggedMiddleware::class);

};
