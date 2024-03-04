<?php

namespace App\Libs;

use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use RuntimeException;

class TicketClient
{
    private const ALGORITHM = 'HS256';

    private readonly CarbonImmutable $now;

    public function __construct()
    {
        $this->now = CarbonImmutable::now();
    }

    /**
     * チケットを利用済みにする
     * 
     * @see https://docs.passkit.io/protocols/event-tickets/#operation/EventTickets_redeemTicket
     * 
     * @param string $ticket_id チケットのID
     * @return int 返却されたstatuscode
     */
    public function redeemTicket(string $ticket_id): int
    {
        $client = new Client();

        $response = $client->put(
            'https://api.pub2.passkit.io/eventTickets/ticket/redeem', [
            RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->generateJWT()}"
            ],
            RequestOptions::JSON => $this->getRequestBody4Redeem($ticket_id)
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to redeem ticket. StatusCode: ' . $response->getStatusCode());
        }

        return $response->getStatusCode();
    }

    /**
     * JWTを生成する
     * 
     * @see https://docs.passkit.io/protocols/event-tickets/#section/Authentication/REST
     */
    private function generateJWT(): string
    {
        $jwt_body = [
            'uid' => getenv('PASSKIT_API_KEY'),
            'iat' => $this->now->getTimestamp(), // 発行時間
            'exp' => $this->now->getTimestamp() + (60 * 10) // 有効期限 (+10分)
        ];

        $header = [
            'alg' => self::ALGORITHM,
            'typ' => 'JWT'
        ];

        return JWT::encode(
            payload: $jwt_body,
            key: getenv('PASSKIT_API_SECRET'),
            alg: self::ALGORITHM,
            head: $header
        );
    }

    /**
     * チケットのIDを受け取り、リクエストボディを生成する
     * 
     * @see https://docs.passkit.io/protocols/event-tickets/#operation/EventTickets_redeemTicket
     */
    private function getRequestBody4Redeem(string $ticket_id): array
    {
        return [
            'ticket' => [
                'ticketId' => $ticket_id
            ]
        ];
    }
}
