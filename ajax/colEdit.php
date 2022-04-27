<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

  $postData = allescape($_POST);
  $tbl = $postData['mode'].'_data';
  $col = strDecrypt($postData['col']);
  switch ($col) {
    case 'start_date':
      $col = 'p_date';
      echo $sql = "SELECT `p_date` FROM `{$tbl}` WHERE `id` = '{$postData['id']}'";
      $currentDateData = get1Record($sql);
      $postData['val'] = str_replace(['年','月','日'],['-','-',''],$postData['val']).' '.date("H:i:00",strtotime($currentDateData['p_date']));
      break;
    case 'start_time':
      $col = 'p_date';
      $sql = "SELECT `p_date` FROM `{$tbl}` WHERE `id` = '{$postData['id']}'";
      $currentDateData = get1Record($sql);
      $postData['val'] = date("Y-m-d ",strtotime($currentDateData['p_date'])).$postData['val'].':00';
      break;
    case 'p_option':
      echo $_POST['val'];
      $postData['val'] = json_encode($_POST['val'],JSON_UNESCAPED_UNICODE);
      break;
    default:
      break;
  }
  echo $q = "UPDATE `{$tbl}` SET {$extendIdQuery} `{$col}` = '{$postData['val']}' WHERE `id` = '{$postData['id']}'";
	DBQuery($q);