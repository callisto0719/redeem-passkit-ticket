<?php

use App\Enums\LogSeverity;
use App\Libs\Logging;
use App\Libs\Slack;
use App\Libs\TicketClient;
use Google\CloudFunctions\FunctionsFramework;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

FunctionsFramework::http('redeemTicket', 'redeemTicket');

/**
 * 送信された値からチケットのIDを取得して、APIを叩く
 */
function redeemTicket(ServerRequestInterface $request): Response
{
    $slack = new Slack();
    $log = new Logging();

    $response_headers = ['Access-Control-Allow-Origin' => '*'];

    if ($request->getMethod() === 'OPTIONS') {
        $response_headers = array_merge($response_headers, [
            'Access-Control-Allow-Methods' => 'POST',
            'Access-Control-Allow-Headers' => 'Content-Type',
            'Access-Control-Max-Age' => '3600',
        ]);
        return new Response(204, $response_headers);
    }

    try {
        if ($request->getMethod() !== 'POST') {
            throw new RuntimeException('Invalid HTTP method.');
        }

        $body = $request->getBody()->getContents();
        if (empty($body)) {
            $log->write($body);
            throw new RuntimeException('Request body is empty.');
        }

        $json = json_decode($body, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new RuntimeException(sprintf(
                'Could not parse body: %s',
                json_last_error_msg()
            ));
        }

        $ticket_client = new TicketClient();
        $ticket_id = $json['ticket_id'];
        $log->write("Start redeem ticket. Ticket Number: {$ticket_id}", LogSeverity::INFO);
        $ticket_client->redeemTicket($ticket_id);

    } catch (Exception $e) {
        $slack->sendMessage("【ERROR】{$e->getMessage()}.");
        $log->write($e->getMessage(), LogSeverity::ERROR);
        return new Response(
            500,
            $response_headers,
            $e->getMessage()
        );
    }

    return new Response(
        200,
        $response_headers,
        htmlspecialchars($ticket_id)
    );
}
