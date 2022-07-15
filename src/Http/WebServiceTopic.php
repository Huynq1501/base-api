<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Libraries\Slug\SlugUrl;
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
    public const STATUS = array(
        'deactivate' => 0,
        'active' => 1,
    );

    public const PAGINATE = array(
        'page_number' => 1,
        'number_of_records' => 10,
    );

    public const IS_HOT = array(
        'normal' => 0,
        'hot' => 1,
    );
    public const DEFAULT_LANGUAGE = 'vietnamese';

    protected $slug;

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
        $this->slug = new SlugUrl();
    }

    protected function formatStatus($inputData = array()): int
    {
        if (isset($inputData['status']) && in_array($inputData['status'], self::STATUS, true)) {
            return $inputData['status'];
        }

        return 1;
    }

    protected function formatIsHot($inputData = array()): int
    {
        if (isset($inputData['is_hot']) && in_array($inputData['is_hot'], self::IS_HOT, true)) {
            return $inputData['is_hot'];
        }

        return 0;
    }

    function formatPageNumber($inputData = array())
    {
        if (isset($inputData['page_number']) && $inputData['page_number'] > 0) {
            return $inputData['page_number'];
        }

        return self::PAGINATE['page_number'];
    }

    function formatSlug($inputData = array()): string
    {
        if (isset($inputData['slugs']) && $inputData['slugs'] != null) {
            $slug = $this->inputData['slugs'];
        } else {
            $slug = $this->inputData['tittle'];
        }
        return $this->slug->convertVietnameseToEnglish($slug);
    }

    function formatDescription($inputData = array())
    {
        if (isset($inputData['description']) && $inputData['description'] != null) {
            return $this->inputData['description'];
        }

        return $this->inputData['tittle'];
    }

    function formatContent($inputData = array())
    {
        if (isset($inputData['content']) && $inputData['content'] != null) {
            return $this->inputData['content'];
        }

        return $this->inputData['tittle'];
    }


    function formatNumberRecordOfPage($inputData = array())
    {
        if (isset($inputData['number_record_of_pages']) && $inputData['number_record_of_pages'] > 0) {
            return $inputData['number_record_of_pages'];
        }

        return self::PAGINATE['number_record_of_pages'];
    }

    function formatPhoto($inputData = array())
    {
        if (isset($inputData['photo']) && $inputData['photo'] != null) {
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
                'desc' => 'sai hoặc thiếu tham số',
                'inputData' => $this->inputData
            );
        } else {
            $status = $this->formatStatus($this->inputData);
            $isHot = $this->formatIsHot($this->inputData);
            $name = $this->inputData['name'];
            $slugs = $this->formatSlug($this->inputData);
            $language = $this->inputData['language'] ?? self::DEFAULT_LANGUAGE;
            $tittle = $this->inputData['tittle'] ?? null;
            $keywords = $this->inputData['keywords'] ?? null;
            $description = $this->formatDescription($this->inputData);
            $content = $this->formatContent($this->inputData);
            $photo = $this->formatPhoto($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            if (empty($name) || empty($slugs) || empty($language) || empty($tittle) || empty($keywords) || empty($photo) || empty($description) || empty($content) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . '$' . $tittle . '$' . $keywords . '$' . $photo . '$' . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => 'Sai chu ky xac thuc.',
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
                        'view_total' => 0,
                        'view_day' => 0,
                        'view_week' => 0,
                        'view_month' => 0,
                        'view_year' => 0,
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
                                    'desc' => 'Đã ghi nhận update topic thành công',
                                    'update_id' => $id,
                                );
                            } else {
                                $response = array(
                                    'result' => self::EXIT_CODE['notFound'],
                                    'desc' => 'Không có gì thay đổi',
                                );
                            }
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => 'Không tồn tại topic',
                                'data' => $this->inputData,
                            );
                        }
                    } else {
                        $data['created_at'] = date("Y/m/d H:i:s");
                        $id = $this->db->createTopic($data);

                        if ($id > 0) {
                            $response = array(
                                'result' => self::EXIT_CODE['success'],
                                'desc' => 'Đã ghi nhận topic thành công',
                                'insert_id' => $id,
                            );
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => 'Ghi nhận topic thất bại',
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

    public function list():WebServiceTopic{
        $pageNumber = $this->formatPageNumber($this->inputData);
        $numberRecordOfPage = $this->formatNumberRecordOfPage($this->inputData);
        $username = $this->formatInputUsername($this->inputData);
        $signature = $this->formatInputSignature($this->inputData);

        if (empty($signature) || empty($username)) {
            $response = array(
                'result' => self::EXIT_CODE['paramsIsEmpty'],
                'desc' => 'Sai hoac thieu tham so.',
                'inputData' => $this->inputData
            );
        } else {
            $user = $this->db->getUserSignature($username);
            $validSignature = !empty($user) ? md5($username . "$" . $user->signature) : "";

            if ($signature !== $validSignature || empty($user)) {
                $response = array(
                    'result' => self::EXIT_CODE['invalidSignature'],
                    'desc' => 'Sai chu ky xac thuc.',
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
                    'desc' => 'Danh sách topic',
                    'data' => $listConfig,
                );
            }

        }

        $this->response = $response;

        return $this;
    }

    public function show(): WebServiceTopic
    {
        $filter = Filter::filterInputDataIsArray($this->inputData, ['id']);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => 'sai hoặc thiếu tham số',
                'inputData' => $this->inputData
            );
        } else {
            $id = $this->inputData['id'] ?? null;
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);
            if (empty($id) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($id . '$' . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => 'Sai chu ky xac thuc.',
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $result = $this->db->showTopic(array('id'=>$id));

                    if ($result->count() === 1) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => 'Đã nhận topic thành công',
                            'data' => json_encode($result[0]),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => 'Không tồn tại topic',
                        );
                    }
                }
            }
        }

        $this->response = $response;

        return $this;
    }

}
