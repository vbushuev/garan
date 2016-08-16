<?php
require_once("test.php");

\Garan24\Garan24::$DB["host"]="151.248.117.239";

$data = file_get_contents('data.json');
$deal = new \Garan24\Deal\Deal();
$deal->byJson($data);
$resp = $deal->sync();
echo $resp;
?>
