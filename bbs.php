<?php
// セッションスタート
session_start();

// ログインしてない場合、login.phpへ戻る
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}

// DB接続ファイル読み込み
require_once('db_connect.php');

// 初期化
$edit_id = null;

// 投稿保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['post'])) {
    // 投稿処理（DBに保存）
    $post = $_POST['post'];
    $username = $_SESSION['user'];

    // prepareメソッドで以下のSQL文を準備
    // executeメソッドでSQLを実行
    if (!empty($post)) {
      $stmt = $pdo->prepare("INSERT INTO posts (username,content,created_at) VALUES (?,?,NOW())");
      $stmt->execute(([$username, $post]));
    }
  } elseif (isset($_POST['delete'])) {
    // 削除処理（DBから削除）
    $post_id = $_POST['delete'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND username = ?");
    $stmt->execute([$post_id, $_SESSION['user']]);
  } elseif (isset($_POST['edit'])) {
    // 編集ファーム表示用（対象の投稿IDを保持）
    $edit_id = $_POST['edit'];
  } elseif (isset($_POST['save_edit'])) {
    // 編集保存（DBを更新）
    $post_id = $_POST['save_edit'];
    $new_content = $_POST['edited_content'];

    $stmt = $pdo->prepare(("UPDATE posts SET content = ? WHERE id = ? AND username = ?"));
    $stmt->execute([$new_content, $post_id, $_SESSION['user']]);
  }
}

$stmt = $pdo->query(("SELECT * FROM posts ORDER BY created_at DESC"));
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
      <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $p): ?>
          <div class="post-card">
            <strong><?php echo htmlspecialchars($p['id']); ?></strong><br>
            <strong><?php echo htmlspecialchars($p['username']) ?></strong><br>

            <?php if (isset($edit_id) && $edit_id == $p['id']): ?>
              <!-- 編集フォーム表示 -->
              <form method="post">
                <input type="hidden" name="save_edit" value="<?php echo $p['id']; ?>">
                <textarea name="edited_content"><?php echo htmlspecialchars($p['content']); ?></textarea>
                <br>
                <button type="submit">編集を保存</button>
              </form>
            <?php else: ?>
              <!-- 通常表示 -->
              <p><?php echo nl2br(htmlspecialchars($p['content'])); ?></p>
              <small>投稿日：<?php echo htmlspecialchars($p['created_at']); ?></small>

              <!-- 編集ボタン -->
              <?php if ($_SESSION['user'] === $p['username']) ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="edit" value="<?php echo $p['id']; ?>">
                <button type="submit">編集</button>
              </form>
            <?php endif; ?>

            <!-- 削除ボタン -->
            <!-- ログインユーザーと投稿ユーザーが一致している場合のみ、削除ボタンを表示 -->
            <?php if ($_SESSION['user'] === $p['username']): ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="delete" value="<?php echo $p['id']; ?>">
                <button type="submit">削除</button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>まだ投稿はありません</p>
      <?php endif; ?>
    </div>

    <div class="logout">
      <a href="logout.php" onclick="return confirm('本当にログアウトしますか？');">ログアウト</a>
    </div>
  </div>
</body>

</html>