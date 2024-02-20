# 二次会用出席管理システム チケットもぎり部分
Cloud Functionsで稼働することを前提としている。
https://github.com/PassKit/passkit-php-quickstart を利用して発券したチケットをもぎる。

叩くAPI  
https://docs.passkit.io/protocols/event-tickets/#operation/EventTickets_redeemTicket  

↓ライブラリを使ったもぎり方(要gRPC extention)
https://github.com/PassKit/passkit-php-quickstart/blob/main/event-tickets/redeem-ticket.php

gRPC extentionはphp.iniファイルをデプロイに含めて有効にする必要がある
https://cloud.google.com/blog/ja/products/application-development/php-comes-to-cloud-functions
https://cloud.google.com/appengine/docs/standard/php-gen2/runtime?hl=ja#dynamically_loadable_extensions

# 想定挙動
1. QRで発行されたチケットの情報を読み取る (このリポジトリの範囲外)
    - 必要な情報は下記
        - 未定

2. 必要な情報をPassKit SDKを持つ場所に向けてリクエストで飛ばす (このリポジトリの範囲外)
3. リクエストを受け取り、必要情報をPasskit SDKを使って送信する。  (このリポジトリの業務)