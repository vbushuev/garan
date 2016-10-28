<?php
date_default_timezone_set('Europe/Moscow');
function __autoload($className){
	$vendorDir = "../vendor";
	$classmap = [
	    'WC_API_Client' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/class-wc-api-client.php',
	    'WC_API_Client_Authentication' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/class-wc-api-client-authentication.php',
	    'WC_API_Client_Exception' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/exceptions/class-wc-api-client-exception.php',
	    'WC_API_Client_HTTP_Exception' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/exceptions/class-wc-api-client-http-exception.php',
	    'WC_API_Client_HTTP_Request' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/class-wc-api-client-http-request.php',
	    'WC_API_Client_Resource' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/abstract-wc-api-client-resource.php',
	    'WC_API_Client_Resource_Coupons' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-coupons.php',
	    'WC_API_Client_Resource_Custom' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-custom.php',
	    'WC_API_Client_Resource_Customers' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-customers.php',
	    'WC_API_Client_Resource_Index' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-index.php',
	    'WC_API_Client_Resource_Order_Notes' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-order-notes.php',
	    'WC_API_Client_Resource_Order_Refunds' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-order-refunds.php',
	    'WC_API_Client_Resource_Orders' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-orders.php',
	    'WC_API_Client_Resource_Products' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-products.php',
	    'WC_API_Client_Resource_Reports' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-reports.php',
	    'WC_API_Client_Resource_Webhooks' => $vendorDir . '/woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-webhooks.php',
	];
	if(isset($classmap[$className])){
		require_once $classmap[$className];
		return true;
	}
	$file = str_replace('\\','/',$className);
	require_once '../src/'.$file.'.php';
	return true;
}
$timeout = 1;
function makeTimeout($timeout){
    switch ($timeout) {
        case 1:
            $timeout = 2;
            break;
        case 2:
            $timeout = 3;
            break;
        case 3:
            $timeout = 5;
            break;
        default:
            $timeout = 15;
            break;
    }
    sleep($timeout);
    return $timeout;
}
$ariuspay = ["akbars" =>[
    "SaleRequest" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "2879",
        "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
        "merchant_login" => "garan24"
    ],
    "RebillRequest" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "3058",
        "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
        "merchant_login" => "garan24"
    ],
    "StatusRequest" => [
        "url" => "https://gate.payneteasy.com/paynet/api/v2/",
        "endpoint" => "3058",
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
?>
