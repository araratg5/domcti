<?php
<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  if($_POST['num']){
    $sql = "SELECT `id` FROM `customer_data` WHERE `is_delete` = 0 AND `tel1` = '{$callData['num']}' OR `tel2` = '{$_POST['num']}' OR `tel3` = '{$_POST['num']}' LIMIT 1";
    $customerData = get1Record($sql);
  }
  $res['num'] = $_POST['num'];
  $res['id'] = $customerData['id'];
  $res['sql'] = $sql;
echo json_encode($res);