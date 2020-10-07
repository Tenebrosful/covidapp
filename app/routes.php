<?php
declare(strict_types=1);

use App\Application\Actions\AuthenticateAction;
use App\Application\Actions\AddUserAction;
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

return function (App $app) {

    $app->get('/testtwig/{name}', function (Request $request, Response $response, $args) {
        $str = $this->get('view')->fetchFromString(
            '<p>Hi, my name is {{ name }}.</p>',
            [
                'name' => $args['name']
            ]
        );
        $response->getBody()->write($str);
        return $response;
    });

    $app->group('/account', function (Group $group) {
        // Affiche le formulaire d'inscription
        $group->get('/signup', function (Request $request, Response $response, $args) {
            return $this->get('view')->render($response, 'signup.html', [
                'message' => isset($_SESSION['message']) ? $_SESSION['message'] : '',
            ]);
        })->setName('signup');

        // Action pour rajouter un utilisateur
        $group->post('/adduser', AddUserAction::class)->setName('adduser');

        // Affiche le formulaire de connexion
        $group->get('/signin', function (Request $request, Response $response, $args) {
            return $this->get('view')->render($response, 'signin.html', [
                'message' => isset($_SESSION['message']) ? $_SESSION['message'] : '',
            ]);
        })->setName('signin');

        // Action pour authentifier l'utilisateur
        $group->post('/authenticate', AuthenticateAction::class)->setName('authenticate');

        // Action pour déconnecter l'utilisateur
        $group->get('/signout', function (Request $request, Response $response, $args) {
            session_destroy();
            return $response->withHeader('Location', 'signin')->withStatus(301);
        })->setName('signout')->add(LoggedMiddleware::class);

        // Formulaire pour changer le mot de passe
        $group->get('/formpassword', function (Request $request, Response $response, $args) {
            return $this->get('view')->render($response, 'formpassword.html', [
                'message' => isset($_SESSION['message']) ? $_SESSION['message'] : ''
            ]);
        })->setName('formpassword')->add(LoggedMiddleware::class);

        // Action pour changer le mot de passe
        $group->post('/changepassword', ChangePasswordAction::class)->setName('changepassword')->add(LoggedMiddleware::class);

        // Action pour supprimer l'utilisateur
        $group->get('/deleteuser', DeleteUserAction::class)->setName('deleteuser')->add(LoggedMiddleware::class);
    });

    $app->group('', function (Group $group) {
        // Action pour souhaiter la bienvenue à l'utilisateur
        $group->get('/welcome',  function (Request $request, Response $response, $args) {
            return $this->get('view')->render($response, 'welcome.html', [
                'nomutilisateur' => $_SESSION['username']
            ]);
        })->setName('welcome');
    })->add(LoggedMiddleware::class);

};
