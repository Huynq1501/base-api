<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */

use nguyenanhung\Backend\BaseAPI\Http\WebServiceTag;

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../config.php';

$inputData = [
    'id' => 222,
    'name' => 'xin chào mọi người',
    'photo' => 'https://vi.wikipedia.org/wiki/H%C3%A0_m%C3%A3#/media/T%E1%BA%ADp_tin:Hippo_memphis.jpg',
    'status' => 0,
    'username' => 'hippo_push',
    'signature' => '72c90dab46842c9dd4922497cacbe250'
];

$listData = [
    'page_number' => 2,
    'max_results' => 4,
    'username' => 'hippo_push',
    'signature' => '073f5afed56bb19a656e34d5020cc63f'
];

$showData = [
    'id' => 233,
    'username' => 'hippo_push',
    'signature' => 'a129614c97321f249ef781456abd93e4'
];

////api  create or update
//$api = new WebServiceTag($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($inputData)
//    ->createOrUpdate();

////api list
//$api = new WebServiceTag($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($listData)
//    ->list();

//api show
$api = new WebServiceTag($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($showData)
    ->show();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";