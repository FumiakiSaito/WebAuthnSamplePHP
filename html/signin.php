<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>WebAuthn認証</title>
    <link rel="stylesheet" href="css/bundle.css"/>
</head>
<body translate="no">
<div class="login-page">
    <div class="form">
        <input type="text" placeholder="ユーザ名" value="taro" id="username"/>
        <button onclick="authenticationAsync()">ログイン</button>
        <p class="message">アカウントが未登録ですか？<a href="signup.php">アカウント作成</a></p>
    </div>
</div>
<script src="js/webauthn.js"></script>
</body>
</html>
