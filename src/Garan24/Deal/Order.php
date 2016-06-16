<?php
namespace Garan24\Deal;
use \Garan24\Deal\WooRequiredObject as G24Object;
class Order extends G24Object{
    public function __construct($a="{}",$wc){
        parent::__construct(
            ["order_id","order_url","order_total","order_currency","items"],
            $a,
            $wc
        );
        $items = [];
        foreach($this->items as $item){
            $i = new Item($item,$wc);
            array_push($items, $i);
        }
        $this->items = $items;
    }
    public function sync(){

    }
};
?>
