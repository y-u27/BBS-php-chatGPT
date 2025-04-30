<?php
// セッションスタート
session_start();

// ログインしてない場合、login.phpへ戻る
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}

// 投稿一覧が未定義なら初期化する
if (!isset($_SESSION['posts'])) {
  $_SESSION['posts'] = [];
}

// 投稿保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['post'])) {
    // 投稿処理
    $post = $_POST['post'];
    // 投稿日時設定
    $now = date('Y/m/d H:i');
    // 投稿ID設定
    $id = count($_SESSION['posts']) + 1;
    $_SESSION['posts'][] = ['id' => $id, 'user' => $_SESSION['user'], 'content' => $post, 'created_at' => $now];
  } elseif (isset($_POST['delete'])) {
    // 削除機能
    $delete_index = $_POST['delete'];
    if (isset($_SESSION['posts'][$delete_index])) {
      unset($_SESSION['posts'][$delete_index]);
      // 配列の添字を振り直す(きれいにする)
      $_SESSION['posts'] = array_values($_SESSION['posts']);
    }
  } elseif (isset($_POST['edit'])) {
    // 編集フォーム表示用(あとで表示側で使う)
    $edit_index = $_POST['edit'];
  } elseif (isset($_POST['save_edit'])) {
    // 編集内容保存
    $index = $_POST['save_edit'];
    $new_content = $_POST['edited_content'];
    $_SESSION['posts'][$index]['content'] = $new_content;
  }
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
        <?php foreach ($_SESSION['posts'] as $i => $p): ?>
          <div class="post-card">
            <strong><?php echo $p['id']; ?></strong><br>
            <strong><?php echo htmlspecialchars($p['user']) ?></strong><br>

            <?php if (isset($edit_index) && $edit_index == $i): ?>
              <!-- 編集フォーム表示 -->
              <form method="post">
                <input type="hidden" name="save_edit" value="<?php echo $i; ?>">
                <textarea name="edited_content"><?php echo htmlspecialchars($p['content']); ?></textarea>
                <br>
                <button type="submit">編集を保存</button>
              </form>
            <?php else: ?>
              <!-- 通常表示 -->
              <p><?php echo nl2br(htmlspecialchars($p['content'])); ?></p>
              <small>投稿日：<?php echo $p['created_at']; ?></small>

              <!-- 編集ボタン -->
              <form method="post" style="display:inline;">
                <input type="hidden" name="edit" value="<?php echo $i; ?>">
                <button type="submit">編集</button>
              </form>

              <!-- 削除ボタン -->
              <!-- ログインユーザーと投稿ユーザーが一致している場合のみ、削除ボタンを表示 -->
              <?php if ($_SESSION['user'] === $p['user']): ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="delete" value="<?php echo $i; ?>">
                  <button type="submit">削除</button>
                </form>
              <?php endif; ?>
            <?php endif; ?>
          </div>
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