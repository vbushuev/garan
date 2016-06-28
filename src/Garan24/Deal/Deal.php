<?php
namespace Garan24\Deal;
use \Garan24\Deal\Customer as Customer;
use \Garan24\Deal\WooRequiredObject as G24Object;
use \Garan24\Store\DBConnector as DBConnector;
use \Garan24\Store\Exception as StoreException;
use \Garan24\Garan24 as Garan24;

class Deal extends G24Object{
    protected $shop;
    protected $cust;
    protected $db;
    protected $redirect_url = "https://service.garan24.ru/checkout/";
    protected $deal;
    protected $payments=[];
    protected $delivery=[];
    public function __construct(){
        parent::__construct([
            "x_secret",
            "x_key",
            "version",
            "response_url",
            "order"
        ]);
        $this->db = new DBConnector();
    }
    public function sync(){
        $ret = new DealResponse();
        try{
            $this->getShop();
        }
        catch(Exception $e){
            $ret->code = 500;
            $ret->error = "Wrong secret or key value. Or auth data is expired.";
        }
        try{
            $this->order->customer_id = $this->shop["user_id"];
            $this->order->sync();
        }
        catch(Exception $e){
            $ret->code = 500;
            $ret->error = "Wrong order parameters.";
        }
        try{
            $this->createDeal();
            $ret->id = $this->order->id;
            $ret->code = 0;
            $ret->error = 0;
            $ret->redirect_url = "https://service.garan24.ru/checkout/?id=".$this->order->id;
        }
        catch(Exception $e){
            $ret->code = 500;
            $ret->error = "No deal registered.";
        }
        return $ret;
    }
    public function byJson($a){
        $a = is_array($a)?json_encode($a):$a;
        $this->_jdata = array_change_key_case(json_decode($a,true),CASE_LOWER);
        $this->initWC($this->x_key,$this->x_secret);
        $this->order = new Order($this->order,$this->wc_client);
    }
    public function byId($id){
        $sql = "select d.id,d.shop_id,d.internal_order_id,s.consumer_key,wak.consumer_secret ";
        $sql.= " from deals d ";
        $sql.= " join shops s on s.id=d.shop_id";
        $sql.= " join woocommerce_api_keys wak on wak.key_id = s.api_key_id";
        $sql.= " where d.internal_order_id =".$id;
        try{
            $this->deal = $this->db->select($sql);
            Garan24::debug("Deal is : ". json_encode($this->deal));
            $this->x_key = $this->deal["consumer_key"];
            $this->x_secret = $this->deal["consumer_secret"];
            $this->getShop();
            $this->getOrder($id);
            $this->getCustomer();
        }
        catch(\Exception $e){
            Garan24::debug("Deal #{$id} not found.");
        }
    }
    public function update($data){
        if(isset($data["customer_id"])){
            $sql = "update deals set customer_id = '".$data["customer_id"]."' where id=".$this->deal["id"];
            $this->db->update($sql);
            $this->order->update(["customer_id"=>$data["customer_id"]]);
        }
        else if( isset($data["billing"]) ){
            $data["billing"]["phone"] = $this->cust->phone;
            $addr = $data["billing"];
            $addr["last_name"] = $data['fio']['last'];
            $addr["first_name"] = $data['fio']['first'];
            $this->order->update(["shipping_address"=>$addr]);
            $this->cust->update([
                "last_name"=>$data['fio']['last'],
                "first_name"=>$data['fio']['first'],
                "billing_address" => $addr,
                "shiping_address" => $addr
            ]);
        }

    }
    public function getCustomer(){
        $this->cust = new Customer('{"id":"'.$this->order->customer_id.'","customer_id":"'.$this->order->customer_id.'"}',$this->wc_client);
        $this->cust->get();
        Garan24::debug("Customer is : ". $this->cust->__toString());
    }
    public function getShopUrl(){
        return $this->shop["link"];
    }
    public function getPaymentTypes(){
        if(count($this->payments))return $this->payments;
        $sql = "select pt.id,pt.code,pt.name,pt.desc";
        $sql.= " from garan24_paymenttype pt";
	    $sql.= " join garan24_shop_payments sp on sp.payment_id=pt.id";
        $sql.= " where sp.shop_id=".$this->shop["id"]." order by pt.id";
        try{$this->payments = $this->db->selectAll($sql);}
        catch(\Exception $e){
            Garan24::debug("getPaymentTypes exception : ". $e);
        }
        return $this->payments;
    }
    public function getDeliveryTypes(){
        if(count($this->delivery))return $this->delivery;
        $sql = "select dt.id,dt.code,convert(dt.name using utf8) as name,convert(dt.desc using utf8) COLLATE utf8_bin as 'desc',dt.price,dt.timelaps";
        $sql.= " from garan24_deliverytype dt";
	    $sql.= " join garan24_shop_delivery sd on sd.delivery_id=dt.id";
        $sql.= " where sd.shop_id=".$this->shop["id"]." order by dt.id";
        try{$this->delivery = $this->db->selectAll($sql);}
        catch(\Exception $e){
            Garan24::debug("getPaymentTypes exception : ". $e);
        }
        return $this->delivery;
    }
    protected function getShop(){
        $sql = "select s.id,s.name,s.link,s.description,s.api_key_id,wak.user_id from woocommerce_api_keys wak";
        $sql.= " join shops s on s.api_key_id = wak.key_id";
        $sql.= " where wak.consumer_secret = '".$this->x_secret."'";
        $this->shop = $this->db->select($sql);
        Garan24::debug("Shop is : ". json_encode($this->shop));
    }
    protected function createDeal(){
        $sql = "insert into deals (amount,currency,shop_id,status,internal_order_id,external_order_id,external_order_url,customer_id) ";
        $sql.= "values(";
        $sql.= $this->order->order_total;
        $sql.= ",'".$this->order->order_currency."'";
        $sql.= ",'".$this->shop["id"]."'";
        $sql.= ",'1'";
        $sql.= ",'".$this->order->id."'";
        $sql.= ",'".$this->order->order_id."'";
        $sql.= ",'".$this->order->order_url."'";
        $sql.= ",'".$this->order->customer_id."'";
        $sql.= ")";
        $this->db->insert($sql);
    }
    protected function getOrder($id){
        $this->initWC($this->x_key,$this->x_secret);
        $this->order = new Order('{"id":"'.$id.'"}',$this->wc_client);
        $this->order->get();
    }
};
?>
