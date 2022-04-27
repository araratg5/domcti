<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  if ($_SERVER["REQUEST_METHOD"]=="POST") {
    if(!$_REQUEST['reset']){
      $_SESSION['start_date'] = $_REQUEST['start_date'];
      $_SESSION['start_time'] = $_REQUEST['start_time'];
      $_SESSION['end_date'] = $_REQUEST['end_date'];
      $_SESSION['end_time'] = $_REQUEST['end_time'];
      $_SESSION['customer_id'] = $_REQUEST['customer_id'];
      $_SESSION['customer_name'] = $_REQUEST['customer_name'];
      $_SESSION['tel'] = $_REQUEST['tel'];
      $_SESSION['address'] = $_REQUEST['address'];
      $_SESSION['free_word'] = $_REQUEST['free_word'];
    } else {
      $_SESSION['start_date'] = date("Y年m月d日",strtotime("-7 day"));
      $_SESSION['start_time'] = date("00:00",strtotime("-7 day"));
      $_SESSION['end_date'] = date("Y年m月d日");
      $_SESSION['end_time'] = date("23:59");
      $_SESSION['customer_id'] = '';
      $_SESSION['customer_name'] = '';
      $_SESSION['tel'] = '';
      $_SESSION['address'] = '';
      $_SESSION['free_word'] = '';
    }
  }
  $_SESSION['start_datetime'] = str_replace(['年','月','日'],['-','-',''],$_SESSION['start_date']).' '.$_SESSION['start_time'].':00';
  $_SESSION['end_datetime'] = str_replace(['年','月','日'],['-','-',''],$_SESSION['end_date']).' '.$_SESSION['end_time'].':00';
  $searchConditionAry[] = "`ch`.`time` BETWEEN '{$_SESSION['start_datetime']}' AND '{$_SESSION['end_datetime']}'";
  if($_SESSION['customer_id']){
    $searchConditionAry[] = "`cd`.`customer_id` = '{$_SESSION['customer_id']}'";
  }
  if($_SESSION['customer_name']){
    $searchConditionAry[] = "`cd`.`name` LIKE '%{$_SESSION['customer_name']}%'";
  }
  if($_SESSION['tel']){
    $searchConditionAry[] = "`ch`.`num` LIKE '%{$_SESSION['tel']}%'";
  }
  if($_SESSION['address']){
    $searchConditionAry[] = "`cd`.`address` LIKE '%{$_SESSION['address']}%'";
  }
  $searchCondition = ' AND '.implode(' AND ',$searchConditionAry);

  $sql = "SELECT `ch`.`id` FROM `call_history` AS `ch` LEFT JOIN `customer_data` AS `cd` ON `ch`.`num` = `cd`.`tel1` WHERE `ch`.`shop_id` = '{$_SESSION['id']}' {$searchCondition} ORDER BY `time` DESC";
  $allCount += dbCount($sql);
  $sql = "SELECT `ch`.`id` FROM `call_history` AS `ch` LEFT JOIN `customer_data` AS `cd` ON `ch`.`num` = `cd`.`tel2` WHERE `ch`.`shop_id` = '{$_SESSION['id']}' {$searchCondition} ORDER BY `time` DESC";
  $allCount += dbCount($sql);
  $sql = "SELECT `ch`.`id` FROM `call_history` AS `ch` LEFT JOIN `customer_data` AS `cd` ON `ch`.`num` = `cd`.`tel3` WHERE `ch`.`shop_id` = '{$_SESSION['id']}' {$searchCondition} ORDER BY `time` DESC";
  $allCount += dbCount($sql);

  if($allCount){  
    $perCount = 300;
    $pagerData = getPager($allCount,$perCount,$_GET['p']);
    $start = ($pagerData['current_page'] - 1) * $perCount;

    $sql = "SELECT `ch`.`id` AS `hid`,`ch`.`num`,`ch`.`time`,`cd`.`id` AS `cid`,`cd`.`customer_id`,`cd`.`name`,`cd`.`remark`,`cd`.`rating`,`cd`.`address` FROM (SELECT `id`,`num`,`time` FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}') AS `ch` INNER JOIN (SELECT `id`,`customer_id`,`name`,`remark`,`rating`,`address`,`tel1` FROM `customer_data`) AS `cd` ON `ch`.`num` = `cd`.`tel1` WHERE 1 {$searchCondition} ORDER BY `time` DESC LIMIT {$start},{$perCount}";
    $res1 = getRecord($sql);
    $sql = "SELECT `ch`.`id` AS `hid`,`ch`.`num`,`ch`.`time`,`cd`.`id` AS `cid`,`cd`.`customer_id`,`cd`.`name`,`cd`.`remark`,`cd`.`rating`,`cd`.`address` FROM (SELECT `id`,`num`,`time` FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}') AS `ch` INNER JOIN (SELECT `id`,`customer_id`,`name`,`remark`,`rating`,`address`,`tel2` FROM `customer_data`) AS `cd` ON `ch`.`num` = `cd`.`tel2` WHERE 1 {$searchCondition} ORDER BY `time` DESC LIMIT {$start},{$perCount}";
    $res2 = getRecord($sql);
    $sql = "SELECT `ch`.`id` AS `hid`,`ch`.`num`,`ch`.`time`,`cd`.`id` AS `cid`,`cd`.`customer_id`,`cd`.`name`,`cd`.`remark`,`cd`.`rating`,`cd`.`address` FROM (SELECT `id`,`num`,`time` FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}') AS `ch` INNER JOIN (SELECT `id`,`customer_id`,`name`,`remark`,`rating`,`address`,`tel3` FROM `customer_data`) AS `cd` ON `ch`.`num` = `cd`.`tel3` WHERE 1 {$searchCondition} ORDER BY `time` DESC LIMIT {$start},{$perCount}";
    $res3 = getRecord($sql);
    $resRaw = array_merge((array)$res1,(array)$res2,(array)$res3);

    $res = sortByKey('hid', SORT_DESC, $resRaw);
  }

// 指定したキーに対応する値を基準に、配列をソートする
function sortByKey($key_name, $sort_order, $array) {
    foreach ($array as $key => $value) {
        $standard_key_array[$key] = $value[$key_name];
    }

    array_multisort($standard_key_array, $sort_order, $array);

    return $array;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css" media="all">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<title>ドM会員管理システム</title>
</head>
<body id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
  <input type="hidden" id="shopid" value="<?php echo $_SESSION['id'] ?>" >
  <div id="wrapper">
<?php include_once(DOCUMENT_ROOT.'/include/navi.php') ?>
    <main>
      <article id="" class="tableWrapper" >
	<div class="headinfo">
        	<div class="title">着信履歴検索</div>
		<div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
	</div>
        <div id="searchBox">
          <form action="" method="post" autocomplete="off" >
            <ul>
              <li>期間：　</li>
              <li>
                <input type="text" name="start_date" id="startDate" value="<?php echo $_SESSION['start_date'] ?>" >
                <input type="text" name="start_time" class="timePicker" data-time-format="H:i" id="startTime" value="<?php echo $_SESSION['start_time'] ?>" >～
                <input type="text" name="end_date" id="endDate" value="<?php echo $_SESSION['end_date'] ?>" >
                <input type="text" name="end_time" class="timePicker" data-time-format="H:i" id="endTime" value="<?php echo $_SESSION['end_time'] ?>" >(デフォルト直近一週間/最大表示件数1000件)
              </li>
            </ul>
            <ul>
              <li>会員ID：　<input type="text" value="<?php echo $_SESSION['customer_id'] ?>" name="customer_id" id="customerId" class="mr10" ></li>
              <li>会員名：　<input type="text" value="<?php echo $_SESSION['customer_name'] ?>" name="customer_name" id="customerName" class="mr10" ></li>
              <li>電話番号：　<input type="text" value="<?php echo $_SESSION['tel'] ?>" name="tel" id="tel" class="mr10" ></li>
              <li>住所：　<input type="text" value="<?php echo $_SESSION['address'] ?>" name="address" id="address" class="mr10" ></li>
            </ul>
            <input type="submit" value="検　索" class="searchBtn" >
            <input type="submit" value="リセット" name="reset" class="resetBtn" >
          </form>
        </div>
<?php if($pagerData['end_page']>1){ ?>
<div class="pager">
<ul class="clearfix">
<?php if($pagerData['prev_page']){ ?><li><a href="<?php echo $pagerData['pager_uri_array'][$pagerData['prev_page']] ?>">&lt;</a></li><?php } ?>
<?php for($pi=$pagerData['start_page'];$pi<=$pagerData['end_page'];$pi++){ ?>
<li><a href="<?php echo $pagerData['pager_uri_array'][$pi] ?>" <?php if($pagerData['current_page'] == $pi){echo 'class="current"';} ?> ><?php echo $pi ?></a></li>
<?php } ?>
<?php if($pagerData['next_page']){ ?><li><a href="<?php echo $pagerData['pager_uri_array'][$pagerData['next_page']] ?>">&gt;</a></li><?php } ?>
</ul>
</div>
<?php } ?>
        <table id="historyList" >
          <thead>
            <tr>
              <th style="width:50px !important;">No</th>
              <th style="width:165px !important;">着信日時</th>
              <th style="width:90px !important;">会員ID</th>
              <th style="width:300px !important;">名前</th>
              <th style="width:130px !important;">番号</th>
              <th>住所</th>
              <!-- <th>備考</th> -->
            </tr>
          </thead>
          <tbody>
<?php
$i = 1;
foreach((array)$res AS $callHistoryData){
  switch ($callHistoryData['rating']) {
    case '注意':
      $statCol = 'style="background: #ffc294"';
      break;
    case '出禁':
      $statCol = 'style="background: #ffb5b5"';
      break;
    default:
      $statCol = 'style="background: #fff"';
      break;
  }
?>
            <tr data-customer-id="<?php echo $callHistoryData['cid'] ?>" data-customer-num="<?php echo $callHistoryData['num'] ?>" <?php echo $statCol ?> >
              <td style="width:50px !important;"><?php echo $i ?></td>
              <td style="width:165px !important; text-align:center;"><?php echo date("Y-m-d H:i:s",strtotime($callHistoryData['time'])) ?></td>
              <td style="width:90px !important; text-align:center;"><?php echo $callHistoryData['customer_id'] ?></td>
              <td style="width:300px !important; text-align:left;"><?php echo $callHistoryData['name'] ?></td>
              <td style="width:130px !important; text-align:center;"><?php echo $callHistoryData['num'] ?></td>
              <td><?php echo $callHistoryData['address'] ?></td>
              <!-- <td><?php echo $callHistoryData['remark'] ?></td> -->
            </tr>
<?php $i++;} ?>
          </tbody>
        </table>
<?php if($pagerData['end_page']>1){ ?>
<div class="pager">
<ul class="clearfix">
<?php if($pagerData['prev_page']){ ?><li><a href="<?php echo $pagerData['pager_uri_array'][$pagerData['prev_page']] ?>">&lt;</a></li><?php } ?>
<?php for($pi=$pagerData['start_page'];$pi<=$pagerData['end_page'];$pi++){ ?>
<li><a href="<?php echo $pagerData['pager_uri_array'][$pi] ?>" <?php if($pagerData['current_page'] == $pi){echo 'class="current"';} ?> ><?php echo $pi ?></a></li>
<?php } ?>
<?php if($pagerData['next_page']){ ?><li><a href="<?php echo $pagerData['pager_uri_array'][$pagerData['next_page']] ?>">&gt;</a></li><?php } ?>
</ul>
</div>
<?php } ?>
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
<script type="text/javascript" src="/js/common_new.js"></script>
<script src="https://cdn.firebase.com/js/client/2.3.2/firebase.js"></script>
<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  const firebaseConfig = {
    apiKey: "AIzaSyDTr3tesD6Sh_lXbrVlLZ8_jpuQ9Q30Ar4",
    authDomain: "araratcti.firebaseapp.com",
    databaseURL: "https://araratcti-default-rtdb.firebaseio.com",
    projectId: "araratcti",
    storageBucket: "araratcti.appspot.com",
    messagingSenderId: "3266214634",
    appId: "1:3266214634:web:be458a761561235cd0f593"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
</script>
<script type="text/javascript" src="/js/callWaitNew.js"></script>
<div class="loading"><div class="fl fl-spinner spinner"><div class="cube1"></div><div class="cube2"></div></div></div>