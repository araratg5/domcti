<?php 
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
$sql = "SELECT * FROM `usage_data` WHERE `is_delete` = 0 AND `customer_id` = '{$_POST['cid']}' ORDER BY `p_date` DESC";
$usageDataAry = getRecord($sql);
?>
			<div class="title" >利用履歴一覧</div>
			<span class="btn dataDelete" data-id="usageList" data-mode="usage" >チェックした履歴を削除</span>
			<table id="usageList" >
				<thead>
					<tr>
						<th><input type="checkbox" class="checkAll" data-id="usageList" ></th>
						<th>利用日時</th>
						<th>利用店舗</th>
						<th>キャスト名</th>
						<th>指名種別</th>
						<th>分</th>
						<th>金額</th>
						<th>備考</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
<?php
foreach((array)$usageDataAry AS $usageData){
?>
					<tr>
						<td><?php if($usageData['shop_id'] == $_SESSION['id']){ ?><input type="checkbox" value="<?php echo $usageData['id'] ?>" ><?php } ?></td>
						<td><?php echo date("Y-m-d H:i",strtotime($usageData['p_date'])) ?></td>
						<td><?php echo $usageData['usage_shopname'] ?></td>
						<td><?php echo $usageData['girl'] ?></td>
						<td><?php echo $usageData['nominate'] ?></td>
						<td><?php echo $usageData['p_time'] ?></td>
						<td><?php echo number_format($usageData['price']) ?></td>
						<td><?php echo $usageData['remark'] ?></td>
						<td>
							<?php if($usageData['shop_id'] == $_SESSION['id']){ ?>
							<div class="btn edit usageEdit" data-cid="<?php echo $usageData['customer_id'] ?>" data-uid="<?php echo $usageData['id'] ?>" >編集</div>
							<?php } else { ?>
							<div class="btn disabled" >編集・削除不可</div>
							<?php } ?>
						</td>
					</tr>
<?php } ?>
				</tbody>
			</table>