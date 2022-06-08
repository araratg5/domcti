<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

$sql = "SELECT * FROM `customer_data` WHERE `is_delete` = 0 AND `id` = '{$_REQUEST['cid']}'";
$customerData = get1Record($sql);
if($_SERVER['REQUEST_METHOD']=='POST'){
	if($_REQUEST['uid'] == ''){
		$pDate = date("Y-m-d 00:00:00");
		$customerData['tel'] = $customerData['tel1'];
		if(!$customerData['tel']){
			$customerData['tel'] = $customerData['tel2'];
		}
		if(!$customerData['tel']){
			$customerData['tel'] = $customerData['tel3'];
		}
		if(!$customerData['tel']){
			$customerData['tel'] = '';
		}
		$sql = "INSERT INTO `usage_data` (`shop_id`,`customer_id`,`customer_internal_id`,`name`,`usage_shopname`,`tel`,`created`) VALUES ('{$_SESSION['id']}','{$customerData['id']}','{$customerData['customer_id']}','{$customerData['name']}','{$_SESSION['shop_name']}','{$customerData['tel']}',NOW())";
		$_REQUEST['uid'] = insData($sql);
	}

	$postData = allescape($_POST);
	$postData['p_date'] = str_replace(['年','月','日'],['-','-',''],$postData['start_date']).' '.date("H:i:00",strtotime($postData['start_time']));
	$postData['p_option'] = json_encode($postData['options'],JSON_UNESCAPED_UNICODE);
	$unsetColumnNameAry = [
		'start_date',
		'start_time',
		'options',
	];
	foreach($unsetColumnNameAry as $v){
		unset($postData[$v]);
	}
	foreach($postData as $column => $value){
		$queryAry[] = "`{$column}` = '{$value}'";
	}
	$queryStr = implode(',',$queryAry);
	$sql ="UPDATE `usage_data` SET {$queryStr} WHERE `id` = '{$_REQUEST['uid']}'";
	dbQuery($sql);
	header("Location:". "/usageData.php?uid={$_REQUEST['uid']}");
}

$sql = "SELECT * FROM `usage_data` WHERE `id` = '{$_REQUEST['uid']}'";
$usageData = get1Record($sql);

$sql = "SELECT * FROM `girls` WHERE `shop_id` = '{$_SESSION['id']}' ORDER BY CAST(`name` AS CHAR)";
$girlDataAry = getRecord($sql,1);

if($_REQUEST['uid'] == ''){
	$sql = "SELECT * FROM `customer_data` WHERE `is_delete` = 0 AND `id` = '{$_REQUEST['cid']}'";
	$customerData = get1Record($sql);
	$usageData['name'] = $customerData['name'];
	$usageData['customer_internal_id'] = $customerData['customer_id'];
	$usageData['p_date'] = date("Y-m-d H:00:00");
}

if($customerData){
	if($customerData['tel1']){
		$telList[] = "'{$customerData['tel1']}'";
	}
	if($customerData['tel2']){
		$telList[] = "'{$customerData['tel2']}'";
	}
	if($customerData['tel3']){
		$telList[] = "'{$customerData['tel3']}'";
	}
	$telListStr = @implode(',',$telList);
	if($telListStr){
		$sql = "SELECT * FROM `call_history` WHERE `is_delete` = 0 AND `num` IN ({$telListStr}) ORDER BY `time` DESC LIMIT 0,1";
		$recentCallData = get1Record($sql);
		$recentCallData['time'] = '<div class="recentCallData">最終着信：'.$recentCallData['time'].'<div class="recentCallShopName">'.$shopNameAry[$recentCallData['shop_id']].'</div></div>';
	}
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<title><?php echo GROUP_NAME ?>会員管理システム</title>
	<style>
	.select2-results , .select2-results__options {
	    max-height: 300px !important;
	    height: 300px !important;
	}
	</style>
</head>
<style>
	#slipListWrapper {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>;
		height: 100vh;
	}
</style>
<body class="<?php echo GROUP_MODE ?>" onUnload="modalClose()" id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
	<div id="slipListWrapper">
<?php
$usageData['option'] = json_decode($usageData['p_option']);
?>
		<form action="" method="POST" >
			<input type="hidden" name="is_delete" value="0" >
			<div class="slipList dataBox" id="slip<?php echo $usageData['id'] ?>" data-mode="usage" data-id="<?php echo $usageData['id'] ?>" >
				<ul>
					<li>
						<input type="text" name="start_date" class="startDate" value="<?php echo date("Y年m月d日",strtotime($usageData['p_date'])) ?>" >
							<input type="text" name="start_time" data-time-format="H:i" class="timePicker startTime" id="startTime" value="<?php echo date("H:i",strtotime($usageData['p_date'])) ?>" ><span id="currentTimeRegister">現在時刻を設定</span>
						<li><?php echo $usageData['name'] ?>様　会員ID：<?php echo $usageData['customer_internal_id'] ?></li>
					</li>
					<li></li>
				</ul>
				<ul>
					<li>
						利用キャスト：　<select name="girl" class="girlSelector" id="select2" style="min-width:200px" inputmode="kana">
							<option value="フリー" <?php if($usageData['girl'] == 'フリー'){echo 'selected';} ?> >フリー</option>
	<?php
	foreach((array)$girlDataAry AS $girlData){
	?>
							<option value="<?php echo $girlData['name'] ?>" <?php if($usageData['girl'] == $girlData['name']){echo 'selected';} ?> ><?php echo $girlData['name'] ?></option>
	<?php } ?>
						</select>
					</li>
					<li>
						指名：　<select name="nominate" class="nominate" id="">
							<option value="フリー" <?php if($usageData['nominate'] == 'フリー'){echo 'selected';} ?> >フリー</option>
							<option value="パネル指名" <?php if($usageData['nominate'] == 'パネル指名'){echo 'selected';} ?> >パネル指名</option>
							<option value="本指名" <?php if($usageData['nominate'] == '本指名'){echo 'selected';} ?> >本指名</option>
						</select>
					</li>
					<li><input type="tel" name="p_time" class="course" value="<?php echo $usageData['p_time'] ?>" min="0" required placeholder="60"  inputmode="tel" style="ime-mode: inactive;" >分</li>
					<li><input type="tel" name="price" class="price" value="<?php echo $usageData['price'] ?>" min="0" required placeholder="12000"  inputmode="tel" style="ime-mode: inactive;" >円</li>
				</ul>
				<ul>
					<li><input type="text" name="address" class="address" value="<?php echo $usageData['address'] ?>" placeholder="利用場所"  inputmode="kana" style="ime-mode: active;"></li>
					<li><input type="tel" name="roomno" class="roomNumber" value="<?php echo $usageData['roomno'] ?>" placeholder="号数" inputmode="tel" style="ime-mode: inactive;" >号室</li>
				</ul>
				<!--
				<ul class="optionBox" >
					<li><label for="option1"><input type="checkbox" id="option1" class="colEdit data-<?php echo $usageData['id'] ?>" value="オプション１" name="options[]" <?php if(@in_array('オプション１',$usageData['option'])===true){ echo 'checked';} ?> >オプション１</label></li>
					<li><label for="option2"><input type="checkbox" id="option2" class="colEdit data-<?php echo $usageData['id'] ?>" value="オプション２" name="options[]" <?php if(@in_array('オプション２',$usageData['option'])===true){ echo 'checked';} ?> >オプション２</label></li>
					<li><label for="option3"><input type="checkbox" id="option3" class="colEdit data-<?php echo $usageData['id'] ?>" value="オプション３" name="options[]" <?php if(@in_array('オプション３',$usageData['option'])===true){ echo 'checked';} ?> >オプション３</label></li>
					<li><label for="option4"><input type="checkbox" id="option4" class="colEdit data-<?php echo $usageData['id'] ?>" value="オプション４" name="options[]" <?php if(@in_array('オプション４',$usageData['option'])===true){ echo 'checked';} ?> >オプション４</label></li>
				</ul>
				-->
				<ul>
					<li><textarea name="remark" id="remark" inputmode="kana" style="ime-mode: active;"><?php echo $usageData['remark'] ?></textarea></li>
				</ul>
			</div>
			<div class="footer"><input type="submit" class="btn saveBtn" value="伝票を保存" >
<?php if($_REQUEST['num'] != '非通知'){ ?><?php echo $recentCallData['time'] ?><?php } ?>
			</div>
		</form>
	</div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js" ></script>
<link rel="stylesheet" href="https://cdn.rawgit.com/jonthornton/jquery-timepicker/3e0b283a/jquery.timepicker.min.css">
<script src="https://cdn.rawgit.com/jonthornton/jquery-timepicker/3e0b283a/jquery.timepicker.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('#select2').select2({
    width: 'resolve'
})
$(".timePicker").timepicker({'step': <?php echo $timeIntervalAry[$_SESSION['id']] ?>});
</script>
<script type="text/javascript" src="/js/commonModal.js?<?php echo BUSTING_DATE ?>"></script>
<script>
	function modalClose(){
		window.opener.modalClose();
	}
	$(document).on("click","#currentTimeRegister",function () {
		let now = new Date();
		let currentTime = ('00'+ now.getHours()).slice(-2) + ':' + ('00'+ now.getMinutes()).slice(-2);
		$('#startTime').val(currentTime)
	});
</script>