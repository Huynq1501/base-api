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

$config = [
    'DATABASE' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '150115',
        'database' => 'base_api',
        'port' => 3306,
        'prefix' => 'tnv_',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'OPTIONS' => [
        'showSignature' => true,
        'debugStatus' => true,
        'debugLevel' => 'error',
        'loggerPath' => __DIR__ . '/../tmp/logs/',
        // Cache
        'cachePath' => __DIR__ . '/../tmp/cache/',
        'cacheTtl' => 3600,
        'cacheDriver' => 'files',
        'cacheFileDefaultChmod' => 0777,
        'cacheSecurityKey' => 'BACKEND-SERVICE',
    ]
];

$inputData = [
    'name' => 'xin chào mọi người',
    'photo' => 'https://vi.wikipedia.org/wiki/H%C3%A0_m%C3%A3#/media/T%E1%BA%ADp_tin:Hippo_memphis.jpg',
    'username' => 'hippo_push',
    'signature' => 'faa082859f092c4bbb3d91899597d51e'
];

$listData = [
    'page_number' => 2,
    'number_record_of_pages' => 4,
    'username' => 'hippo_push',
    'signature' => 'c525b0327d09be071ecb8733b0553b07'
];

$showData = [
    'id' => 23,
    'username' => 'hippo_push',
    'signature' => 'ec5bcf92ff257297746cb2ea9a15b971'
];

//api  create or update
$api = new WebServiceTag($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($inputData)
    ->createOrUpdate();

//api list
$api = new WebServiceTag($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($listData)
    ->list();

//api show
$api = new WebServiceTag($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($showData)
    ->show();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";