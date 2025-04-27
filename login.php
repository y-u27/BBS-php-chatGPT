<?php
// セッションスタート
session_start();

// ログインチェック
if(isset($_SESSION['user'])) {
  // すでにログインしていたら掲示板へリダイレクト
  header('Location: bbs.php');
  exit();
}

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 入力されたユーザー名とパスワードを取得
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  // 仮の正しいユーザー情報
  $correct_username = 'testuser';
  $correct_password = 'password123';

  if ($username === $correct_username && $password === $correct_password) {
    // 正しい情報ならセッションに保存
    $_SESSION['user'] = $username;
    // 掲示板画面へ
    header('Location: bbs.php');
    exit();
  } else {
    // 間違っていた場合
    $error = 'ユーザー名またはパスワードが違います';
  }
}
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン</title>
</head>
<body>
  <h1>ログイン</h1>

  <?php if (!empty($error)) : ?>
    <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></p>
  <?php endif; ?>

  <form action="login.php" method="post">
    <div>
      <label>ユーザー名:<input type="text" name="username"/></label>
    </div>
    <div>
      <label>パスワード:<input type="password" name="password"/></label>
    </div>
    <div>
      <button type="submit">ログイン</button>
    </div>
  </form>
</body>
</html>