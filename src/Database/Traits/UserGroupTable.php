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
trait UserGroupTable
{
    protected function UserGroupTable(): BaseModel
    {
        // connect to department table
        $table = 'user_group';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    public function checkUserGroupExists($id): bool
    {
        $DB = $this->UserGroupTable();

        //create result
        $result = $DB->checkExists($id);
        $DB->disconnect();

        return $result === 1;
    }


}
