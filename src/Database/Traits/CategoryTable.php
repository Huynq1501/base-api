<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

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
    protected function categoryTable(): BaseModel
    {
        // connect to Category table
        $table = 'category';
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
    public function createCategory(array $data = array()): int
    {
        $DB = $this->categoryTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    public function updateCategory(array $data = array()): int
    {
        // connect to category table
        $DB = $this->categoryTable();

        //update category
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    public function checkCategoryExists($where): bool
    {
        $DB = $this->categoryTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result;
    }

    public function listCategory(array $data = array())
    {
        // connect to category table
        $DB = $this->categoryTable();
        //get category data
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

    public function showCategory(array $data = array())
    {
        $DB = $this->categoryTable();
        //show result
        $result = $DB->getInfo(
            [
                'id' => [
                    'field' => 'id',
                    'operator' => '=',
                    'value' => $data['id']
                ]
            ],
            'id',
            'array',
            ['id','uuid','name','language','slugs','title','description','keywords','photo','parent','order_stt']);

        $DB->disconnect();

        return $result;
    }

}
