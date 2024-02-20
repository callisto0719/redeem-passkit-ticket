<?php

use Google\CloudFunctions\FunctionsFramework;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ServerRequestInterface;

FunctionsFramework::http('redeemTicket', 'redeemTicket');

/**
 * 送信された値からチケットのIDを取得して、APIを叩く
 */
function redeemTicket(ServerRequestInterface $request): string
{
    // 以下はお試し
    $name = 'World';
    $body = $request->getBody()->getContents();
    if (!empty($body)) {
        $json = json_decode($body, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new RuntimeException(sprintf(
                'Could not parse body: %s',
                json_last_error_msg()
            ));
        }
        $name = $json['name'] ?? $name;
    }

    // クエリパラメータはこうやって受け取る
    $queryString = $request->getQueryParams();
    $name = $queryString['name'] ?? $name;

    if (sendToSlack('slackで通知が送れるのかテスト中') !== 200) {
        print 'Failed to send message to Slack';
    } else {
        print 'Message sent to Slack';
    }

    print "\n";
    return sprintf('Hello, %s!', htmlspecialchars($name));
}

/**
 * Slackにメッセージを送信する
 * 
 * @param string $message 送信するメッセージ
 * @return int HTTPステータスコード
 */
function sendToSlack(string $message): int
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

    return $response->getStatusCode();
}
