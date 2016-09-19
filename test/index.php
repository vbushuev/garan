<?php
require_once("test.php");

\Garan24\Garan24::$DB["host"]="151.248.117.239";

$deal = new \Garan24\Deal\Deal(["id"=>"5821"]);
$d2b = new \Garan24\Delivery\BoxBerry\Converter();
$bb = new \Garan24\Delivery\BoxBerry\BoxBerry();
//echo $deal;
//echo $deal->getCustomer();
$con = $d2b->convert($deal);
//echo json_encode($con,JSON_PRETTY_PRINT);
$bbres = json_decode($bb->ParcelCreateForeign($con),true);
//$bbres = json_decode($bb->ParselCreate($con),true);
//$bbres = json_decode($bb->ListCities(),true);
if(isset($bbres["err"])) echo "Error: ".$bbres["err"];
else {
    echo json_encode($bbres["result"],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}
?>
