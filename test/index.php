<?php
require_once("test.php");

\Garan24\Garan24::$_log_dir = "./";
\Garan24\Garan24::$DB["host"]="151.248.117.239";
\Garan24\Garan24::$DB["prefix"] = "xr_";
\Garan24\Garan24::$DB["schema"] = "gauzymall";
\Garan24\Garan24::$DB["user"] = "gauzymall";
\Garan24\Garan24::$DB["pass"] = "D6a8O2e1";
$obj = [
    "client_orderid" => "6368",
    "order_desc" => "Post payment order test",
    "cardrefid" => "4037580",
    "amount" => "3800",
    "currency" => "RUB" ,
    //"enumerate_amounts","cvv2",
    "ipaddress" => "213.87.145.97",//$_SERVER['REMOTE_ADDR'],
    "redirect_url" => "home.bs2"
    //"control","redirect_url","server_callback_url"
];

$crdData = array_merge($ariuspay["akbars"]["RebillRequest"],["data"=>$obj]);
$crdData["data"]["login"] = $crdData["merchant_login"];
$request = new \Garan24\Gateway\Ariuspay\RebillRequest($crdData);
$connector = new \Garan24\Gateway\Ariuspay\Connector();
$connector->setRequest($request);
$connector->call();
$response =  $connector->getResponse();

$field = "paynet-order-id";
$stat = array_merge($ariuspay["akbars"]["StatusRequest"],["data"=> [
    "client_orderid"=>$obj["client_orderid"],
    "orderid" => $response->$field,
    "login" =>$crdData["merchant_login"]
]]);
$stat["endpoint"] = $crdData["endpoint"];
$status = new \Garan24\Gateway\Ariuspay\StatusRequest($stat);
$connector->setRequest($status);
$connector->call();
$response =  $connector->getResponse();
echo "current status [".$response->status."]";
while(preg_replace("/[\r\n\s]/m","",$response->status) == "processing"){
    sleep(5);
    $connector->call();
    $response =  $connector->getResponse();
}
echo "\n\tResponse status is[".preg_replace("/[\r\n\s]/m","",$response->status)."]\n";
?>
