<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */

use nguyenanhung\Backend\BaseAPI\Http\WebServiceOption;

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__.'/../config.php';

$inputData = [
    'id' => 9,
    'name' => 'option8',
    'value' => 'test option 2',
    'status' => 1,
    'username' => 'hippo_push',
    'signature' => '9f62790541d6f58c86f4f3e5d0dd56f4'
];

$showData = [
    'id' => 8,
    'username' => 'hippo_push',
    'signature' => 'a90e3586166497ecbcac3e5117aac34a'
];
//
$listData = [
    'page_number' => 3,
    'number_record_of_pages' => 3,
    'username' => 'hippo_push',
    'signature' => '073f5afed56bb19a656e34d5020cc63f'
];
//api list
$api = new WebServiceOption($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($listData)
    ->list();

//api show
$api = new WebServiceOption($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($showData)
    ->show();

//api  create or update
$api = new WebServiceOption($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($inputData)
    ->createOrUpdate();


echo "<pre>";
print_r($api->getResponse());
echo "</pre>";