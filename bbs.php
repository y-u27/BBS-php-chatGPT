<?php
// セッションスタート
session_start();

// ログインしてない場合、login.phpへ戻る
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}

// 投稿保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $post = $_POST['post'];
  $_SESSION['posts'][] = ['user' => $_SESSION['user'], 'content' => $post];
}
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>シンプル掲示板</title>
  <link rel="stylesheet" href="bbs.css">
</head>
<body>
  <div class="container">

    <h1>掲示板</h1>
  
    <form action="bbs.php" method="post" class="post-form">
        <textarea name="post" placeholder="ここに投稿を書いてください" required></textarea><br>
        <button type="submit">投稿する</button>
    </form>
  
    <h2>投稿一覧</h2>
    <div class="posts">
      <?php if (!empty($_SESSION['posts'])): ?>
        <?php foreach ($_SESSION['posts'] as $p): ?>
          <?php if (isset($p['user']) && isset($p['content'])): ?>
            <div class="post-card">
              <strong><?php echo htmlspecialchars($p['user']); ?></strong><br>
              <p><?php echo nl2br(htmlspecialchars($p['content'])); ?></p>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <p>まだ投稿はありません</p>
      <?php endif; ?>
    </div>
  
    <div class="logout">
      <a href="logout.php">ログアウト</a>
    </div>
  </div>
  
</body>
</html>