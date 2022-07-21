<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use Illuminate\Support\Collection;
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
    /**
     * Connect to the user table in the database
     * @return BaseModel
     */
    protected function initUserTable(): BaseModel
    {
        // connect to user table
        $table = 'beetsoft_user';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create user
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
        $DB = $this->initUserTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function update user
     * @param array $data
     * @return int
     */
    public function updateUser(array $data = array()): int
    {
        // connect to user table
        $DB = $this->initUserTable();

        //update user
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function check user exist or not by user id
     * @param $id
     * @return bool
     */
    public function checkUserExists($id): bool
    {
        $DB = $this->initUserTable();

        //create result
        $result = $DB->checkExists($id);
        $DB->disconnect();

        return $result === 1;
    }

    /**
     * Function get list user with paginate
     * @param array $data
     * @return array|Collection|object|string
     */
    public function listUser(array $data = array())
    {
        // connect to user table
        $DB = $this->initUserTable();

        //get user data
        $where = [
            'id' =>
                [
                    'field' => 'id',
                    'operator' => 'like',
                    'value' => $data['category'] . '%'
                ]
        ];
        $select = [
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
     * Function show user
     * @param array $data
     * @return array|Collection|object|string|null
     */
    public function showUser(array $data = array())
    {
        $DB = $this->initUserTable();
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
            'department_id',
            'parent',
            'username',
            'fullname',
            'email',
            'status',
            'group_id',
            'created_at',
            'updated_at'
        ];

        $result = $DB->getInfo($where, 'id', 'array', $select);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function delete user
     * @param array $data
     * @return int
     */
    public function deleteUser(array $data = array()): int
    {
        $DB = $this->initUserTable();
        //delete
        $where = [
            'id' => [
                'field' => 'id',
                'operator' => '=',
                'value' => $data['id']
            ]
        ];
        $result = $DB->delete($where);

        $DB->disconnect();

        return $result;
    }

    /**
     * Function to check if the login account matches the username or email in the database
     * @param array $data
     * @return array|false|Collection|object|string
     */
    public function checkUserLogin(array $data = array())
    {
        $DB = $this->initUserTable();
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
