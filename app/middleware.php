<?php
declare(strict_types=1);

use Slim\App;
use App\Application\Middleware\SessionMiddleware;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    // Rajout twig
    $app->add(TwigMiddleware::createFromContainer($app));
};
