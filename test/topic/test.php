<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */

use nguyenanhung\Backend\BaseAPI\Http\WebServiceTopic;

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__.'/../config.php';

$inputData = [
    'id' => 9,
    'name' => 'xin chào mọi người',
    'tittle' => 'topic tittle nè hehe hehe',
    'keywords' => 'topic 1, topic_1',
    'photo' => 'https://vi.wikipedia.org/wiki/H%C3%A0_m%C3%A3#/media/T%E1%BA%ADp_tin:Hippo_memphis.jpg',
    'username' => 'hippo_push',
    'signature' => '7dd8e6dbb9e834f3acc62b2c8357ae8d'
];

$listData = [
    'page_number' => 1,
    'number_record_of_pages' => 4,
    'username' => 'hippo_push',
    'signature' => 'c525b0327d09be071ecb8733b0553b07'
];

$showData = [
    'id' => 9,
    'username' => 'hippo_push',
    'signature' => '5599b81fd17b25d0cc75d65b73c646a5'
];

//api  create or update
//$api = new WebServiceTopic($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($inputData)
//    ->createOrUpdate();

//api list
//$api = new WebServiceTopic($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($listData)
//    ->list();

//api show
$api = new WebServiceTopic($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($showData)
    ->show();
echo "<pre>";
print_r($api->getResponse());
echo "</pre>";