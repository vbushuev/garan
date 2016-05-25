<?php
require_once("test.php");

$data = [
    "request"=>[
        "@"=>["id"=>1,"date"=>"YYYY-MM-DDTHH:MM:SS","request_type"=>"ALL,MFO,COL"],
        "checkhash"=>[
            "@"=>["first_name"=>"V","middle_name"=>"S","today"=>date('Ymd')]
        ],
        "person_data"=>[
            "person"=>[
                "@"=>["record_id" => "INT",
        			"type" => "Тип персоналии (справочник)",
        			"hash_last_name" => "Фамилия",
        			"hash_first_name" => "Имя",
        			"hash_middle_name" => "Отчество",
        			"hash_birth_date" => "Дата рождения",
        			"hash_birth_place" => "Место рождения",
        			"sex" => "Пол (справочник)",
        			"reason_request" => "Основание для запроса данных (справочник)",
                ],
                "person_docs"=>["doc"=>["@"=>[
                        "record_id" => "INT",
        				"doc_type" => "Тип документа (справочник)",
        				"hash_doc_serial" => "Серия документа",
        				"hash_doc_number" => "Номер документа",
        				"hash_doc_issue_date" => "Дата выдачи документа",
        				"doc_issue_auth" => "Орган, выдавший документ",
                    ]]],
                "person_addresses"=>["address"=>["@"=>[
                        "record_id" => "INT",
    					"address_type" => "Тип адреса",
    					"address_index" => "Индекс",
    					"address_region" => "Субъект РФ",
    					"address_area" => "Район",
    					"address_city_type" => "Тип населенного пункта",
    					"address_city_name" => "Наименование населенного пункта",
    					"address_street_type" => "Улица/проспект/проезд/шоссе и т.п.",
    					"hash_address_street_name" => "Название улицы",
    					"hash_address_building_number" => "Номер дома",
    					"hash_address_building_addition" => "Номер корпуса/строения",
    					"hash_address_flat_number" => "Номер квартиры",
    					"hash_address_kladr_code" => "Код КЛАДР",
                    ]]],
                "person_phones"=>["phone"=>["@"=>[
                        "record_id" => "INT",
                        "phone_type" => "Тип телефона",
                        "phone_country_code" => "Код страны",
                        "phone_number" => "Номер (10 цифр)",
                        "phone_extension_number" => "Расширение",
                    ]]],
                "person_cards"=>["card"=>["@"=>[
                        "record_id" => "INT",
    					"card_number" => "Номер карты (123456XXXXXX6789)",
    					"card_exp_date" => "Срок действия карты",
    					"card_ref_id" => "card ref id",
                    ]]],
            ]
        ]
    ]
];
$request = new \Garan24\Gateway\Finkarta\Request(["data"=>$data]);
$fkarta = new \Garan24\Gateway\Finkarta\Connector();
try{
    //echo $obj->getRequest()->__toString();//call();
    echo htmlspecialchars($fkarta->call($request));

}catch(\Garan24\Gateway\Exception $e){
    print("Exception in gateway:".$e->getMessage());
}
catch(Exception $e){
    print("Exception :".$e->getMessage());
}

?>
<form action="http://testrequest.f-karta.ru/">

    <input type="file" />
    <input type="submit" />
</form>
