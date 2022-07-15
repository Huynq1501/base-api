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
use nguyenanhung\Classes\Helper\Str;
use Ramsey\Uuid\Uuid;

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
//    'id' => 7,
    'name' => 'xin chào mọi người',
    'tittle' => 'topic tittle nè hehe hehe',
    'keywords' => 'topic 1, topic_1',
    'photo' => 'https://vi.wikipedia.org/wiki/H%C3%A0_m%C3%A3#/media/T%E1%BA%ADp_tin:Hippo_memphis.jpg',
    'username' => 'hippo_push',
    'signature' => '7dd8e6dbb9e834f3acc62b2c8357ae8d'
];

//api  create or update
$api = new WebServiceTopic($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($inputData)
    ->createOrUpdate();


echo "<pre>";
print_r($api->getResponse());
echo "</pre>";