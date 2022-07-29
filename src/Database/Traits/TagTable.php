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
    /**
     * Connect to the tag table in the database
     * @return BaseModel
     */
    protected function initTagTable(): BaseModel
    {
        // connect to Tag table
        $table = 'tag';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create tag
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
        $DB = $this->initTagTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function update tag
     * @param array $data
     * @return int
     */
    public function updateTag(array $data = array()): int
    {
        // connect to tag table
        $DB = $this->initTagTable();

        //update tag
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function to check tag exists or not
     * @param $where
     * @return bool
     */
    public function checkTagExists($where): bool
    {
        $DB = $this->initTagTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result === 1;
    }

    /**
     * Function get list tag with paginate
     * @param array $data
     * @return object|array|string
     */
    public function listTag(array $data = array()): object|array|string
    {
        // connect to tag table
        $DB = $this->initTagTable();
        //get tag data
        $select = [
            'id',
            'name',
            'is_hot',
            'slugs',
            'language',
            'title',
            'status',
            'description',
            'keywords',
            'photo',
            'created_at',
            'updated_at'
        ];
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
     * Function show tag by tag id
     * @param array $data
     * @return object|array|string|null
     */
    public function showTag(array $data = array()): object|array|string|null
    {
        $DB = $this->initTagTable();
        //show result
        $where = [
            'id' => [
                'field' => 'id',
                'operator' => '=',
                'value' => $data['id']
            ]
        ];
        $select = [
            'id',
            'name',
            'is_hot',
            'slugs',
            'language',
            'title',
            'status',
            'description',
            'keywords',
            'photo',
            'created_at',
            'updated_at'
        ];
        $result = $DB->getInfo($where, 'id', 'result', $select);
        $DB->disconnect();

        return $result;
    }

}
