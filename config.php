<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$sql ="UPDATE `shops` SET `cti_color` = '{$_POST['cti_color']}',`per_count` = '{$_POST['per_count']}' WHERE `shop_id` = '{$_SESSION['id']}' AND `shop_pw` = '{$_SESSION['pass']}'";
	DBQuery($sql,1);
	$shopBgColorAry[$_SESSION['id']] = $_POST['cti_color'];
	$shopPerCountAry[$_SESSION['id']] = $_POST['per_count'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
	<title>ドM会員管理システム</title>
</head>
<style>
	#wrapper {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>
	}
</style>
<body id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
  <div id="wrapper">
<?php include_once(DOCUMENT_ROOT.'/include/navi.php') ?>
    <main>
      <article id="callHistoryTableWrapper" class="tableWrapper" >
				<form action="" method="POST" enctype="multipart/form-data">
					<div class="headinfo">
						<div class="title">店舗設定</div>
						<div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
					</div>
					<div class="box">
						<div class="title">背景色</div>
							<input type="color" id="colorPickerBox" name="cti_color" value="<?php echo $shopBgColorAry[$_SESSION['id']] ?>" >
							<input type="submit" class="btn edit csvUpload" id="customerRegist" name="customerRegist" value="設定" >
					</div>
					<div class="box">
						<div class="title">トップ表示件数</div>
							<select name="per_count" id="">
								<option value="25" <?php if($shopPerCountAry[$_SESSION['id']] == 25){ echo 'selected';} ?>>25</option>
								<option value="50" <?php if($shopPerCountAry[$_SESSION['id']] == 50){ echo 'selected';} ?>>50</option>
								<option value="100" <?php if($shopPerCountAry[$_SESSION['id']] == 100){ echo 'selected';} ?>>100</option>
								<option value="150" <?php if($shopPerCountAry[$_SESSION['id']] == 150){ echo 'selected';} ?>>150</option>
								<option value="300" <?php if($shopPerCountAry[$_SESSION['id']] == 300){ echo 'selected';} ?>>300</option>
							</select>
							<input type="submit" class="btn edit csvUpload" id="customerRegist" name="customerRegist" value="設定" >
					</div>
				</form>
      </article>
    </main>
    <div class="clear"></div>
  </div>
</body>
</html>