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
            "billing_address",
            "shipping_address"
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
        if(isset($data["passport"])||isset($data["fio"]['middle'])){
            if(isset($data["passport"])){
                $data["passport"]["'where'"]=rtrim(isset($data["passport"]["'where'"])?$data["passport"]["'where'"]:"");
                $d = preg_replace("/'/mi","",json_encode($data["passport"]));
                if($this->db->exists("select 1 from garan24_usermeta where user_id='{$this->customer_id}' and value_key='passport'")){
                    $this->db->update("update garan24_usermeta set value_data = '{$d}' where user_id='{$this->customer_id}' and value_key='passport'");
                }else{
                    $this->db->insert("insert into garan24_usermeta (user_id,value_key,value_data) values ('{$this->customer_id}','passport','{$d}')");
                }
            }
            if(isset($data["fio"]['middle'])){
                if($this->db->exists("select 1 from garan24_usermeta where user_id='{$this->customer_id}' and value_key='fio_middle'"))
                    $this->db->update("update garan24_usermeta set value_data = '{$data["fio"]['middle']}' where user_id='{$this->customer_id}' and value_key='fio_middle'");
                else $this->db->insert("insert into garan24_usermeta (user_id,value_key,value_data) values ('{$this->customer_id}','fio_middle','{$data["fio"]['middle']}')");
            }
            return;
        }

        $resp = $this->wc_client->customers->update($this->id,$data);
        $this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->customer),true));
    }
    public function toAddressString(){
        $str = $this->billing_address['city']
            .", ".$this->billing_address['postcode']
            .", ".$this->billing_address['address_1'];
        return $str;
    }
    protected function create(){
        try{
            $resp = $this->wc_client->customers->get_by_email($this->email);
            Garan24::debug($resp);
            if(isset($resp->customer)){
                $this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->customer),true));
                $this->customer_id = $this->id;
                //$this->update(['billing_address' => ['country' => 'RU','phone' => $this->phone]]);
                return;
            }
        }
        catch(\Exception $e){
            try{
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
            }
            catch(\Exception $e){
                $this->customer_id = 0;
            }
        }//$this->update([]);
    }
    protected function getCustomer(){
        $sql = "select u.id,u.user_email,um.meta_value,u.id as `customer_id`";
        $sql.= " ,fio.value_data as `fio_middle`";
        $sql.= " ,passport.value_data as `passport`";
        $sql.= "from users u";
        $sql.= " join usermeta um on u.id = um.user_id and um.meta_key='billing_phone'";
        $sql.= " left outer join garan24_usermeta fio on u.id = fio.user_id and fio.value_key='fio_middle'";
        $sql.= " left outer join garan24_usermeta passport on u.id = passport.user_id and passport.value_key='passport'";
        if(isset($this->email)&&isset($this->phone)){
            $sql.= " where u.user_email = '".$this->email."'";
            $sql.= " and um.meta_value = '".$this->phone."'";
        }
        elseif (isset($this->id)) {
            $sql.= " where u.id = '".$this->id."'";
        }
        $user = $this->db->select($sql);
        $this->_jdata = array_merge($this->_jdata,$user);
        Garan24::debug($this->_jdata["passport"]);
        $this->_jdata["passport"] = json_decode($this->_jdata["passport"],true);
    }
};
?>
