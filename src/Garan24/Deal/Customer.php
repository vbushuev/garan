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
    }
    public function sync(){
        $this->getCustomer();
        $resource = new \WC_API_Client_Resource_Customers($this->wc_client);
        $resp=null;
        try{
            $resp = $resource->get($this->customer_id);
            $this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->customer),true));
            Garan24::debug($this->__toString());
        }
        catch(\WC_API_Client_Exception $e){
            $this->create();
        }
        catch(\WC_API_Client_HTTP_Exception $e){
            echo $resp;
        }
    }
    protected function create(){
        $resp = $this->wc_client->customers->create(["customer"=> [
            "email"=>$this->email,
            "password"=>$this->phone,
            "username"=>$this->email
        ]]);
        $this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->customer),true));
    }
    protected function getCustomer(){
        $sql = "select u.id ";
        $sql.= "from users u";
        $sql.= " join usermeta um on u.id = um.user_id and um.meta_key='billing_phone'";
        $sql.= " where u.user_email = '".$this->email."'";
        $sql.= " and um.meta_value = '".$this->phone."'";
        $user = $this->db->select($sql);
        Garan24::debug("User is : ". json_encode($user));
        $this->id = $user["id"];
        $this->customer_id = $user["id"];
    }
};
?>
