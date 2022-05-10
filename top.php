<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  $sql = "SELECT * FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}' ORDER BY `time` DESC LIMIT 300";
  $res = getRecord($sql);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
	<title>ドM会員管理システム</title>
</head>
<body id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
  <input type="hidden" id="shopid" value="<?php echo $_SESSION['id'] ?>" >
  <input type="hidden" id="mode" value="top" >
  <div id="wrapper">
<?php include_once(DOCUMENT_ROOT.'/include/navi.php') ?>
    <main>
      <article id="latestTableWrapper" class="tableWrapper" >
        <div class="headinfo">
          <div class="title">着信履歴（最新300件）<div class="btn customerEdit" id="customerAddBtn" >会員新規作成</div></div>
          <div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
        </div>
        <table id="latestList" class="w100" >
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
$sql = "SELECT `id` AS `cid`,`customer_id`,`name`,`address`,`rating` FROM `customer_data` WHERE (`tel1` = '{$callHistoryData['num']}' OR `tel2` = '{$callHistoryData['num']}' OR `tel3` = '{$callHistoryData['num']}')";
$callUserData = get1Record($sql);
switch ($callUserData['rating']) {
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
            <tr <?php if($i==0){echo 'class="current"';} ?> data-customer-id="<?php echo $callUserData['cid'] ?>" data-customer-num="<?php echo $callHistoryData['num'] ?>" <?php echo $statCol ?> >
              <td style="width:50px !important;"><?php echo $i ?></td>
              <td style="width:165px !important; text-align:center;"><?php echo date("Y-m-d H:i:s",strtotime($callHistoryData['time'])) ?></td>
              <td style="width:90px !important; text-align:center;"><?php echo $callUserData['customer_id'] ?></td>
              <td style="width:300px !important; text-align:left;"><?php echo $callUserData['name'] ?></td>
              <td style="width:130px !important; text-align:center;"><?php echo $callHistoryData['num'] ?></td>
              <td><?php echo $callUserData['address'] ?></td>
              <!-- <td><?php echo $callHistoryData['remark'] ?></td> -->
            </tr>
<?php $i++;} ?>
          </tbody>
        </table>
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