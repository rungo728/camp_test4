<?php
// NOTICEエラーを非表示にする記述
error_reporting(E_ALL & ~E_NOTICE);
?>
<?php
// 入力画面で入力した情報を保存しておく
session_start();
// 項目がすべて入力がされている時にのみ発動するようにする
if (!empty($_POST)){
  // $_POSTは配列になっている
  // ニックネームが空かどうかの確認
  if ($_POST['name']=== ''){
    $error['name']='blank';
  }
  // メールアドレスが空かどうかの確認
  if ($_POST['email']=== ''){
    $error['email']='blank';
  }
  // パスワードが空かどうか,５文字以上で入力しているかの確認
  // strlen関数でpasswordの文字列の長さを取得Rugii5434/
  if (strlen($_POST['password']) < 5){
    $error['password']='length';
  }
  if ($_POST['password']=== ''){
    $error['password']='blank';
  }
  // まず画像を＄filenameに代入
  $filename = $_FILES['image']['name'];
  if (!empty($filename)){
    $ext =substr($filename, -3);
    // ファイルの拡張子を得ることができるのでそこから条件をつける
    if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
      // 画像をtypeにするというエラー
      $error['image'] = 'type';
      // htmlの入力フォーム部分でエラー表示するように記述する
    }

  }
  if (empty($error)){
    // アップロードするファイル名を記述する
    // $_FILES['image']は配列、['name']はファイル名
    $image = date('YmdHis') . $_FILES['image']['name'];
    // 例えば20200528052017myarticle.pngというファイルが作成される
    // $_FILESに選択されたファイルをアップロード
    // 一時的に['image']['tmp_name']の中に画像をアップロード（１番目のパラメーター）
    move_uploaded_file($_FILES['image']['tmp_name'],
    // '../member_picture/' . $imageはアップロードしたファイルの移動先（２番目のパラメーター）
    '../member_pictures/' . $image);
    // エラーが発生しなかったら入力内容を保存する
    // 配列$_POSTと$imageをjoinに代入する
    $_SESSION['join']= $_POST;
    $_SESSION['join']['image'] = $image;
    // エラーが発生しなかったら入力確認画面に進む
    header('Location: check.php');
    // index.phpが呼び出された時にcheck.phpにジャンプする設定
    exit();
  }

}
// アクションがrewrite出会った場合かつjoinに値が入っている時だけ
if ($_REQUEST['action']== 'rewrite' && isset($_SESSION['join'])){
  // joinに入っている値を$_POSTに代入して値を再現する
  $_POST = $_SESSION['join'];
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<!-- form actionが空の場合は元のページ（index.phpにジャンプする -->
<!-- enctype="multipart/form-data"は画像アップロードするための記述 -->
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
      <!-- Value属性にPOSTの値を設定 -->
      <!-- htmlspecialchars( 変換対象, 変換パターン, 文字コード )  -->
      <input type="text" name="name" size="35" maxlength="255" placeholder="" value="<?php print (htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
      <?php if ($error['name']==='blank'): ?>
      <p class="error">ニックネームを入力してください</p>
      <?php endif; ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
      <input type="text" name="email" size="35" maxlength="255" value="<?php print (htmlspecialchars($_POST['email'],ENT_QUOTES)); ?>" />
      <?php if ($error['email']==='blank'): ?>
      <p class="error">メールアドレスを入力してください</p>
      <?php endif; ?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
      <input type="password" name="password" size="10" maxlength="20" value="<?php print (htmlspecialchars($_POST['password'],ENT_QUOTES)); ?>" />
      <?php if ($error['password']==='length'): ?>
      <p class="error">パスワードは5文字以上で入力してください！</p>
      <?php endif; ?>
      <?php if ($error['password']==='blank'): ?>
      <p class="error">パスワードを入力してください</p>
      <?php endif; ?>
    </dd>
		<dt>写真など</dt>
		<dd>
      <input type="file" name="image" size="35" value="test"  />
      <?php if ($error['image']==='type'): ?>
      <p class="error">写真は「.gif」または「．jpg」「.png」の画像を指定してください</p>
      <?php endif; ?>
      <!-- ファイルを選択した後でエラーが起こっても再現できるようにする -->
      <?php if (!empty($error)): ?>
      <p class="error">改めて画像を指定してください</p>
      <?php endif; ?>
    </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
