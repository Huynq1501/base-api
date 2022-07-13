<?php

namespace nguyenanhung\Backend\Your_Project\Http;

/**
 * Class WebServiceAccount
 *
 * @package   nguyenanhung\Backend\Your_Project\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceAccount extends BaseHttp
{
    /**
     * WebServiceAccount constructor.
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

    public function register(): WebServiceAccount
    {
        $data           = [
            'code'        => self::EXIT_CODE['success'],
            'description' => 'Success'
        ];
        $this->response = $data;

        return $this;
    }

    public function login(): WebServiceAccount
    {
        $data           = [
            'code'        => self::EXIT_CODE['success'],
            'description' => 'Success'
        ];
        $this->response = $data;

        return $this;
    }

    public function logout(): WebServiceAccount
    {
        $data           = [
            'code'        => self::EXIT_CODE['success'],
            'description' => 'Success'
        ];
        $this->response = $data;

        return $this;
    }

    public function updatePassword(): WebServiceAccount
    {
        $data           = [
            'code'        => self::EXIT_CODE['success'],
            'description' => 'Success'
        ];
        $this->response = $data;

        return $this;
    }

    public function forgotPassword(): WebServiceAccount
    {
        $data           = [
            'code'        => self::EXIT_CODE['success'],
            'description' => 'Success'
        ];
        $this->response = $data;

        return $this;
    }
}
