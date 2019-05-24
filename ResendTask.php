<?php

require __DIR__.'/vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

function send($port)
{
    $client = new GuzzleHttp\Client();

    try {
        $client->request('GET', 'http://localhost:'.$port.'/send');
        return;
    }
    catch(RequestException $exception) {
        return true;
    }
}

echo $argv[1];

while(true) {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $arList = $redis->lrange("messageId", 0, 0);
    if (count($arList) > 0)
    {
        print_r($arList[0]);
        send($argv[1]);
    }
    sleep(5);
    $redis->close();
}
