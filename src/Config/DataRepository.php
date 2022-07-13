<?php

namespace nguyenanhung\Backend\Your_Project\Config;

/**
 * Class DataRepository
 *
 * @package   nguyenanhung\Backend\Your_Project\Config
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class DataRepository
{
    const CONFIG_PATH = 'config';
    const CONFIG_EXT  = '.php';

    /**
     * Hàm lấy nội dung config được quy định trong thư mục config
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 9/28/18 14:47
     *
     * @param string $configName Tên file config
     *
     * @return array|mixed
     */
    public static function getData(string $configName)
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . self::CONFIG_PATH . DIRECTORY_SEPARATOR . $configName . self::CONFIG_EXT;
        if (is_file($path) && file_exists($path)) {
            return require $path;
        }

        return array();
    }
}
