<?php
namespace Garan24\Deal;
use \Garan24\Deal\WooRequiredObject as G24Object;
class Order extends G24Object{
    public function __construct($a="{}",$wc){
        parent::__construct(
            ["id","order_id","order_url","order_total","order_currency","items","line_items","customer_id"],
            $a,
            $wc
        );
        if(isset($this->items)){
            $items = [];
            foreach($this->items as $item){
                $i = new Item($item,$wc);
                array_push($items, $i);
            }
            $this->items = $items;
        }
    }
    public function sync(){
        $resource = new \WC_API_Client_Resource_Orders($this->wc_client);
        $data = $this->_jdata;
        unset($data["items"]);
        $data["line_items"] = [];
        foreach($this->items as $item){
            $item->sync();
            array_push($data["line_items"],$item->toArray());
        }
        $resp=$resource->create($data);
        echo ("Order is : ". json_encode($resp->order));
        $this->id= $resp->order->id;
    }
    public function get(){
        if(!isset($this->id)){
            echo ("Order id is missing.");
            return false;
        }
        $resource = new \WC_API_Client_Resource_Orders($this->wc_client);
        $resp = $resource->get($this->id);
        echo ("Order is : ". $resp->http->response->body);
        $this->_jdata = json_decode(json_encode($resp->order),true);
        if(isset($this->line_items)){
            $items = [];
            foreach($this->line_items as $item){
                $i = new Item($item,$this->wc_client);
                array_push($items, $i);
            }
            $this->items = $items;
        }
    }
    public function update($data){
        $resource = new \WC_API_Client_Resource_Orders($this->wc_client);
        $resp = $resource->update($this->id,$data);
        echo ("Order is : ". $resp->http->response->body);
        $this->_jdata = json_decode(json_encode($resp->order),true);
        if(isset($this->line_items)){
            $items = [];
            foreach($this->line_items as $item){
                $i = new Item($item,$this->wc_client);
                array_push($items, $i);
            }
            $this->items = $items;
        }
    }
};
?>
