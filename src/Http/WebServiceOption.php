<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use nguyenanhung\Classes\Helper\Filter;

/**
 * Class WebServiceAccount
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceOption extends BaseHttp
{
    protected const API_NAME = 'option';

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

    public function createOrUpdate(): WebServiceOption
    {
        $required = ['name', 'value', 'status'];
        $filter = Filter::filterInputDataIsArray($this->inputData, $required);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $name = $this->inputData['name'] ?? null;
            $value = $this->inputData['value'] ?? null;
            $status = $this->formatStatus($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            if (empty($name) || $status === null || empty($value) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($name . self::KEY . $value . self::KEY . $username . self::KEY . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
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
                                'desc' => self::MESSAGES['invalidParams'],
                                'inputData' => $this->inputData
                            );
                        } else {
                            $id = $this->inputData['id'] ?? null;
                            $data['id'] = $id;
                            $result = $this->db->updateOption($data);

                            if ($result) {
                                $response = array(
                                    'result' => self::EXIT_CODE['success'],
                                    'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                                    'update_id' => $id,
                                );
                            } else {
                                $response = array(
                                    'result' => self::EXIT_CODE['notFound'],
                                    'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['failed'],
                                    'data' => $this->inputData
                                );
                            }
                        }

                    } else {
                        $id = $this->db->createOption($data);

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
            $validSignature = !empty($user) ? md5($username . self::KEY . $user->signature) : "";

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
                $listConfig = $this->db->listOption($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => self::ACTION['getAll'] . ' ' . self::API_NAME . '-' . self::MESSAGES['success'],
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
                $validSignature = !empty($user) ? md5($id . self::KEY . $username . self::KEY . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $result = $this->db->showOption(array('id' => $id));

                    if ($result) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => 'Đã nhận option thành công',
                            'data' => json_encode($result),
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
