<?php
namespace Garan24\Delivery\BoxBerry;
class BoxBerry extends \Garan24\HTTPConnector{
    protected $token;
    protected $pvz;
    public function __construct(){
        $this->token = '17324.prpqcdcf';
        //$this->pvz = '77961'; // Moscow
        //$this->pvz = 'B70 0BD'; //  - Англия
        $this->pvz = '2983 GR'; //  Голландия
    }
    public function __call($f,$a){
        $this->validateFunction($f);
        $url = "https://api.boxberry.de/json.php?token=".$this->token."&method=".$f;
        return $this->get($url,(count($a)?$a[0]:null));
    }
    protected function validateFunction($f){
        if(!in_array($f,[
            "ParselCreate",
            "ParselCheck",
            "ParselList",
            "ParselDel",
            "ParselStory",
            "ParselSend",
            "ParselSendStory",
            "OrdersBalance",

            "ListCities",
            "ZipCheck",
            "ListPoints",
            "ListZips",
            "ListStatusesFull",
            "ListServices",
            "CourierListCities",
            "DeliveryCosts",
            "PointsByPostCode",
            "PointsDescription",

            "DeliveryCostsF"
            ])) throw new Exception("No such service or function in BoxBerry:{".$f."}");
    }
};

?>
