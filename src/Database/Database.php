<?php

namespace nguyenanhung\Backend\BaseAPI\Database;

use nguyenanhung\Backend\BaseAPI\Base\BaseCore;
use nguyenanhung\Backend\BaseAPI\Database\Traits\ConfigTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\OptionTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\SignatureTable;
use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Class Database
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class Database extends BaseCore
{
    use SignatureTable,ConfigTable,OptionTable;

    /** @var array $database */
    protected $database;

    /**
     * Database constructor.
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

    /**
     * Function setDatabase
     *
     * @param $database
     *
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 38:16
     */
    public function setDatabase($database): Database
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Function connection
     *
     * @return BaseModel
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 40:58
     */
    public function connection(): BaseModel
    {
        $DB                      = new BaseModel();
        $DB->debugStatus         = $this->options['debugStatus'];
        $DB->debugLevel          = $this->options['debugLevel'];
        $DB->debugLoggerPath     = $this->options['loggerPath'];
        $DB->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
        $DB->setDatabase($this->database);
        $DB->__construct($this->database);

        return $DB;
    }
}
