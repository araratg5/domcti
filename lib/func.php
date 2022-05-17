<?php
session_start();

ini_set('opcache.enable', 0);
ini_set('opcache.enable_cli', 0);

error_reporting(E_ALL ^ E_NOTICE^ E_DEPRECATED);
ini_set('display_errors', 'On');
ini_set("log_errors", 1);
ini_set("error_log", "/log/error.log");
ini_set("allow_url_fopen",1);

/**************************************
  DB Setting_0(doemu-new)
****************************************/
define("SITE_DB_HOST_DOM",		'o4043-291.kagoya.net');
define("SITE_DB_DBNAME_DOM",	'search_dom');
define("SITE_DB_USER_DOM",		'doemu_mst');
define("SITE_DB_PASS_DOM",		'doemu81!');

define("SITE_DB_HOST_CTI",		'araratcti.cmj29l3jswws.ap-northeast-1.rds.amazonaws.com');
define("SITE_DB_DBNAME_CTI",	'dom_cti');
define("SITE_DB_USER_CTI",		'araratcti');
define("SITE_DB_PASS_CTI",		'arrtcti2022');

define("BUSTING_DATE",		'20220518');

define("SALT",			'EVW53pu3Gm');

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);

//mysqli_report(MYSQLI_REPORT_STRICT);

if($_SERVER['SCRIPT_NAME']!='/index.php' && $_SERVER['SCRIPT_NAME']!='/api/responseSender.php'){
  $sql ="SELECT * FROM `shops` WHERE `shop_id` = '{$_SESSION['id']}' AND `shop_pw` = '{$_SESSION['pass']}' ";
	$num = DBCount($sql,1);
	if ($num <= 0) {
    header("Location:". "/");
    exit;
  }
}

function dbOpen($mode=0){
// 接続失敗時、リトライを5回行う
for($i = 1 ; $i < 5 ; $i++ ){
  try{
    if($mode==0){
      $mysqli = new mysqli(SITE_DB_HOST_CTI, SITE_DB_USER_CTI, SITE_DB_PASS_CTI, SITE_DB_DBNAME_CTI);
    } elseif($mode==1){
      $mysqli = new mysqli(SITE_DB_HOST_DOM, SITE_DB_USER_DOM, SITE_DB_PASS_DOM, SITE_DB_DBNAME_DOM);//login,schedule
    }
    $mysqli->query("SET NAMES utf8mb4");

    // 接続に成功
    return $mysqli;
  }catch(Exception $e){
    sleep(3); // リトライのため3秒待機
  }
}
}

function dbQuery($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);
  return $mysqli->query($sql);
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

function updateData($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);
  $mysqli->query($sql);
  return $mysqli->affected_rows;
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

function deleteData($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);
  $mysqli->query($sql);
  return $mysqli->affected_rows;
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

function insData($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);
  $result = $mysqli->query($sql);
  return $mysqli->insert_id;
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

//SQLからレコード取得(array)
function getRecord($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);

  if ($result = $mysqli->query($sql)) {
    // 連想配列を取得
    while ($row = $result->fetch_assoc()) {
      $res[] = $row;
    }
    return $res;
  }
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

function getRecordWithAllCount($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);

  if ($result = $mysqli->query($sql)) {
    // 連想配列を取得
    while ($row = $result->fetch_assoc()) {
      $res[] = $row;
    }
    $num_query = $mysqli->query('SELECT FOUND_ROWS()');
    $result = [$res,$num_query->fetch_row()];    
    return $result;
  }
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

//SQLから一件レコード取得(array)
function get1Record($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);

  if ($result = $mysqli->query($sql)) {
    return $result->fetch_assoc();
  }
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

//クエリ条件でレコード数を返す関数
function dbCount($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);

  if ($result = $mysqli->query($sql)) {
    return $result->num_rows;
  }else{
    return -1;
  }
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}

function queryEscape($sql,$mode = 0){
try{
  $mysqli = dbOpen($mode);
  $res = $mysqli->real_escape_string($sql);
  return str_replace('`', '', $res);
}catch(Exception $e){
  echo 'exception->'.$e->getMessage();
}
}



function is_mobile()
{
$USER_agent = $_SERVER['HTTP_USER_AGENT']; // HTTP ヘッダからユーザー エージェントの文字列を取り出す

// ui：パターン修飾子で「u」は UTF-8 エンコードを意味し、「i」は大文字・小文字を区別しません。
  return preg_match('/iPhone|Android.+Mobile/ui', $USER_agent) != 0; // 既知の判定用文字列を検索
}

function opetaionLog($shop_id, $comment, $type=0){
  if(is_array($comment)){
      ob_start();
      var_dump($comment);
      $msgStr = ob_get_clean();
  } else {
      $msgStr = str_replace('`','',$comment);
  }
  $now = date('Y-m-d H:i:s');
  $q = "INSERT INTO `operation_logs` (`shop_id`,`type`,`comment`,`update`)VALUES({$shop_id},{$type},'{$created_at}','{$now}')";
  dbQuery($q);
}

function debugLogger($shop_no, $site_id = '', $msg = ''){
  if(is_array($msg)){
      ob_start();
      var_dump($msg);
      $msgStr = ob_get_clean();
  } else {
      $msgStr = str_replace('`','',$msg);
  }
  $now = date('Y-m-d H:i:s');
  $q = "INSERT INTO `debugs` (`shop_no`,`site_id`,`msg`,`update`)VALUES('{$shop_no}','{$site_id}','{$msgStr}','{$now}')";
  dbQuery($q);
}

/* img系*/
function imgUpdate($files,$filePath) {
$extBase = explode('.', $files["name"]);
$ext = strtolower(array_pop($extBase));
$filePath .= '.'.$ext;
move_uploaded_file($files["tmp_name"],$filePath);
system("chmod 777 {$filePath}");
return $filePath;
}
function imgResized($filePath,$resizeFilePath,$resizeX,$resizeY=0){
list($width, $height) = getimagesize($filePath);
$extBase = explode('.', $filePath);
$ext = strtolower(array_pop($extBase));
if($ext=='gif' || $ext=='GIF'){
  if($width!=$resizeX && $height!=$resizeY){
    $image = new Imagick($filePath); // 画像のパス
    $image = $image->coalesceImages();
    do {
      $image->scaleImage($resizeX,$resizeY); // リサイズしたい幅と高さ
    } while ($image->nextImage());
    $image->writeImages($resizeFilePath, true);
    system("chmod 777 {$resizeFilePath}");
  } else {
    copy($filePath, $resizeFilePath);
    system("chmod 777 {$resizeFilePath}");
  }
} else {
  //resize
  $image = new Imagick($filePath);
  $image->thumbnailImage($resizeX, $resizeY);
  $image->writeImage($resizeFilePath);
  system("chmod 777 {$resizeFilePath}");
}

}
/*---------------------------------------------------------
セキュリティ系定義関数
---------------------------------------------------------*/

// SQLインジェクション対策(str)
function OptSql($str) {
$str = addslashes($str);
$str = str_replace(";","",$str);
$str = str_replace("%","\%",$str);
return $str;
}

//NULLかどうかをチェックする
function OptNULL($str){
if(empty($str)){ $str = "NULL";}else{ $str = "'".$str."'";}
  return $str;
}

/*****************************************
 * ランダムな文字列を生成する。
 * @param int $nLengthRequired 必要な文字列長。省略すると 8 文字
 * @return String ランダムな文字列
 ******************************************/
function randomString($length = 16) {
  $baseChars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz1234567890';
  $baseCharLength = strlen($baseChars);

  // 指定された数だけランダムな文字を取得する
  $output = '';
  for ($i = 0; $i < $length; $i++) {
      $output .= $baseChars[random_int(0, $baseCharLength - 1)];
  }

  return $output;
}

/*****************************************
 * 引数データに一気に指定するファンクションをかける
 * @param 処理する配列
 *  * @return 処理済み
 ******************************************/

function allescape($array) {
  foreach((array)$array as $key => $value) {
      if (is_array($value)) {
          $array[$key] = allescape($value);
      } else {
          $array[$key] = addslashes(str_replace('&amp;','&',htmlspecialchars($value,ENT_QUOTES)));
      }
  }
  return $array;
}

function aryEncrypt($array) {
  foreach((array)$array as $key => $value) {
      if (is_array($value)) {
          $array[$key] = allescape($value);
      } else {
          $array[$key] = @openssl_encrypt($value, 'AES-128-ECB', SALT);
      }
  }
  return $array;
}

function strEncrypt($v) {
          $r = @openssl_encrypt($v, 'AES-128-ECB', SALT);
  return $r;
}

function aryDecrypt($array) {
  foreach((array)$array as $key => $value) {
      if (is_array($value)) {
          $array[$key] = allescape($value);
      } else {
          $array[$key] = @openssl_decrypt($value, 'AES-128-ECB', SALT);
      }
  }
  return $array;
}

function strDecrypt($v) {
          $r = @openssl_decrypt($v, 'AES-128-ECB', SALT);
  return $r;
}
/*****************************************
 * 文字列からタグなどを除去
 * @param 処理する文字列
 *  * @return 文字列
 ******************************************/
function sanitizeStr($str){

$str = stripslashes($str);
$str = strip_tags($str);
$sanitizeStr = htmlspecialchars($str, ENT_QUOTES);

return $sanitizeStr;
}


/*****************************************
 * 文字列からタグとスペースを除去
 * @param 処理する文字列
 *  * @return 文字列
 ******************************************/
function sanitizeTrimStr($str){

$str = stripslashes($str);
$str = strip_tags($str);
$str = trim($str);
$sanitizeStr = htmlspecialchars($str, ENT_QUOTES);

return $sanitizeStr;
}


/*****************************************
 * 文字列からタグなどを除去（顔文字対応）
 * @param 処理する文字列
 *  * @return 文字列
 ******************************************/
function sanitizeKaoStr($str){

$str = str_replace('<', '＜', $str);
$str = str_replace('>', '＞', $str);
$str = stripslashes($str);
$str = strip_tags($str);
$sanitizeStr = htmlspecialchars($str, ENT_QUOTES);

return $sanitizeStr;
}

/*****************************************
 * 半角英字チェック
 * @param str
 * @return String 結果
 ******************************************/
function AlphabetCheck($strChk){

if(preg_match("/^[a-zA-Z]+$/",$strChk)){
  //半角英字の場合、True
  return true;
}else{
  //半角英字以外の場合、False
  return false;
}
}

/*****************************************
 * 半角数字チェック
 * @param str
 * @return String 結果
 ******************************************/
function NumericCheck($strChk){

$val = substr($strChk, 0, 1);
if($val == "-"){
  $strChk = substr($strChk, 1);
}

if(preg_match("/^[0-9]+$/",$strChk)){
  //半角数字の場合、True
  return true;
}else{
  //半角数字以外の場合、False
  return false;
}
}


/*****************************************
 * 半角英数字チェック
 * @param str
 * @return String 結果
 ******************************************/
function AlphaNumericCheck($strChk){

if(preg_match("/^[a-zA-Z0-9]+$/",$strChk)){
  //半角英数字の場合、True
  return true;
}else{
  //半角英数字以外の場合、False
  return false;
}
}


/*****************************************
 * 桁数チェック
 * @param str
 * @return String 結果
 ******************************************/
function LengthCheck($strChk,$intMax){
$strLen = strlen($strChk);

if($strLen <= $intMax){
  //最大入力可能バイト数以内の場合、True
  return true;
}else{
  //最大入力可能バイト数を超えている場合、False
  return false;
}
}


/*****************************************
 * メールアドレスチェック
 * @param str
 * @return String 結果
 ******************************************/
function MailAddressCheck($strChk){

if(preg_match("/^[a-zA-Z0-9_\.\-]+?@[A-Za-z0-9_\.\-]+$/",$strChk)){
  //正式なメールアドレスの場合、True
  return true;
}else{
  //不正なメールアドレスの場合、False
  return false;
}
}


/*****************************************
 * ＵＲＬチェック
 * @param str
 * @return String 結果
 ******************************************/
function UrlCheck($strChk){

if(preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/",$strChk)){
  //正式なＵＲＬの場合、True
  return true;
}else{
  //不正なＵＲＬの場合、False
  return false;
}
}


/*****************************************
 * 電話番号チェック
 * @param str
 * @return String 結果
 ******************************************/
function TelCheck($strChk){

$tel = ereg_replace( '-', '', $strChk);
if(preg_match("/^0[1-9]0[0-9]{8}$/",$tel)){
  //正式な電話番号の場合、True
  return true;
}else{
  //不正な電話番号の場合、False
  return false;
}
}


/*****************************************
 * 携帯アドレスチェック
 * @param str
 * @return String 結果
 ******************************************/
function MobileAddressCheck($strChk){
require("lib_resource.php");

$pos = strpos($strChk, "@");
$domain = substr($strChk, $pos);

foreach((array)$CARRIER as $key => $value){
  if($domain == $value){
    //携帯アドレスの場合、true
    return true;
  }
}
//携帯アドレスじゃない場合、false
return false;
}

/*****************************************
 * 日付チェック
 * @param str
 * @return boolean 結果
 ******************************************/
function validateDateYMD($str){
// 数値以外で分割
$ary = preg_split('/[^0-9]/', $str);

// $str が YYYYMMDD形式
if(strlen($ary[0]) == 8){
  return validateDate($ary[0], "Ymd");
}

// $str が YYYY-MM-DD形式(区切り文字は問わない)
if(isset($ary[1]) && isset($ary[2])){
  return checkdate($ary[1], $ary[2], $ary[0]);
}

return false;
}

/*****************************************
 * 日付チェック（フォーマット指定）
 * @param str
 * @return boolean 結果
 ******************************************/
function validateDate($date, $format = 'Y-m-d H:i:s'){
$d = DateTime::createFromFormat($format, $date);
return $d && $d->format($format) == $date;
}

function uaCheck(){
  //UA判定
  $ua = $_SERVER['HTTP_USER_AGENT'];
  if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) {
      // スマートフォンからアクセスされた場合
      $deviceMode = 'sp';

  } elseif ((strpos($ua, 'Android') !== false) || (strpos($ua, 'iPad') !== false)) {
      // タブレットからアクセスされた場合
      $deviceMode = 'pc';

  } elseif ((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'KDDI') !== false) || (strpos($ua, 'SoftBank') !== false) || (strpos($ua, 'Vodafone') !== false) || (strpos($ua, 'J-PHONE') !== false)) {
      // 携帯からアクセスされた場合
      $deviceMode = 'sp';

  } else {
      // その他（PC）からアクセスされた場合
      $deviceMode = 'pc';
  }
  return $deviceMode;
}
function urlShortner($longUrl){

$api_url = "https://www.googleapis.com/urlshortener/v1/url";
$api_key = "AIzaSyAw4ZgQTE0YBbxAhJpbC9xZtxQpXbdpERI";

$curl = curl_init();
$curl_params = array(
    CURLOPT_URL => $api_url . "?" .
        http_build_query( array( "key" => $api_key ) ),
    CURLOPT_HTTPHEADER => array( "Content-Type: application/json" ),
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => jsonEncodeOrig( array( "longUrl" => $longUrl ) ),
    CURLOPT_RETURNTRANSFER => true
);
curl_setopt_array( $curl, $curl_params );
$result = @json_decode( curl_exec( $curl ) );
$shortUrl = $result->id;
return $shortUrl;
}
function http_post ($url, $data)
{
  $data_url = http_build_query ($data);
  $data_len = strlen ($data_url);

  return array (
        'content'=>  @file_get_contents (
            $url,
            false,
            stream_context_create (
              array ('http' =>
                  array (
                      'method'=>'POST',
                      'header'=>"Content-Type: application/x-www-form-urlencoded\r\nContent-Length: $data_len\r\n",
                      'content'=>$data_url)
                  )
              )
            ),
      'headers'=> $http_response_header
  );
}

function base64ToJpeg($base64_string) {
  $data = explode(';', $base64_string);
  $dataa = explode(',', $base64_string);
  $part = explode("/", $data[0]);
  if (empty($part))
      return false;
  $file = md5(uniqid(rand(), true)) . ".{$part[1]}";
  if (!is_dir(realpath(__DIR__) . "/tmp/"))
      mkdir(realpath(__DIR__) . "/tmp/");

  $ifp = fopen(realpath(__DIR__) . "/tmp/{$file}", 'wb');
  fwrite($ifp, base64_decode($dataa[1]));
  fclose($ifp);
  return $file;
}
function delete_empty_array($array) {
  $tmp;
  foreach((array)$array as $item) {
      if( is_array($item) ) {
            $tmp[] = delete_empty_array($item);
      } else {
            if( ! empty($item)) {
                $tmp[] = $item;
            }
      }
  }
  return $tmp;
}
  function getExt($mimeSubtype){
      switch ($mimeSubtype) {
          case 'jpeg':
              $ext = 'jpg';
              break;
          case 'svg+xml':
              $ext = 'svg';
              break;
          case 'quicktime':
              $ext = 'mov';
              break;
          case 'x-ms-wmv':
              $ext = 'wmv';
              break;
          case 'x-msvideo':
              $ext = 'avi';
              break;
          default:
              $ext = $mimeSubtype;
              break;
      }
      return $ext;
  }
function statChangeByVal($setValue,$getValue,$stat){
  if($setValue == $getValue){
    echo $stat;
  }
}
function statChangeByAry($setValue,$getValueAry,$stat){
  if(@in_array($setValue,$getValueAry,true)===true){
    echo $stat;
  }
}

$_ = function($s){return $s;};

const HASH_ALGO = 'sha256';

function generate()
{
    if (session_status() === PHP_SESSION_NONE) {
        throw new \BadMethodCallException('Session is not active.');
    }
    return hash(HASH_ALGO, session_id());
}

function validate($token, $throw = false)
{
    $success = generate() === $token;
    if (!$success && $throw) {
        throw new \RuntimeException('CSRF validation failed.', 400);
    }
    return $success;
}

$alphabetAry = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','?'];

function getCustomerId($id){
  global $alphabetAry;
  $numeric = substr($id, -3);
  $numeric = sprintf('%03d', $numeric);

  $alpha1 = ($id / 1000) % 26;
  $alpha2 = ($id / 26000) % 26;
  $alpha3 = ($id / 676000) % 26;
  $customerId = $alphabetAry[$alpha3].$alphabetAry[$alpha2].$alphabetAry[$alpha1].$numeric;
  return $customerId;
}

function minuteToHourMin($min) {//分を時間換算

$hours = floor(($min / 60) % 60);
$minutes = $min % 60;
//$seconds = $min % 60;

//$hms = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
$hm = sprintf("%02d:%02d", $hours, $minutes);

return $hm;

}

function getpager($allNum, $itemperCount, $currentPage = 1){
	if(!$currentPage){
		$currentPage = 1;
	}
	$maxPage = ceil($allNum/$itemperCount);
	if($currentPage > 1){
		$res['prev_page'] = $currentPage - 1;
	}
	if($currentPage != $maxPage){
		$res['next_page'] = $currentPage + 1;
	}

	$res['start_page'] = $currentPage - 2;
	$res['end_page'] = $res['start_page'] + 4;
	if($res['end_page'] > $maxPage){
		$res['end_page'] = $maxPage;
		$res['start_page'] = $res['end_page'] - 4;
	}
	if($res['start_page'] <= 0){
		$res['start_page'] = 1;
		$res['end_page'] = 5;
		if($maxPage<$res['end_page']){
			$res['end_page'] = $maxPage;
		}
	}

	parse_str($_SERVER['QUERY_STRING'], $uriQuery);
	for($i=$res['start_page'];$i<=$res['end_page'];$i++){
	$uriQuery['p'] = $i;
	$res['pager_uri_array'][$i] = '?'.http_build_query($uriQuery);
	}
	$res['current_page'] = $currentPage;
	$res['min_data_num'] = ($res['current_page'] - 1) * $itemperCount;
	$res['max_data_num'] = $res['min_data_num'] + $itemperCount;
/*
	$res['start_page'] = 0;
	$res['end_page'] = 0;
	$res['prev_page'] = 0;
	$res['next_page'] = 0;
	$res['pager_uri_array'] = 0;
*/
	if(explode('/', $_SERVER['REQUEST_URI'])[1]=='m'){
		mb_convert_variables('SJIS-win','UTF-8',$res['data']);
	}
	return $res;
}

$sql ="SELECT * FROM `shops` WHERE `is_disabled` = 0";
$result = getRecord($sql,1);
foreach($result AS $shopNameData){
  $shopNameAry[$shopNameData['shop_id']] = $shopNameData['name_ja'];
}
  $shopNameAry['testshop'] = 'テスト店舗';

$weekJaAry = ['日','月','火','水','木','金','土'];