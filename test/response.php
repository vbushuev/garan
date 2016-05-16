<?php
require_once("test.php");
$r = [
    "url" => $_SERVER["HTTP_ORIGIN"],
    "data" => $_REQUEST
];
$obj = new \Garan24\Gateway\Ariuspay\CallbackResponse($r,function($d){
    //echo "Make order is payed.";
    //echo \Garan24\Garan24::obj2str($d);
    try{
        $capture = new \Garan24\Gateway\Ariuspay\Capture([
            'client_orderid' => $d["client_orderid"],
            'orderid' => $d["orderid"]
        ]);
        $capture->call();
    }
    catch(\Garan24\Gateway\Aruispay\Exception $e){
        print("Exception in AruisPay gateway Create Card reference:".$e->getMessage());
    }
});
try{
    if($obj->accept()){
        try{
            $crd = new \Garan24\Gateway\Ariuspay\CreateCardRef([
                'client_orderid' => $obj->client_orderid,
                'orderid' => $obj->orderid
            ]);
            $crd->call();
        }
        catch(\Garan24\Gateway\Aruispay\Exception $e){
            print("Exception in AruisPay gateway Create Card reference:".$e->getMessage());
        }
    }
}
catch(\Garan24\Gateway\Aruispay\Exception $e){
    print("Exception in AruisPay Response gateway:".$e->getMessage());
}

// [error_message] => [processor-tx-id] => PNTEST-313635 [merchant_order] => 902B4FF5 [orderid] => 313635 [client_orderid] => 902B4FF5 [bin] => 444455 [control] => d67cd0f82e92b28dfd1d6a702c24213ee1de6d76 [gate-partial-reversal] => enabled [descriptor] => GARAN24 - non3d [gate-partial-capture] => enabled [type] => sale [card-type] => VISA [merchantdata] => VIP customer [phone] => +12063582043 [last-four-digits] => 1111 [card-holder-name] => Vladimir Bushuev [status] => approve
?>
