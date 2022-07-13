<?php

namespace nguyenanhung\Backend\Your_Project\Database\Traits;

/**
 * Trait SignatureTable
 *
 * @package   nguyenanhung\Backend\Your_Project\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait SignatureTable
{
    /**
     * Function getUserSignature
     *
     * @param string|null $nickname
     * @param int|null    $role
     *
     * @return array|bool|\Illuminate\Support\Collection|mixed|object|string|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 56:41
     */
    public function getUserSignature(string $nickname = '', int $role = null)
    {
        $table    = 'data_signature';
        $cacheKey = $this->cachePrefix . __FUNCTION__ . hash('md5', $table . $nickname . $role);
        if ($this->cache->has($cacheKey)) {
            $result = $this->cache->get($cacheKey);
        } else {
            $DB                 = $this->connection();
            $wheres             = [];
            $wheres['nickname'] = ['field' => 'nickname', 'operator' => $DB::OPERATOR_EQUAL_TO, 'value' => $nickname];
            $wheres['status']   = ['field' => 'status', 'operator' => $DB::OPERATOR_EQUAL_TO, 'value' => 1];
            if (!empty($role)) {
                $wheres['status'] = ['field' => 'role', 'operator' => $DB::OPERATOR_EQUAL_TO, 'value' => $role];
            }
            $result = $DB->setTable($table)->getInfo($wheres, 'id', null, ['nickname', 'signature', 'role']);
            $this->cache->save($cacheKey, $result);
            $DB->disconnect();
            unset($DB);
        }

        return $result;
    }
}
