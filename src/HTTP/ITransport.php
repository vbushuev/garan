<?php
namespace Garan24\HTTP;
interface ITransport{
    public function setTimeout($timeout);
    public function send($request);
    public function create($url);
}
?>
