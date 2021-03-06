<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Backend\BaseAPI\Base\BaseCore;
use nguyenanhung\Backend\BaseAPI\Database\Database;
use nguyenanhung\Libraries\Slug\SlugUrl;

/**
 * Class BaseHttp
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class BaseHttp extends BaseCore
{
    public const EXIT_CODE = [
        'success' => 0,
        'contentIsEmpty' => 1,
        'invalidParams' => 2,
        'invalidSignature' => 3,
        'outdatedSignature' => 4,
        'invalidService' => 5,
        'paramsIsEmpty' => 6,
        'duplicatePrimaryKey' => 7,
        'notFound' => 8,
        'notChange' => 9,
        'notUnique' => 10,
        'failed' => 11,
    ];

    public const PAGINATE = array(
        'page_number' => 1,
        'max_results' => 10,
    );

    public const STATUS_LEVEL = [0, 1];

    public const PREFIX_AUTH = '$';

    public const MESSAGES = array(
        'invalidSignature' => 'Sai chu ky xac thuc',
        'success' => 'Ghi nhan thanh cong',
        'failed' => 'Ghi nhan that bai',
        'invalidParams' => 'Sai hoac thieu tham so',
        'duplicate' => 'Duplicate value',
        'notFound' => 'Khong ton tai ban ghi tuong ung',
        'notChange' => 'Update that bai, data khong thay doi',
        'notUnique' => 'Da ton tai, hay thu lai',
    );

    public const ACTION = array(
        'create' => 'create',
        'getAll' => 'list',
        'update' => 'update',
        'read' => 'show',
        'delete' => 'delete',
        'login' => 'login',
        'register' => 'register',
    );

    public const STATUS = array(
        'deactivate' => 0,
        'active' => 1,
        'wait_active' => 2,
    );

    public const SHOW_STATUS = array(
        'deactivate' => 0,
        'active' => 1,
    );

    /** @var Database|SlugUrl */
    protected SlugUrl|Database $slug;
    protected Database $db;

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
        $this->slug = new SlugUrl();
    }

    protected function formatInputStartDate($inputData = array()): mixed
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

    /**
     * function format username login
     * @param array $inputData
     * @return mixed|null
     */
    protected function formatInputUsername(array $inputData = array()): mixed
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

    /**
     * Function get signature
     * @param array $inputData
     * @return mixed|null
     */
    protected function formatInputSignature(array $inputData = array()): mixed
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

    /**
     * Function format page number
     * @param array $inputData
     * @return int|mixed
     */
    protected function formatPageNumber(array $inputData = array()): mixed
    {
        if (isset($inputData['page_number']) && $inputData['page_number'] > 0) {
            return $inputData['page_number'];
        }

        return self::PAGINATE['page_number'];
    }

    /**
     * Function format max result in page
     * @param array $inputData
     * @return int|mixed
     */
    protected function formatMaxResult(array $inputData = array()): mixed
    {
        if (isset($inputData['max_results']) && $inputData['max_results'] > 0) {
            return $inputData['max_results'];
        }

        return self::PAGINATE['max_results'];
    }

    /**
     * Function format status
     * @param array $inputData
     * @return int|null
     */
    protected function formatStatus(array $inputData = array()): ?int
    {
        if (!empty($inputData['status']) && in_array($inputData['status'], self::STATUS)) {
            return $inputData['status'];
        }

        return self::STATUS['active'];
    }

    /**
     * Function format show or hidden
     * @param array $inputData
     * @param $field
     * @return int
     */
    public function formatShow(array $inputData = array(), $field): int
    {
        if (isset($inputData[$field]) && in_array($inputData[$field], self::SHOW_STATUS)) {
            return $inputData[$field];
        }

        return self::SHOW_STATUS['deactivate'];
    }

    /**
     * Function format if param 1 does not exist, assign param 1 equal to param 2
     * @param $first
     * @param $second
     * @return string
     */
    public function formatInput($first, $second): string
    {
        if (isset($this->inputData[$first])) {
            $res = $this->inputData[$first];
        } elseif (isset($this->inputData[$second])) {
            $res = $this->inputData[$second];
        } else {
            $res = '';
        }

        return ($res);
    }

    /**
     * Declared function if not initialized input returns null
     * @param $field
     * @return mixed|null
     */
    public function formatInputNull($field): mixed
    {
        return empty($this->inputData[$field]) ? null : $this->inputData[$field];
    }

}
