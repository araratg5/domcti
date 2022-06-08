<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  if ($_SERVER["REQUEST_METHOD"]=="POST") {
    if(!$_REQUEST['reset']){
      $_SESSION['start_date'] = $_REQUEST['start_date'];
      $_SESSION['start_time'] = $_REQUEST['start_time'];
      $_SESSION['end_date'] = $_REQUEST['end_date'];
      $_SESSION['end_time'] = $_REQUEST['end_time'];
      $_SESSION['all_period'] = $_REQUEST['all_period'];
      $_SESSION['customer_id'] = $_REQUEST['customer_id'];
      $_SESSION['customer_name'] = $_REQUEST['customer_name'];
      $_SESSION['rating'] = $_REQUEST['rating'];
      $_SESSION['tel'] = $_REQUEST['tel'];
      $_SESSION['address'] = $_REQUEST['address'];
      $_SESSION['free_word'] = $_REQUEST['free_word'];
      $_SESSION['p'] = $_REQUEST['p'];
    } else {
      $_SESSION['start_date'] = date("Y年m月d日",strtotime("-7 day"));
      $_SESSION['start_time'] = date("00:00",strtotime("-7 day"));
      $_SESSION['end_date'] = date("Y年m月d日");
      $_SESSION['end_time'] = date("23:59");
      $_SESSION['all_period'] = '';
      $_SESSION['customer_id'] = '';
      $_SESSION['customer_name'] = '';
      $_SESSION['rating'] = '';
      $_SESSION['tel'] = '';
      $_SESSION['address'] = '';
      $_SESSION['free_word'] = '';
    }
  }
  $_SESSION['history_p'] = $_REQUEST['p'];

  $_SESSION['start_datetime'] = str_replace(['年','月','日'],['-','-',''],$_SESSION['start_date']).' '.$_SESSION['start_time'].':00';
  $_SESSION['end_datetime'] = str_replace(['年','月','日'],['-','-',''],$_SESSION['end_date']).' '.$_SESSION['end_time'].':00';

  $callHistorySearchConditionAry[] = "`shop_id` = '{$_SESSION['id']}'";
  if(!$_SESSION['all_period']){
    $callHistorySearchConditionAry[] = "`ch`.`time` BETWEEN '{$_SESSION['start_datetime']}' AND '{$_SESSION['end_datetime']}'";
  }
  if($_SESSION['customer_id']){
    $searchConditionAry[] = "`cd`.`customer_id` = '{$_SESSION['customer_id']}'";
  }
  if($_SESSION['customer_name']){
    $searchConditionAry[] = "`cd`.`name` LIKE '%{$_SESSION['customer_name']}%'";
  }
  if($_SESSION['rating']){
    $searchConditionAry[] = "`cd`.`rating` = '{$_SESSION['rating']}'";
  }
  if($_SESSION['tel']){
    $callHistorySearchConditionAry[] = "`ch`.`num` LIKE '%{$_SESSION['tel']}%'";
  }
  if($_SESSION['address']){
    $searchConditionAry[] = "`cd`.`address` LIKE '%{$_SESSION['address']}%'";
  }
  if(is_array($searchConditionAry)){
    $searchCondition = ' AND '.implode(' AND ',$searchConditionAry);
  }
  $callHistorySearchCondition = implode(' AND ',$callHistorySearchConditionAry);
  
  $sql = "SELECT `id` FROM `call_history` AS `ch` WHERE `is_delete` = 0 AND  {$callHistorySearchCondition} ORDER BY `time` DESC";
  $allCount = dbCount($sql);

  $perCount = 300;
  $pagerData = getPager($allCount,$perCount,$_GET['p']);
  $start = ($pagerData['current_page'] - 1) * $perCount;

  $sql = "SELECT * FROM `call_history` AS `ch` WHERE `is_delete` = 0 AND  {$callHistorySearchCondition} ORDER BY `time` DESC LIMIT {$start},{$perCount}";
  $usageDataAry = getRecord($sql);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<title><?php echo GROUP_NAME ?>会員管理システム</title>
</head>
<style>
	#wrapper {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>
	}
</style>
<body class="<?php echo GROUP_MODE ?>" id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
  <input type="hidden" id="shopid" value="<?php echo $_SESSION['id'] ?>" >
  <input type="hidden" id="mode" value="history" >
  <div id="wrapper">
<?php include_once(DOCUMENT_ROOT.'/include/navi.php') ?>
    <main>
      <article id="historyTableWrapper" class="tableWrapper" >
        <div class="headinfo">
                <div class="title">着信履歴検索</div>
          <div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
        </div>
        <div id="searchBox">
          <form action="/history.php" method="post" autocomplete="off" >
            <ul>
              <li>期間：　</li>
              <li>
                全て：<input type="checkbox" name="all_period" value="1" <?php if($_SESSION['all_period']){ echo 'checked';} ?> >
                <input type="text" name="start_date" id="startDate" value="<?php echo $_SESSION['start_date'] ?>" <?php if($_SESSION['all_period']){ echo 'readonly';} ?> >
                <input type="text" name="start_time" <?php if($_SESSION['all_period']){ echo 'readonly';} ?> class="timePicker" data-time-format="H:i" id="startTime" value="<?php echo $_SESSION['start_time'] ?>" >～
                <input type="text" name="end_date" id="endDate" value="<?php echo $_SESSION['end_date'] ?>" <?php if($_SESSION['all_period']){ echo 'readonly';} ?> >
                <input type="text" name="end_time" class="timePicker" data-time-format="H:i" id="endTime" value="<?php echo $_SESSION['end_time'] ?>" <?php if($_SESSION['all_period']){ echo 'readonly';} ?> >(デフォルト直近一週間)
              </li>
            </ul>
            <ul>
              <li>会員ID：　<input type="tel" inputmode="tel" style="ime-mode: inactive;"  value="<?php echo $_SESSION['customer_id'] ?>" name="customer_id" id="customerId" class="mr10" ></li>
              <li>会員名：　<input type="text" inputmode="kana" style="ime-mode: active;" value="<?php echo $_SESSION['customer_name'] ?>" name="customer_name" id="customerName" class="mr10" ></li>
              <li>評価：　
								<select name="rating" id="rating" style="background:<?php if($_SESSION['rating']=='注意'){ echo '#ffc294';} elseif($_SESSION['rating']=='出禁'){ echo '#ffb5b5';} elseif($_SESSION['rating']=='優良'){ echo '#fff9cf';} ?> !important" >
                  <option value="">全て</option>
									<option style="background: #fff !important" value="一般" <?php if($_SESSION['rating']=='一般'){ echo 'selected';} ?> >一般</option>
									<option style="background: #fff !important" value="優良" <?php if($_SESSION['rating']=='優良'){ echo 'selected';} ?> >優良</option>
									<option style="background: #fff !important" value="注意" <?php if($_SESSION['rating']=='注意'){ echo 'selected';} ?> >注意</option>
									<option style="background: #fff !important" value="出禁" <?php if($_SESSION['rating']=='出禁'){ echo 'selected';} ?> >出禁</option>
									<option style="background: #fff !important" value="スタッフ" <?php if($_SESSION['rating']=='スタッフ'){ echo 'selected';} ?> >スタッフ</option>
									<option style="background: #fff !important" value="業者" <?php if($_SESSION['rating']=='業者'){ echo 'selected';} ?> >業者</option>
									<option style="background: #fff !important" value="その他" <?php if($_SESSION['rating']=='その他'){ echo 'selected';} ?> >その他</option>
								</select> 
              </li>
              <li>電話番号：　<input type="tel" inputmode="tel" style="ime-mode: inactive;" value="<?php echo $_SESSION['tel'] ?>" name="tel" id="tel" class="mr10" ></li>
              <li>住所：　<input type="text" inputmode="kana" style="ime-mode: active;" value="<?php echo $_SESSION['address'] ?>" name="address" id="address" class="mr10" ></li>
            </ul>
            <input type="submit" value="検　索" class="searchBtn" >
            <input type="submit" value="リセット" name="reset" class="resetBtn" >
          </form>
        </div>
        <span class="btn dataDelete" data-id="historyList" data-mode="call" >チェックした履歴を削除</span>
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
        <table id="historyList" class="w100" >
          <thead>
            <tr>
              <th style="width:33px !important;"><input type="checkbox" class="checkAll" data-id="historyList" ></th>
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
foreach((array)$usageDataAry AS $callHistoryData){
  $sql = "SELECT `id` FROM `customer_data` AS `cd` WHERE (`tel1` = '{$callHistoryData['num']}' OR `tel2` = '{$callHistoryData['num']}' OR `tel3` = '{$callHistoryData['num']}') LIMIT 1";
  $customerExist = dbCount($sql);
  $sql = "SELECT * FROM `customer_data` AS `cd` WHERE (`tel1` = '{$callHistoryData['num']}' OR `tel2` = '{$callHistoryData['num']}' OR `tel3` = '{$callHistoryData['num']}') {$searchCondition} LIMIT 1";
  $customerData = get1Record($sql);
  if(($customerExist && $customerData) || (!$searchConditionAry && $callHistoryData)){
  switch ($customerData['rating']) {
    case '注意':
      $statCol = 'style="background: #ffc294"';
      break;
    case '優良':
      $statCol = 'style="background: #fff9cf"';
      break;
    case '出禁':
      $statCol = 'style="background: #ffb5b5"';
      break;
    default:
      $statCol = 'style="background: #fff"';
      break;
  }
?>
            <tr data-customer-id="<?php echo $customerData['id'] ?>" data-customer-num="<?php echo $callHistoryData['num'] ?>" <?php echo $statCol ?> >
              <td><input type="checkbox" value="<?php echo $callHistoryData['id'] ?>" ></td>
              <td style="width:50px !important;"><?php echo $start + $i ?></td>
              <td style="width:165px !important; text-align:center;"><?php echo date("Y-m-d H:i:s",strtotime($callHistoryData['time'])) ?></td>
              <td style="width:90px !important; text-align:center;"><?php echo $customerData['customer_id'] ?></td>
              <td style="width:300px !important; text-align:left;"><?php echo $customerData['name'] ?></td>
              <td style="width:130px !important; text-align:center;"><?php echo telSeparator($callHistoryData['num']) ?></td>
              <td><?php echo $customerData['address'] ?></td>
              <!-- <td><?php echo $customerData['remark'] ?></td> -->
            </tr>
<?php $i++;}} ?>
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
<script type="text/javascript" src="/js/common_new.js?<?php echo BUSTING_DATE ?>"></script>
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
<script type="text/javascript" src="/js/callWaitNew.js?<?php echo BUSTING_DATE ?>"></script>
<div class="loading"><div class="fl fl-spinner spinner"><div class="cube1"></div><div class="cube2"></div></div></div>