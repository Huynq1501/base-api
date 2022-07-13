<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Libraries\Slug\SlugUrl;

/**
 * Class WebServiceAccount
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceConfig extends BaseHttp
{
    public const TYPE = array(
        'string' => 0,
        'number' => 1,
        'json' => 2,
    );

    public const STATUS = array(
        'deactivate' => 0,
        'active' => 1,
    );

    public const PAGINATE = array(
        'page_number' => 1,
        'number_of_records' => 10,
    );

    protected $slug;

    /**
     * WebServiceConfig constructor.
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


    protected function formatType($inputData = array()): int
    {
        if (in_array($inputData['type'], self::TYPE, true)) {
            return $inputData['type'];
        }

        return 0;
    }

    protected function formatStatus($inputData = array()): int
    {
        if (in_array($inputData['status'], self::STATUS, true)) {
            return $inputData['status'];
        } else {
            return 1;
        }
    }

    public function formatPageNumber($inputData = array())
    {
        if (isset($inputData['page_number']) && $inputData['page_number'] > 1) {
            return $inputData['page_number'];
        }

        return self::PAGINATE['page_number'];
    }

    public function formatNumberRecordOfPage($inputData = array())
    {
        if (isset($inputData['number_record_of_pages']) && $inputData['number_record_of_pages'] > 0) {
            return $inputData['number_record_of_pages'];
        }

        return self::PAGINATE['number_record_of_pages'];
    }

    public function createOrUpdate(): WebServiceConfig
    {
        $required = ['id', 'value', 'language'];

        $filter = Filter::filterInputDataIsArray($this->inputData, $required);
        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => 'sai hoặc thiếu tham số',
                'inputData' => $this->inputData
            );
        } else {
            $id = $this->inputData['id'] ?? null;
            $value = $this->inputData['value'] ?? null;
            $language = $this->inputData['language'] ?? null;
            $label = $this->inputData['label'] ?? null;
            $type = $this->formatType($this->inputData);
            $status = $this->formatStatus($this->inputData);
            $username  = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);
            if (empty($id) || empty($language) || empty($value)|| empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user           = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($id . '$' . $value . '$' . $language . "$" . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc'   => 'Sai chu ky xac thuc.',
                        'valid'  => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                }
                else{
                    $id = $this->slug->slugify($id, '_');
                    $data = array(
                        'id' => $id,
                        'language' => $language,
                        'value' => $value,
                        'label' => $label,
                        'type' => $type,
                        'status' => $status,
                    );
                    $wheres = array(
                        'id' => $id,
                        'language' => $language,
                    );
                    $checkDuplicateId = $this->db->checkConfigExists($wheres);

                    if ($checkDuplicateId) {
                        $result = $this->db->updateConfig($data);
                        if ($result) {
                            $response = array(
                                'result' => self::EXIT_CODE['success'],
                                'desc' => 'Đã ghi nhận update config thành công',
                                'update_id' => $data['id'],
                            );
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['success'],
                                'desc' => 'Không có gì thay đổi',
                            );
                        }
                    } else {
                        $this->db->createConfig($data);
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => 'Đã ghi nhận config thành công',
                            'insert_id' => $data['id'],
                        );
                    }
                }
            }
        }
        $this->logger->info('WebConfig.createOrUpdate',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;

    }

    public function list(): WebServiceConfig
    {
        $filter = Filter::filterInputDataIsArray($this->inputData, ['category']);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => 'sai hoặc thiếu tham số',
                'inputData' => $this->inputData
            );
        } else {
            $category = $this->inputData['category'] ?? null;
            $pageNumber = $this->formatPageNumber($this->inputData);
            $numberRecordOfPage = $this->formatNumberRecordOfPage($this->inputData);

            if (empty($category)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                $data = array(
                    'category' => $category,
                    'pageNumber' => $pageNumber,
                    'numberRecordOfPage' => $numberRecordOfPage,
                );
                $listConfig = $this->db->listConfig($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => 'Danh sách config',
                    'data' => $listConfig,
                );
            }
        }

        $this->response = $response;

        return $this;
    }

    public function show(): WebServiceConfig
    {
        $filter = Filter::filterInputDataIsArray($this->inputData, ['id', 'language']);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => 'sai hoặc thiếu tham số',
                'inputData' => $this->inputData
            );
        } else {
            $id = $this->inputData['id'] ?? null;
            $language = $this->inputData['language'] ?? null;

            if (empty($id) || empty($language)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                // Đoạn id này em đang phân vân không biết có nên format không.
                $id = $this->slug->slugify($this->inputData['id'], '_');

                $data = array(
                    'id' => $id,
                    'language' => $language,
                );
                $result = $this->db->showConfig($data);

                if ($result->count() === 1) {
                    $response = array(
                        'result' => self::EXIT_CODE['success'],
                        'desc' => 'Thành công',
                        'data' => json_encode($result[0]),
                    );
                } else {
                    $response = array(
                        'result' => self::EXIT_CODE['notFound'],
                        'desc' => 'Không tồn tại config',
                        'inputData' => $this->inputData
                    );
                }
            }
        }

        $this->response = $response;

        return $this;
    }
}
