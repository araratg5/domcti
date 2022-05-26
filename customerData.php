<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

if($_SERVER['REQUEST_METHOD']=='POST'){
	if($_REQUEST['cid'] == ''){
		$sql = "INSERT INTO `customer_data` (`shop_id`,`usage_shopname`,`name`,`tel1`,`is_delete`,`created`) VALUES ('{$_SESSION['id']}','{$_SESSION['shop_name']}','{$_REQUEST['name']}','{$_REQUEST['num']}',0,NOW())";
		$_REQUEST['cid'] = insData($sql);
		$customerId = getCustomerId($_REQUEST['cid']);
		$sql = "UPDATE `customer_data` SET `customer_id`= '{$customerId}' WHERE `id` = '{$_REQUEST['cid']}'";
		dbQuery($sql);
	}
	$postData = allescape($_POST);
	foreach($postData as $column => $value){
		$queryAry[] = "`{$column}` = '{$value}'";
	}
	$queryStr = implode(',',$queryAry);
	$sql ="UPDATE `customer_data` SET {$queryStr} WHERE `id` = '{$_REQUEST['cid']}'";
	dbQuery($sql);
	header("Location:". "/customerData.php?cid={$_REQUEST['cid']}&num={$_REQUEST['num']}");
}

$sql = "SELECT * FROM `usage_data` WHERE `is_delete` = 0 AND `customer_id` = '{$_REQUEST['cid']}' ORDER BY `p_date` DESC";
$usageDataAry = getRecord($sql);

$sql = "SELECT COUNT(`id`) AS `count`,SUM(`p_time`) AS `time`,SUM(`price`) AS `price` FROM `usage_data` WHERE `customer_id` = '{$_REQUEST['cid']}' ORDER BY `created` DESC";
$usageStatistics['all'] = get1Record($sql);		

$sql = "SELECT COUNT(`id`) AS `count`,SUM(`p_time`) AS `time`,SUM(`price`) AS `price` FROM `usage_data` WHERE `nominate` = '本指名' AND `customer_id` = '{$_REQUEST['cid']}' ORDER BY `created` DESC";
$usageStatistics['hon'] = get1Record($sql);		

$sql = "SELECT COUNT(`id`) AS `count`,SUM(`p_time`) AS `time`,SUM(`price`) AS `price` FROM `usage_data` WHERE `nominate` = 'パネル指名' AND `customer_id` = '{$_REQUEST['cid']}' ORDER BY `created` DESC";
$usageStatistics['panel'] = get1Record($sql);		

$sql = "SELECT COUNT(`id`) AS `count`,SUM(`p_time`) AS `time`,SUM(`price`) AS `price` FROM `usage_data` WHERE `nominate` = 'フリー' AND `customer_id` = '{$_REQUEST['cid']}' ORDER BY `created` DESC";
$usageStatistics['free'] = get1Record($sql);	

$sql = "SELECT COUNT(`id`) AS `count`,`girl` FROM `usage_data` WHERE `customer_id` = '{$_REQUEST['cid']}' AND `is_delete` = 0 GROUP BY `girl` ORDER BY `created` DESC";
$girlUsageExist = dbCount($sql);	
$girlUsageListAry = getRecord($sql);		

$sql = "SELECT * FROM `girls` WHERE `shop_id` = '{$_SESSION['id']}'";
$girlDataAry = getRecord($sql,1);

$sql = "SELECT * FROM `customer_data` WHERE `id` = '{$_REQUEST['cid']}'";
$customerData = get1Record($sql);
if(!$customerData){
	$customerData['created'] = '未登録';
}

if($_REQUEST['cid'] == ''){
	if($_REQUEST['num'] == 'null'){
		$_REQUEST['num'] = '';
	}
	if($_REQUEST['num'] == 'undefined'){
		$_REQUEST['num'] = '';
	}
	$customerData['name'] = '新規：'.$_REQUEST['num'];
	$customerData['tel1'] = $_REQUEST['num'];
}
//直近のコール
if($_GET['isCall'] == 1){
	$mode = 1;
} else {
	$mode = 0;
}
if(is_numeric($_GET['num'])){
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
		$telListStr = implode(',',$telList);
		$sql = "SELECT * FROM `call_history` WHERE `is_delete` = 0 AND `num` IN ({$telListStr}) ORDER BY `time` DESC LIMIT {$mode},1";
	} else {
		$sql = "SELECT * FROM `call_history` WHERE `is_delete` = 0 AND `num` = '{$_GET['num']}' ORDER BY `time` DESC LIMIT {$mode},1";
	}
	$recentCallData = get1Record($sql);
	if($_GET['isCall'] == 1 && $recentCallData['time']==''){
		$recentCallData['time'] = '<div class="recentCallData">初回着信<div class="recentCallShopName">'.$shopNameAry[$recentCallData['shop_id']].'</div></div>';
	} elseif($_GET['isCall'] == 1) {
		$recentCallData['time'] = '<div class="recentCallData">直近着信：'.$recentCallData['time'].'<div class="recentCallShopName">'.$shopNameAry[$recentCallData['shop_id']].'</div></div>';
	} elseif($recentCallData['time']) {
		$recentCallData['time'] = '<div class="recentCallData">前回着信：'.$recentCallData['time'].'<div class="recentCallShopName">'.$shopNameAry[$recentCallData['shop_id']].'</div></div>';
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
	<title><?php echo $customerData['name'] ?></title>
</head>
<style>
	body {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>
	}
</style>
<script>
function modalClose(num){
	$('.modalLoading').show();
	var userData ={cid: $('#cid').val()};
	$.ajax({
		url: "ajax/modalUsageUpdate.php",
		data: userData,
		type: "POST",
		cache: false,
	})
	.done(function (data) {
		$('#modalUsageListWrapper').html(data);
		$('.modalLoading').hide();
	})
	.fail(function (jqXHR, textStatus, errorThrown) {
	});
	window.opener.modalClose();
  showingNumberListjson = localStorage.getItem('showing_number_list');
  if(showingNumberListjson){
    showingNumberListArray = JSON.parse(showingNumberListjson);
  }
  showingNumberListArray = showingNumberListArray.filter(function(v){
    return ! num.includes(v);
  });
  showingNumberListJson = JSON.stringify(showingNumberListArray, undefined, 1);
	localStorage.setItem('showing_number_list', showingNumberListJson);
}
</script>
<body onBlur="focus()" onUnload="modalClose('<?php echo $_REQUEST['num'] ?>')" id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
<div id="loader"></div>
  <div id="modal">
    <!-- <div id="modalClose" onClick="close()" ><i class="fas fa-times"></i></div> -->
<div id="customerData">
				<form action="" method="POST">
				<input type="hidden" name="is_delete" value="0" >
				<input type="hidden" id="cid" value="<?php echo $_REQUEST['cid'] ?>" >
				<div class="title" >会員データ<input type="button" class="btn saveBtn" value="会員データを保存" ><?php if($_REQUEST['cid'] != ''){ ?><div class="btn add usageAdd" data-cid="<?php echo $_REQUEST['cid'] ?>" >この会員で伝票起票</div><?php } ?><?php if($_REQUEST['num'] != '非通知'){ ?><?php echo $recentCallData['time'] ?><?php } ?></div>
				<table id="customerDataList" class="dataBox" data-mode="customer" data-id="<?php echo $_REQUEST['cid'] ?>" >
					<tbody>
						<tr>
							<th>会員名</th>
							<td colspan="3">
								<input type="text" name="name" required id="name" value="<?php echo $customerData['name'] ?>" inputmode="kana" >様
								<?php if($_REQUEST['cid'] != ''){ ?>
								<span class="registShopName">（<?php echo $customerData['usage_shopname'] ?>登録）</span>
								<?php } ?>
							</td>
							<th>会員ID</th>
							<td><?php echo $customerData['customer_id'] ?></td>
						</tr>
						<tr>
							<th>登録日時</th><td><?php echo $customerData['created'] ?></td><th>ステータス</th>
							<td>
								<select name="rating" id="rating" style="background:<?php if($customerData['rating']=='注意'){ echo '#ffc294';} elseif($customerData['rating']=='出禁'){ echo '#ffb5b5';} elseif($customerData['rating']=='優良'){ echo '#fff9cf';} ?> !important" >
									<option style="background: #fff !important" value="一般" <?php if($customerData['rating']=='一般'){ echo 'selected';} ?> >一般</option>
									<option style="background: #fff !important" value="優良" <?php if($customerData['rating']=='優良'){ echo 'selected';} ?> >優良</option>
									<option style="background: #fff !important" value="注意" <?php if($customerData['rating']=='注意'){ echo 'selected';} ?> >注意</option>
									<option style="background: #fff !important" value="出禁" <?php if($customerData['rating']=='出禁'){ echo 'selected';} ?> >出禁</option>
									<option style="background: #fff !important" value="スタッフ" <?php if($customerData['rating']=='スタッフ'){ echo 'selected';} ?> >スタッフ</option>
									<option style="background: #fff !important" value="業者" <?php if($customerData['rating']=='業者'){ echo 'selected';} ?> >業者</option>
									<option style="background: #fff !important" value="その他" <?php if($customerData['rating']=='その他'){ echo 'selected';} ?> >その他</option>
								</select>
							</td>
							<th>誕生日</th>
							<td>
								<select name="birth_month" id="">
									<option value="">-</option>
<?php for($i = 1;$i < 13;$i++){ ?>
									<option value="<?php echo $start + $i ?>" <?php if($customerData['birth_month']==$i){ echo 'selected';} ?> ><?php echo $start + $i ?></option>
<?php } ?>
								</select>/
								<select name="birth_day" id="">
									<option value="">-</option>
<?php for($i = 1;$i < 32;$i++){ ?>
									<option value="<?php echo $start + $i ?>" <?php if($customerData['birth_day']==$i){ echo 'selected';} ?> ><?php echo $start + $i ?></option>
<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>住所１</th>
							<td colspan="5"><input type="text" name="address" style="width: 442px" id="address" value="<?php echo $customerData['address'] ?>" inputmode="kana" ><input type="text" style="width: 55px;" name="roomno" value="<?php echo $customerData['roomno'] ?>" inputmode="numeric" >号室</td>
						</tr>
						<tr>
							<th>住所２</th>
							<td colspan="5"><input type="text" name="address2" style="width: 442px" id="address2" value="<?php echo $customerData['address2'] ?>" inputmode="kana" ><input type="text" style="width: 55px;" name="roomno2" value="<?php echo $customerData['roomno2'] ?>" inputmode="numeric" >号室</td>
						</tr>
						<tr>
							<th>住所３</th>
							<td colspan="5"><input type="text" name="address3" style="width: 442px" id="address3" value="<?php echo $customerData['address3'] ?>" inputmode="kana" ><input type="text" style="width: 55px;" name="roomno3" value="<?php echo $customerData['roomno3'] ?>" inputmode="numeric" >号室</td>
						</tr>
						<tr>
							<th>登録番号リスト</th>
							<td colspan="5">
								<input type="number" min="0" name="tel1" id="tel1" value="<?php echo $customerData['tel1'] ?>" inputmode="numeric" style="width: 208px">
								<input type="number" min="0" name="tel2" id="tel2" value="<?php echo $customerData['tel2'] ?>" inputmode="numeric" style="width: 208px">
								<input type="number" min="0" name="tel3" id="tel3" value="<?php echo $customerData['tel3'] ?>" inputmode="numeric" style="width: 208px">
							</td>
						</tr>
						<tr>
							<th>備考</th>
							<td colspan="5">
								<textarea name="remark" id="remark" inputmode="kana" ><?php echo $customerData['remark'] ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
				</form>
<!--
							<div class="title" >統計データ</div>
							<hr>
								<table id="usageStatistics" >
									<tbody><tr>
										<th>
										総接客数:
										</th><td><?php echo $usageStatistics['all']['count'] ?></td>
										<th>
										本指名数:
										</th><td><?php echo $usageStatistics['hon']['count'] ?></td>
										<th>
										パネル指名数:
										</th><td><?php echo $usageStatistics['panel']['count'] ?></td>
										<th>
										フリー数	:
										</th><td><?php echo $usageStatistics['free']['count'] ?></td>
									</tr>
									<tr>
										<th>
										利用時間合計:
										</th><td><?php echo minuteToHourMin($usageStatistics['all']['time']) ?></td>
										<th>
										本指名時間合計:
										</th><td><?php echo minuteToHourMin($usageStatistics['hon']['time']) ?></td>
										<th>
										売上合計:
										</th><td><?php echo number_format($usageStatistics['all']['price']) ?></td>
										<th>
										本指名率:
										</th><td><?php echo @round((($usageStatistics['hon']['count'] / $usageStatistics['all']['count']) * 100),2) ?>％</td>
									</tr>
								</tbody></table>
-->
<?php if($girlUsageExist){ ?>
<div id="usageCastList">
	<div class="title" >利用キャスト履歴一覧　<span class="col1">赤字は本指名利用あり</span></div>
	<ul id="clist">
<?php
foreach((array)$girlUsageListAry AS $girlUsageList){
$sql = "SELECT `id` FROM `usage_data` WHERE `customer_id` = '{$_REQUEST['cid']}' AND `girl` = '{$girlUsageList['girl']}' AND `nominate` = '本指名'";
$honshimeiExist[$girlUsageList['girl']] = dbCount($sql);
?>
<li <?php if($honshimeiExist[$girlUsageList['girl']]){ echo 'class="honshiExist"';} ?> ><?php echo $girlUsageList['girl'] ?><span class="cnt">(<?php echo $girlUsageList['count'] ?>)</span></li>
<?php } ?>
	</ul>
</div>
<?php } ?>
		<div id="modalUsageListWrapper">
			<div class="title" >利用履歴一覧</div>
			<span class="btn dataDelete" data-id="usageList" data-mode="usage" >チェックした履歴を削除</span>
			<table id="usageList" >
				<thead>
					<tr>
						<th><input type="checkbox" class="checkAll" data-id="usageList" ></th>
						<th>利用日時</th>
						<th>利用店舗</th>
						<th>キャスト名</th>
						<th>指名種別</th>
						<th>分</th>
						<th>金額</th>
						<th>備考</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
<?php
foreach((array)$usageDataAry AS $usageData){
?>
					<tr>
						<td><?php if($usageData['shop_id'] == $_SESSION['id']){ ?><input type="checkbox" value="<?php echo $usageData['id'] ?>" ><?php } ?></td>
						<td><?php echo date("Y-m-d H:i",strtotime($usageData['p_date'])) ?></td>
						<td><?php echo $usageData['usage_shopname'] ?></td>
						<td><?php echo $usageData['girl'] ?></td>
						<td><?php echo $usageData['nominate'] ?></td>
						<td><?php echo $usageData['p_time'] ?></td>
						<td><?php echo number_format($usageData['price']) ?></td>
						<td><?php echo $usageData['remark'] ?></td>
						<td>
							<?php if($usageData['shop_id'] == $_SESSION['id']){ ?>
							<div class="btn edit usageEdit" data-cid="<?php echo $usageData['customer_id'] ?>" data-uid="<?php echo $usageData['id'] ?>" >編集</div>
							<?php } else { ?>
							<div class="btn disabled" >編集・削除不可</div>
							<?php } ?>
						</td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</div>
			<div class="clear"></div>
		</div>
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
<script>
$(document).on("click", ".saveBtn", function () {
	let err;
	if(!$('#name').val()){
		Swal.fire(
			'必須入力エラー',
			'会員名が入力されていません。',
			'error'
		)
		err = true;
	}
	if($('#tel1').val() && ($('#tel1').val().length > 11 || $('#tel1').val().length < 10)){
		Swal.fire(
			'電話番号エラー',
			'電話番号１の桁数が異常です。',
			'error'
		)
		err = true;
	}
	if($('#tel2').val() && ($('#tel2').val().length > 11 || $('#tel2').val().length < 10)){
		Swal.fire(
			'電話番号エラー',
			'電話番号２の桁数が異常です。',
			'error'
		)
		err = true;
	}
	if($('#tel3').val() && ($('#tel3').val().length > 11 || $('#tel3').val().length < 10)){
		Swal.fire(
			'電話番号エラー',
			'電話番号３の桁数が異常です。',
			'error'
		)
		err = true;
	}
	$.ajax({
		url: '/ajax/numberDuplicateCheck.php',
		type: "POST",
		data: {
				id: $('#cid').val(),
				num1: $('#tel1').val(),
				num2: $('#tel2').val(),
				num3: $('#tel3').val(),
		},
		cache: false,
		async : false,
	})
	.done(function (duplicateUserJson) {
		const duplicateUserArray = JSON.parse(duplicateUserJson)
		if(duplicateUserArray){
			Swal.fire(
				'番号登録済み',
				'この番号' + duplicateUserArray.number + 'は既に登録されています。ID:' + duplicateUserArray.id,
				'error'
			)
			err = true;
		}
	})
	.fail(function (jqXHR, textStatus, errorThrown) {
	});
	if(err){
		return false;
	} else {
		$('.modalLoading').show();
		$('form').submit();
	}
});

</script>
<div class="modalLoading"><div class="fl fl-spinner spinner"><div class="cube1"></div><div class="cube2"></div></div></div>