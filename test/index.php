<?php
require_once("test.php");

\Garan24\Garan24::$DB["host"]="151.248.117.239";

$data = file_get_contents('data.json');


$curlOptions = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_VERBOSE => 1,
    CURLOPT_SSL_VERIFYPEER => false,
    //CURLOPT_FOLLOWLOCATION => true
];
$curl = curl_init("http://service.garan24.ru/checkout");
curl_setopt_array($curl, $curlOptions);
$response = curl_exec($curl);
echo $response;
exit;
$deal = new \Garan24\Deal\Deal();
$deal->byJson($data);
$resp = $deal->sync();
echo $resp;
?>
