# 二次会用出席管理システム チケットもぎり部分
Cloud Functionsで稼働することを前提としている。

叩くAPI  
https://docs.passkit.io/protocols/event-tickets/#operation/EventTickets_redeemTicket  

PHPのライブラリはgRPCを利用する必要があり、  
extensionの追加ビルドなどが~~めんどくさい~~Cloud Functions環境での再現が難しそうだったので、  
REST APIを使ってチケットをもぎる方針にした。

# 想定挙動
1. QRで発行されたチケットの情報を読み取る (このリポジトリの範囲外)
    - 必要な情報は下記
        - ticket_id

2. 必要な情報をPassKit SDKを持つ場所に向けてリクエストで飛ばす (このリポジトリの範囲外)
3. リクエストを受け取り、必要情報をPasskit REST APIを使って送信する。  (このリポジトリの業務)

# このAPIが期待するリクエスト
* HTTP method
  * POST
* Request Body Schema
  * application/json
    ```json
    {
        "ticket_id": "string"
    }
    ```

# deployについて
このリポジトリのコードはGCP上で稼働することを前提としている。
* Cloud Functions
  * このソースが動く場所
* Secret Manager
  * PasskitのAPI KEY等の管理用、必要な値は下記
    * PASSKIT_API_KEY
    * PASSKIT_API_SECRET
deployする際はsecret managerから上記の値を読み取る前提なので、  
環境変数として素直に渡したい場合は適宜書き換えること。
```sh
$ cd {このリポジトリの場所}
$ . gcloud.sh deploy-function
```


# localで試すとき
1. php8.2を用意する
2. 関連ライブラリをinstallする
    ```sh
    $ cd {このリポジトリの場所}/src
    // composerはpathを通すか、pharファイルから呼ぶ
    $ composer install
    ```
3. phpのビルトインウェブサーバーを使って起動する
    ```sh
    $ cd {このリポジトリの場所}
    $ vim .env # env.exampleを参考に作る
    $ . gcloud.sh run-local
    ```