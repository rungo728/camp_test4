<?php
session_start();
require('dbconnect.php');
// session変数に保存したidとtimeがある場合は
// time()は現在の時刻、それよりも大きい、つまり現在の時刻から一時間以上経っている場合、自動的にログアウト
if (isset($_SESSION['id']) && $_SESSION['time']+ 3600 > time()){
  // time()を代入することで時間を更新する
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  // idを使ってデータベースから会員情報を出力する
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
}else{
  // ログインしていない時にログイン画面に促す処理
  header('Location: login.php');
  exit();
}
// 投稿ボタンがクリックされれば
if (!empty($_POST)){

}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="css/style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <!-- 上記変数$memberのデータベース情報からname部分を取り出して出力 -->
        <dt><?php print(htmlspecialchars($member['name'], ENT_QUOTES));?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"></textarea>
          <input type="hidden" name="reply_post_id" value="" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

    <div class="msg">
    <img src="member_picture" width="48" height="48" alt="" />
    <p><span class="name">（）</span>[<a href="index.php?res=">Re</a>]</p>
    <p class="day"><a href="view.php?id="></a>
<a href="view.php?id=">
返信元のメッセージ</a>
[<a href="delete.php?id="
style="color: #F33;">削除</a>]
    </p>
    </div>

<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
