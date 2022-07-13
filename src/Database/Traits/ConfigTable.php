<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait SignatureTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait ConfigTable
{
    protected function configTable(): BaseModel
    {
        // connect to config table
        $table = 'config';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create
     *
     * @param array $data
     *
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 56:41
     */
    public function createConfig(array $data = array()): bool
    {
        $DB = $this->configTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result !== 0;
    }

    public function updateConfig(array $data = array()): int
    {
        // connect to config table
        $DB = $this->configTable();

        //update config
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    public function checkConfigExists($id): bool
    {
        $DB = $this->configTable();

        //create result
        $result = $DB->checkExists($id);
        $DB->disconnect();

        return $result === 1;
    }

    public function listConfig(array $data = array())
    {
        // connect to config table
        $DB = $this->configTable();

        //get config data
        $result = $DB->getResult(
            [
                'where' =>
                    [
                        'field' => 'id',
                        'operator' => 'like',
                        'value' => $data['category'] . '%'
                    ]
            ],
            '*',
            [
                'limit' => $data['numberRecordOfPage'],
                'offset' => ($data['pageNumber'] - 1) * $data['numberRecordOfPage'],
                'orderBy' => ['id' => 'desc']
            ]
        );
        $DB->disconnect();

        return $result;
    }

    public function showConfig(array $data = array()){
        $DB = $this->configTable();

        //show result
        $result = $DB->getResult(
            [
                'id' => ['field' => 'id', 'operator' => 'like', 'value' => $data['category'] . '%']
            ],
            '*',null);
    }

}
