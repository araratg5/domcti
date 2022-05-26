<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  if ($_SERVER["REQUEST_METHOD"]=="POST") {
    if(!$_REQUEST['reset']){
      $_SESSION['customer_id'] = $_REQUEST['customer_id'];
      $_SESSION['customer_name'] = $_REQUEST['customer_name'];
      $_SESSION['rating'] = $_REQUEST['rating'];
      $_SESSION['tel'] = $_REQUEST['tel'];
      $_SESSION['address'] = $_REQUEST['address'];
      $_SESSION['shop_id'] = $_REQUEST['shop_id'];
    } else {
      $_SESSION['customer_id'] = '';
      $_SESSION['customer_name'] = '';
      $_SESSION['rating'] = '';
      $_SESSION['tel'] = '';
      $_SESSION['address'] = '';
      $_SESSION['shop_id'] = '';
      header('Location: /customer.php');
    }
  }
  $_SESSION['customer_p'] = $_REQUEST['p'];

  $searchConditionAry[] = 1;
  if($_SESSION['customer_id']){
    $searchConditionAry[] = "`customer_id` LIKE '%{$_SESSION['customer_id']}%'";
  }
  if($_SESSION['customer_name']){
    $searchConditionAry[] = "`name` LIKE '%{$_SESSION['customer_name']}%'";
  }
  if($_SESSION['rating']){
    $searchConditionAry[] = "`rating` = '{$_SESSION['rating']}'";
  }
  if($_SESSION['tel']){
    $searchConditionAry[] = "((`tel1` LIKE '%{$_SESSION['tel']}%') OR (`tel2` LIKE '%{$_SESSION['tel']}%') OR (`tel3` LIKE '%{$_SESSION['tel']}%'))";
  }
  if($_SESSION['address']){
    $searchConditionAry[] = "`address` LIKE '%{$_SESSION['address']}%'";
  }
  if($_SESSION['shop_id']){
    $searchConditionAry[] = "`shop_id` LIKE '%{$_SESSION['shop_id']}%'";
  }
  $searchCondition = implode(' AND ',$searchConditionAry);


  $sql = "SELECT `id` FROM `customer_data` WHERE {$searchCondition} ORDER BY `id` DESC";
  $allCount = dbCount($sql);
  
  $perCount = 300;
  $pagerData = getPager($allCount,$perCount,$_GET['p']);
  $start = ($pagerData['current_page'] - 1) * $perCount;
  
  $sql = "SELECT * FROM `customer_data` WHERE {$searchCondition} ORDER BY `id` DESC LIMIT {$start},{$perCount}";
  $customerDataAry = getRecord($sql);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="favicon.ico" size="16x16" type="image/png">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css?<?php echo BUSTING_DATE ?>" media="all">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<title>ドM会員管理システム</title>
	<style>
	.select2-results , .select2-results__options {
	    max-height: 500px !important;
	    height: 500px !important;
	}
	</style>
</head>
<style>
	#wrapper {
		background: <?php echo $shopBgColorAry[$_SESSION['id']] ?>
	}
</style>
<body id="<?php echo str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME']) ?>" >
  <input type="hidden" id="shopid" value="<?php echo $_SESSION['id'] ?>" >
  <input type="hidden" id="mode" value="customer" >
  <div id="wrapper">
<?php include_once(DOCUMENT_ROOT.'/include/navi.php') ?>
    <main>
      <article id="customerTableWrapper" class="tableWrapper" >
	<div class="headinfo">
        	<div class="title">会員情報検索<div class="btn customerEdit" id="customerAddBtn" >新規作成</div></div>
		<div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
	</div>
        <div id="searchBox">
          <form action="/customer.php" method="post" autocomplete="off" >
            <ul>
              <li>会員ID：　<input type="text" value="<?php echo $_SESSION['customer_id'] ?>" name="customer_id" id="customerId" class="mr10" ></li>
              <li>会員名：　<input type="text" value="<?php echo $_SESSION['customer_name'] ?>" name="customer_name" id="customerName" class="mr10" ></li>
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
              <li>電話番号：　<input type="text" value="<?php echo $_SESSION['tel'] ?>" name="tel" id="tel" class="mr10" ></li>
              <li>住所：　<input type="text" value="<?php echo $_SESSION['address'] ?>" name="address" id="address" class="mr10" ></li>
              <li>登録店舗：　
<select name="shop_id" class="shopSelector" id="select2" style="min-width:360px">
  <option value="">未選択</option>
<?php 
foreach ($shopNameAry as $key => $value) { ?>
  <option value="<?php echo $key ?>" <?php if($key == $_SESSION['shop_id']){echo 'selected';} ?> ><?php echo $value ?></option>
<?php }?>
</select>  
              </li>
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
        <table id="customerList" >
          <thead>
            <tr>
              <th>会員ID</th>
              <th>登録店舗</th>
              <th>会員名</th>
              <th>評価</th>
              <th>電話番号</th>
              <th>住所</th>
              <!-- <th>備考</th> -->
              <th></th>
            </tr>
          </thead>
          <tbody>
<?php
foreach((array)$customerDataAry AS $customerData){
  $customerData['tel'] = $customerData['tel1'];
  if(!$customerData['tel']){
    $customerData['tel'] = $customerData['tel2'];
  }
  if(!$customerData['tel']){
    $customerData['tel'] = $customerData['tel3'];
  }
  if(!$customerData['tel']){
    $customerData['tel'] = '番号未登録';
  }
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
              <td><?php echo $customerData['customer_id'] ?></td>
              <td><?php echo $customerData['usage_shopname'] ?></td>
              <td><?php echo $customerData['name'] ?></td>
              <td><?php echo $customerData['rating'] ?></td>
              <td><?php echo telSeparator($customerData['tel']) ?></td>
              <td><?php echo $customerData['address'] ?></td>
              <!-- <td><?php echo $customerData['remark'] ?></td> -->
              <td><div class="btn edit customerEdit" data-customer-id="<?php echo $customerData['id'] ?>" data-customer-num="<?php echo $customerData['tel'] ?>" >編集</div></td>
            </tr>
<?php } ?>
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
<script>
$('#select2').select2({
    width: 'resolve'
})
</script>
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