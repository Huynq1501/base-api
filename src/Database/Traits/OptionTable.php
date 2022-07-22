<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use Illuminate\Support\Collection;
use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait OptionTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait OptionTable
{
    /**
     * Connect to the option table in the database
     * @return BaseModel
     */
    protected function initOptionTable(): BaseModel
    {
        // connect to Option table
        $table = 'option';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create option
     * @param array $data
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 56:41
     */
    public function createOption(array $data = array()): int
    {
        $DB = $this->initOptionTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function update option
     * @param array $data
     * @return int
     */
    public function updateOption(array $data = array()): int
    {
        // connect to option table
        $DB = $this->initOptionTable();

        //update option
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function to check option exists or not
     * @param $where
     * @return bool
     */
    public function checkOptionExists($where): bool
    {
        $DB = $this->initOptionTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result === 1;
    }

    /**
     * Function get list option with paginate
     * @param array $data
     * @return array|Collection|object|string
     */
    public function listOption(array $data = array())
    {
        // connect to option table
        $DB = $this->initOptionTable();
        //get option data
        $select = ['id', 'name', 'value', 'status', 'created_at'];
        $option = [
            'limit' => $data['numberRecordOfPage'],
            'offset' => $data['pageNumber'],
            'orderBy' => ['id' => 'desc']
        ];
        $result = $DB->getResult(array(), $select, $option);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function show option by option id
     * @param array
     * @return array|Collection|object|string|null
     */
    public function showOption(array $data = array())
    {
        $DB = $this->initOptionTable();
        //show result
        $where = [
            'id' => [
                'field' => 'id',
                'operator' => '=',
                'value' => $data['id']
            ]
        ];
        $select = ['id', 'name', 'value', 'status', 'created_at'];

        $result = $DB->getInfo($where, 'id', 'result', $select);
        $DB->disconnect();

        return $result;
    }

}
