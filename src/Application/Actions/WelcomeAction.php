<?php

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 */
final class WelcomeAction {
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
    ): ResponseInterface {
        session_start();
        if (isset($_SESSION['username']) && $_SESSION['username'] != '') {
            return $this->get('view')->render($response, 'welcome.html', [
                'nomutilisateur' => $_SESSION['username'],
            ]);
        } else
            return $response->withHeader('Location', 'signin')->withStatus(301);
    }
}
