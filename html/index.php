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

$users = DB::query("SELECT * FROM test_table");
echo '<pre>';
var_dump($users);
echo '</pre>';

$user = DB::queryFirstRow("SELECT * FROM test_table WHERE name = %s", 'fumiaki');
echo '<pre>';
var_dump($user);
echo '</pre>';

// これはユニークキーが重複するので登録されない
DB::insertIgnore('test_table', ['email' => 'a.jp', 'name' => 'fumiaki']);
$users = DB::query("SELECT * FROM test_table");
echo '<pre>';
var_dump($users);
echo '</pre>';

// これはユニークキーが重複しないので登録される
DB::insertIgnore('test_table', ['email' => time().'.jp', 'name' => 'fumiaki']);
$users = DB::query("SELECT * FROM test_table");
echo '<pre>';
var_dump($users);
echo '</pre>';
