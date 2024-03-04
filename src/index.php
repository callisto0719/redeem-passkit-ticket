<?php

use App\Enums\LogSeverity;
use App\Libs\Logging;
use App\Libs\Slack;
use App\Libs\TicketClient;
use Google\CloudFunctions\FunctionsFramework;
use Psr\Http\Message\ServerRequestInterface;

FunctionsFramework::http('redeemTicket', 'redeemTicket');

/**
 * 送信された値からチケットのIDを取得して、APIを叩く
 */
function redeemTicket(ServerRequestInterface $request): string
{
    $slack = new Slack();
    $log = new Logging();

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
        return $e->getMessage();
    }

    return sprintf('Ticket id %s has redeemed!', htmlspecialchars($ticket_id));
}
