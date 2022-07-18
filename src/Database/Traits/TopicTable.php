<?php

namespace nguyenanhung\Backend\BaseAPI\Database\Traits;

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
    protected function topicTable(): BaseModel
    {
        // connect to Topic table
        $table = 'topic';
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
    public function createTopic(array $data = array()): int
    {
        $DB = $this->topicTable();

        //create result
        $result = $DB->add($data);
        $DB->disconnect();

        return $result;
    }

    public function updateTopic(array $data = array()): int
    {
        // connect to topic table
        $DB = $this->topicTable();

        //update topic
        $result = $DB->update($data, $data['id']);
        $DB->disconnect();

        return $result;
    }

    public function checkTopicExists($where): bool
    {
        $DB = $this->topicTable();

        //create result
        $result = $DB->checkExists($where);
        $DB->disconnect();

        return $result === 1;
    }

    public function listTopic(array $data = array())
    {
        // connect to topic table
        $DB = $this->topicTable();
        //get topic data
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

    public function showTopic(array $data = array())
    {
        $DB = $this->topicTable();
        //show result
        $result = $DB->getInfo([
            'id' => [
                'field' => 'id',
                'operator' => '=',
                'value' => $data['id']
            ]
        ],
            'id',
            'array',
            ['id', 'name', 'is_hot', 'slugs', 'title', 'status', 'description', 'content', 'keywords', 'photo']);

        $DB->disconnect();

        return $result;
    }

}
