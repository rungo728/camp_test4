<?php
	session_start();
	// 会員入力内容をデータベースに登録する
	require('../dbconnect.php');
	// joinに値が入っているかどうかを検査する
	if (!isset($_SESSION['join'])){
		// 値が入っていなければ入力画面に戻す
		header('Location: index.php');
		exit();
	}
	if (!empty($_POST)){
		// データベースへの登録(membersテーブルに)
		$statement = $db->prepare('INSERT INTO members SET name=?, email=?,password=?,picture=?,created=NOW()');
		// executeメソッドの値として１つずつ設定をしていく
		echo $statement->execute(array(
			// joinというセッション変数のname等を以下に記入することによって上記の？の部分を埋めていく
			$_SESSION['join']['name'],
			$_SESSION['join']['email'],
			// sha1という機能を使ってパスワードを暗号化する
			sha1($_SESSION['join']['password']),
			$_SESSION['join']['image']
		));
		// unsetでSESSIONで保存した内容を空にする（データベースに保存した後は不要になるので）
		unset($_SESSION['join']);

		header('Location: thanks.php');
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

	<link rel="stylesheet" href="../css/style.css" />
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
			<?php if ($_SESSION['join']['image'] !== ''): ?>
				<img src="../member_pictures/<?php print(htmlspecialchars($_SESSION['join']['image'],ENT_QUOTES)); ?>" alt="" style="width: 200px;">
			<?php endif; ?>	
		</dd>
	</dl>
	<div>
		<a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | 
		<input type="submit" value="登録する" />
	</div>
</form>
</div>

</div>
</body>
</html>
