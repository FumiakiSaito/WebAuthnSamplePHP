<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>WebAuthnデモ</title>
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css"/>
</head>
<body translate="no">
<div class="login-page">
    <h1>WebAuthnデモ</h1>
    <div class="form">
        <div class="inputWithIcon">
            <input type="text" placeholder="ユーザ名" value="taro" id="username"/>
            <i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i>
            <button class="btn-square" onclick="authenticationAsync()">サインイン</button>
        </div>
        <p class="message">アカウントが未登録の方は<a href="signup.php">こちら</a></p>
    </div>
</div>
<script src="js/webauthn.js"></script>
</body>
</html>
