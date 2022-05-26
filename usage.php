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
      $_SESSION['girl'] = $_REQUEST['girl'];
      $_SESSION['nominate'] = $_REQUEST['nominate'];
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
      $_SESSION['girl'] = '';
      $_SESSION['nominate'] = '';
    }
  }
  $_SESSION['usage_p'] = $_REQUEST['p'];

  $_SESSION['start_datetime'] = str_replace(['年','月','日'],['-','-',''],$_SESSION['start_date']).' '.$_SESSION['start_time'].':00';
  $_SESSION['end_datetime'] = str_replace(['年','月','日'],['-','-',''],$_SESSION['end_date']).' '.$_SESSION['end_time'].':00';

  $searchConditionAry[] = "`shop_id` = '{$_SESSION['id']}'";
  if(!$_SESSION['all_period']){
    $searchConditionAry[] = "`p_date` BETWEEN '{$_SESSION['start_datetime']}' AND '{$_SESSION['end_datetime']}'";
  }
  if($_SESSION['customer_id']){
    $searchConditionAry[] = "`customer_internal_id` LIKE '%{$_SESSION['customer_id']}%'";
  }
  if($_SESSION['customer_name']){
    $searchConditionAry[] = "`name` LIKE '%{$_SESSION['customer_name']}%'";
  }
/*
  if($_SESSION['rating']){
    $searchConditionAry[] = "`rating` = '{$_SESSION['rating']}'";
  }
*/
  if($_SESSION['tel']){
    $searchConditionAry[] = "`tel` LIKE '%{$_SESSION['tel']}%'";
  }
  if($_SESSION['address']){
    $searchConditionAry[] = "`address` LIKE '%{$_SESSION['address']}%'";
  }
  if($_SESSION['girl']){
    $searchConditionAry[] = "`girl` LIKE '%{$_SESSION['girl']}%'";
  }
  if($_SESSION['nominate']){
    $searchConditionAry[] = "`nominate` LIKE '%{$_SESSION['nominate']}%'";
  }
  $searchCondition = implode(' AND ',$searchConditionAry);

  $sql = "SELECT `id` FROM `usage_data` WHERE `is_delete` = 0 AND {$searchCondition} ORDER BY `p_date` DESC";
  $allCount = dbCount($sql);
  
  $perCount = 300;
  $pagerData = getPager($allCount,$perCount,$_GET['p']);
  $start = ($pagerData['current_page'] - 1) * $perCount;
  
  $sql = "SELECT * FROM `usage_data` WHERE `is_delete` = 0 AND {$searchCondition} ORDER BY `p_date` DESC LIMIT {$start},{$perCount}";
  $usageDataAry = getRecord($sql);

  $sql = "SELECT * FROM `girls` WHERE `shop_id` = '{$_SESSION['id']}'";
  $girlDataAry = getRecord($sql,1);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
	<title>ドM会員管理システム</title>
</head>
<style>
	#wrapper {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>
	}
</style>
<body id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
  <input type="hidden" id="shopid" value="<?php echo $_SESSION['id'] ?>" >
  <input type="hidden" id="mode" value="usage" >
  <div id="wrapper">
<?php include_once(DOCUMENT_ROOT.'/include/navi.php') ?>
    <main>
      <article id="usageTableWrapper" class="tableWrapper" >
	<div class="headinfo">
        	<div class="title">利用履歴検索</div>
		<div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
	</div>
        <div id="searchBox">
          <form action="/usage.php" method="post" autocomplete="off" >
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
              <li>会員ID：　<input type="text" value="<?php echo $_SESSION['customer_id'] ?>" name="customer_id" id="customerId" class="mr10" ></li>
              <li>会員名：　<input type="text" value="<?php echo $_SESSION['customer_name'] ?>" name="customer_name" id="customerName" class="mr10" ></li>
              <!--
              <li>評価：　
								<select name="rating" id="rating" style="background:<?php if($_SESSION['rating']=='注意'){ echo '#ffc294';} elseif($_SESSION['rating']=='出禁'){ echo '#ffb5b5';} elseif($_SESSION['rating']=='優良'){ echo '#fff9cf';} ?> !important" >
									<option style="background: #fff !important" value="一般" <?php if($_SESSION['rating']=='一般'){ echo 'selected';} ?> >一般</option>
									<option style="background: #fff !important" value="優良" <?php if($_SESSION['rating']=='優良'){ echo 'selected';} ?> >優良</option>
									<option style="background: #fff !important" value="注意" <?php if($_SESSION['rating']=='注意'){ echo 'selected';} ?> >注意</option>
									<option style="background: #fff !important" value="出禁" <?php if($_SESSION['rating']=='出禁'){ echo 'selected';} ?> >出禁</option>
									<option style="background: #fff !important" value="スタッフ" <?php if($_SESSION['rating']=='スタッフ'){ echo 'selected';} ?> >スタッフ</option>
									<option style="background: #fff !important" value="業者" <?php if($_SESSION['rating']=='業者'){ echo 'selected';} ?> >業者</option>
									<option style="background: #fff !important" value="その他" <?php if($_SESSION['rating']=='その他'){ echo 'selected';} ?> >その他</option>
								</select> 
              </li>
              -->
              <li>電話番号：　<input type="text" value="<?php echo $_SESSION['tel'] ?>" name="tel" id="tel" class="mr10" ></li>
              <li>住所：　<input type="text" value="<?php echo $_SESSION['address'] ?>" name="address" id="address" class="mr10" ></li>
            </ul>
            <ul>
              <li>キャスト名：　
								<select name="girl" class="girlSelector colEdit mr10" data-col="<?php echo strEncrypt('girl') ?>" id="">
                  <option value="">未選択</option>
									<option value="フリー" <?php if($_SESSION['girl'] == 'フリー'){echo 'selected';} ?> >フリー</option>
<?php
foreach((array)$girlDataAry AS $girlData){
?>
									<option value="<?php echo $girlData['name'] ?>" <?php if($_SESSION['girl'] == $girlData['name']){echo 'selected';} ?> ><?php echo $girlData['name'] ?></option>
<?php } ?>
								</select>

              </li>
              <li>　指名タイプ：　
                <select name="nominate" class="nominate colEdit" data-col="<?php echo strEncrypt('nominate') ?>" id="">
                  <option value="">未選択</option>
									<option value="フリー" <?php if($_SESSION['nominate'] == 'フリー'){echo 'selected';} ?> >フリー</option>
									<option value="パネル指名" <?php if($_SESSION['nominate'] == 'パネル指名'){echo 'selected';} ?> >パネル指名</option>
									<option value="本指名" <?php if($_SESSION['nominate'] == '本指名'){echo 'selected';} ?> >本指名</option>
								</select>
              </li>
            </ul>
            <input type="submit" value="検　索" class="searchBtn" >
            <input type="submit" value="リセット" name="reset" class="resetBtn" >
          </form>
        </div>
        <span class="btn dataDelete" data-id="usageList" data-mode="usage" >チェックした履歴を削除</span>
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
        <table id="usageList" >
          <thead>
            <tr>
              <th><input type="checkbox" class="checkAll" data-id="usageList" ></th>
              <th>利用日時</th>
              <th>会員ID</th>
              <th>会員名</th>
              <th>電話番号</th>
              <th>キャスト名</th>
              <th>指名種別</th>
              <th>分</th>
              <th>金額</th>
              <th>住所</th>
              <!-- <th>備考</th> -->
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
<?php
foreach((array)$usageDataAry AS $usageData){
  if($di < $pagerData['max_data_num'] && $di >= $pagerData['min_data_num']){
  $sql = "SELECT * FROM `customer_data` WHERE `id` = '{$usageData['customer_id']}'";
  $customerData = get1Record($sql);
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
            <tr <?php echo $statCol ?> >
              <td><input type="checkbox" value="<?php echo $usageData['id'] ?>" ></td>
              <td><?php echo date("Y-m-d H:i",strtotime($usageData['p_date'])) ?></td>
              <td><?php echo $usageData['customer_internal_id'] ?></td>
              <td><?php echo $usageData['name'] ?></td>
              <td><?php echo telSeparator($usageData['tel']) ?></td>
              <td><?php echo $usageData['girl'] ?></td>
              <td><?php echo $usageData['nominate'] ?></td>
              <td><?php echo $usageData['p_time'] ?></td>
              <td><?php echo number_format($usageData['price']) ?></td>
              <td><?php echo $usageData['address'] ?></td>
              <!-- <td><?php echo $usageData['remark'] ?></td> -->
              <td>
                <div class="btn edit usageEdit" data-cid="<?php echo $usageData['customer_id'] ?>" data-uid="<?php echo $usageData['id'] ?>" >編集</div>
              </td>
            </tr>
<?php }$di++;} ?>
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