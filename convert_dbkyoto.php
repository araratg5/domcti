<?php 
ini_set("memory_limit", "-1");
ini_set("max_execution_time",0); // タイムアウトしない
ini_set("max_input_time",0); // パース時間を設定しない

include_once('/var/www/html/lib/func.php');
$_GET["shop_id"] = 'dbkyoto';
echo 'start'.date('Y-m-d H:i:s');
if ($_GET["shop_id"] != "") {
	$sql ="SELECT * FROM `shops` WHERE `shop_id` = '{$_GET["shop_id"]}'";
	$shopData = get1Record($sql,1);
	$di = 0;
	$sql ="SELECT * FROM `import_data_{$shopData['shop_id']}`";
	$importMasterData = getRecord($sql);
	foreach ($importMasterData as $array) {
		$head = $array["col_1"];
		if($head != '電話番号１'){
			for ($i = 0; $i < count($array); ++$i) {
				$i2 = $i+1;
				$itemData[$di][$i] = $array["col_{$i2}"];
			}
			//電話番号１
			$insertData[$di]['tel1'] = str_replace('-','',$itemData[$di][0]);

			//電話番号重複チェック
			$sql = "SELECT `id`,`customer_id`,`customer_id`,`remark` FROM `customer_data_cp_0415_import_check` WHERE (`tel1` = '{$insertData[$di]['tel1']}' OR `tel2` = '{$insertData[$di]['tel1']}' OR `tel3` = '{$insertData[$di]['tel1']}')";
			$exist[$di] = get1Record($sql);

			$backupJson = json_encode($itemData[$di],JSON_UNESCAPED_UNICODE);
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
				$insertData[$di]['remark'] = '/'.$shopData['name_ja'].'：'.$itemData[$di][23];
			}
			//更新
			$insertData[$di]['modified'] = $itemData[$di][24].' '.$itemData[$di][25];
			//登録
			$insertData[$di]['created'] = $itemData[$di][26].' '.$itemData[$di][27];
			//バックアップ
			$insertData[$di]['sharoku_backup'] = json_encode($itemData[$di],JSON_UNESCAPED_UNICODE);
			if(!$exist[$di]){//重複なし
				$sql = "INSERT INTO `customer_data_cp_0415_import_check` (`shop_id`,`usage_shopname`,`modified`,`created`) VALUES ('{$shopData['shop_id']}','{$shopData['name_ja']}','{$insertData[$di]['modified']}','{$insertData[$di]['created']}')";
				$insertId = insData($sql);
				$insertData[$di]['customer_id'] = getCustomerId($insertId);
				foreach($insertData[$di] as $column => $value){
					$queryAry[] = "`{$column}` = '{$value}'";
				}
				$queryStr = implode(',',$queryAry);
				$sql ="UPDATE `customer_data_cp_0415_import_check` SET {$queryStr} WHERE `id` = '{$insertId}'";
				dbQuery($sql);
			} elseif($shopData['shop_id'] != $exist[$di]['shop_id']) {//更新(同一店舗は何もしない)
				//更新対象に同じ店舗名の備考がなくて読み込みデータに備考が存在する場合のみ更新
				if(strpos($exist[$di]['remark'],$shopData['name_ja']) === false && $itemData[$di][23]!=''){
					$sql ="UPDATE `customer_data_cp_0415_import_check` SET `remark` = '{$exist[$di]['remark']}{$insertData[$di]['remark']}' WHERE `id` = '{$exist[$di]['id']}'";
					dbQuery($sql);
				}
			}
			$di++;
		}
	}
}
echo 'end'.date('Y-m-d H:i:s');
