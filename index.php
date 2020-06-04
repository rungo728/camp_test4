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
  var_dump($member['id']);
  var_dump($_POST);
  // もしメッセージが保存されたら
  if($_POST['message'] !== ''){
    // 下記条件を実行する
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

$page = $_REQUEST['page'];
if ($page == ''){
  $page = 1;
}
$page = max($page, 1);
// COUNTをcntで取得
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
// これで最小のページ数がわかる
$maxPage = ceil($cnt['cnt']/5);
// $maxpageのページ数よりも大きい数字を指定しても$maxpageが代入されそれ以上にならないようにする
$page = min($page, $maxPage);
// urlのpage=?の部分は１ページ目から５つずつ表示なので２ページ目以降で５の倍数ずつ増やす
// スタートの位置は0
$start = ($page - 1)*5;
// 投稿を取得するプログラム、queryメソッドで直接SQLを呼び出す
// mとpはテーブル名につけるショートカットの名前
$posts = $db->prepare('SELECT m.name,m.picture, p.* FROM members m,posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
// executeでやると文字列のパラメータとして渡されるので、bindparamを使用
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();


// もしresがクリックされた場合
if (isset($_REQUEST['res'])){
  // 返信処理、まずデータベースに問い合わせ(p.idも確認？)
  $response = $db->prepare('SELECT m.name,m.picture, p.* FROM members m,posts p WHERE m.id=p.member_id AND p.id=?');
  // p.idに対してurlパラメータの数字を指定する
  $response->execute(array($_REQUEST['res']));
  // 返事が返ってくる
  $table = $response->fetch();
  // @をつけてtableのnameとmessageも出力
  $message = '@'.$table['name'].''.$table['message'] .'＞';
}
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
      <nav>
       <ul class="nav_menu" >
         <li>
          <a href="logout.php">ログアウト</a>
         </li>
      </ul>
      </nav>
    </div>
  </header>
  <div id="content">      
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

    <div id="top_right">
      <h3>新着メッセージ投稿一覧</h3>
      <?php foreach($posts as $post):?>
      <div class="msg">
        <img src="member_pictures/<?php print(htmlspecialchars($post['picture'],ENT_QUOTES));?>" width="160" height="100" alt="新着メッセージ投稿の画像" />
        <!-- 変数$postの中からメッセージ部分を表示させる -->
        <p><?php print(htmlspecialchars($post['message'],ENT_QUOTES));?>
          <span class="name">（<?php print(htmlspecialchars($post['name'],ENT_QUOTES));?>）
          </span>
          <!-- Reを押すことでurlのパラメーターが変わりメッセージ投稿部分に名前が表示されるようにする -->
          [<a href="index.php?res=<?php print(htmlspecialchars($post['id'],ENT_QUOTES));?>">Re</a>]
        </p>
        <p class="day"><a href="show.php?id=<?php print(htmlspecialchars($post['id']));?>"><span class="new_date"><?php print(htmlspecialchars($post['created']));?></span>...続きを読む</a>
          <?php if ($post['reply_message_id'] > 0):?>
          <a href="show.php?id=<?php print(htmlspecialchars($post['reply_message_id'],ENT_QUOTES));?>"><span class="new_tag">返信元のメッセージ</span>
          </a>
          <?php endif; ?>
          <!-- どのidの投稿を削除するのかを指定 -->
          <!-- 自分が投稿したものだけを削除できるように、他人の投稿は削除できないように -->
          <?php if ($_SESSION['id'] == $post['member_id']): ?>
          [<a href="delete.php?id=<?php print(htmlspecialchars($post['id']));?>"
          style="color: #F33;">削除</a>]
          <?php endif; ?>
        </p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <ul class="paging">
    <?php if($page > 1): ?>
    <li><a href="index.php?page=<?php print($page-1);?>">前のページへ</a></li>
    <?php endif; ?>
    <?php if($page < $maxPage): ?>
    <li><a href="index.php?page=<?php print($page+1);?>">次のページへ</a></li>
    <?php endif; ?>
  </ul>
</body>
</html>
