<?php
namespace Garan24\Deal;
use \Garan24\Deal\WooRequiredObject as G24Object;
use \Garan24\Store\DBConnector as DBConnector;
use \Garan24\Store\Exception as StoreException;
use \Garan24\Garan24 as Garan24;

class Customer extends G24Object{
    protected $db;
    public function __construct($a=[],$wc){
        $ii = is_array($a)?json_encode($a):$a;
        parent::__construct([
            "id",
            "customer_id",
            "email",
            "phone",
        ],$ii,$wc);
        $this->db = new DBConnector();
        //$this->_jdata["phone"] = isset($this->_jdata["phone"])?preg_replace("/\+7/","7",$this->_jdata["phone"]):"";
    }
    public function sync(){
        $resource = new \WC_API_Client_Resource_Customers($this->wc_client);
        $resp=null;
        try{
            $this->getCustomer();
        }
        catch(\Exception $e){
            $this->create();
        }
        try{
            $this->get();
        }
        catch(\WC_API_Client_Exception $e){
            $this->create();
        }
        catch(\Exception $e){
            echo $resp;
        }
    }
    public function get(){
        $resp = $this->wc_client->customers->get($this->customer_id);
        if(isset($resp->customer))$this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->customer),true));
        $this->phone = $resp->customer->billing_address->phone;
    }
    public function update($data){
        $resp = $this->wc_client->customers->update($this->id,$data);
        $this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->customer),true));
    }
    protected function create(){
        $resp = $this->wc_client->customers->create(["customer"=> [
            "email"=>$this->email,
            "password"=>$this->phone,
            "username"=>$this->email,
            'billing_address' => [
                /*'first_name' => $this->email,
                'last_name' => $this->email,
                'company' => '',
                'address_1' => '',
                'address_2' => '',
                'city' => '',
                'state' => '',
                'postcode' => '',*/
                'country' => 'RU',
                'email' => $this->email,
                'phone' => $this->phone
            ]
        ]]);

        $this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->customer),true));
        $this->customer_id = $this->id;
        //$this->update([]);
    }
    protected function getCustomer(){
        $sql = "select u.id,u.user_email,um.meta_value,u.id as `customer_id`";
        $sql.= "from users u";
        $sql.= " join usermeta um on u.id = um.user_id and um.meta_key='billing_phone'";
        if(isset($this->email)&&isset($this->phone)){
            $sql.= " where u.user_email = '".$this->email."'";
            $sql.= " and um.meta_value = '".$this->phone."'";
        }
        elseif (isset($this->id)) {
            $sql.= " where u.id = '".$this->id."'";
        }
        $user = $this->db->select($sql);
        $this->_jdata = $user;
    }
};
?>
