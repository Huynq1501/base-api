<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Classes\Helper\UUID;

/**
 * Class WebServiceTopic
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceTopic extends BaseHttp
{
    public const IS_HOT = array(
        'normal' => 0,
        'hot' => 1,
    );
    public const DEFAULT_LANGUAGE = 'vietnamese';

    protected const API_NAME = 'topic';

    /**
     * WebServiceOption constructor.
     *
     * @param array $options
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->logger->setLoggerSubPath(__CLASS__);
    }

    protected function formatIsHot($inputData = array()): int
    {
        if (isset($inputData['is_hot']) && in_array($inputData['is_hot'], self::IS_HOT, true)) {
            return $inputData['is_hot'];
        }

        return 0;
    }

    protected function formatPhoto($inputData = array())
    {
        if (isset($inputData['photo'])) {
            return json_encode(
                [
                    'photo' => $inputData['photo']
                ]
            );
        }

        return null;
    }

    public function createOrUpdate(): WebServiceTopic
    {
        $required = ['name', 'tittle', 'keywords', 'photo'];
        $filter = Filter::filterInputDataIsArray($this->inputData, $required);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $status = $this->formatStatus($this->inputData);
            $isHot = $this->formatIsHot($this->inputData);
            $name = $this->inputData['name'] ?? null;
            $slugs = $this->slug->slugify($this->formatInput('slugs', 'tittle'));
            $language = $this->inputData['language'] ?? self::DEFAULT_LANGUAGE;
            $tittle = $this->inputData['tittle'] ?? null;
            $keywords = $this->inputData['keywords'] ?? null;
            $description = $this->formatInput('description', 'tittle');
            $content = $this->formatInput('content', 'tittle');
            $photo = $this->formatPhoto($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            if (empty($name) || $status === null || empty($slugs) || empty($language) || empty($tittle) || empty($keywords) || empty($photo) || empty($description) || empty($content) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . '$' . $tittle . '$' . $keywords . '$' . $photo . '$' . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $data = array(
                        'uuid' => UUID::v4(),
                        'status' => $status,
                        'is_hot' => $isHot,
                        'name' => $name,
                        'slugs' => $slugs,
                        'language' => $language,
                        'title' => $tittle,
                        'description' => $description,
                        'keywords' => $keywords,
                        'content' => $content,
                        'photo' => $photo,
                    );

                    if (isset($this->inputData['id'])) {
                        $id = $this->inputData['id'] ?? null;

                        $wheres = array(
                            'id' => $id,
                        );
                        $checkTopicExits = $this->db->checkTopicExists($wheres);
                        if ($checkTopicExits) {
                            $data['id'] = $id;
                            $data['updated_at'] = date("Y/m/d H:i:s");
                            $result = $this->db->updateTopic($data);
                            if ($result) {
                                $response = array(
                                    'result' => self::EXIT_CODE['success'],
                                    'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                                    'update_id' => $id,
                                );
                            } else {
                                $response = array(
                                    'result' => self::EXIT_CODE['notChange'],
                                    'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['notChange'],
                                );
                            }
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['notFound'],
                                'data' => $this->inputData,
                            );
                        }
                    } else {
                        $data['created_at'] = date("Y/m/d H:i:s");
                        $data['view_total'] = 0;
                        $data['view_day'] = 0;
                        $data['view_week'] = 0;
                        $data['view_month'] = 0;
                        $data['view_year'] = 0;

                        $id = $this->db->createTopic($data);

                        if ($id > 0) {
                            $response = array(
                                'result' => self::EXIT_CODE['success'],
                                'desc' => self::ACTION['create'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                                'insert_id' => $id,
                            );
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => self::ACTION['create'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['failed'],
                                'inputData' => $this->inputData,
                            );
                        }
                    }
                }
            }
        }
        $this->logger->info('WebTopic.createOrUpdate',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;

    }

    public function list(): WebServiceTopic
    {
        $pageNumber = $this->formatPageNumber($this->inputData);
        $numberRecordOfPage = $this->formatMaxResult($this->inputData);
        $username = $this->formatInputUsername($this->inputData);
        $signature = $this->formatInputSignature($this->inputData);

        if (empty($signature) || empty($username)) {
            $response = array(
                'result' => self::EXIT_CODE['paramsIsEmpty'],
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $user = $this->db->getUserSignature($username);
            $validSignature = !empty($user) ? md5($username . "$" . $user->signature) : "";

            if ($signature !== $validSignature || empty($user)) {
                $response = array(
                    'result' => self::EXIT_CODE['invalidSignature'],
                    'desc' => self::MESSAGES['invalidSignature'],
                    'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                );
            } else {
                $data = array(
                    'pageNumber' => $pageNumber,
                    'numberRecordOfPage' => $numberRecordOfPage,
                );
                $listConfig = $this->db->listTopic($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => self::ACTION['getAll'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                    'data' => $listConfig,
                );
            }

        }
        $this->logger->info('WebTopic.list',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;
    }

    public function show(): WebServiceTopic
    {
        $filter = Filter::filterInputDataIsArray($this->inputData, ['id']);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $id = $this->inputData['id'] ?? null;
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);
            if (empty($id) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($id . '$' . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $result = $this->db->showTopic(array('id' => $id));

                    if ($result) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => self::ACTION['read'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                            'data' => json_encode($result),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => self::ACTION['read'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['notFound'],
                        );
                    }
                }
            }
        }
        $this->logger->info('WebTopic.show',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;
    }

}
