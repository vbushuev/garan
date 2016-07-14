<?php
namespace Garan24\Deal;
use \Garan24\Deal\Customer as Customer;
use \Garan24\Deal\WooRequiredObject as G24Object;
use \Garan24\Store\DBConnector as DBConnector;
use \Garan24\Store\Exception as StoreException;
use \Garan24\Garan24 as Garan24;

class Deal extends G24Object{
    protected $_loaded = false;
    protected $shop;
    protected $customer;
    protected $db;
    protected $redirect_url = "https://service.garan24.ru/checkout/";
    protected $deal;
    protected $raw_request="";
    public function __construct($id=null){
        parent::__construct([
            "x_secret",
            "x_key",
            "version",
            "response_url",
            "payment",
            "delivery",
            "order"
        ]);
        $this->db = new DBConnector();
        if(!is_null($id))$this->byId($id);
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
    public function finish(){
        $this->order->update(['status' => 'processing']);
        $ret = new DealResponse();
        $ret->id = $this->deal["internal_order_id"];
        $ret->code = 0;
        $ret->error = 0;
        $ret->order = [
            "order_id" => $this->deal["external_order_id"],
            "status"=>"onconfirm",
            "shipping"=>$this->customer->shipping_address
        ];
        return $ret;
    }
    public function byJson($a){
        $this->raw_request = $a;
        $a = is_array($a)?json_encode($a):$a;
        $this->_jdata = array_change_key_case(json_decode($a,true),CASE_LOWER);
        $this->initWC($this->x_key,$this->x_secret);
        $this->order = new Order($this->order,$this->wc_client);
        $this->_loaded = true;
    }
    public function byId($id){
        $sql = "select d.id,d.shop_id,d.internal_order_id,d.external_order_id,s.consumer_key,wak.consumer_secret,d.response_url,d.status";
        $sql.= ",d.payment_id as payment_type_id,d.delivery_id as delivery_type_id ";
        $sql.= ",dt.name as delivery_type_name,dt.desc as delivery_type_desc ";
        $sql.= ",pt.name as payment_type_name,pt.desc as payment_type_desc ";
        $sql.= " from deals d ";
        $sql.= " join shops s on s.id=d.shop_id";
        $sql.= " join woocommerce_api_keys wak on wak.key_id = s.api_key_id";
        $sql.= " left outer join garan24_deliverytype dt on dt.id = d.delivery_id";
        $sql.= " left outer join garan24_paymenttype pt on pt.id = d.payment_id";
        $sql.= " where d.internal_order_id =".$id;
        try{
            $this->deal = $this->db->select($sql);
            Garan24::debug("Deal is : ". json_encode($this->deal));
            $this->x_key = $this->deal["consumer_key"];
            $this->x_secret = $this->deal["consumer_secret"];
            $this->response_url = $this->deal["response_url"];
            $this->payment = ["id"=>$this->deal["payment_type_id"],"name"=>$this->deal["payment_type_name"],"desc"=>$this->deal["payment_type_desc"] ];
            $this->delivery = ["id"=>$this->deal["delivery_type_id"],"name"=>$this->deal["delivery_type_name"],"desc"=>$this->deal["delivery_type_desc"] ];
            $this->_loaded = true;
            $this->getShop();
            $this->getOrder($id);
            $this->getCustomer();
            return true;
        }
        catch(\Exception $e){
            Garan24::debug("Deal [#{$id}] not found.".$e->getMessage());
            return false;
        }
        return false;
    }
    public function update($data){
        if(!$this->_loaded)return;
        if(isset($data["customer_id"])){
            $sql = "update deals set customer_id = '".$data["customer_id"]."' where id=".$this->deal["id"];
            $this->db->update($sql);
            $this->order->update(["customer_id"=>$data["customer_id"]]);
        }
        if (isset($data["payment_id"])) {
            $sql = "update deals set payment_id = '".$data["payment_id"]."' where id=".$this->deal["id"];
            $this->db->update($sql);
        }
        if (isset($data["delivery_id"])) {
            $sql = "update deals set delivery_id = '".$data["delivery_id"]."' where id=".$this->deal["id"];
            $this->db->update($sql);
        }
        if( isset($data['fio'])){
            $this->customer->update([
                "last_name"=>$data['fio']['last'],
                "first_name"=>$data['fio']['first'],
                "billing_address" => [
                    "last_name"=>$data['fio']['last'],
                    "first_name"=>$data['fio']['first'],
                ],
                "shiping_address" => [
                    "last_name"=>$data['fio']['last'],
                    "first_name"=>$data['fio']['first'],
                ]
            ]);
        }
        if( isset($data["billing"]) ){
            $data["billing"]["phone"] = $this->customer->phone;
            $addr = $data["billing"];
            if(isset($data['fio'])){
                $addr["last_name"] = $data['fio']['last'];
                $addr["first_name"] = $data['fio']['first'];
                $this->customer->update([
                    "last_name"=>$data['fio']['last'],
                    "first_name"=>$data['fio']['first']
                ]);
            }
            $this->order->update([
                "shipping_address"=>$addr,
                "shipping_lines"=>[

                ]
            ]);
            $this->customer->update([
                "billing_address" => $addr,
                "shiping_address" => $addr
            ]);
        }
        if(isset($data["card-ref-id"])){
            $sql = "insert into garan24_cardrefs (card_ref_id) values('".$data["card-ref-id"]."')";
            $this->db->insert($sql);
            $sql = "insert into garan24_user_cardref (user_id,card_ref_id) values(".$this->customer->customer_id.",last_insert_id())";
            $this->db->insert($sql);
        }

    }
    public function getCustomer(){
        if(!$this->_loaded)return;
        if(!$this->customer&&is_object($this->customer)&&($this->customer instanceof Customer)) return $this->customer;
        $this->customer = new Customer('{"id":"'.$this->order->customer_id.'","customer_id":"'.$this->order->customer_id.'"}',$this->wc_client);
        $this->customer->get();
        Garan24::debug("Customer is : ". $this->customer->__toString());
        return $this->customer;
    }
    public function getShopUrl(){
        if(!$this->_loaded)return;
        return $this->shop["link"];
    }
    public function getPaymentTypes(){
        if(!$this->_loaded)return;
        //if(count($this->payments))return $this->payments;
        $sql = "select pt.id,pt.code,pt.name,pt.desc";
        $sql.= " from garan24_paymenttype pt";
	    $sql.= " join garan24_shop_payments sp on sp.payment_id=pt.id";
        $sql.= " where sp.shop_id=".$this->shop["id"];
        if(isset($this->_jdata["payments"])&&count($this->_jdata["payments"])){
            $sql.= " and pt.code in ('".join("','",$this->_jdata["payments"])."')";
        }
        $sql.= " order by pt.id";
        $payments=[];
        Garan24::debug("getPaymentTypes sql query:".$sql);
        try{$payments = $this->db->selectAll($sql);}
        catch(\Exception $e){
            Garan24::debug("getPaymentTypes exception : ". $e);
        }
        return $payments;
    }
    public function getDeliveryTypes(){
        if(!$this->_loaded)return;
        //if(count($this->deliveries))return $this->deliveries;
        $deliveries=[];
        $sql = "select dt.id,dt.code,convert(dt.name using utf8) as name,convert(dt.desc using utf8) COLLATE utf8_bin as 'desc',dt.price,dt.timelaps";
        $sql.= " from garan24_deliverytype dt";
	    $sql.= " join garan24_shop_delivery sd on sd.delivery_id=dt.id";
        $sql.= " where sd.shop_id=".$this->shop["id"];
        if(isset($this->_jdata["deliveries"])&&count($this->_jdata["deliveries"])){
            $sql.= " and dt.code in ('".join("','",$this->_jdata["deliveries"])."')";
        }
        $sql.= " order by dt.id";
        Garan24::debug("getDeliveryTypes sql query:".$sql);
        try{$deliveries = $this->db->selectAll($sql);}
        catch(\Exception $e){
            Garan24::debug("getDeliveryTypes exception : ". $e);
        }
        return $deliveries;
    }
    protected function getShop(){
        if(!$this->_loaded)return;
        $sql = "select s.id,s.name,s.link,s.description,s.api_key_id,wak.user_id from woocommerce_api_keys wak";
        $sql.= " join shops s on s.api_key_id = wak.key_id";
        $sql.= " where wak.consumer_secret = '".$this->x_secret."'";
        $this->shop = $this->db->select($sql);
        Garan24::debug("Shop is : ". json_encode($this->shop));
    }
    protected function createDeal(){
        if(!$this->_loaded)return;
        $sql = "insert into deals (amount,currency,shop_id,status,internal_order_id,external_order_id,external_order_url,customer_id,response_url,payments,deliveries,raw_request) ";
        $sql.= "values(";
        $sql.= $this->order->order_total;
        $sql.= ",'".$this->order->order_currency."'";
        $sql.= ",'".$this->shop["id"]."'";
        $sql.= ",'1'";
        $sql.= ",'".$this->order->id."'";
        $sql.= ",'".$this->order->order_id."'";
        $sql.= ",'".$this->order->order_url."'";
        $sql.= ",'".$this->order->customer_id."'";
        $sql.= ",'".$this->response_url."'";
        $sql.= ",'".(isset($this->payment)?$this->payment:"")."'";
        $sql.= ",'".(isset($this->delivery)?$this->delivery:"")."'";
        $sql.= ",'".$this->raw_request."'";
        $sql.= ")";
        $this->db->insert($sql);
    }
    protected function getOrder($id){
        if(!$this->_loaded)return;
        $this->initWC($this->x_key,$this->x_secret);
        $this->order = new Order('{"id":"'.$id.'"}',$this->wc_client);
        $this->order->get();
    }
};
?>
