<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
if($_POST['num1']){
  $sql = "SELECT `id`,`customer_id` FROM `customer_data` WHERE (`tel1` = '{$_POST['num1']}' OR `tel2` = '{$_POST['num1']}' OR `tel3` = '{$_POST['num1']}') && `id` != '{$_POST['id']}' LIMIT 1";
  $raw1 = get1Record($sql);
  if($raw1){
    $res['id'] = $raw1['customer_id'];
    $res['number'] = $_POST['num1'];
  }
}
if($_POST['num2']){
  $sql = "SELECT `id`,`customer_id` FROM `customer_data` WHERE (`tel1` = '{$_POST['num2']}' OR `tel2` = '{$_POST['num2']}' OR `tel3` = '{$_POST['num2']}') && `id` != '{$_POST['id']}' LIMIT 1";
  $raw2 = get1Record($sql);
  if($raw2){
    $res['id'] = $raw2['customer_id'];
    $res['number'] = $_POST['num2'];
  }
}
if($_POST['num3']){
  $sql = "SELECT `id`,`customer_id` FROM `customer_data` WHERE (`tel1` = '{$_POST['num3']}' OR `tel2` = '{$_POST['num3']}' OR `tel3` = '{$_POST['num3']}') && `id` != '{$_POST['id']}' LIMIT 1";
  $raw3 = get1Record($sql);
  if($raw3){
    $res['id'] = $raw3['customer_id'];
    $res['number'] = $_POST['num3'];
  }
}
echo json_encode($res);