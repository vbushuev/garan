<?php
namespace Garan24\Deal;
use \Garan24\Deal\WooRequiredObject as G24Object;
use \Garan24\Garan24 as Garan24;
class Item extends G24Object{
    public function __construct($a=[],$wc){
        $ii = is_array($a)?json_encode($a):$a;
        parent::__construct([
            "product_id",
            "title",
            "description",
            "product_url",
            "product_img",
            "quantity",
            "weight",
            "dimensions",
            "regular_price",
            "variations",
            "images",
            "_links",
            "external_url"
        ],$ii,$wc);
        if(isset($this->dimensions)) $this->dimensions = new Dimensions($this->dimensions);
        if(isset($this->variations)) $this->variations = new Variations($this->variations);
    }
    public function sync(){
        $resource = new \WC_API_Client_Resource_Products($this->wc_client);
        $resp=null;
        try{
            $resp = $resource->get($this->product_id);
            $this->_jdata = array_merge($this->_jdata,json_decode(json_encode($resp->product),true));
            Garan24::debug($this->__toString());
            Garan24::debug($this->images);
        }
        catch(\WC_API_Client_Exception $e){
            $this->create();
        }
        catch(\WC_API_Client_HTTP_Exception $e){
            echo $resp;
        }
        $this->product_id = $resp->product->id;
    }
    protected function create(){
        $item = $this->_jdata;
        $item["type"]="external";
        $item["images"]=[[
            'src'=>$item["product_img"],
            'position'=>0
        ]];
        $item["external_url"] = $item["product_url"];
        $resp = $resource->create(["product"=> $item]);
        $this->product_id = $resp->product->id;
    }
};
?>
