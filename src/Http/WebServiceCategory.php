<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Libraries\Slug\SlugUrl;
use nguyenanhung\Classes\Helper\UUID;

/**
 * Class WebServiceCategory
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceCategory extends BaseHttp
{
    public const STATUS_LEVEL = array(0, 1);

    public const PAGINATE = array(
        'page_number' => 1,
        'number_of_records' => 10,
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

    function formatStatusAndLevel($inputData = array(), $field): int
    {
        if (isset($inputData[$field]) && in_array($inputData[$field], self::STATUS_LEVEL, true)) {
            return $inputData[$field];
        }

        return 1;
    }

    function formatLanguage($inputData = array())
    {
        if (isset($inputData['language']) && $inputData['language'] != null) {
            return $inputData['is_hot'];
        }

        return self::DEFAULT_LANGUAGE;
    }

    function formatParentID($inputData = array()): int
    {
        if (isset($inputData['parent']) && $inputData['parent'] != null && is_int($inputData['parent'])) {
            $checkExits = $this->db->checkCategoryExists(
                [
                    'id' => $inputData['parent']
                ]
            );
            if ($checkExits = 1) {
                return $inputData['parent'];
            }
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
            $slug = $this->inputData['name'];
        }
        return $this->slug->convertVietnameseToEnglish($slug);
    }

    /**
     * @param array $inputData
     * @param $field
     * @return mixed
     */
    function formatDescriptionAndKeywords(array $inputData = array(), $field)
    {
        if (isset($inputData[$field]) && $inputData[$field] != null) {
            return $this->inputData[$field];
        }

        return $this->inputData['title'];
    }

    function formatShow(array $inputData = array(), $field)
    {
        if (isset($inputData[$field]) && $inputData[$field] != null && in_array($inputData[$field], self::STATUS_LEVEL)) {
            return $this->inputData[$field];
        }

        return 0;
    }

    function formatOrderStatus($inputData = array())
    {
        if (isset($inputData['order_status']) && $inputData['order_status'] != null && is_int($inputData['order_status'])) {
            return $this->inputData['order_status'];
        }

        return 0;
    }

    function formatNumberRecordOfPage($inputData = array())
    {
        if (isset($inputData['number_record_of_pages']) && $inputData['number_record_of_pages'] > 0) {
            return $inputData['number_record_of_pages'];
        }

        return self::PAGINATE['number_record_of_pages'];
    }

    public function createOrUpdate(): WebServiceCategory
    {
        $required = ['name', 'title', 'parent'];
        $filter = Filter::filterInputDataIsArray($this->inputData, $required);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => 'sai hoặc thiếu tham số',
                'inputData' => $this->inputData
            );
        } else {
            $status = $this->formatStatusAndLevel($this->inputData, 'status');
            $name = $this->inputData['name'] ?? null;
            $slugs = $this->formatSlug($this->inputData);
            $language = $this->formatLanguage($this->inputData);
            $title = $this->inputData['title'] ?? null;
            $description = $this->formatDescriptionAndKeywords($this->inputData, 'description');
            $keywords = $this->formatDescriptionAndKeywords($this->inputData, 'keywords');
            $oderStatus = $this->formatOrderStatus($this->inputData);
            $parent = $this->formatParentID($this->inputData);
            $photo = $this->inputData['photo'] ?? null;
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);
            $showTop = $this->formatShow($this->inputData, 'show_top');
            $showHome = $this->formatShow($this->inputData, 'show_home');
            $showRight = $this->formatShow($this->inputData, 'show_right');
            $showBottom = $this->formatShow($this->inputData, 'show_bottom');
            $level = $this->formatStatusAndLevel($this->inputData, 'level');

            if (empty($name) || empty($title) || empty($keywords) || empty($description) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . '$' . $title . '$' . $keywords . '$' . $description . '$' . $parent . '$' . $username . "$" . $user->signature) : "";

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
                        'name' => $name,
                        'language' => $language,
                        'slugs' => $slugs,
                        'title' => $title,
                        'description' => $description,
                        'keywords' => $keywords,
                        'photo' => $photo,
                        'parent'=>$parent,
                        'order_stt'=>$oderStatus,
                        'show_top'=>$showTop,
                        'show_home'=>$showHome,
                        'show_right'=>$showRight,
                        'show_bottom'=>$showBottom,
                        'level'=>$level,
                    );

                    if (isset($this->inputData['id'])) {
                        $id = $this->inputData['id'] ;
                        
                        $checkCategoryExits = $this->db->checkCategoryExists(['id'=>$id]);
                        if ($checkCategoryExits) {
                            $data['id'] = $id;
                            $data['updated_at'] = date("Y/m/d H:i:s");
                            $result = $this->db->updateCategory($data);
                            if ($result) {
                                $response = array(
                                    'result' => self::EXIT_CODE['success'],
                                    'desc' => 'Đã ghi nhận update category thành công',
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
                                'desc' => 'Không tồn tại category',
                                'data' => $this->inputData,
                            );
                        }
                    } else {
                        $data['created_at'] = date("Y/m/d H:i:s");
                        //Đoạn này db đang k cho updated_at null , em k rõ là a quên chỉnh hay nó có ý nghĩa gì đó=> em pdate sau
                        $data['updated_at'] = date("Y/m/d H:i:s");
                        $id = $this->db->createCategory($data);

                        if ($id > 0) {
                            $response = array(
                                'result' => self::EXIT_CODE['success'],
                                'desc' => 'Đã ghi nhận category thành công',
                                'insert_id' => $id,
                            );
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => 'Ghi nhận category thất bại',
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

    public function list(): WebServiceCategory
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
                $listConfig = $this->db->listCategory($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => 'Danh sách category',
                    'data' => $listConfig,
                );
            }

        }

        $this->response = $response;

        return $this;
    }

    public function show(): WebServiceCategory
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
                    $result = $this->db->showCategory(array('id' => $id));

                    if ($result->count() === 1) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => 'Đã nhận category thành công',
                            'data' => json_encode($result[0]),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => 'Không tồn tại category',
                        );
                    }
                }
            }
        }

        $this->response = $response;

        return $this;
    }

}
