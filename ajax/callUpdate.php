<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

  $sql = "SELECT `id` FROM `call_history` WHERE `is_delete` = 0 AND `shop_id` = '{$_SESSION['id']}' ORDER BY `time` DESC";
  $allCount = dbCount($sql);

  $perCount = $shopPerCountAry[$_SESSION['id']];
  $pagerData = getPager($allCount,$perCount,$_SESSION['top_p']);
  $start = ($pagerData['current_page'] - 1) * $perCount;

  $sql = "SELECT * FROM `call_history` WHERE `is_delete` = 0 AND `shop_id` = '{$_SESSION['id']}' ORDER BY `time` DESC LIMIT {$start},{$perCount}";
  $usageDataAry = getRecord($sql);

  $tableSequence = explode(',',$tableSequenceAry[$_SESSION['id']]);
  $tHeadAry = ['','<th style="width:50px !important;">No</th>','<th style="width:165px !important;">着信日時</th>','<th style="width:90px !important;">会員ID</th>','<th style="width:300px !important;">名前</th>','<th style="width:130px !important;">番号</th>','<th>住所</th>'];
?>
        <div class="headinfo">
          <div class="title">着信履歴<div class="btn customerEdit" id="customerAddBtn" >会員新規作成</div></div>
          <div class="loginName"><?php echo $_SESSION['shop_name'] ?>様</div>
        </div>
        <span class="btn dataDelete" data-id="latestList" data-mode="call" >チェックした履歴を削除</span>
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
        <table id="latestList" class="w100" >
          <thead>
            <tr>
              <th style="width:33px !important;"><input type="checkbox" class="checkAll" data-id="latestList" ></th>
              <?php foreach ((array)$tableSequence as $id) { echo $tHeadAry[$id];} ?>
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
  $start2 = $start + $i;
  $tBodyAry = [
  '',
  '<td style="width:50px !important;">'.$start2.'</td>',
  '<td style="width:165px !important; text-align:center;">'.date("Y-m-d H:i:s",strtotime($callHistoryData['time'])).'</td>',
  '<td style="width:90px !important; text-align:center;">'.$customerData['customer_id'].'</td>',
  '<td style="width:300px !important; text-align:left;">'.$customerData['name'].'</td>',
  '<td style="width:130px !important; text-align:center;">'.telSeparator($callHistoryData['num']).'</td>',
  '<td>'.$customerData['address'].'</td>'];
?>
            <tr data-customer-id="<?php echo $customerData['id'] ?>" data-customer-num="<?php echo $callHistoryData['num'] ?>" <?php echo $statCol ?> >
              <td><input type="checkbox" value="<?php echo $callHistoryData['id'] ?>" ></td>
              <?php foreach ((array)$tableSequence as $id) { echo $tBodyAry[$id];} ?>
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