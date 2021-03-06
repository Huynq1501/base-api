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
//    'id' => 30,
//    'department_id' => 10,
//    'parent' => 4,
//    'status' => 1,
//    'user_name' => 'huynq1501',
    'fullname' => 'Nham Quang Huy',
    'address' => 'addresss',
    'email' => 'huynq15111111101@beetsoft.com.vn',
//    'avatar' => '3432a2da78d5e5530b7ff890b5f14233',
//    'group_id' => 4,
//    'password' => 'Hippo99',
//    'reset_password'=>,
    'phone' => '0961618660',
//    'note' => 'note',
//    'photo'=>'aaaaa',
//    'thumb'=>'',
//    'remember_token' => '111111',
//    'google_token' => '',
//    'google_refresh_token' => '',
    'username' => 'hippo_push',
    'signature' => '7671f360aaa9bc0d80cb6622536a1abe'
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
    'user' => 'hippo99',
    'password' => 'Hippo99',
];

$registerData = [
//    'user_name' => 'huynq150111',
    'fullname' => 'Nham Quang Huy',
    'email' => 'huynq15011111@beetsoft.com.vn',
    'password' => 'Hippo99',
    'confirm_password' => 'Hippo99',
    'phone' => '0961618660',
];

//api  create or update
$api = new WebServiceUser($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($inputData)
    ->createOrUpdate();

//api list
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($listData)
//    ->list();
//
////api show
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($showData)
//    ->show();
//
////api delete
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($deleteData)
//    ->delete();
//
//api login
$api = new WebServiceUser($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($loginData)
    ->userLogin();
////
////register
//$api = new WebServiceUser($config['OPTIONS']);
//$api->setSdkConfig($config);
//$api->setInputData($registerData)
//    ->userRegister();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";