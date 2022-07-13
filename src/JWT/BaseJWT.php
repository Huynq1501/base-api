<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:38
 */

namespace nguyenanhung\Backend\Your_Project\JWT;

use nguyenanhung\Backend\Your_Project\Base\BaseCore;

/**
 * Class BaseJWT
 *
 * @package   nguyenanhung\Backend\Your_Project\JWT
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class BaseJWT extends BaseCore
{
    /**
     * BaseJWT constructor.
     *
     * @param array $options
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->logger->setLoggerSubPath(__CLASS__);
    }
}
