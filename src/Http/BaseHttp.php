<?php

namespace nguyenanhung\Backend\Your_Project\Http;

use nguyenanhung\Backend\Your_Project\Base\BaseCore;
use nguyenanhung\Backend\Your_Project\Database\Database;

/**
 * Class BaseHttp
 *
 * @package   nguyenanhung\Backend\Your_Project\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class BaseHttp extends BaseCore
{
    const EXIT_CODE = [
        'success'           => 0,
        'contentIsEmpty'    => 1,
        'invalidParams'     => 2,
        'invalidSignature'  => 3,
        'outdatedSignature' => 4,
        'invalidService'    => 5,
        'paramsIsEmpty'     => 6,
    ];

    /** @var \nguyenanhung\Backend\Your_Project\Database\Database */
    protected $db;

    /**
     * BaseHttp constructor.
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
        $this->db = new Database($options);
    }

    protected function formatInputStartDate($inputData = array())
    {
        if (isset($inputData['begin_date'])) {
            $startDate = $inputData['begin_date'];
        } elseif (isset($inputData['begindate'])) {
            $startDate = $inputData['begindate'];
        } elseif (isset($inputData['start_date'])) {
            $startDate = $inputData['start_date'];
        } else {
            $startDate = null;
        }

        return $startDate;
    }

    protected function formatInputEndDate($inputData = array())
    {
        if (isset($inputData['end_date'])) {
            $endDate = $inputData['end_date'];
        } elseif (isset($inputData['enddate'])) {
            $endDate = $inputData['enddate'];
        } else {
            $endDate = null;
        }

        return $endDate;
    }

    protected function formatInputUsername($inputData = array())
    {
        if (isset($inputData['username'])) {
            $res = $inputData['username'];
        } elseif (isset($inputData['nickname'])) {
            $res = $inputData['nickname'];
        } elseif (isset($inputData['account'])) {
            $res = $inputData['account'];
        } elseif (isset($inputData['acc'])) {
            $res = $inputData['acc'];
        } else {
            $res = null;
        }

        return $res;
    }

    protected function formatInputSignature($inputData = array())
    {
        if (isset($inputData['signature'])) {
            $res = $inputData['signature'];
        } elseif (isset($inputData['signal'])) {
            $res = $inputData['signal'];
        } elseif (isset($inputData['token'])) {
            $res = $inputData['token'];
        } elseif (isset($inputData['secret_token'])) {
            $res = $inputData['secret_token'];
        } else {
            $res = null;
        }

        return $res;
    }
}
