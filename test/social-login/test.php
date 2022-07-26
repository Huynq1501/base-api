<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */

use nguyenanhung\Backend\BaseAPI\Http\WebServiceSSOLogin;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../config.php';

$state = 'github';

//api  login
$api = new WebServiceSSOLogin($config['OPTIONS']);
$api->setSdkConfig($config)
    ->setState($state)
    ->login();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";