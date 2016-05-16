<?php
namespace Garan24;
class Garan24{
    protected static $logger = "laravel";
    protected static $_debug = true;
    protected static $DB = [];
    public static function setLogger($l="laravel"){
        self::$logger = $l;
    }
    public static function setDebugMode($b = true){
        self::$_debug=$b;
    }
    public static function getRemoteIp(){
        $ip="";
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    public static function debug($mix){
        if(!self::$_debug)return;
        $str=self::obj2str($mix);
        (class_exists("Log",false))?call_user_func("Log::debug",$str."\n"):file_put_contents("../garan24-".date("Y-m-d").'.log',$str."\n",FILE_APPEND);

    }
    public static function obj2str($mix){
        $str="";
        if(is_array($mix)){
            foreach($mix as $k=>$v){
                $str.="\t{$k} = ".rtrim($v)."\n";
            }
            $str="array [\n".$str."]";
        }
        elseif (is_object($mix)) {
            $mix = json_decode(json_encode($mix),true);
            foreach($mix as $k=>$v){
                $str.="\t{$k} = {$v}\n";
            }
            $str="object [\n".$str."]";
        }
        else $str=$mix;
        return $str;
    }
}
?>
