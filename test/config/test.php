<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */

use nguyenanhung\Backend\BaseAPI\Http\WebServiceConfig;

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
    'id' => 'company_name',
    'language' => 'vietnamese',
    'value' => 31111,
    'label' => 'abc',
    'type' => 2,
    'status' => 1,
    'username'=>'hippo_push',
    'signature'=>'8eb1e73941faa30b18352c8e0cc218e7'
];

$showData = [
    'id' => 'company-name',
    'language' => 'vietnamese',
    'username'=>'hippo_push',
    'signature'=>'ae53d7b281ba1d62ab315301e33b3073'
];

$listData = [
    'category' => 'site',
    'page_number' => 2,
    'number_record_of_pages' => 2,
    'username'=>'hippo_push',
    'signature'=>'94426fe48898d120d14c300279ee454a'
];
//api list
//$api = new WebServiceConfig($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($listData)
//    ->list();

//api show
$api = new WebServiceConfig($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($showData)
    ->show();

////api  create or update
//$api = new WebServiceConfig($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($inputData)
//    ->createOrUpdate();


echo "<pre>";
print_r($api->getResponse());
echo "</pre>";