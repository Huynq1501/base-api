<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Libraries\Slug\SlugUrl;
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

    protected function formatLanguage($inputData = array())
    {
        if (isset($inputData['language']) && $inputData['language'] != null) {
            return $inputData['language'];
        }

        return self::DEFAULT_LANGUAGE;
    }

    protected function formatByField($inputData = array(), $field)
    {
        if (isset($inputData[$field]) && $inputData[$field] != null) {
            return $this->inputData[$field];
        }

        return $this->inputData['name'];
    }

    protected function formatPageNumber($inputData = array())
    {
        if (isset($inputData['page_number']) && $inputData['page_number'] > 0) {
            return $inputData['page_number'];
        }

        return self::PAGINATE['page_number'];
    }

    protected function formatSlug($inputData = array()): string
    {
        if (isset($inputData['slugs']) && $inputData['slugs'] != null) {
            $slug = $this->inputData['slugs'];
        } else {
            $slug = $this->inputData['name'];
        }
        return $this->slug->convertVietnameseToEnglish($slug);
    }

    protected function formatDescription($inputData = array())
    {
        if (isset($inputData['description']) && $inputData['description'] != null) {
            return $this->inputData['description'];
        }

        return $this->inputData['tittle'];
    }

    protected function formatContent($inputData = array())
    {
        if (isset($inputData['content']) && $inputData['content'] != null) {
            return $this->inputData['content'];
        }

        return $this->inputData['tittle'];
    }

    protected function formatNumberRecordOfPage($inputData = array())
    {
        if (isset($inputData['number_record_of_pages']) && $inputData['number_record_of_pages'] > 0) {
            return $inputData['number_record_of_pages'];
        }

        return self::PAGINATE['number_record_of_pages'];
    }

    protected function formatPhoto($inputData = array())
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

    public function createOrUpdate(): WebServiceTag
    {
        $required = ['name', 'photo'];
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
            $name = $this->inputData['name'] ?? null;
            $slugs = $this->formatSlug($this->inputData);
            $language = $this->formatLanguage($this->inputData);
            $keywords = $this->formatByField($this->inputData, 'keywords');
            $title = $this->formatByField($this->inputData, 'title');
            $description = $this->formatByField($this->inputData, 'description');
            $photo = $this->formatPhoto($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            if (empty($name) || empty($slugs) || empty($language) || empty($title) || empty($keywords) || empty($photo) || empty($description) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . '$' . $language . '$' . $title . '$' . $keywords . '$' . $photo . '$' . $username . "$" . $user->signature) : "";

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
                                    'desc' => 'Đã ghi nhận update tag thành công',
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
                                'desc' => 'Không tồn tại tag',
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
                                'desc' => 'Đã ghi nhận tag thành công',
                                'insert_id' => $id,
                            );
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => 'Ghi nhận tag thất bại',
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

    public function list(): WebServiceTag
    {
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
                $listConfig = $this->db->listTag($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => 'Danh sách tag',
                    'data' => $listConfig,
                );
            }

        }

        $this->response = $response;

        return $this;
    }

    public function show(): WebServiceTag
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
                    $result = $this->db->showTag(array('id' => $id));

                    if ($result->count() === 1) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => 'Đã nhận tag thành công',
                            'data' => json_encode($result[0]),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => 'Không tồn tại tag',
                        );
                    }
                }
            }
        }

        $this->response = $response;

        return $this;
    }

}
