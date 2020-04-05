<?php
/**
 * 認証器から公開鍵クレデンシャルを取得するためのパラメータ
 * 公開鍵クレデンシャル生成オプション(PublicCredentialCreationOptions)を作成するエンドポイント
 *
 * 本パラメータはJavaScriptの公開鍵クレデンシャル要求API（navigator.credentials.get）で使用する
 */
require_once "./vendor/autoload.php";
require_once "./db.php";
require_once "./PublicKeyCredentialSourceRepository.php";

use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialRequestOptions;

// -----------------------------------------------------------------
// パラメータ取得
// -----------------------------------------------------------------
$json = file_get_contents("php://input");
$json = json_decode($json);


// -----------------------------------------------------------------
// RPサーバの情報設定
// -----------------------------------------------------------------
$rpEntity = new PublicKeyCredentialRpEntity(
    'WebAuthnDemoRP',        // RPサーバのname
    'localhost.webauthndemo' // RPサーバのid(ドメイン名を設定する)
);

// -----------------------------------------------------------------
// RPサーバに「認証したいユーザー情報」を設定
// -----------------------------------------------------------------
$userEntity = new PublicKeyCredentialUserEntity(
    $json->username,        // name
    $json->username,        // id
    strtoupper($json->username) // displayName
);

$publicKeyCredentialSourceRepository = new PublicKeyCredentialSourceRepository();
$registeredAuthenticators = $publicKeyCredentialSourceRepository->findAllForUserEntity($userEntity);
$allowedCredentials = array_map(
    static function (PublicKeyCredentialSource $credential): PublicKeyCredentialDescriptor {
        return $credential->getPublicKeyCredentialDescriptor();
    },
    $registeredAuthenticators
);


$publicKeyCredentialRequestOptions = new PublicKeyCredentialRequestOptions(
    random_bytes(32),            // チャレンジ
    60000,                       // タイムアウト
    'localhost.webauthndemo',    // RP ID
    $allowedCredentials
);

// 後で取り出したいのでセッションに保存しておく
session_start();
$_SESSION['creation'] = serialize($publicKeyCredentialRequestOptions);

echo json_encode($publicKeyCredentialRequestOptions);
