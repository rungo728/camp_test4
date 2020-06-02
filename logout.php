<?php
session_start();

// sessionの情報を削除するので空っぽの配列で上書き
$_SESSION = array();
if (ini_set('session.use_cookies')){
  $params = session_get_cookie_params();
// cookieの有効期限を切ることでsessionで使ったcookieを削除する
  setcookie(session_name(). '',time() - 42000,
  // sessionのcookieが使っているそれぞれのオプションを指定して
  $params['path'],$params['domain'],$params['secure'],$params['httponly']);
}
session_destroy();
// 空の値を設定して有効期限を切る
setcookie('email', '', time(-3600));

header('Location: login.php');
exit();
?>
