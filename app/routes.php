<?php
declare(strict_types=1);

use App\Application\Actions\AddGroupAction;
use App\Application\Actions\AddMessageAction;
use App\Application\Actions\AddUserAction;
use App\Application\Actions\AuthenticateAction;
use App\Application\Actions\ChangePasswordAction;
use App\Application\Actions\DeleteUserAction;
use App\Application\Middleware\LoggedMiddleware;
use App\Domain\Groupe\Groupe;
use App\Domain\Utilisateur\Utilisateur;
use App\Domain\GroupeUtilisateur\GroupeUtilisateur;
use Slim\Routing\RouteContext;
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

        // Action pour déconnecter l'utilisateur (redirection vers la page de connexion)
        $group->get('/signout', function (Request $request, Response $response, $args) {
            session_destroy();
            return $response->withHeader('Location', 'signin')->withStatus(301);
        })->setName('signout')->add(LoggedMiddleware::class);

        // Action pour rajouter un utilisateur
        $group->post('/adduser', AddUserAction::class)->setName('adduser');

        // Action pour authentifier l'utilisateur
        $group->post('/authenticate', AuthenticateAction::class)->setName('authenticate');

        // Action pour changer le statut d'inféction de l'utilisateur
        $group->get('/covid/{statutcovid}', function (Request $request, Response $response, $args) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $utilisateurAModifier = Utilisateur::getById($_SESSION["user_id"]);
            $utilisateurAModifier->covid = $args['statutcovid'];
            $utilisateurAModifier->save();
            return $response->withHeader('Location', $routeParser->urlFor('welcome'))->withStatus(301);
        })->setName('covid')->add(LoggedMiddleware::class);

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
                'email' => $_SESSION['email'],
                'covid' => Utilisateur::getById($_SESSION["user_id"])->covid
            ]);
        })->setName('welcome');

        // Action pour afficher la messagerie (A ACTIONNER)
        $group->get('/messagerie/{groupid}', function (Request $request, Response $response, $args) {
            $groupeconcerne = Groupe::getById($args['groupid']);
            // On récupère le nom du groupe
            $nomgroupe = $groupeconcerne->nom;
            // On récupère des messages crées dans ce groupe (toArray() nécessaire pour que Twig puisse reconnaître le tableau vide)
            $messages = $groupeconcerne->messages()->toArray();
            /*
             * @Todo Amélioration pour réduire le nombre de requête SQL
             */
            foreach ($messages as $clemessage => $message) {
                $auteurMessage = Utilisateur::getById($message['id_user_auteur']);
                $messages[$clemessage]['nomprenomauteur'] = $auteurMessage->prenom." ".$auteurMessage->nom;
            }
            return $this->get('view')->render($response, 'messagerie.html', [
                'idgroupe' => $args['groupid'],
                'nomgroupe' => $nomgroupe,
                'idutilisateurcourant' => $_SESSION['user_id'],
                'messages' => $messages
            ]);
        })->setName('messagerie');

        // Action pour rajouter un message
        $group->post('/addmessage', AddMessageAction::class)->setName('addmessage');

        // Action pour afficher les groupes (A ACTIONNER)
        $group->get('/groupes', function (Request $request, Response $response, $args) {
            // Il faut récuperer la liste de groupes
            $groupes = Utilisateur::getById($_SESSION["user_id"])->groupes();
            // Et la liste des utilisateurs
            $utilisateurs = Utilisateur::all()->toArray();
            return $this->get('view')->render($response, 'groupes.html', [
                'groupes' => $groupes,
                'utilisateurs' => $utilisateurs,
                'idutilisateurcourant' => $_SESSION['user_id']
            ]);
        })->setName('groupes');

        // Action pour rajouter un groupe
        $group->post('/addgroup', AddGroupAction::class)->setName('addgroup');

        // Pour récuperer les informations sur le groupe (A ACTIONNER)
        $group->get('/group/{groupid}', function (Request $request, Response $response, $args) {
            $groupeAModifier = Groupe::getById($args['groupid']);
            $informationsGroupe = [$groupeAModifier->toArray()];
            $informationsGroupe[] = $groupeAModifier->membres();
            $response->getBody()->write(json_encode($informationsGroupe));
            return $response;
        })->setName('group');

        // Pour modifier un groupe donné (A ACTIONNER)
        $group->post('/modifygroup', function (Request $request, Response $response, $args) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            // On change le nom du groupe
            $groupeAModifier = Groupe::getById($_POST['groupid']);
            $groupeAModifier->nom = filter_var($_POST['grouptitle'], FILTER_SANITIZE_STRING);
            $groupeAModifier->save();
            // On supprime tous les liaisons pour ce groupe
            GroupeUtilisateur::getById($_POST['groupid'])->delete();
            // On rajoute des liaisons comme pour le rajout du groupe
            foreach (explode(',', $_POST['users']) as $idUser) {
                $nouveauGroupeUtilisateur = new GroupeUtilisateur();
                $nouveauGroupeUtilisateur->id_groupe = $_POST['groupid'];
                $nouveauGroupeUtilisateur->id_user = $idUser;
                $nouveauGroupeUtilisateur->save();
            }
            return $response->withHeader('Location', $routeParser->urlFor('groupes'))->withStatus(301);
        })->setName('modifygroup');
        $group->get('/deletegroup/{groupid}', function (Request $request, Response $response, $args) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $groupeASupprimer = Groupe::getById($args['groupid']);
            // On supprime les messages qui était dans le groupe
            $messagesASupprimer = $groupeASupprimer->messages();
            foreach ($messagesASupprimer as $messageASupprimer)
                $messageASupprimer->delete();
            // On supprime les liaisons avec cette groupe
            GroupeUtilisateur::getById($args['groupid'])->delete();
            // Et on supprime le groupe elle-même
            $groupeASupprimer->delete();
            return $response->withHeader('Location', $routeParser->urlFor('groupes'))->withStatus(301);
        })->setName('deletegroup');


    })->add(LoggedMiddleware::class);

};
