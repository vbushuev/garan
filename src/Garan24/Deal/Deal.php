<?php
namespace Garan24\Deal;
use \Garan24\Deal\WooRequiredObject as G24Object;
class Deal extends G24Object{
    protected $shop;
    public function __construct($a="{}"){
        parent::__construct([
            "x_secret",
            "x_key",
            "version",
            "response_url",
            "order"
        ],$a);
        $this->initWC($this->x_key,$this->x_secret);
        $this->order = new Order($this->order,$this->wc_client);
    }
    public function sync(){
        $ret = new DealResponse();
        return $ret->__toString();
    }
};
?>
