<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_FILES['csvdata_customer']["tmp_name"]) {
		if (($fp = @fopen($_FILES['csvdata_customer']["tmp_name"], "r")) === false) {
			die("CSVファイル読み込みエラー");
		}
		$di=0;
		while (($array = fgetcsv($fp)) !== FALSE) {
			//空行を取り除く
			if (!array_diff($array, array(''))) {
				continue;
			}
			$head = mb_convert_encoding($array[0], 'UTF-8', 'SJIS');
			if($head != '電話番号１'){
				for ($i = 0; $i < count($array); ++$i) {
					$itemData[$di][$i] = mb_convert_encoding($array[$i], 'UTF-8', 'SJIS');
				}
				//電話番号１
				$insertData[$di]['tel1'] = str_replace('-','',$itemData[$di][0]);

				//電話番号重複チェック
				$sql = "SELECT `id`,`customer_id`,`customer_id`,`remark` FROM `customer_data` WHERE (`tel1` = '{$insertData[$di]['tel1']}' OR `tel2` = '{$insertData[$di]['tel1']}' OR `tel3` = '{$insertData[$di]['tel1']}')";
				$exist[$di] = get1Record($sql);
				$latestNotBlankRemark = $exist[$i]['remark'];
				//登録処理
				//電話番号２
				$insertData[$di]['tel2'] = str_replace('-','',$itemData[$di][1]);
				//住所
				$insertData[$di]['address'] = $itemData[$di][3].$itemData[$di][4].$itemData[$di][5].$itemData[$di][6];
				//名前
				$insertData[$di]['name'] = $itemData[$di][8].$itemData[$di][9].$itemData[$di][10].$itemData[$di][11];
				if(!$insertData[$di]['name']){
					$insertData[$di]['name'] = '新規：'.$insertData[$di]['tel1'];
				}
				//カナ
				$insertData[$di]['kana'] = $itemData[$di][8].$itemData[$di][10];
				//備考
				if($itemData[$di][23]!=''){
					$insertData[$di]['remark'] = '/'.$_SESSION["shop_name"].'：'.$itemData[$di][23];
					$insertData[$di]['remark2'] = '/：'.$itemData[$di][23];
				}
				//更新
				$insertData[$di]['modified'] = $itemData[$di][24].' '.$itemData[$di][25];
				//登録
				$insertData[$di]['created'] = $itemData[$di][26].' '.$itemData[$di][27];
				//バックアップ
				$insertData[$di]['sharoku_backup'] = json_encode($itemData[$di],JSON_UNESCAPED_UNICODE);
				if(!$exist[$di]){//重複なし
					$sql = "INSERT INTO `customer_data` (`shop_id`,`usage_shopname`,`modified`,`created`) VALUES ('{$_SESSION["id"]}','{$_SESSION["shop_name"]}','{$insertData[$di]['modified']}','{$insertData[$di]['created']}')";
					$insertId = insData($sql);
					$insertData[$di]['customer_id'] = getCustomerId($insertId);
					unset($insertData[$di]['remark2']);
					foreach($insertData[$di] as $column => $value){
						$queryAry[$di][] = "`{$column}` = '{$value}'";
					}
					$queryStr[$di] = implode(',',$queryAry[$di]);
					$sql ="UPDATE `customer_data` SET {$queryStr[$di]} WHERE `id` = '{$insertId}'";
					dbQuery($sql);
				} else {
					if($itemData[$di][23]=='' && $latestNotBlankRemark){
						$replace[$di]['remark'] = $latestNotBlankRemark;
					} else {
						$replace[$di]['remark'] = $exist[$di]['remark'];
					}
					//アップしようとしたテキストと同じものが存在したら消す
					$exist[$di]['remark'] = str_replace($insertData[$di]['remark'],'',$replace[$di]['remark']);
					$exist[$di]['remark'] = str_replace($insertData[$di]['remark2'],'',$exist[$di]['remark']);
					$itemData[$di]['update_remark'] = $exist[$di]['remark'].$insertData[$di]['remark'];
					$sql ="UPDATE `customer_data` SET `remark` = '{$itemData[$di]['update_remark']}' WHERE `id` = '{$exist[$di]['id']}'";
					dbQuery($sql);
				}
				$di++;
			}
		}
		@fclose($fp);
	}
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css" media="all">
	<title>ドM会員管理システム</title>
</head>
<body id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
  <div id="wrapper">
<?php include_once(DOCUMENT_ROOT.'/include/navi.php') ?>
    <main>
      <article id="callHistoryTableWrapper" class="tableWrapper" >
	<div class="headinfo">
        	<div class="title">写録データインポート</div>
		<div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
	</div>
			<div class="box">
				<div class="title">顧客データ</div>
				<form action="" method="POST" enctype="multipart/form-data">
					<input type="file" name="csvdata_customer" >
					<input type="submit" class="btn edit csvUpload" id="" value="CSVアップロード" >
				</form>
			</div>
      </article>
    </main>
    <div class="clear"></div>
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