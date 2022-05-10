<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

  $searchConditionAry[] = 1;
  if($_SESSION['customer_id']){
    $searchConditionAry[] = "`customer_id` LIKE '%{$_SESSION['customer_id']}%'";
  }
  if($_SESSION['customer_name']){
    $searchConditionAry[] = "`name` LIKE '%{$_SESSION['customer_name']}%'";
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
  $pagerData = getPager($allCount,$perCount,$_SESSION['customer_p']);
  $start = ($pagerData['current_page'] - 1) * $perCount;
  
  $sql = "SELECT * FROM `customer_data` WHERE {$searchCondition} ORDER BY `id` DESC LIMIT {$start},{$perCount}";
  $customerDataAry = getRecord($sql);
?>
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
              <li>電話番号：　<input type="text" value="<?php echo $_SESSION['tel'] ?>" name="tel" id="tel" class="mr10" ></li>
              <li>住所：　<input type="text" value="<?php echo $_SESSION['address'] ?>" name="address" id="address" class="mr10" ></li>
              <li>登録店舗：　
<select name="shop_id" class="shopSelector" id="select2" style="min-width:360px">
  <option value="">未選択</option>
<?php foreach ($shopNameAry as $key => $value) { ?>
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
              <td><?php echo $customerData['tel'] ?></td>
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