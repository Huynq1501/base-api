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
$config = require __DIR__ . '/../config.php';

$inputData = [
//    'id' => 3,
    'name' => 'xin chào mọi người',
//    'status' => 0,
    'tittle' => 'topic tittle nè hehe hehe',
    'keywords' => 'topic 1, topic_1',
    'photo' => 'https://vi.wikipedia.org/wiki/H%C3%A0_m%C3%A3#/media/T%E1%BA%ADp_tin:Hippo_memphis.jpg',
    'username' => 'hippo_push',
    'signature' => '3432a2da78d5e5530b7ff890b5f14233'
];

$listData = [
    'page_number' => 1,
    'max_results' => 4,
    'username' => 'hippo_push',
    'signature' => '073f5afed56bb19a656e34d5020cc63f'
];

$showData = [
    'id' => 2,
    'username' => 'hippo_push',
    'signature' => '2e32b21c33c23e7ffb4ea5c6182449f8'
];

//api  create or update
$api = new WebServiceTopic($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($inputData)
    ->createOrUpdate();

////api list
//$api = new WebServiceTopic($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($listData)
//    ->list();

////api show
//$api = new WebServiceTopic($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($showData)
//    ->show();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";