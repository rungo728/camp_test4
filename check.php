<?php
	session_start();
	// joinに値が入っているかどうかを検査する
	if (!isset($_SESSION['join'])){
		// 値が入っていなければ入力画面に戻す
		header('Location: index.php');
		exit();
	}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>ニックネーム</dt>
		<dd>
    <!-- htmlspecialcharshaは安全に出力するための記述 -->
    <?php print(htmlspecialchars($_SESSION['join']
    // joinのなかのnameを出力するということ
    ['name'], ENT_QUOTES)); ?>
    </dd>
		<dt>メールアドレス</dt>
		<dd>
    <?php print(htmlspecialchars($_SESSION['join']
    // joinのなかのemailを出力するということ
    ['email'], ENT_QUOTES)); ?>
    </dd>
		<dt>パスワード</dt>
		<dd>
		【表示されません】
		</dd>
		<dt>写真など</dt>
		<dd>
		</dd>
	</dl>
	<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
</form>
</div>

</div>
</body>
</html>
