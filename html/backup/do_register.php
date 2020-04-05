<?php
require_once "./vendor/autoload.php";
require_once "./db.php";
require_once "./PublicKeyCredentialSourceRepository.php";
use Webauthn\Server;
use Webauthn\PublicKeyCredentialRpEntity;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
var_dump(file_get_contents("php://input"));
session_start();

// -----------------------------------------------------------------
// RPサーバの作成
// -----------------------------------------------------------------
$rpEntity = new PublicKeyCredentialRpEntity(
    'WebAuthnDemoRP',        // RPサーバのname
    'localhost.webauthndemo' // RPサーバのid(ドメイン名を設定する)
);
$publicKeyCredentialSourceRepository = new PublicKeyCredentialSourceRepository();
$server = new Server(
    $rpEntity,
    $publicKeyCredentialSourceRepository,
    null
);

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);
$serverRequest = $creator->fromGlobals();

try {

    // -----------------------------------------------------------------
    // チェック
    // -----------------------------------------------------------------
    $publicKeyCredentialSource = $server->loadAndCheckAttestationResponse(
        file_get_contents("php://input"),
        unserialize($_SESSION['creation']),
        $serverRequest
    );
    // 公開鍵クレデンシャルを公開鍵リポジトリに追加
    $publicKeyCredentialSourceRepository->saveCredentialSource($publicKeyCredentialSource);
    echo "success!";
} catch(\Throwable $exception) {
    var_dump($exception);
}