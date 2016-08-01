<?php
require_once("test.php");

//\Garan24\Garan24::$DB["host"]="151.248.117.239";

//$shiping = new Garan24\Shipping\Shipping(["test"=>"test_value"]);
$shipping = new Garan24\Shipping\Shipping('{"test":"test_value"}');
var_dump($shipping);
$address = new Garan24\Shipping\Address('{"address_1":"myhome place"}');
//var_dump($address);
echo $shipping->CheckAddress($address);

?>
