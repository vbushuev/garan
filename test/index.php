<?php

require_once("test.php");
$str = '{
    "x_secret":"cs_0c12a03d43781fd418696af84e2a5519ef0b829e",
    "x_key":"ck_7b5f8404d88a0d2aa32cda366bcb2ad1a06b6ae3",
    "version":"1.0",
    "response_url":"https://youronlinestore.com/response",
    "order":
    {
        "order_id":"555",
        "order_url":"https://youronlinestore.com/order/#id",
        "order_total":"14313.00",
        "order_currency":"RUB",
        "items":[
			{
                "product_id":"product-9",
                "title":"Mens Jacket",
                "description":"Fasion men\'s jacket",
                "product_url":"http://demostore.garan24.ru/product/jacket/",
                "product_img":"http://demostore.garan24.ru/wp-content/uploads/2016/04/jacket.jpg",
                "quantity":"1",
                "weight":"500",
                "dimensions":{
                    "height":"100",
                    "width":"10",
                    "depth":"40"
                },
                "regular_price":"5211.20",
                "variations":{"color":"brown"}
            },
            {
                "product_id":"product-30",
                "title":"Causual men’s shoes",
                "description":"Tiny style shoes",
                "product_url":"http://demostore.garan24.ru/product/causual-mens-shoes/",
                "product_img":"http://demostore.garan24.ru/wp-content/uploads/2016/04/x._V293494175_-600x381.jpg",
                "quantity":"1",
                "weight":"400",
                "dimensions":{
                    "height":"11",
                    "width":"8",
                    "depth":"40"
                },
                "regular_price":"9101.80",
                "variations":{"color":"brown"}
            }
		]
    }
}
';
$o =  new Garan24\Deal\Deal($str);
//echo ($o->check(false)?"checked":"no required fields")."\n";
echo $o->sync();
//echo ($o)."\n";

?>
