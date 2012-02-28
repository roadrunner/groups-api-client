<?php

require_once __DIR__.'/src/HttpClient.php';

$client = new \Groups\HttpClient(array('groupName' => 'saksuka', 'debug' => false, 'api_host' => '192.168.56.111'));

//print_r($client->doRequest('api.php', array('module' => 'foo')));

// print_r($client->doRequest('api.php', array('module' => 'generals', 'method' => 'getTagline')));

print_r($client->generals->getTagline());