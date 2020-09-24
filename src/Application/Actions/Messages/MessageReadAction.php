<?php

namespace App\Application\Actions\Messages;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Message\Message;

/**
 * Action
 */
final class MessageReadAction {
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
        // Collect input from the HTTP request
        $messageId = (int)$args['id'];

        Message::all();


        // Invoke the Domain with inputs and retain the result
        /*$userData = $this->userReader->getUserDetails($userId);

        // Transform the result into the JSON representation
        $result = [
            'user_id' => $userData->id,
            'username' => $userData->username,
            'first_name' => $userData->firstName,
            'last_name' => $userData->lastName,
            'email' => $userData->email,
        ];*/

        // Build the HTTP response
        $response->getBody()->write((string)json_encode(Message::all()));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
