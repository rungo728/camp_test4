<?php
session_start();
require('dbconnect.php');
// idが空だった場合はトップページに移動
if (empty($_REQUEST['id'])){
  header('Location: index.php');
  exit();
}

$posts = $db->prepare('SELECT m.name,m.picture, p.* FROM members m,posts p WHERE m.id=p.member_id AND p.id=?');
// urlのパラメータから取得されたidを使ってメッセージを１件取得する
$posts->execute(array($_REQUEST['id']));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>
	<link rel="stylesheet" href="css/reset.css" />
	<link rel="stylesheet" href="css/index.css" />
</head>

<body>
<header>
	<div class="header_inner">
		<h1>ひとこと掲示板</h1>
	</div>
</header>
<div id="content" style="min-height: 250px;">
  <p>&laquo;<a href="index.php">一覧にもどる</a></p>
  <!-- 投稿メッセージが存在する場合は -->
  <!-- SQLから受け取った$postsにfetchによって正常に$postに値が入れば -->
  <?php if ($post = $posts->fetch()):?>
    <div class="msg">
    <img src="member_pictures/<?php print(htmlspecialchars($post['picture']));?>"width="200" height="200"/>
    <p><?php print(htmlspecialchars($post['message']));?><span class="name">（<?php print(htmlspecialchars($post['name']));?>）</span></p>
    <p class="day"><?php print(htmlspecialchars($post['created']));?></p>
    </div>
  <?php else: ?>
  <p>その投稿は削除されたか、URLが間違えています</p>
  <?php endif; ?>
</div>
<footer>
  <p>Copyright (C) 2019-2020 Campus inc. All Right Reserved.</p>
</footer>
</body>
</html>
