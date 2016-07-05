<?php
namespace Garan24\Gateway;
use \Garan24\Garan24 as Garan24;
class HTTPConnector implements \Garan24\Interfaces\IConnector{
    /*******************************************************************************
     * Производит перенаправление пользователя на заданный адрес
     *
     * @param string $url адрес
     ******************************************************************************/
    public function redirect($url){
        Header("HTTP 302 Found");
        Header("Location: ".$url);
        die();
    }
    /*******************************************************************************
     * Совершает POST запрос с заданными данными по заданному адресу. В ответ
     * ожидается JSON
     *
     * @param string $url
     * @param array|null $data POST-данные
     *
     * @return array
     ******************************************************************************/
    public function post($url,$data = null){
        $fp=fopen('../garan-curl-'.date("Y-m-d").'.log', 'wa');
        $curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_VERBOSE => 1,
            CURLOPT_STDERR => $fp,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ];
        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);
        Garan24::debug("RAW RESPONSE:[{$response}]");
        return $response;
    }
    /*******************************************************************************
     * Совершает GET запрос с заданными данными по заданному адресу. В ответ
     * ожидается JSON
     *
     * @param string $url
     * @param array|null $data POST-данные
     *
     * @return array
     ******************************************************************************/
    public function get($url,$data = null){
        $fp=fopen('../garan-curl-'.date("Y-m-d").'.log', 'wa');
        $curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => 1,
            CURLOPT_STDERR => $fp,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ];
        $urlparams = implode('&',$data);
        $curl = curl_init($url.'?'.$urlparams);
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);
        Garan24::debug("RAW RESPONSE:[{$response}]");
        return $response;
    }
};
?>
