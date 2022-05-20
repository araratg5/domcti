<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/func.php');
$now = date("Y-m-d H:i:s");
$sql = "INSERT INTO `call_history` (`shop_id`,`sendcode`,`time`,`num`) VALUES ('{$_POST['shop_id']}','','{$now}','{$_POST['tel']}')";
    dbQuery($sql);
if($_POST['tel']){
  $curl = curl_init();
  $data = [
      $now => [
        'tel' => $_POST['tel'],
        'time' => $now,
      ]
  ];

  curl_setopt($curl, CURLOPT_URL, "https://araratcti-default-rtdb.firebaseio.com/{$_POST['shop_id']}.json");
  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // jsonデータを送信
  //curl_setopt($curl, CURLOPT_HTTPHEADER, $header); // リクエストにヘッダーを含める
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // curl_execの結果を文字列で返す

  $response = curl_exec($curl);

  $responseElem['header_size'] = curl_getinfo($curl, CURLINFO_HEADER_SIZE); 
  $responseElem['header'] = substr($response, 0, $header_size);
  $responseElem['body'] = substr($response, $header_size);
  $responseElem['result'] = json_decode($body, true); 

  var_dump($responseElem);

  curl_close($curl);
}