<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

if($_SERVER['REQUEST_METHOD']=='POST'){
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
}




if($_REQUEST['uid']=='undefined'){
	$sql = "SELECT * FROM `customer_data` WHERE `is_delete` = 0 AND `id` = '{$_REQUEST['cid']}'";
	$customerData = get1Record($sql);
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
	$sql = "INSERT INTO `usage_data` (`shop_id`,`customer_id`,`customer_internal_id`,`name`,`usage_shopname`,`address`,`tel`,`roomno`,`girl`,`p_date`,`nominate`,`is_delete`,`created`) VALUES ('{$_SESSION['id']}','{$customerData['id']}','{$customerData['customer_id']}','{$customerData['name']}','{$_SESSION['shop_name']}','{$customerData['address']}','{$customerData['tel']}','{$customerData['roomno']}','フリー','{$pDate}','フリー',1,NOW())";
	$insertId = insData($sql);
	header("Location:". "/usageData.php?uid={$insertId}");
}
$sql = "SELECT * FROM `usage_data` WHERE `id` = '{$_REQUEST['uid']}'";
$usageData = get1Record($sql);

$sql = "SELECT * FROM `girls` WHERE `shop_id` = '{$_SESSION['id']}'";
$girlDataAry = getRecord($sql,1);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<title>ドM会員管理システム</title>
</head>
<body onUnload="window.opener.modalClose()" id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
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
							<input type="text" name="start_time" data-time-format="H:i" class="timePicker startTime" value="<?php echo date("H:i",strtotime($usageData['p_date'])) ?>" >
						<li><?php echo $usageData['name'] ?>様　会員ID：<?php echo $usageData['customer_internal_id'] ?></li>
					</li>
				</ul>
				<ul>
					<li>
						利用キャスト：　<select name="girl" class="girlSelector" id="">
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
					<li><input type="number" name="p_time" class="course" value="<?php echo $usageData['p_time'] ?>" min="0" required placeholder="60" >分</li>
					<li><input type="number" name="price" class="price" value="<?php echo $usageData['price'] ?>" min="0" required placeholder="12000" >円</li>
				</ul>
				<ul>
					<li><input type="text" name="address" class="address" value="<?php echo $usageData['address'] ?>" placeholder="利用場所" ></li>
					<li><input type="text" name="roomno" class="roomNumber" value="<?php echo $usageData['roomno'] ?>" placeholder="号数" >号室</li>
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
					<li><textarea name="remark" id="remark" ><?php echo $usageData['remark'] ?></textarea></li>
				</ul>
			</div>
			<div class="footer"><input type="submit" class="btn saveBtn" value="伝票を保存" ></div>
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
<script type="text/javascript" src="/js/commonModal.js?<?php echo BUSTING_DATE ?>"></script>