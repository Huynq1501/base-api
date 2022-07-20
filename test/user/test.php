<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */

use nguyenanhung\Backend\BaseAPI\Http\WebServiceUser;

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../config.php';

$inputData = [
//    'id' => 9,
//    'department_id' => 10,
//    'parent' => 4,
    'status' => 1,
    'user_name' => 'huynq1501',
    'fullname' => 'Nham Quang Huy',
    'address' => 'addresss',
    'email' => 'huynq1501@beetsoft.com.vn',
//    'avatar' => '3432a2da78d5e5530b7ff890b5f14233',
//    'group_id' => 4,
    'password' => 'Hippo99',
//    'reset_password'=>,
    'phone' => '0961618660',
//    'note' => 'note',
//    'photo'=>'aaaaa',
//    'thumb'=>'',
//    'remember_token' => '111111',
    'google_token' => '',
    'google_refresh_token' => '',
    'username' => 'hippo_push',
    'signature' => '9827f37cedae3d8806aa4c773511fd59'
];

$listData = [
    'page_number' => 1,
    'max_results' => 4,
    'username' => 'hippo_push',
    'signature' => '073f5afed56bb19a656e34d5020cc63f'
];

$showData = [
    'id' => 22,
    'username' => 'hippo_push',
    'signature' => '766ed16833bcb9542f403ed87dfbd807'
];

$deleteData = [
    'id' => 10,
    'username' => 'hippo_push',
    'signature' => '71188241d981844544f9ea8fb189db64'
];

$loginData = [
    'user' => 'huynq1501@beetsoft.com.vn',
    'password' => 'Hippo99',
];

$registerData = [
//    'user_name' => 'huynq13',
    'fullname' => 'Nham Quang Huy',
    'email' => 'huynq14@beetsoft.com.vn',
    'password' => 'Hippo99',
    'confirm_password' => 'Hippo99',
    'phone' => '0961618660',
];

////api  create or update
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($inputData)
//    ->createOrUpdate();

//api list
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($listData)
//    ->list();

////api show
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($showData)
//    ->show();

////api delete
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($deleteData)
//    ->delete();

////api login
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($loginData)
//    ->login();

//register
$api = new WebServiceUser($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($registerData)
    ->register();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";