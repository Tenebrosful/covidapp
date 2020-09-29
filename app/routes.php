<?php
declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use App\Application\Actions\AuthenticateAction;

use App\Application\Actions\Messages\MessageReadAction;

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

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

    // Affiche le formulaire de connexion (rajout en double pour une version avec et sans le message)
    $app->get('/signin', function (Request $request, Response $response, $args) {
        return $this->get('view')->render($response, 'signin.html', [
            'message' => isset($_POST['message']) ? $_POST['message'] : '',
        ]);
    })->setName('signin');

    // Action pour authentifier l'utilisateur
    $app->post('/authenticate', AuthenticateAction::class)->setName('authenticate');
    // Il faut récupérer le retour de AuthenticateAction et faire une redirection en fonction
    //$app->redirect()

    $app->get('/messages/{id}', MessageReadAction::class)->setName('messages-get');

    $app->group('/users', function (Group $group) {
    });
};
