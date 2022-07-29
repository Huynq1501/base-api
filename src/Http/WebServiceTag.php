<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Classes\Helper\UUID;

/**
 * Class WebServiceTag
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceTag extends BaseHttp
{
    public const STATUS = array(
        'deactivate' => 0,
        'active' => 1,
    );

    public const IS_HOT = array(
        'normal' => 0,
        'hot' => 1,
    );

    protected const API_NAME = 'tag';

    public const DEFAULT_LANGUAGE = 'vietnamese';

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

    /**
     * Function format field is_hot tag
     * @param array $inputData
     * @return int
     */
    protected function formatIsHot(array $inputData = array()): int
    {
        if (isset($inputData['is_hot']) && in_array($inputData['is_hot'], self::IS_HOT)) {
            return $inputData['is_hot'];
        }

        return 0;
    }

    /**
     * Function format photo
     * @param array $inputData
     * @return false|string|null
     */
    protected function formatPhoto(array $inputData = array()): bool|string|null
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

    /**
     * Function create or update tag
     * @return $this
     */
    public function createOrUpdate(): WebServiceTag
    {
        $required = ['name', 'photo', 'status'];
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
            $slugs = $this->slug->slugify($this->formatInput('slugs', 'name'));
            $language = $this->inputData['language'] ?? self::DEFAULT_LANGUAGE;
            $keywords = $this->formatInput('keywords', 'name');
            $title = $this->formatInput('title', 'name');
            $description = $this->formatInput('description', 'name');
            $photo = $this->formatPhoto($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            if (empty($name) || $status === null || empty($slugs) || empty($language) || empty($title) || empty($keywords) || empty($photo) || empty($description) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . self::PREFIX_AUTH . $language . self::PREFIX_AUTH . $title . self::PREFIX_AUTH . $keywords . self::PREFIX_AUTH . $photo . self::PREFIX_AUTH . $username . self::PREFIX_AUTH . $user->signature) : "";

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
                        'title' => $title,
                        'description' => $description,
                        'keywords' => $keywords,
                        'photo' => $photo,
                    );

                    if (isset($this->inputData['id'])) {
                        $id = $this->inputData['id'] ?? null;

                        $wheres = array(
                            'id' => $id,
                        );
                        $checkTagExits = $this->db->checkTagExists($wheres);
                        if ($checkTagExits) {
                            $data['id'] = $id;
                            $data['updated_at'] = date("Y/m/d H:i:s");
                            $result = $this->db->updateTag($data);
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
                                    'input_Data' => $this->inputData,
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
                        $id = $this->db->createTag($data);

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
        $this->logger->info('WebConfig.createOrUpdate',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;

    }

    /**
     * Function list tag with paginate
     * @return $this
     */
    public function list(): WebServiceTag
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
            $validSignature = !empty($user) ? md5($username . self::PREFIX_AUTH . $user->signature) : "";

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
                $listConfig = $this->db->listTag($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => self::ACTION['getAll'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                    'data' => $listConfig,
                );
            }

        }

        $this->response = $response;

        return $this;
    }

    /**
     * Function view detail tag
     * @return $this
     */
    public function show(): WebServiceTag
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
                $validSignature = !empty($user) ? md5($id . self::PREFIX_AUTH . $username . self::PREFIX_AUTH . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $result = $this->db->showTag(array('id' => $id));

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

        $this->response = $response;

        return $this;
    }

}
