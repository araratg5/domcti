<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
  $sql = "SELECT * FROM `call_history` WHERE `shop_id` = '{$_SESSION['id']}' ORDER BY `time` DESC LIMIT 300";
  $res = getRecord($sql);
?>
        <div class="title">着信履歴（最新300件）</div>
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