<?php
// NOTICEエラーを非表示にする記述
error_reporting(E_ALL & ~E_NOTICE);
?>
<?php
session_start();
require('dbconnect.php');
// 自分のメッセージを削除する
if (isset($_SESSION['id'])){
  $id = $_REQUEST['id'];

  $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
  // urlに渡されたidを受け取って$messagesに入れる
  $messages->execute(array($id));
  $message = $messages->fetch();
  if ($message['member_id'] == $_SESSION['id']){
    $del = $db->prepare('DELETE FROM posts WHERE id=?');
    $del->execute(array($id));
  }

}
header('Location: index.php');
exit();
?>