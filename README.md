# WebAuthnSamplePHP

## Description

PHPで作成したWebAuthnのデモサイトです。  
Dockerでローカル環境が起動し、  
実際に認証器の登録・認証を試す事ができます。

![demo](https://user-images.githubusercontent.com/11142740/79528806-ed744280-80a5-11ea-9fbb-c8f4234922e0.gif)

## Requirement

* Docker

## How to use

hostsにドメインを追記:  
※自己証明書でhttps化してアクセスできるようにするため

```
127.0.0.1 localhost.webauthndemo
```

dockerコンテナ起動:  
```
$ docker-compose up -d
```


パッケージインストール:
```
$ docker exec -it php-apache /bin/bash -c "cd /var/www/html && composer install"
```

デモページを開く: 

※Chromeは自己証明書のサイトを開けないため、Safari, FireFox等を使用する

```
$ open https://localhost.webauthndemo/signup.php
```

phpMyAdmin: 

```
$ open http://localhost:4000/index.php?lang=ja
```


## NOTE

###  起動コンテナ

* PHP7 & Apache2
* PHPMyAdmin
* MySQL5.7
* Nginx (自己証明書でhttps化のためのリバースプロキシ)

### 制限・注意
* WebAuthnの仕様的にはhttpsかlocalhostのみ許容  
* しかし使用する[web-auth/webauthn-lib](https://github.com/web-auth/webauthn-framework/)ではhttpsしか許容しておらずエラーとなる。対応予定？ [Issue](https://github.com/web-auth/webauthn-framework/issues/125)  
* そのため自己証明書を自動で生成しリバプロになるDocker:[https-portal](https://github.com/SteveLTN/https-portal)で対応した  
* FirefoxやSafariはChromeと挙動が違う場合がある…