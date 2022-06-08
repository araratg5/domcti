<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
//login system

if($_COOKIE['id']){
  $id = strDecrypt($_COOKIE['id']);
  $pass = strDecrypt($_COOKIE['pass']);
  $sql ="SELECT * FROM `shops` WHERE `shop_id` = '{$id}' AND `shop_pw` = '{$pass}' ";
	$num = DBCount($sql,1);
	if ($num == 1) {
    header("Location:". "/top.php");
    exit;
  }
}

if ($_SERVER["REQUEST_METHOD"]=="POST") {
//パスワード照会
	$sql ="SELECT * FROM `shops` WHERE `shop_id` = '{$_POST['id']}' AND `shop_pw` = '{$_POST['pw']}' ";
	$num = DBCount($sql,1);
	if ($num==1) {
		$shopData = get1Record($sql,1);
		setcookie('id',strEncrypt($_POST["id"]),time()+60*60*24*30*12);
		setcookie('pass',strEncrypt($_POST["pw"]),time()+60*60*24*30*12);
		setcookie('mode','admin',time()+60*60*24*30*12);
		setcookie('signal_mode',$shopData["signal_mode"],time()+60*60*24*30*12);
		setcookie('shop_name',$shopData["name_ja"],time()+60*60*24*30*12);
		setcookie('start_date',date("Y年m月d日",strtotime("-7 day")),time()+60*60*24*30*12);
		setcookie('start_time',date("00:00",strtotime("-7 day")),time()+60*60*24*30*12);
		setcookie('end_date',date("Y年m月d日"),time()+60*60*24*30*12);
		setcookie('end_time',date("23:59"),time()+60*60*24*30*12);

		header("Location:". "top.php");
	}else{
		$msg = "<div class=\"error\" >IDかパスワードが間違っています。</div>";
	}
}
?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
	<title><?php echo GROUP_NAME ?>会員管理システム</title>
</head>
<style>
	#wrapper {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>
	}
</style>
<body class="<?php echo GROUP_MODE ?>" id="index" >
	<div id="inwrap">
		<div id="panel">
			<form action="" method="post" >
				<table>
					<tr>
						<th colspan="2"><img src="img/logo_<?php echo GROUP_MODE ?>.png" id="indexLogo" alt="<?php echo GROUP_NAME ?>会員管理システム LOGO"></th>
					</tr>
					<tr>
						<th colspan="2"><?php echo $msg ?></th>
					</tr>
					<tr>
						<th><label>ログインID</label></th>
						<td><input type="text" name="id" style="width: 100%;" value="<?php echo  $_POST['id'] ?>" ></td>
					</tr>
					<tr>
						<th><label>パスワード</label></th>
						<td><input type="password" name="pw" style="width: 100%;" value="<?php echo  $_POST['pw'] ?>" /></td>
					</tr>
					<tr>
						<th colspan="2" ><input type="submit" value="ログイン" class="btn" id="loginbtn"></th>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>
<script>
localStorage.removeItem("showing_number_list");
</script>



