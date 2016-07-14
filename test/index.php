<?php
require_once("test.php");
\Garan24\Garan24::$DB["host"]="151.248.117.239";
$bb = new \Garan24\Delivery\BoxBerry\BoxBerry();
//echo $bb->ListCities();
echo $bb->ZipCheck(["Zip"=>"127221"]);
?>
