<?php


namespace App\Application\Actions;


use App\Domain\Message\Message;
use App\Domain\Messagerie\Messagerie;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class AddMessageAction
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
        $nouveauMessage = new Message();

        $nouveauMessage->id_user_auteur = $_POST['authorid'];
        $nouveauMessage->contenu = filter_var($_POST['content'], FILTER_SANITIZE_STRING);

        $datetime = new DateTime('now');
        $nouveauMessage->date = $datetime->format('Y-m-d H:i:s');

        $nouveauMessage->save();

        $nouveauMessagerie = new Messagerie();

        $nouveauMessagerie->id_groupe = $_POST['groupid'];
        $nouveauMessagerie->id_message = $nouveauMessage->id;

        $nouveauMessagerie->save();

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->urlFor('messagerie', ['groupid' => $_POST['groupid']]))->withStatus(301);
    }
}