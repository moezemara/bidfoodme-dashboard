<?php
//error_reporting(0);
include('../config.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$url = $config['API_LOGIN'];
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/x-www-form-urlencoded',
  'Accept: */*',
  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36'
));
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_POST));
curl_setopt($curl, CURLOPT_TIMEOUT, 25);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $results = curl_exec($curl);
  curl_close($curl);

  if (!$results){
    $response = array('success' => 0, 'message' => 'Connection time out');
    return $response;
  }

  $json_array = json_decode($results);

  if ($json_array->success){
    session_start();
    $_SESSION['Authorization']='Bearer '.$json_array->message;
    echo json_encode(array('success' => 1));
  }else{
    echo json_encode($json_array);
  }  
}

exit();

?>
