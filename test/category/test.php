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
    'id' => 301,
    'status'=>1,
    'name' => 'xin chào mọi người',
    'title' => 'category demo',
    'language'=>'',
    'description' => 'description category',
    'keywords'=>'keyword category',
    'photo' => 'https://vi.wikipedia.org/wiki/H%C3%A0_m%C3%A3#/media/T%E1%BA%ADp_tin:Hippo_memphis.jpg',
    'parent' => 301,
    'order_status' => 123,
    'show_top' => 1,
    'show_home' => 1,
    'show_right' => 1,
    'show_bottom' => 1,
    'level' => 22,
    'username' => 'hippo_push',
    'signature' => '30e09ad584f160388379b0d31226f1e9'
];

$listData = [
    'page_number' => 1,
    'max_results' => 4,
    'username' => 'hippo_push',
    'signature' => '073f5afed56bb19a656e34d5020cc63f'
];

$showData = [
    'id' => 304,
    'username' => 'hippo_push',
    'signature' => '76c366b45b7a34ffec59bc9df17e4c3d'
];

////api  create or update
//$api = new WebServiceCategory($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($inputData)
//    ->createOrUpdate();

//api list
$api = new WebServiceCategory($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($listData)
    ->list();

////api show
//$api = new WebServiceCategory($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($showData)
//    ->show();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";