<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait TagTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait TagTable
{
    protected function tagTable(): BaseModel
    {
        // connect to Tag table
        $table = 'tag';
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
    public function createTag(array $data = array()): int
    {
        $DB = $this->tagTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    public function updateTag(array $data = array()): int
    {
        // connect to tag table
        $DB = $this->tagTable();

        //update tag
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    public function checkTagExists($where): bool
    {
        $DB = $this->tagTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result === 1;
    }

    public function listTag(array $data = array())
    {
        // connect to tag table
        $DB = $this->tagTable();
        //get tag data
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

    public function showTag(array $data = array())
    {
        $DB = $this->tagTable();
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
