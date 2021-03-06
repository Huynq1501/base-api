<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;
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
    public const API_NAME = 'category';

    public const STATUS_LEVEL = array(0, 1);

    protected const DEFAULT_ORDER_STATUS = 0;
    protected const DEFAULT_LANGUAGE = 'vietnamese';

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
     * format category parent id
     * @param array $inputData
     * @return int
     */
    protected function formatParentID(array $inputData = array()): int
    {
        if (isset($inputData['parent'])) {
            $checkExits = $this->db->checkCategoryExists(
                [
                    'id' => $inputData['parent']
                ]
            );
            if ((int)$checkExits === 1) {
                return $inputData['parent'];
            }
        }
        return 0;
    }

    /**
     * create or update category
     * @return $this
     */
    public function createOrUpdate(): WebServiceCategory
    {
        $required = ['name', 'title', 'parent', 'status'];
        $filter = Filter::filterInputDataIsArray($this->inputData, $required);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $status = $this->formatStatus($this->inputData);
            $name = $this->inputData['name'] ?? null;
            $slugs = $this->slug->slugify($this->formatInput('slugs', 'name'));
            $language = empty($this->inputData['language']) ? self::DEFAULT_LANGUAGE : $this->inputData['language'];
            $title = $this->inputData['title'] ?? null;
            $description = $this->slug->slugify($this->formatInput('description', 'title'));
            $keywords = $this->slug->slugify($this->formatInput('keywords', 'title'));
            $orderStatus = empty($this->inputData['order_status']) ? self::DEFAULT_ORDER_STATUS : $this->inputData['order_status'];
            $parent = $this->formatParentID($this->inputData);
            $photo = $this->inputData['photo'] ?? null;
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);
            $showTop = $this->formatShow($this->inputData, 'show_top');
            $showHome = $this->formatShow($this->inputData, 'show_home');
            $showRight = $this->formatShow($this->inputData, 'show_right');
            $showBottom = $this->formatShow($this->inputData, 'show_bottom');
            $level = $this->formatStatus($this->inputData);

            if (empty($name) || empty($title) || empty($keywords) || empty($description) || $status === null || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . self::PREFIX_AUTH . $title . self::PREFIX_AUTH . $keywords . self::PREFIX_AUTH . $description . self::PREFIX_AUTH . $parent . self::PREFIX_AUTH . $username . self::PREFIX_AUTH . $user->signature) : "";

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
                        'name' => $name,
                        'language' => $language,
                        'slugs' => $slugs,
                        'title' => $title,
                        'description' => $description,
                        'keywords' => $keywords,
                        'photo' => $photo,
                        'parent' => $parent,
                        'order_stt' => $orderStatus,
                        'show_top' => $showTop,
                        'show_home' => $showHome,
                        'show_right' => $showRight,
                        'show_bottom' => $showBottom,
                        'level' => $level,
                    );

                    if (isset($this->inputData['id'])) {
                        $id = $this->inputData['id'];

                        $checkCategoryExits = $this->db->checkCategoryExists(['id' => $id]);
                        if ($checkCategoryExits) {
                            $data['id'] = $id;
                            $data['updated_at'] = date("Y/m/d H:i:s");
                            $result = $this->db->updateCategory($data);
                            if ($result) {
                                $response = array(
                                    'result' => self::EXIT_CODE['success'],
                                    'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                                    'update_id' => $id,
                                );
                            } else {
                                $response = array(
                                    'result' => self::EXIT_CODE['notFound'],
                                    'desc' => self::MESSAGES['notFound'],
                                    'inputData' => $this->inputData,
                                );
                            }
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => self::MESSAGES['notFound'],
                                'data' => $this->inputData,
                            );
                        }
                    } else {
                        $data['created_at'] = date("Y/m/d H:i:s");
                        //??o???n n??y db ??ang k cho updated_at null , em k r?? l?? a qu??n ch???nh hay n?? c?? ?? ngh??a g?? ????=> em pdate sau
                        $data['updated_at'] = date("Y/m/d H:i:s");
                        $id = $this->db->createCategory($data);

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
        $this->logger->info('WebCategory.createOrUpdate',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;

    }

    /**
     * Function list category with paginate
     * @return $this
     */
    public function list(): WebServiceCategory
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
                $listConfig = $this->db->listCategory($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => self::ACTION['getAll'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                    'data' => $listConfig,
                );
            }
        }
        $this->logger->info('WebCategory.list',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;
    }

    /**
     * view detail category
     * @return $this
     */
    public function show(): WebServiceCategory
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
                    $result = $this->db->showCategory(array('id' => $id));

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
                            'input_data' => $this->inputData,
                        );
                    }
                }
            }
        }
        $this->logger->info('WebCategory.show',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;
    }

}
