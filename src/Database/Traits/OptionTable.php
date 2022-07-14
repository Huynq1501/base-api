<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

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
    protected function optionTable(): BaseModel
    {
        // connect to Option table
        $table = 'option';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create
     *
     * @param array $data
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 56:41
     */
    public function createOption(array $data = array()): int
    {
        $DB = $this->optionTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    public function updateOption(array $data = array()): int
    {
        // connect to option table
        $DB = $this->optionTable();

        //update option
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    public function checkOptionExists($where): bool
    {
        $DB = $this->optionTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result === 1;
    }

    public function listOption(array $data = array())
    {
        // connect to option table
        $DB = $this->optionTable();
        //get option data
        $result = $DB->getResult(
            [],
            '*',
            [
                'limit' => $data['numberRecordOfPage'],
                'offset' => $data['pageNumber'],
                'orderBy' => ['id' => 'desc']
            ]
        );

        $DB->disconnect();

        return $result;
    }

    public function showOption(array $data = array())
    {
        $DB = $this->optionTable();
        //show result
        $result = $DB->getResult(
            [
                'id' => [
                    'field' => 'id',
                    'operator' => '=',
                    'value' => $data['id']
                ]
            ],
            '*');

        $DB->disconnect();

        return $result;
    }

}
