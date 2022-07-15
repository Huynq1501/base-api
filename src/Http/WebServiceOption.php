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
class WebServiceOption extends BaseHttp
{
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
        if (in_array($inputData['status'], self::STATUS, true)) {
            return $inputData['status'];
        }

        return 1;
    }

    public function formatPageNumber($inputData = array())
    {
        if (isset($inputData['page_number']) && $inputData['page_number'] > 0) {
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

    public function createOrUpdate(): WebServiceOption
    {
        $required = ['name', 'value'];
        $filter = Filter::filterInputDataIsArray($this->inputData, $required);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => 'sai hoặc thiếu tham số',
                'inputData' => $this->inputData
            );
        } else {
            $name = $this->inputData['name'] ?? null;
            $value = $this->inputData['value'] ?? null;
            $status = $this->formatStatus($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            if (empty($name) || empty($status) || empty($value) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => 'Sai hoac thieu tham so.',
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . '$' . $value . '$' . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => 'Sai chu ky xac thuc.',
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $name = $this->slug->slugify($name, '_');
                    $data = array(
                        'name' => $name,
                        'value' => $value,
                        'status' => $status,
                        'created_at' => date("Y/m/d H:i:s"),
                    );
                    $wheres = array(
                        'name' => $name,
                    );
                    $checkDuplicateName = $this->db->checkOptionExists($wheres);

                    if ($checkDuplicateName) {
                        $filter = Filter::filterInputDataIsArray($this->inputData, ['id']);
                        if ($filter === false) {
                            $response = array(
                                'result' => self::EXIT_CODE['invalidParams'],
                                'desc' => 'sai hoặc thiếu tham số',
                                'inputData' => $this->inputData
                            );
                        } else {
                            $id = $this->inputData['id'] ?? null;
                            $data['id'] = $id;
                            $result = $this->db->updateOption($data);

                            if ($result) {
                                $response = array(
                                    'result' => self::EXIT_CODE['success'],
                                    'desc' => 'Đã ghi nhận update option thành công',
                                    'update_id' => $id,
                                );
                            } else {
                                $response = array(
                                    'result' => self::EXIT_CODE['success'],
                                    'desc' => 'Không có gì thay đổi',
                                );
                            }
                        }

                    } else {
                        $id = $this->db->createOption($data);

                        if ($id > 0) {
                            $response = array(
                                'result' => self::EXIT_CODE['success'],
                                'desc' => 'Đã ghi nhận option thành công',
                                'insert_id' => $id,
                            );
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notFound'],
                                'desc' => 'Ghi nhận option thất bại',
                                'insert_id' => $id,
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

    public function list(): WebServiceOption
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
                $listConfig = $this->db->listOption($data);

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

    public function show(): WebServiceOption
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
                    $result = $this->db->showOption(array('id'=>$id));

                    if ($result->count() === 1) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => 'Đã nhận option thành công',
                            'data' => json_encode($result[0]),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => 'Không tồn tại option',
                        );
                    }
                }
            }
        }

        $this->response = $response;

        return $this;
    }
}
