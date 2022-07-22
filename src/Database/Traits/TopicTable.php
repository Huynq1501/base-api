<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

use Illuminate\Support\Collection;
use nguyenanhung\MyDatabase\Model\BaseModel;

/**
 * Trait TopicTable
 *
 * @package   nguyenanhung\Backend\BaseAPI\Database\Traits
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait TopicTable
{
    /**
     * Connect to the topic table in the database
     * @return BaseModel
     */
    protected function initTopicTable(): BaseModel
    {
        // connect to Topic table
        $table = 'topic';
        $DB = $this->connection();
        $DB->setTable($table);

        return $DB;
    }

    /**
     * Function create topic
     * @param array $data
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 22/06/2022 56:41
     */
    public function createTopic(array $data = array()): int
    {
        $DB = $this->initTopicTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function update topic
     * @param array $data
     * @return int
     */
    public function updateTopic(array $data = array()): int
    {
        // connect to topic table
        $DB = $this->initTopicTable();

        //update topic
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    /**
     * Function to check topic exists or not
     * @param $where
     * @return bool
     */
    public function checkTopicExists($where): bool
    {
        $DB = $this->initTopicTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result === 1;
    }

    /**
     * Function get list topic with paginate
     * @param array $data
     * @return array|Collection|object|string
     */
    public function listTopic(array $data = array())
    {
        // connect to topic table
        $DB = $this->initTopicTable();
        //get topic data
        $select = ['id', 'name', 'is_hot', 'slugs', 'title', 'status', 'description', 'content', 'keywords', 'photo'];
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
     * Function show topic by topic id
     * @param array $data
     * @return array|Collection|object|string|null
     */
    public function showTopic(array $data = array())
    {
        $DB = $this->initTopicTable();
        //show result
        $where = [
            'id' => [
                'field' => 'id',
                'operator' => '=',
                'value' => $data['id']
            ]
        ];
        $select = ['id', 'name', 'is_hot', 'slugs', 'title', 'status', 'description', 'content', 'keywords', 'photo'];
        $result = $DB->getInfo($where, 'id', 'result', $select);
        $DB->disconnect();

        return $result;
    }

}
