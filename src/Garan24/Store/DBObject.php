<?php
namespace \Garan24\Store;
use \Garan24\Garan24 as GARAN24;
use \Garan24\Object as Garan24Object;
use \Garan24\Store\Exception as StoreException;
class DBObject extends Garan24Object{
    protected $_dbdata = [];
    public function __construct($a){
        parent::__contruct($a);
        $this->_dbdata["host"]=GARAN24::$DB["host"];
        $this->_dbdata["user"]=GARAN24::$DB["user"];
        $this->_dbdata["pass"]=GARAN24::$DB["pass"];
        $this->_dbdata["schema"]=GARAN24::$DB["schema"];
        $this->_dbdata["connected"]=false;
        $this->_dbdata["conn"]=null;
    }
    public function __destruct(){
        if($this->_dbdata["connected"]) $this->_dbdata["conn"]->close();
    }
    protected function connect(){
        $this->_dbdata["conn"] = new mysqli($this->_dbdata["host"],$this->_dbdata["user"],$this->_dbdata["pass"],$this->_dbdata["schema"]);
        if($this->_dbdata["conn"]->connect_errno) throw new StoreException("No db connection. Error:".$this->_dbdata["conn"]->connect_error);
        $this->_dbdata["connected"] = true;
    }
    protected function execute($sql){
        if(!$this->_dbdata["connected"]) $this->connect();
        $result = $this->_dbdata["conn"]->query($sql,MYSQLI_USE_RESULT);
        if(!$result) throw new StoreException("Fail to execute {$sql}. Error:".$this->_dbdata["conn"]->error);
        if(!$result->num_rows) throw new StoreException("Sync failed no data is retrieved.");
        $this->_jdata = $result->fetch_array(MYSQLI_ASSOC);
        $result->close();
    }
};
?>
