<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$sql ="UPDATE `shops` SET `cti_color` = '{$_POST['cti_color']}',`per_count` = '{$_POST['per_count']}',`time_interval` = '{$_POST['time_interval']}',`table_sequence` = '{$_POST['table_sequence']}' WHERE `shop_id` = '{$_SESSION['id']}' AND `shop_pw` = '{$_SESSION['pass']}'";
	DBQuery($sql,1);
	$shopBgColorAry[$_SESSION['id']] = $_POST['cti_color'];
	$shopPerCountAry[$_SESSION['id']] = $_POST['per_count'];
	$timeIntervalAry[$_SESSION['id']] = $_POST['time_interval'];
	$tableSequenceAry[$_SESSION['id']] = $_POST['table_sequence'];
}

$tableSequence = explode(',',$tableSequenceAry[$_SESSION['id']]);
$defaultTableAry = [1,2,3,4,5,6];
$remainColumnAry = array_diff($defaultTableAry, $tableSequence);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
	<title><?php echo GROUP_NAME ?>会員管理システム</title>
</head>
<style>
	#wrapper {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>
	}
</style>
<body class="<?php echo GROUP_MODE ?>" id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
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
							
					</div>
					<div class="box">
						<div class="title">利用履歴登録プルダウン時間間隔</div>
							<select name="time_interval" id="">
								<option value="30" <?php if($timeIntervalAry[$_SESSION['id']] == 30){ echo 'selected';} ?>>30</option>
								<option value="10" <?php if($timeIntervalAry[$_SESSION['id']] == 10){ echo 'selected';} ?>>10</option>
							</select>
							
					</div>
					<div class="box">
						<div class="title">最新着歴　表示項目設定　<span style="font-size: 12px;" >チェックで表示</span></div>
							<ul id="sequenceSettingList" >
								<?php foreach ((array)$tableSequence as $id) { ?>
								<li id="<?php echo $id ?>" >
										<input type="checkbox" name="sequenceVisible<?php echo $id ?>" id="sequenceVisible<?php echo $id ?>" value="<?php echo $id ?>" checked >
										<?php echo $tableColumnNameAry[$id] ?>
								</li>
								<?php } ?>
								<?php foreach ((array)$remainColumnAry as $id) { ?>
								<li id="<?php echo $id ?>" >
										<input type="checkbox" name="sequenceVisible<?php echo $id ?>" id="sequenceVisible<?php echo $id ?>" value="<?php echo $id ?>" >
										<?php echo $tableColumnNameAry[$id] ?>
								</li>
								<?php } ?>
						</ul>
							<input type="hidden" name="table_sequence" id="tableSequence" value="<?php echo $tableSequenceAry[$_SESSION['id']] ?>" >
					</div>
					<div class="box">
						<input type="submit" class="btn edit csvUpload" id="customerRegist" name="customerRegist" value="設定" >
					</div>
				</form>
      </article>
    </main>
    <div class="clear"></div>
  </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$("#sequenceSettingList").sortable({
	items: "li" ,
	placeholder: "hover" ,
	tolerance : "pointer" ,
	stop : function(){
		var data=[];
		$("li","#sequenceSettingList").each(function(i,v){
			if($(this).children('input').prop('checked')){
				data.push(v.id);
			}
		});
		$('#tableSequence').val(data);
	},
	update : function(){
		$('#submit').removeAttr('disabled');
	}
});
$(document).on("click", "#sequenceSettingList input[type='checkbox']", function () {
	var data=[];
	$("li","#sequenceSettingList").each(function(i,v){
		if($(this).children('input').prop('checked')){
			data.push(v.id);
		}
	});
	$('#tableSequence').val(data);
});
</script>