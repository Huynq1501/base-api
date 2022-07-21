<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use Illuminate\Support\Collection;
use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait CategoryTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait CategoryTable
{
    /**
     * Connect to the category table in the database
     * @return BaseModel
     */
    protected function initCategoryTable(): BaseModel
    {
        // connect to Category table
        $table = 'category';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create category
     *
     * @param array $data
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 56:41
     */
    public function createCategory(array $data = array()): int
    {
        $DB = $this->initCategoryTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function update category
     * @param array $data
     * @return int
     */
    public function updateCategory(array $data = array()): int
    {
        // connect to category table
        $DB = $this->initCategoryTable();

        //update category
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function to check category exists or not
     * @param $where
     * @return bool
     */
    public function checkCategoryExists($where): bool
    {
        $DB = $this->initCategoryTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function get list category with paginate
     * @param array $data
     * @return array|Collection|object|string
     */
    public function listCategory(array $data = array())
    {
        // connect to category table
        $DB = $this->initCategoryTable();
        //get category data

        $where = array();
        $select = [
            'id',
            'name',
            'language',
            'slugs',
            'title',
            'description',
            'keywords',
            'photo',
            'level',
            'status',
            'created_at',
            'updated_at'
        ];
        $option = [
            'limit' => $data['numberRecordOfPage'],
            'offset' => $data['pageNumber'],
            'orderBy' => ['id' => 'desc']
        ];
        $result = $DB->getResult($where, $select, $option);

        $DB->disconnect();

        return $result;
    }

    /**
     * Function show category by category id
     * @param array $data
     * @return array|Collection|object|string|null
     */
    public function showCategory(array $data = array())
    {
        $DB = $this->initCategoryTable();
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
            'uuid',
            'name',
            'language',
            'slugs',
            'title',
            'description',
            'keywords',
            'photo',
            'parent',
            'order_stt'
        ];

        $result = $DB->getInfo($where, 'id', 'array', $select);
        $DB->disconnect();

        return $result;
    }

}
