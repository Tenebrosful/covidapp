<?php
declare(strict_types=1);

use App\Application\Actions\AuthenticateAction;
use App\Application\Actions\Messages\MessageReadAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\WelcomeAction;
use App\Application\Middleware\LoggedMiddleware;
use App\Application\Middleware\SessionMiddleware;
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
        // Affiche le formulaire de connexion (rajout en double pour une version avec et sans le message)
        $group->get('/signin', function (Request $request, Response $response, $args) {
            session_start();
            return $this->get('view')->render($response, 'signin.html', [
                'message' => isset($_SESSION['message']) ? $_SESSION['message'] : '',
            ]);
        })->setName('signin');

        // Action pour authentifier l'utilisateur
        $group->post('/authenticate', AuthenticateAction::class)->setName('authenticate');
    });

    $app->group('', function (Group $group) {
        // Action pour souhaiter la bienvenue à l'utilisateur
        $group->get('/welcome', WelcomeAction::class)->setName('welcome');

        $group->get('/messages/{id}', MessageReadAction::class)->setName('messages-get');
    })->add(LoggedMiddleware::class);

};