<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
//login system
if ($_SERVER["REQUEST_METHOD"]=="POST") {
//パスワード照会
	$sql ="SELECT * FROM `shops` WHERE `shop_id` = '{$_POST['id']}' AND `shop_pw` = '{$_POST['pw']}' ";
	$num = DBCount($sql,1);
	if ($num==1) {
		$shopData = get1Record($sql,1);
		$_SESSION["id"] = $_POST["id"];
		$_SESSION["pass"] = $_POST["pw"];
		$_SESSION["mode"] = 'admin';
		$_SESSION["signal_mode"] = $shopData["signal_mode"];
		$_SESSION["shop_name"] = $shopData["name_ja"];
		$_SESSION['start_date'] = date("Y年m月d日",strtotime("-7 day"));
		$_SESSION['start_time'] = date("00:00",strtotime("-7 day"));
		$_SESSION['end_date'] = date("Y年m月d日");
		$_SESSION['end_time'] = date("23:59");
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
	<title>ドM会員管理システム</title>
</head>
<body id="index" >
	<div id="inwrap">
		<div id="panel">
			<form action="" method="post" >
				<table>
					<tr>
						<th colspan="2"><img src="img/logo.png" id="indexLogo" alt="ドM会員管理システム LOGO"></th>
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




