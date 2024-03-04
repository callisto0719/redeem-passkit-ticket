<?php

namespace App\Libs;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Slack
{
    function sendMessage(string $message): bool
    {
        $client = new Client();
        $message = [
            'username' => '二次会出席管理君',
            "icon_emoji" => ":ghost:",
            'text' => $message
        ];
        $response = $client->post(
            getenv('SLACK_WEBHOOK_URL'),
            options: [
                RequestOptions::BODY => json_encode($message),
            ]
        );

        return $response->getStatusCode() === 200;
    }
}
