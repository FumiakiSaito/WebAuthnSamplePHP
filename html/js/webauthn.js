/**
 * 「アカウント作成」ボタン押下の処理
 * @returns {Promise<void>}
 */
async function registerAsync() {

  if (!window.PublicKeyCredential) {
    alert("未対応のブラウザです");
    return;
  }

  try {
    // RPサーバから公開鍵クレデンシャル生成オプションを取得
    const optionsRes = await postAttestationOptions();
    const optionsJSON = await optionsRes.json();

    // 認証器からアテステーションレスポンスを取得
    const credential = await createCredential(optionsJSON);
    // RPサーバにアテステーションレスポンスを送信
    const response = await registerFinish(credential);
    // ログインページへ移動
    redirectToSignInPage(response);
  } catch (error) {
    alert(error);
  }
}

function postAttestationOptions() {
  const url = '/creation_options.php';
  const data = {
    'username': document.getElementById('username').value,
  };

  return fetch(url, {
    method: 'POST',
    body: JSON.stringify(data),
    headers: {
      'Content-Type': 'application/json'
    }
  });
}

function createCredential(options) {
  // ArrayBufferに変換
  options.challenge = Uint8Array.from(window.atob(base64url2base64(options.challenge)), function (c) {
    return c.charCodeAt(0);
  });
  options.user.id = Uint8Array.from(window.atob(options.user.id), function (c) {
    return c.charCodeAt(0);
  });
  if (options.excludeCredentials) {
    options.excludeCredentials = options.excludeCredentials.map(function (data) {
      data.id = Uint8Array.from(window.atob(base64url2base64(data.id)), function (c) {
        return c.charCodeAt(0);
      });
      return data;
    });
  }





  // 認証器からアテステーションレスポンスを取得するWebAuthn API
  return navigator.credentials.create({
    'publicKey': options
  });
}

function registerFinish(credential) {
  const url = '/register.php';

  const publicKeyCredential = {
    id: credential.id,
    type: credential.type,
    rawId: arrayToBase64String(new Uint8Array(credential.rawId)),
    response: {
      clientDataJSON: arrayToBase64String(new Uint8Array(credential.response.clientDataJSON)),
      attestationObject: arrayToBase64String(new Uint8Array(credential.response.attestationObject))
    }
  };

  return fetch(url, {
    method: 'POST',
    body: JSON.stringify(publicKeyCredential),
    headers: {
      'Content-Type': 'application/json'
    }
  });
}

function redirectToSignInPage(response) {
  if (response.ok) {
    alert('登録しました');
    location.href = 'index.php'
  } else {
    alert(response);
  }
}



/*----------------------------------------------------
 * 認証
 *--------------------------------------------------*/

async function authenticationAsync() {
  // ※サンプルコードでは、WebAuthn API対応判定を追加
  if (!window.PublicKeyCredential) {
    alert("未対応のブラウザです");
    return;
  }

  try {
    // RPサーバから公開鍵クレデンシャル要求オプションを取得
    const optionsRes = await postAssertionOptions();
    const optionsJSON = await optionsRes.json();
    // 認証器からアサーションレスポンスを取得
    const assertion = await getAssertion(optionsJSON);
    // RPサーバにアサーションレスポンスを送信
    const response = await authenticationFinish(assertion);
    signedIn(response);
  } catch (error) {
    alert(error);
  }
}

function postAssertionOptions() {
  const url = '/assertion/options';
  const data = {
    'email': document.getElementById('email').value
  };

  return fetch(url, {
    method: 'POST',
    body: JSON.stringify(data),
    headers: {
      'Content-Type': 'application/json'
    }
  });
}

function getAssertion(options) {
  // ArrayBufferに変換
  options.challenge = stringToArrayBuffer(options.challenge.value);
  options.allowCredentials = options.allowCredentials
    .map(credential => Object.assign({},
      credential, {
        id: base64ToArrayBuffer(credential.id),
      }));

  // 認証器からアサーションレスポンスを取得するWebAuthn API
  return navigator.credentials.get({
    'publicKey': options
  });
}

function authenticationFinish(assertion) {
  const url = '/assertion/result';
  const data = {
    'credentialId': arrayBufferToBase64(assertion.rawId),
    'clientDataJSON': arrayBufferToBase64(assertion.response.clientDataJSON),
    'authenticatorData': arrayBufferToBase64(assertion.response.authenticatorData),
    'signature': arrayBufferToBase64(assertion.response.signature),
    'userHandle': arrayBufferToBase64(assertion.response.userHandle),
  };
  return fetch(url, {
    method: 'POST',
    body: JSON.stringify(data),
    headers: {
      'Content-Type': 'application/json'
    }
  });
}

function signedIn(response) {
  if (response.ok) {
    alert('ログインしました');
  } else {
    alert(response);
  }
}



/*----------------------------------------------------
 * 共通
 *--------------------------------------------------*/


// demo.phpから追加
function arrayToBase64String(a) {
  return btoa(String.fromCharCode(...a));
}

// demo.phpから追加
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



// 文字列をArrayBufferに変換
function stringToArrayBuffer(string) {
  return new TextEncoder().encode(string);
}

// Base64文字列をArrayBufferにデコード
function base64ToArrayBuffer(base64String) {
  return Uint8Array.from(atob(base64String), c => c.charCodeAt(0));
}

// ArrayBufferをBase64文字列にエンコード
function arrayBufferToBase64(arrayBuffer) {
  return btoa(String.fromCharCode(...new Uint8Array(arrayBuffer)));
}
