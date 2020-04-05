<?php
/**
 * 認証器から受け取った署名を、RPサーバの公開鍵で検証するエンドポイント
 */
require_once "./vendor/autoload.php";
require_once "./db.php";
require_once "./PublicKeyCredentialSourceRepository.php";
require_once "./PublicKeyCredentialUserEntityRepository.php";
use Webauthn\Server;
use Webauthn\PublicKeyCredentialRpEntity;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

session_start();

// -----------------------------------------------------------------
// 公開鍵情報取得
// -----------------------------------------------------------------
$publicKeyCredential = file_get_contents("php://input");
//var_dump($publicKeyCredential);

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


$UserEntityRepository = new PublicKeyCredentialUserEntityRepository();
$userEntity = $UserEntityRepository->findWebauthnUserByUsername('taro');

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
    $publicKeyCredentialSource = $server->loadAndCheckAssertionResponse(
        $publicKeyCredential,
        unserialize($_SESSION['creation']),
        $userEntity,
        $serverRequest
    );

    // 公開鍵リポジトリからユーザー情報を取得
    $user_handle = $publicKeyCredentialSource->getUserHandle();
    echo $user_handle;
} catch(\Throwable $exception) {
    var_dump($exception->getTraceAsString());
    var_dump($exception->getMessage());
}