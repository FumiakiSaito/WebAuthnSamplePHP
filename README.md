# WebAuthnSamplePHP

## Requirement

Docker

## Usage

hostsファイルに以下を追記  
※WebAuthnはhttps必須のため自己証明書でhttps化し、  
下記ドメインでアクセスできるようにします。  
MACの場合）/private/etc/hosts
```
127.0.0.1 localhost.webauthndemo
```

dockerコンテナ起動  
以下のコンテナが起動します
* PHP7 & Apache2
* MySQL5.7
* PHPMyAdmin
* Nginx (自己証明書でhttps化のためのリバースプロキシ)

```
docker-compose up -d
```

composer install

```
docker ps
docker exec -it {phpコンテナID} /bin/bash -c "cd /var/www/html && composer install"
```

ブラウザで開く  
※Chromeは自己証明書のサイトを開けないため、FireFox等を使用する

```
open https://localhost.webauthndemo/
```


## NOTE

phpMyAdmin

```
open http://localhost:4000/index.php?lang=ja
```