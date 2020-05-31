<!-- データベースに接続するために書く -->
<?php
try {
  $db = new PDO('mysql:dbname=mini_bbs; host=localhost; charset=utf8','root','');
}
// データベース接続のエラーメッセージを表示
catch(PDOException $e){
  print('DB接続エラー:' . $e->getMessage());
}
?>
