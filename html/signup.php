<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>WebAuthnデモ (登録)</title>
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css"/>
</head>
<body translate="no">
<div class="login-page">
    <div class="form">
        <h1>WebAuthnデモ (登録)</h1>
        <div class="inputWithIcon">
            <input type="text" placeholder="ユーザ名" value="" id="username"/>
            <i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i>
            <button class="btn-square" onclick="registerAsync()">サインアップ</button>
        </div>
        <p class="message">アカウントを作成済みの方は<a href="signin.php">こちら</a></p>
    </div>
</div>
<script src="js/webauthn.js"></script>
</body>
</html>