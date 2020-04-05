# WebAuthnSamplePHP

## Description

PHPで作成したWebAuthnのデモサイトです。  
数コマンドでサイトが起動し、  
認証器の登録・認証を試す事ができます。



## Requirement

* Docker

## Usage

#### hostsに接続ドメインを追記  
※自己証明書でhttps化し、下記ドメインでアクセスできるようにします。  
MACの場合）/private/etc/hosts

```
127.0.0.1 localhost.webauthndemo
```

#### dockerコンテナ起動  
下記コンテナが起動します。
* PHP7 & Apache2
* PHPMyAdmin
* MySQL5.7
* Nginx (自己証明書でhttps化のためのリバースプロキシ)

```
docker-compose up -d
```

#### PHPのパッケージをインストール

```
docker ps
docker exec -it {phpコンテナID} /bin/bash -c "cd /var/www/html && composer install"
```

### ブラウザでデモページを開く  

* デモ画面  
※Chromeは自己証明書のサイトを開けないため、Safari, FireFox等を使用する

```
open https://localhost.webauthndemo/
```

* phpMyAdmin

```
open http://localhost:4000/index.php?lang=ja
```


## NOTE

* WebAuthnの仕様的には、セキュアなhttpsかlocalhostのみ許容。
* 使用したPHPパッケージ(web-auth/webauthn-lib)ではhttpsしか許容していないっぽいので自己証明書で対応…

* 公開鍵要求でallowdCredentialsに入っていない要求を出したときの挙動がブラウザによって違う
* * Firefox: 接続要求は出るが「InvalidStateError: An attempt was made to use an object that is not, or is no longer, usable」が出る
* * Safari: 接続要求自体でない


https://www.w3.org/TR/webauthn/
