<?php
$host = 'localhost';
$dbname = 'bbs-app';
$user = 'root';
$pass = 'root';

try {
  // DSN (Data Source Name) を使ってPDOオブジェクトを作成
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
  // エラーモードを例外に設定
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  // 接続エラーが起きたら表示して終了
  echo '接続失敗:' . $e->getMessage();
  exit();
}
