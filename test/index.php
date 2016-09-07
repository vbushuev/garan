<?php
require_once("test.php");

\Garan24\Garan24::$DB["host"]="151.248.117.239";

$deal = new \Garan24\Deal\Deal(["id"=>"5824"]);
$d2b = new \Garan24\Delivery\BoxBerry\Converter();
$bb = new \Garan24\Delivery\BoxBerry\BoxBerry();
//echo $deal;
//echo $deal->getCustomer();
$con = $d2b->convert($deal);
//echo json_encode($con,JSON_PRETTY_PRINT);
//$bbres = json_decode($bb->ParcelCreateForeign($con),true);
//$bbres = json_decode($bb->ParselCreate($con),true);
$bbres = json_decode($bb->ListCities(),true);
if(isset($bbres[0]["err"])) echo "Error: ".$bbres[0]["err"];
else {
    echo json_encode($bbres[0]["result"],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}
?>
