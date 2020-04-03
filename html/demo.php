<?php
require_once "./vendor/autoload.php";
require_once "./db.php";
require_once "./PublicKeyCredentialSourceRepository.php";
use Webauthn\Server;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialDescriptor;
use Cose\Algorithms;

if ($_POST['id'] && $_POST['username'] && $_POST['displayname']) {

    $rpEntity = new PublicKeyCredentialRpEntity(
        'Webauthn Server',
        'localhost' // https環境のドメインか"localhost"以外だと失敗する
    );

    $userEntity = new PublicKeyCredentialUserEntity(
        $_POST['username'],
        $_POST['id'],
        $_POST['displayname']
    );

    $publicKeyCredentialSourceRepository = new PublicKeyCredentialSourceRepository();

    $server = new Server(
        $rpEntity,
        $publicKeyCredentialSourceRepository,
        null
    );

    $credentialSources = $publicKeyCredentialSourceRepository->findAllForUserEntity($userEntity);

    $excludeCredentials = array_map(function (PublicKeyCredentialSource $credential) {
        return $credential->getPublicKeyCredentialDescriptor();
    }, $credentialSources);



    // PINを要求しないように
    $authenticatorSelectionCriteria = new AuthenticatorSelectionCriteria(
        null,
        false,
        /* AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_REQUIRED,*/ // ユーザー検証を必要とする
        /* AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_PREFERRED*/ // ユーザー検証を可能な限り行う？
        AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_DISCOURAGED    // ユーザー検証しない
    );

    $challenge = random_bytes(16);
    $timeout = 10000; // ms
    $publicKeyCredentialParametersList = [
        new PublicKeyCredentialParameters('public-key', Algorithms::COSE_ALGORITHM_ES256),
        new PublicKeyCredentialParameters('public-key', Algorithms::COSE_ALGORITHM_RS256),
    ];

/*
    $creation = $server->generatePublicKeyCredentialCreationOptions(
        $userEntity,
        PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
        $excludeCredentials,
        $authenticatorSelectionCriteria
    );
*/

    $excludedPublicKeyDescriptors = [
        new PublicKeyCredentialDescriptor(PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY, 'ABCDEFGH…'),
    ];

    $publicKeyCredentialCreationOptions = new PublicKeyCredentialCreationOptions(
        $rpEntity,
        $userEntity,
        $challenge,
        $publicKeyCredentialParametersList,
        $timeout,
        $excludedPublicKeyDescriptors,
        $authenticatorSelectionCriteria,
        PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
        null
    );

    // 公開鍵を取り出すために保存しておく
    session_start();
    $_SESSION['creation'] = serialize($publicKeyCredentialCreationOptions);

    $creation = json_encode($publicKeyCredentialCreationOptions);
    //var_dump($creation);
?>

    <html lang="">
    <head>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <title>Register</title>
    </head>
    <body>
    <a href="/register.php"> To Register </a> <br>
    <a href="/login.php"> To Login </a>
    <script>
      const publicKey = <?php echo $creation; ?>;

      function arrayToBase64String(a) {
        return btoa(String.fromCharCode(...a));
      }

      function base64url2base64(input) {
        input = input
          .replace(/=/g, "")
          .replace(/-/g, '+')
          .replace(/_/g, '/');

        const pad = input.length % 4;
        if (pad) {
          if (pad === 1) {
            throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
          }
          input += new Array(5 - pad).join('=');
        }

        return input;
      }

      publicKey.challenge = Uint8Array.from(window.atob(base64url2base64(publicKey.challenge)), function (c) {
        return c.charCodeAt(0);
      });
      publicKey.user.id = Uint8Array.from(window.atob(publicKey.user.id), function (c) {
        return c.charCodeAt(0);
      });
      if (publicKey.excludeCredentials) {
        publicKey.excludeCredentials = publicKey.excludeCredentials.map(function (data) {
          data.id = Uint8Array.from(window.atob(base64url2base64(data.id)), function (c) {
            return c.charCodeAt(0);
          });
          return data;
        });
      }

      navigator.credentials.create({'publicKey': publicKey})
        .then(function (data) {
          const publicKeyCredential = {
            id: data.id,
            type: data.type,
            rawId: arrayToBase64String(new Uint8Array(data.rawId)),
            response: {
              clientDataJSON: arrayToBase64String(new Uint8Array(data.response.clientDataJSON)),
              attestationObject: arrayToBase64String(new Uint8Array(data.response.attestationObject))
            }
          };
          console.log(publicKeyCredential)
          axios.post("/do_register.php", publicKeyCredential).then(function(response){
            console.log(response);
            alert(response.data)
          });
        })
        .catch(function (error) {
          alert('Open your browser console!');
          console.log('FAIL', error);
        });

    </script>
    </body>
    </html>
<?php }else{ ?>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <title>登録</title>
    </head>
    <body>
    <form action="" method="POST">
        <input type="text" name="username" placeholder="ユーザー名"/>
        <input type="text" name="id" placeholder="ID"/>
        <input type="text" name="displayname" placeholder="表示名"/>
        <input type="submit"/>
    </form>

    <a href="/register.php"> To Register </a> <br>
    <a href="/login.php"> To Login </a>
    </body>
    </html>
    <?php
}


