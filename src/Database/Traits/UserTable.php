<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait UserTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait UserTable
{
    protected function UserTable(): BaseModel
    {
        // connect to user table
        $table = 'beetsoft_user';
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
    public function createUser(array $data = array()): int
    {
        $DB = $this->UserTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    public function updateUser(array $data = array()): int
    {
        // connect to user table
        $DB = $this->UserTable();

        //update user
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    public function checkUserExists($id): bool
    {
        $DB = $this->UserTable();

        //create result
        $result = $DB->checkExists($id);
        $DB->disconnect();

        return $result === 1;
    }

    public function listUser(array $data = array())
    {
        // connect to user table
        $DB = $this->UserTable();

        //get user data
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
                'offset' => $data['pageNumber'],
                'orderBy' => ['id' => 'desc']
            ]
        );
        $DB->disconnect();

        return $result;
    }

    public function showUser(array $data = array())
    {
        $DB = $this->UserTable();
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
            [
                'id',
                'department_id',
                'parent',
                'username',
                'fullname',
                'email',
                'status',
                'group_id',
                'created_at',
                'updated_at'
            ]);

        $DB->disconnect();

        return $result;
    }

    public function deleteUser(array $data = array()): int
    {
        $DB = $this->UserTable();
        //delete
        $result = $DB->delete(
            [
                'id' => [
                    'field' => 'id',
                    'operator' => '=',
                    'value' => $data['id']
                ]
            ]);

        $DB->disconnect();

        return $result;
    }

    public function checkUserLogin(array $data = array())
    {
        $DB = $this->UserTable();
        //check login by user or email
        $where = ['username' => ['field' => 'username', 'operator' => '=', 'value' => $data['account']]];
        $field = 'username';
        $format = 'array';
        $select = ['username', 'email', 'salt', 'password'];
        $userName = $DB->getInfo($where, $field, $format, $select);

        $field = 'email';
        $where = ['email' => ['field' => 'email', 'operator' => '=', 'value' => $data['account']]];
        $email = $DB->getInfo($where, $field, $format, $select);

        $DB->disconnect();

        if ($userName) {
            return $userName;
        }

        if ($email) {
            return $email;
        }

        return false;
    }

}
