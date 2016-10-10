<?php
require_once("test.php");

\Garan24\Garan24::$_log_dir = "../logs";
\Garan24\Garan24::$DB["host"]="151.248.117.239";
\Garan24\Garan24::$DB["prefix"] = "xr_";
\Garan24\Garan24::$DB["schema"] = "gauzymall";
\Garan24\Garan24::$DB["user"] = "gauzymall";
\Garan24\Garan24::$DB["pass"] = "D6a8O2e1";
$obj = json_decode(json_encode([
    "client_orderid" => "5653",
    "orderid" => "59595841"
]));
$ariuspay = ["akbars" =>[
    "SaleRequest" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "2879",
        "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
        "merchant_login" => "garan24"
    ],
    "CaptureRequest" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "2879",
        "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
        "merchant_login" => "garan24"
    ],
    "PreauthRequest" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "3028",
        "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
        "merchant_login" => "garan24"
    ],
    "CreateCardRef_RIB" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "3028",
        "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
        "merchant_login" => "garan24"
    ],
    "CreateCardRef" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "2879",
        "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
        "merchant_login" => "garan24"
    ]
]];
$crdData = array_merge($ariuspay["akbars"]["CreateCardRef"],["data"=>[
    'client_orderid' => $obj->client_orderid,
    'orderid' => $obj->orderid
]]);
$request = new \Garan24\Gateway\Ariuspay\CreateCardRefRequest($crdData);
$connector = new \Garan24\Gateway\Ariuspay\Connector();
$connector->setRequest($request);
$connector->call();
$key = "card-ref-id";
$cardref =  $connector->getResponse()->$key;
$deal = new \Garan24\Deal\Deal(["id"=>$obj->client_orderid,"data"=>["card-ref-id"=>$cardref]]);
\Garan24\Garan24::debug("Order ".$obj->client_orderid." set customer cardref ".$cardref);
print("Order ".$obj->client_orderid." set customer cardref ".$cardref);
?>
