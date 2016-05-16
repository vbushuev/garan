<?php
namespace \Garan24;
class Object {
    protected $_jdata=[];
    public function __construct($a="{}"){
        $this->_jdata = json_decode(array_change_key_case($a,CASE_LOWER));
    }
    public function ___toString(){
        return $this->toJson();
    }
    public function toJson(){
        return json_encode($this->_jdata);
    }
    public function __isset($nc){
        $n=strtolower($nc);
        return isset($this->_jdata["{$n}"]);
    }
    public function __unset($nc){
        $n=strtolower($nc);
        unset($this->_jdata["{$n}"]);
    }
    public function __get($nc){
        $n=strtolower($nc);
        if(!$this->__isset($n)) throw new Exception("No such parameter \{{$n}\}");
        return $this->_jdata["{$n}"];
    }
    public function __set($nc,$v){
        $n=strtolower($nc);
        $this->_jdata["{$n}"]=$v;
    }
};
?>
