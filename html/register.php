<?php
require_once "./vendor/autoload.php";
require_once "./db.php";
require_once "./PublicKeyCredentialSourceRepository.php";
use Webauthn\Server;
use Webauthn\PublicKeyCredentialRpEntity;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

session_start();

// -----------------------------------------------------------------
// 公開鍵情報取得
// -----------------------------------------------------------------
$publicKeyCredential = file_get_contents("php://input");
//$publicKeyCredential = json_decode($publicKeyCredential);
var_dump($publicKeyCredential);

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
        $publicKeyCredential,
        unserialize($_SESSION['creation']),
        $serverRequest
    );
    // 公開鍵クレデンシャルを公開鍵リポジトリに追加
    $publicKeyCredentialSourceRepository->saveCredentialSource($publicKeyCredentialSource);
    echo "success!";
} catch(\Throwable $exception) {
    var_dump($exception);
}