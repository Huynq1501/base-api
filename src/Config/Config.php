<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:27
 */

namespace nguyenanhung\Backend\Your_Project\Config;

/**
 * Class Config
 *
 * @package   nguyenanhung\Backend\Your_Project\Config
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class Config
{
    public static function configGlobal()
    {
        return DataRepository::getData('config_global');
    }

    public static function configItem($item)
    {
        $config = self::configGlobal();
        if (isset($config[$item])) {
            return $config[$item];
        }

        return null;
    }
}
