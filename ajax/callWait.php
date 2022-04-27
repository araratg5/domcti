<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  $time1 = date("Y-m-d H:i:s",strtotime("-2 second"));
  $time2 = date("Y-m-d H:i:s");
  $sql = "SELECT `id`,`num` FROM `call_history` WHERE (`time` > '{$time1}' AND `time` < '{$time2}') AND `shop_id` = '{$_SESSION['id']}'";
  $callData = get1Record($sql);
  if($callData['num']){
    $sql = "SELECT `id` FROM `customer_data` WHERE `is_delete` = 0 AND `tel1` = '{$callData['num']}' OR `tel2` = '{$callData['num']}' OR `tel3` = '{$callData['num']}' LIMIT 1";
    $customerData = get1Record($sql);
  }
  $res['num'] = $callData['num'];
  $res['id'] = $customerData['id'];
  $res['sql'] = $sql;
echo json_encode($res);