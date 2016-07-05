<?php
namespace Garan24\Delivery\BoxBerry;
class BoxBerry extends \Garan24\HTTPConnector{
    protected $token;
    protected $pvz;
    public function __construct(){
        $this->token = '17324.prpqcdcf';
        $this->pvz = '77961';
    }
    public function __call($f,$a){

    }
    public function ParselCreate(){

    }
};

?>
