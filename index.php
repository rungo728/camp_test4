<?php
// NOTICEエラーを非表示にする記述
error_reporting(E_ALL & ~E_NOTICE);
?>
<?php
// エラー確認のために記述
ini_set('display_errors', 1);

session_start();
require('dbconnect.php');
// session変数に保存したidとtimeがある場合は
// time()は現在の時刻、それよりも大きい、つまり現在の時刻から一時間以上経っている場合、自動的にログアウト
if (isset($_SESSION['id']) && $_SESSION['time']+ 3600 > time()){
  // ログインしている時
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
if(!empty($_POST)){
  if($_POST['message'] !== ''){
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?,reply_message_id=?, created=NOW()');
    // エラー確認のため記述
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $message->execute(array(
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']

    ));
    header('Location: index.php');
    exit();
  }
}
// 投稿を取得するプログラム、queryメソッドで直接SQLを呼び出す
// mとpはテーブル名につけるショートカットの名前
$posts = $db->query('SELECT m.name,m.picture, p.* FROM members m,posts p WHERE m.id=p.member_id ORDER BY p.created DESC');

// もしresがクリックされた場合
if (isset($_REQUEST['res'])){
  // 返信処理、まずデータベースに問い合わせ(p.idも確認？)
  $response = $db->prepare('SELECT m.name,m.picture, p.* FROM members m,posts p WHERE m.id=p.member_id AND p.id=?');
  // p.idに対してurlパラメータの数字を指定する
  $response->execute(array($_REQUEST['res']));
  // 返事が返ってくる
  $table = $response->fetch();
  // @をつけてtableのnameとmessageも出力
  $message = '@'.$table['name'].''.$table['message'];
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
          <textarea name="message" cols="50" rows="5"><?php print(htmlspecialchars($message,ENT_QUOTES));?></textarea>
          <!-- どのメッセージに対しての返信かを判別する -->
          <!-- value属性に該当の返信するメンバーのidを入れる -->
          <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'],ENT_QUOTES));?>" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>
    <!-- 配列の中身を精査していき、最後まで$postsから$postに繰り返し代入される -->
    <?php foreach($posts as $post):?>
    <div class="msg">
      <img src="member_pictures/<?php print(htmlspecialchars($post['picture'],ENT_QUOTES));?>" width="60" height="50" alt="" />
      <!-- 変数$postの中からメッセージ部分を表示させる -->
      <p><?php print(htmlspecialchars($post['message'],ENT_QUOTES));?>
        <span class="name">（<?php print(htmlspecialchars($post['name'],ENT_QUOTES));?>）
        </span>
        <!-- Reを押すことでurlのパラメーターが変わりメッセージ投稿部分に名前が表示されるようにする -->
        [<a href="index.php?res=<?php print(htmlspecialchars($post['id'],ENT_QUOTES));?>">Re</a>]
      </p>
      <p class="day"><a href="show.php?id=<?php print(htmlspecialchars($post['id']));?>"><?php print(htmlspecialchars($post['created'],ENT_QUOTES));?></a>
        <?php if ($post['reply_message_id'] > 0):?>
        <a href="show.php?id=<?php print(htmlspecialchars($post['reply_message_id'],ENT_QUOTES));?>">
        返信元のメッセージ</a>
        <?php endif; ?>
        [<a href="delete.php?id="
        style="color: #F33;">削除</a>]

      </p>
    </div>
    <?php endforeach; ?>
  </div>
  <ul class="paging">
    <li><a href="index.php?page=">前のページへ</a></li>
    <li><a href="index.php?page=">次のページへ</a></li>
  </ul>
</div>
</div>
</body>
</html>
