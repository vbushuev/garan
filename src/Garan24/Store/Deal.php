<?php
namespace \Garan24\Store;
use \Garan24\Garan24 as GARAN24;
use \Garan24\Store\DBObject as Garan24dbObject;
use \Garan24\Store\Exception as StoreException;
class Deal extends Garan24dbObject{
    public function __construct($id){
        parent::__construct("{id:\"{$id}\"}");
        $this->sync();
    }
    protected function sync(){
        if(!isset($this->id))return;
        $this->execute("select * from gr1_deals where id = ".$this->id);
    }
    public function __set($nc,$v){
        $n=strtolower($nc);
        parent::__set($nc,$v);
        if(in_array($n,["status","amount","currency"])){
            
        }
    }
};
?>
