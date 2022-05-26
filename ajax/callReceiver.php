<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  if($_POST['num']){
    //静的DBに登録
    $sql2 = "SELECT `id`,`customer_id`,`name`,`address`,`rating` FROM `customer_data` WHERE `is_delete` = 0 AND `tel1` = '{$_POST['num']}' OR `tel2` = '{$_POST['num']}' OR `tel3` = '{$_POST['num']}' LIMIT 1";
    $customerData = get1Record($sql2);
  }
  switch ($customerData['rating']) {
    case '注意':
      $res['rating'] = 'style="background: #ffc294"';
      break;
    case '優良':
      $res['rating'] = 'style="background: #fff9cf"';
      break;
    case '出禁':
      $res['rating'] = 'style="background: #ffb5b5"';
      break;
    default:
      $res['rating'] = 'style="background: #fff"';
      break;
  }
  $res['num'] = $_POST['num'];
  $res['separated_num'] = telSeparator($_POST['num']);
  $res['id'] = $customerData['id'];
  $res['cid'] = $customerData['customer_id'];
  $res['name'] = $customerData['name'];
  $res['address'] = $customerData['address'];
  $res['sql'] = $sql;
echo json_encode($res);