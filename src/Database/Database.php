<?php

namespace nguyenanhung\Backend\BaseAPI\Database;

use nguyenanhung\Backend\BaseAPI\Base\BaseCore;
use nguyenanhung\Backend\BaseAPI\Database\Traits\CategoryTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\ConfigTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\OptionTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\SignatureTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\TagTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\TopicTable;
use nguyenanhung\Backend\BaseAPI\Database\Traits\UserTable;
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
    use SignatureTable,ConfigTable,OptionTable,TopicTable,CategoryTable,TagTable,UserTable;

    /** @var array $database */
    protected array $table;
    protected array $database;

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

    public function checkExitsRecords($wheres , $tableName): bool
    {
        $table = $tableName;
        $DB = $this->connection();
        $DB->setTable($table);

        $result = $DB->checkExists($wheres);
        $DB->disconnect();

        return $result === 1;
    }
}
