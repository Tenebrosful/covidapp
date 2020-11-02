<?php
declare(strict_types=1);

use App\Application\Actions\AddGroupAction;
use App\Application\Actions\AddMessageAction;
use App\Application\Actions\AddUserAction;
use App\Application\Actions\AuthenticateAction;
use App\Application\Actions\CovidAction;
use App\Application\Actions\ChangePasswordAction;
use App\Application\Actions\DeleteUserAction;
use App\Application\Actions\ModifyGroupAction;
use App\Application\Actions\DeleteGroupAction;
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
        $group->get('/covid/{statutcovid}', CovidAction::class)->setName('covid')->add(LoggedMiddleware::class);

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

        // Action pour afficher la messagerie
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

        // Pour modifier un groupe donné
        $group->post('/modifygroup', ModifyGroupAction::class)->setName('modifygroup');

        // Action pour supprimer un groupe
        $group->get('/deletegroup/{groupid}', DeleteGroupAction::class)->setName('deletegroup');

        $group->get('/map', function (Request $request, Response $response, $args){
            return $this->get('view')->render($response, 'map.html');
        });

    })->add(LoggedMiddleware::class);

    $app->group('/apimap', function (Group $group) {
        $group->get('', function (Request $request, Response $response, $args) {
            $covideds = Utilisateur::all()->where("covid", "=", true);
            $covidedslocalisations = [];
            foreach ($covideds as $covided) {
                $covidedlocalisation = $covided->localisations()->toArray()[0];
                $covidedslocalisations[] = [$covidedlocalisation['latitude'], $covidedlocalisation['longitude']];
            }
            $response->getBody()->write(json_encode($covidedslocalisations));
            return $response->withHeader('Content-Type', 'application/json');
        });
        $group->post('', function (Request $request, Response $response, $args) {
        });
    });

};
