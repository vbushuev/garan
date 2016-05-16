<?php
date_default_timezone_set('Europe/Moscow');
function __autoload($className){
	$file = str_replace('\\','/',$className);
	require_once '../src/'.$file.'.php';
	return true;
}
$timeout = 1;
function makeTimeout($timeout){
    switch ($timeout) {
        case 1:
            $timeout = 2;
            break;
        case 2:
            $timeout = 3;
            break;
        case 3:
            $timeout = 5;
            break;
        default:
            $timeout = 15;
            break;
    }
    sleep($timeout);
    return $timeout;
}
?>
