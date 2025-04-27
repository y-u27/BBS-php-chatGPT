<?php
// セッションスタート
session_start();

// セッション変数を全て破棄
$_SESSION = [];

// セッションを完全に破棄
session_destroy();

// login.phpにリダイレクト
header('Location: login.php');
exit();
?>