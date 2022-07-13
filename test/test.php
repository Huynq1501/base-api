<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */
require_once __DIR__ . '/../vendor/autoload.php';
$config = [
    'DATABASE' => [
        'driver'    => 'mysql',
        'host'      => 'mariadb',
        'username'  => 'root',
        'password'  => 'hungna',
        'database'  => 'docker_database',
        'port'      => 3306,
        'prefix'    => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'OPTIONS'  => [
        'showSignature'         => true,
        'debugStatus'           => true,
        'debugLevel'            => 'error',
        'loggerPath'            => __DIR__ . '/../tmp/logs/',
        // Cache
        'cachePath'             => __DIR__ . '/../tmp/cache/',
        'cacheTtl'              => 3600,
        'cacheDriver'           => 'files',
        'cacheFileDefaultChmod' => 0777,
        'cacheSecurityKey'      => 'BACKEND-SERVICE',
    ]
];

use nguyenanhung\Backend\Your_Project\Http\WebServiceAccount;

$inputData = [
    'start_date' => '2022-06-01',
    'end_date'   => '2022-06-05',
    'type'       => 1,
    'acc'        => 'xxx',
    'signature'  => 'xxx'
];

$api = new WebServiceAccount($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($inputData)
    ->register();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";