<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');

  $postData = allescape($_POST);
  $tbl = $postData['mode'].'_data';
  if($postData['mode'] == 'call'){
    $tbl = 'call_history';
  }
  echo $q = "UPDATE `{$tbl}` SET `is_delete` = 1 WHERE `id` = '{$postData['id']}'";
	DBQuery($q);