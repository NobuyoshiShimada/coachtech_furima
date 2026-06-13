# mogitate

##　環境構築
**Dockerビルド**
1. `git clone git@github.com:NobuyoshiShimada/test_mogitate.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*
``` bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. stripe決済の公式ライブラリをインストール
   `composer require stripe/stripe-php`
4. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
5. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=test_mogitate_db
DB_USERNAME=test_mogitate_user
DB_PASSWORD=test_mogitate_pass
STRIPE_KEY=your_stripe_publishable_key_here
STRIPE_SECRET=your_stripe_secret_key_here

※ `your_...` の部分には、ご自身のStripeダッシュボードから取得したテスト用のAPIキーを入力してください。
```

6. アプリケーションキーの作成
``` bash
php artisan key:generate
```

7. マイグレーションの実行
``` bash
php artisan migrate
```

8. シーディングの実行
``` bash
php artisan db:seed
```

## 使用技術(実行環境)
- macOS Swquoia 15.6
- PHP8.5.3
- Laravel8.83.29
- MySQL8.0.26
- nginx 1.21.1

## ER図
![alt](test_coachtech_furima_.png)

## URL
- 開発環境：http://localhost/products
- phpMyAdmin:：http://localhost:8080/
