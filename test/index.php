<?php
require_once("test.php");

\Garan24\Garan24::$_log_dir = "./";
\Garan24\Garan24::$DB["host"]="151.248.117.239";
\Garan24\Garan24::$DB["prefix"] = "xr_";
\Garan24\Garan24::$DB["schema"] = "gauzymall";
\Garan24\Garan24::$DB["user"] = "gauzymall";
\Garan24\Garan24::$DB["pass"] = "D6a8O2e1";
$db = new \Garan24\Store\DBConnector();
$order = "9512";
//$order = 9763;
$dd = $db->select("
select
	d.internal_order_id as 'client_orderid',
    'Post payment for order #".$order."' as 'order_desc',
    cr.card_ref_id as 'cardrefid',
    d.amount+d.service_fee+d.shipping_cost as 'amount',
    'RUB' as 'currency',
    '213.87.145.97' as 'ipaddress',
    'gauzymall.com' as 'redirect_url'
from garan24_cardrefs cr
	join garan24_user_cardref ucr on ucr.card_ref_id = cr.id
    join userinfo u on u.user_id = ucr.user_id
    join deals d on d.id = ucr.deal_id
where d.internal_order_id =".$order);
//$obj = ["client_orderid" => "5653","order_desc" => "Post payment for order","cardrefid" => "4009091","amount" => "3405.37","currency" => "RUB" ,"ipaddress" => "213.87.145.97","redirect_url" => "gauzymall.com"];
//print_r($dd);exit;
$crdData = array_merge($ariuspay["akbars"]["RebillRequest"],["data"=>$dd]);
$crdData["data"]["login"] = $crdData["merchant_login"];

//print_r($obj);
//print_r($dd);exit;
$request = new \Garan24\Gateway\Ariuspay\RebillRequest($crdData);
$connector = new \Garan24\Gateway\Ariuspay\Connector();
$connector->setRequest($request);
$connector->call();
$response =  $connector->getResponse();

$field = "paynet-order-id";
$stat = array_merge($ariuspay["akbars"]["StatusRequest"],["data"=> [
    "client_orderid"=>$dd["client_orderid"],
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
if(preg_replace("/[\r\n\s]/m","",$response->status) == 'approved'){
	$db->insert("update deals set status=(select id from garan24_deal_statuses where status = 'payed') where internal_order_id = ".$order);
}
?>
