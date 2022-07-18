<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */

use nguyenanhung\Backend\BaseAPI\Http\WebServiceCategory;

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__.'/../config.php';

$inputData = [
    'id' => 302,
    'status'=>22,
    'name' => 'xin chào mọi người',
    'title' => 'category demo',
    'language'=>'',
    'description' => 'description category',
    'keywords'=>'keyword category',
    'photo' => 'https://vi.wikipedia.org/wiki/H%C3%A0_m%C3%A3#/media/T%E1%BA%ADp_tin:Hippo_memphis.jpg',
    'parent' => 302,
    'order_status' => 123,
    'show_top' => 1,
    'show_home' => 1,
    'show_right' => 1,
    'show_bottom' => 1,
    'level' => 22,
    'username' => 'hippo_push',
    'signature' => '2006b0dac19d40e01a6ce3f6432f9ca0'
];

$listData = [
    'page_number' => 1,
    'number_record_of_pages' => 4,
    'username' => 'hippo_push',
    'signature' => 'c525b0327d09be071ecb8733b0553b07'
];

$showData = [
    'id' => 301,
    'username' => 'hippo_push',
    'signature' => '1fe02087bf04f957c3cc6c077e357102'
];

//api  create or update
//$api = new WebServiceCategory($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($inputData)
//    ->createOrUpdate();

////api list
//$api = new WebServiceCategory($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($listData)
//    ->list();

//api show
$api = new WebServiceCategory($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($showData)
    ->show();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";