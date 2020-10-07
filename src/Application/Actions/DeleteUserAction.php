<?php

namespace App\Application\Actions;

use App\Domain\Utilisateur\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 */
final class DeleteUserAction
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
        $utilisateurasupprimer = Utilisateur::getByEmail($_SESSION['username']);
        $utilisateurasupprimer->delete();
        session_destroy();
        return $response->withHeader('Location', 'signin')->withStatus(301);
    }
}
