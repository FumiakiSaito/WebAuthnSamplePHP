<?php
require_once "./vendor/autoload.php";
require_once "./db.php";
require_once "./PublicKeyCredentialSourceRepository.php";
use Webauthn\Server;
use Webauthn\PublicKeyCredentialRpEntity;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$rpEntity = new PublicKeyCredentialRpEntity(
    'WebAuthnDemoRP',
    'localhost.webauthndemo'
);

// 公開鍵リポジトリ
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
session_start();

try {
    $publicKeyCredentialSource = $server->loadAndCheckAttestationResponse(
        file_get_contents("php://input"),
        unserialize($_SESSION['creation']),
        $serverRequest
    );
    $publicKeyCredentialSourceRepository->saveCredentialSource($publicKeyCredentialSource);
    echo "success!";
} catch(\Throwable $exception) {
    var_dump($exception);

}