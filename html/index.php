<?php
require_once 'vendor/autoload.php';

/**
 * MySQL接続テスト
 * Dockerではホストにコンテナ名を指定する (docker psのNAMES)
 */
DB::$host = 'webauthnsamplephp_mysql_1';
DB::$user= 'webauthn';
DB::$password = 'webauthn';
DB::$dbName = 'webauthn';

$result = DB::query("SELECT * FROM test_table WHERE name = %s", 'fumiaki');
if ($result) {
    echo 'MySQL接続OK';
} else {
    echo 'MySQL接続NG';
}
