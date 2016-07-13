<?php
require_once("test.php");
$str = '{
	"x_secret": "cs_f8bcb640c24e4304f4e469d4a5f90b22b82986fb",
	"x_key": "ck_6c43b79838264f96fc89b26edea7a5792f207dbe",
	"version": "1.0",
	"response_url": "https://magnitolkin.ru/Handlers/Garan24CheckoutResponse/",
	"order": {
		"order_id": "6830",
		"order_url": "https://magnitolkin.ru/orders/1da04b7ce510/",
		"order_total": 21380.0,
		"order_currency": "RUR",
		"items": [{
			"product_id": "32423",
			"title": "b򮠪󱲨렠6 x 9\" Vibe BDQB69-V2",
			"description": null,
			"product_url": "https://magnitolkin.ru/catalogue/Akustika/coaxial/6_6_5_inch/Avtoakustika_6-6_5__Vibe_BDQB69-V2/",
			"product_img": "https://magnitolkin.ru/Handlers/CatalogueShopItemImage/?id=32423",
			"quantity": 2,
			"weight": "0",
			"dimensions": {
				"height": 30,
				"width": 30,
				"depth": 30
			},
			"regular_price": 10690.0,
			"variations": null
		}]
	}
}';
\Garan24\Garan24::$DB["host"]="151.248.117.239";

$result = file_get_contents("https://service.garan24.ru/checkout", null, stream_context_create(array(
    'http' => array(
        'method' => 'POST',
        'header' => array('Content-Type: application/json'."\r\n"
        . 'Content-Length: ' . strlen($str) . "\r\n"),
        'content' => $str)
        )
    )
);
echo $result;
//$o =  new Garan24\Deal\Deal();
//$o->byJson($str);
//$o->sync();
//echo $o->byId(500);
//echo ($o->check(false)?"checked":"no required fields")."\n";
/*$o->update(["customer_id"=>2,"address"=>[
    "first_name"=> "John",
    "last_name"=> "Doe",
    "address_1"=> "969 Market",
    "address_2"=> "",
    "city"=> "San Francisco",
    "state"=> "CA",
    "postcode"=> "94103",
    "country"=> "US",
    "email"=> "john.doe@example.com",
    "phone"=> "(555) 555-5555"
]]);*/
print_r ($o->getDeliveryTypes());
print_r ($o->getPaymentTypes());

?>
