<?php
// NOTICEエラーを非表示にする記述
error_reporting(E_ALL & ~E_NOTICE);
?>
<!-- データベースに接続するためプログラム -->
<?php
session_start();
require('dbconnect.php');
// ログインボタンが押されて呼び出されたのかまたはログイン画面が初めて開かれて呼び出されたのかの判断を記述
if ($_COOKIE['email'] !== ''){
  // 変数$email
  $email = $_COOKIE['email'];
}
// $_POSTが空でなく、ログインボタンを押した時に
if(!empty($_POST)){
  // $_POST['email']で$emailを上書きする
  $email = $_POST['email'];
  // データベースから情報を引っ張ってくる
  if ($_POST['email']!== '' && $_POST['password'] !== ''){
    // データベースにお問い合わせ
    $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
    // 次にemail=?とpassword=?の部分に登録された情報を指定すればデータベースに問い合わせすることができる
    $login->execute(array(
      $_POST['email'],
      // 入力したパスワードを暗号化する
      sha1($_POST['password'])
    ));
    // データが返ってきていればログイン成功
    $member = $login->fetch();
    // ログインが成功していれば
    if ($member){
      // ログインした情報をsession変数に保存しておくため
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();
      // トップページに遷移する直前の部分で
      // $_POSTのsaveキーがオンになっていれば
      if ($_POST['save']=== 'on'){
        // メールアドレスをcookieに保存する
        setcookie('email',$_POST['email'],time()+60*60*24*14);

      }
      header('Location: index.php');
      exit();
    }else{
      // ここで一度記録しておいてログイン画面上でエラー表示させる
      $error['login'] = 'failed';
    }
  }else{
    // メールアドレス、パスワードどちらかが空である場合
    $error['login'] = 'blank';
  }

}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/reset.css" />  
<link rel="stylesheet" type="text/css" href="css/index.css" />
<title>ログインする</title>
</head>

<body>
  <header>
    <div class="header_inner">
      <h1>ひとこと掲示板</h1>
    </div>
  </header>
  <div id="content">
    <div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
      <p>入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="join/">入会手続きをする</a></p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input class="input-default" type="text" name="email" size="35" maxlength="255" value="<?php print (htmlspecialchars($email,ENT_QUOTES)); ?>" />
          <?php if($error['login'] === 'blank'):?>
            <p class="error">メールアドレスとパスワードをご記入ください</p>
          <?php endif; ?>
          <?php if($error['login'] === 'failed'):?>
            <p class="error">ログインに失敗しました。正しくご記入ください</p>
          <?php endif; ?>
        </dd>
        <dt>パスワード</dt>
        <dd>
          <input class="input-default" type="password" name="password" size="35" maxlength="255" value="<?php print (htmlspecialchars($_POST['password'],ENT_QUOTES)); ?>" />
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
        <input class="btn-default" type="submit" value="ログインする" />
      </div>
    </form>
  </div>
  
  <div id="foot">
    <p><img src="" width="136" height="15" alt="" /></p>
  </div>
</div>
</body>
</html>
