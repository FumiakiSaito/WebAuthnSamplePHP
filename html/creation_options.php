<?php
require_once "./vendor/autoload.php";
require_once "./db.php";
require_once "./PublicKeyCredentialSourceRepository.php";

use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialDescriptor;
use Cose\Algorithms;

/**
 * 認証器から公開鍵クレデンシャルを取得するためのパラメータである
 * 公開鍵クレデンシャル生成オプション(PublicCredentialCreationOptions)を作成する
 *
 * 本パラメータはJavaScriptの公開鍵クレデンシャル生成API（navigator.credentials.create）で使用する
 */

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
// RPサーバに登録したいユーザー情報を設定
// -----------------------------------------------------------------
$userEntity = new PublicKeyCredentialUserEntity(
    $json->username,        // name
    $json->username,        // id
    strtoupper($json->username) // displayName
);

// -----------------------------------------------------------------
// 認証器への要求事項を設定
//
// 1. 認証機の接続方法を指定
// 2. 認証器でレジデントクレデンシャルを保管するか
// 3. 認証器に糖鎖された生体認証やPINでユーザーを検証するかを指定
// -----------------------------------------------------------------
$authenticatorSelectionCriteria = new AuthenticatorSelectionCriteria(
    AuthenticatorSelectionCriteria:: AUTHENTICATOR_ATTACHMENT_CROSS_PLATFORM, // ローミング認証器
    false,
    AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_PREFERRED   // ユーザー検証を可能な限り行う
);

// -----------------------------------------------------------------
// リプレイ攻撃を回避するためのワンタイム乱数
// -----------------------------------------------------------------
$challenge = random_bytes(16);

// -----------------------------------------------------------------
// ユーザーの登録操作にかかるタイムアウト時間 (ms)
// -----------------------------------------------------------------
$timeout = 10000;

// -----------------------------------------------------------------
// クレデンシャルの生成方法を設定
//
// 1. クレデンシャルの種類: 'public-key'(公開鍵)のみ定義されている
// 2. 暗号化アルゴリズムのID
// 優先度が高い順に設定
// -----------------------------------------------------------------
$publicKeyCredentialParametersList = [
    new PublicKeyCredentialParameters(
        PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
        Algorithms::COSE_ALGORITHM_ES256
    ),
    new PublicKeyCredentialParameters(
        PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
        Algorithms::COSE_ALGORITHM_RS256
    ),
];


// -----------------------------------------------------------------
// 公開鍵クレデンシャルの生成から除外したいクレデンシャル設定
//
// 1. クレデンシャルの種類: 'public-key'(公開鍵)のみ定義されている
// 2. 除外したいクレデンシャルのID
// 3. 認証器の接続方法 ※任意 (USB, NFC BLE, プラットフォーム認証器)
// -----------------------------------------------------------------
$excludedPublicKeyDescriptors = [
    new PublicKeyCredentialDescriptor(
        PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
        'ABCDEFGH…',
        [
            PublicKeyCredentialDescriptor::AUTHENTICATOR_TRANSPORT_BLE,
            PublicKeyCredentialDescriptor::AUTHENTICATOR_TRANSPORT_NFC
        ]
    )
];
// 例えば公開鍵リポジトリに登録済みの認証器を除外したい場合は、以下のようにする
/*
    $publicKeyCredentialSourceRepository = new PublicKeyCredentialSourceRepository();
    $credentialSources = $publicKeyCredentialSourceRepository->findAllForUserEntity($userEntity);
    $excludedPublicKeyDescriptors = array_map(function (PublicKeyCredentialSource $credential) {
        return $credential->getPublicKeyCredentialDescriptor();
    }, $credentialSources);
*/

// -----------------------------------------------------------------
// 公開鍵クレデンシャル生成オプションを作成する
// -----------------------------------------------------------------
$publicKeyCredentialCreationOptions = new PublicKeyCredentialCreationOptions(
    $rpEntity,
    $userEntity,
    $challenge,
    $publicKeyCredentialParametersList,
    $timeout,
    $excludedPublicKeyDescriptors,
    $authenticatorSelectionCriteria,
    // 認証器のアテステーションステートメント(認証器の信頼性に関する情報)を要求するか (none: 要求しない)
    PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
    // 拡張
    null
);

// 後で取り出したいのでセッションに保存しておく
session_start();
$_SESSION['creation'] = serialize($publicKeyCredentialCreationOptions);

echo json_encode($publicKeyCredentialCreationOptions);
