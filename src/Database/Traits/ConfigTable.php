<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use Illuminate\Support\Collection;
use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait ConfigTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait ConfigTable
{
    /**
     * Connect to the config table in the database
     * @return BaseModel
     */
    protected function initConfigTable(): BaseModel
    {
        // connect to config table
        $table = 'config';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create config
     * @param array $data
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 56:41
     */
    public function createConfig(array $data = array()): bool
    {
        $DB = $this->initConfigTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result !== 0;
    }

    /**
     * Function update config
     * @param array $data
     * @return int
     */
    public function updateConfig(array $data = array()): int
    {
        // connect to config table
        $DB = $this->initConfigTable();

        //update config
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function to check config exists or not
     * @param $id
     * @return bool
     */
    public function checkConfigExists($id): bool
    {
        $DB = $this->initConfigTable();

        //create result
        $result = $DB->checkExists($id);
        $DB->disconnect();

        return $result === 1;
    }

    /**
     * Function get list config with paginate
     * @param array $data
     * @return array|Collection|object|string
     */
    public function listConfig(array $data = array())
    {
        // connect to config table
        $DB = $this->initConfigTable();
        $where = [
            'id' =>
                [
                    'field' => 'id',
                    'operator' => 'like',
                    'value' => $data['category'] . '%'
                ]
        ];
        $select = ['id', 'language', 'value', 'label', 'type', 'status'];
        $option = [
            'limit' => $data['numberRecordOfPage'],
            'offset' => $data['pageNumber'],
            'orderBy' => ['id' => 'desc']
        ];
        //get config data
        $result = $DB->getResult($where, $select, $option);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function show config by config id
     * @param array $data
     * @return array|Collection|object|string|null
     */
    public function showConfig(array $data = array())
    {
        $DB = $this->initConfigTable();
        //show result
        $where = [
            'id' => [
                'field' => 'id',
                'operator' => '=',
                'value' => $data['id']
            ]
        ];
        $select = ['id', 'language', 'value', 'label', 'type', 'status'];

        $result = $DB->getInfo($where, 'id', 'result', $select);
        $DB->disconnect();

        return $result;
    }

}
