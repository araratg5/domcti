<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  
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

  $sql = "SELECT `ch`.`id` FROM (SELECT `id`,`num`,`time` FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}') AS `ch` INNER JOIN (SELECT `id`,`customer_id`,`name`,`remark`,`rating`,`address`,`tel1` FROM `customer_data`) AS `cd` ON `ch`.`num` = `cd`.`tel1` WHERE 1 {$searchCondition} ORDER BY `time` DESC";
  $allCount += dbCount($sql);
  $sql = "SELECT `ch`.`id` FROM (SELECT `id`,`num`,`time` FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}') AS `ch` INNER JOIN (SELECT `id`,`customer_id`,`name`,`remark`,`rating`,`address`,`tel2` FROM `customer_data`) AS `cd` ON `ch`.`num` = `cd`.`tel2` WHERE 1 {$searchCondition} ORDER BY `time` DESC";
  $allCount += dbCount($sql);
  $sql = "SELECT `ch`.`id` FROM (SELECT `id`,`num`,`time` FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}') AS `ch` INNER JOIN (SELECT `id`,`customer_id`,`name`,`remark`,`rating`,`address`,`tel3` FROM `customer_data`) AS `cd` ON `ch`.`num` = `cd`.`tel3` WHERE 1 {$searchCondition} ORDER BY `time` DESC";
  $allCount += dbCount($sql);

  if($allCount){  
    $perCount = 300;
    $pagerData = getPager($allCount,$perCount,$_SESSION['history_p']);
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

    @array_multisort($standard_key_array, $sort_order, $array);

    return $array;
}
?>
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