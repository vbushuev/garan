<?php
namespace Garan24\Deal;
use \Garan24\Deal\WooRequiredObject as G24Object;
use \Garan24\Store\DBConnector as DBConnector;
use \Garan24\Store\Exception as StoreException;
use \Garan24\Garan24 as Garan24;

class Deal extends G24Object{
    protected $shop;
    protected $db;
    protected $redirect_url = "https://service.garan24.ru/checkout/";
    public function __construct($a="{}"){
        parent::__construct([
            "x_secret",
            "x_key",
            "version",
            "response_url",
            "order"
        ],$a);
        $this->db = new DBConnector();
        $this->initWC($this->x_key,$this->x_secret);
        $this->order = new Order($this->order,$this->wc_client);
    }
    public function sync(){
        $ret = new DealResponse();
        try{
            $this->getShop();
            $this->order->customer_id = $this->shop["user_id"];
            $this->order->sync();
            $ret->id = $this->order->id;
            $ret->code = 0;
            $ret->error = 0;
            $ret->redirect_url = "https://service.garan24.ru/checkout/".$this->order->id;
        }
        catch(StoreException $e){
            $ret->code = 500;
            $ret->error = "Wrong secret or key value. Or auth data is expired.";
        }
        return $ret->__toString();
    }
    protected function getShop(){
        Garan24::debug("Secret is : ". $this->x_secret);
        $sql = "select s.id,s.name,s.link,s.description,s.api_key_id,wak.user_id from woocommerce_api_keys wak";
        $sql.= " join shops s on s.api_key_id = wak.key_id";
        $sql.= " where wak.consumer_secret = '".$this->x_secret."'";
        $this->shop = $this->db->select($sql);
        Garan24::debug("Shop is : ". json_encode($this->shop));
    }
};
?>
