<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait DepartmentTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait DepartmentTable
{
    protected function DepartmentTable(): BaseModel
    {
        // connect to department table
        $table = 'department_structure';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    public function checkDepartmentExists($id): bool
    {
        $DB = $this->DepartmentTable();

        //create result
        $result = $DB->checkExists($id);
        $DB->disconnect();

        return $result === 1;
    }

}
