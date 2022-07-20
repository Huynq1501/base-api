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
class WebServiceConfig extends BaseHttp
{
    protected const API_NAME = 'config';

    protected const TYPE = array(
        'string' => 0,
        'number' => 1,
        'json' => 2,
    );

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
    }

    protected function formatType($inputData = array()): int
    {
        if (in_array($inputData['type'], self::TYPE, true)) {
            return $inputData['type'];
        }

        return 0;
    }

    public function createOrUpdate(): WebServiceConfig
    {
        $required = ['id', 'value', 'language'];

        $filter = Filter::filterInputDataIsArray($this->inputData, $required);
        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $id = $this->inputData['id'] ?? null;
            $value = $this->inputData['value'] ?? null;
            $language = $this->inputData['language'] ?? null;
            $label = $this->inputData['label'] ?? null;
            $type = $this->formatType($this->inputData);
            $status = $this->formatStatus($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);
            if (empty($id) || $status === null || empty($language) || empty($value) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($id . '$' . $value . '$' . $language . "$" . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
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
                                'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                                'update_id' => $data['id'],
                            );
                        } else {
                            $response = array(
                                'result' => self::EXIT_CODE['notChange'],
                                'desc' => self::ACTION['update'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['notChange'],
                                'input_Data' => $this->inputData,
                            );
                        }
                    } else {
                        $this->db->createConfig($data);
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => self::ACTION['create'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
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
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $category = $this->inputData['category'] ?? null;
            $pageNumber = $this->formatPageNumber($this->inputData);
            $numberRecordOfPage = $this->formatMaxResult($this->inputData);
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            if (empty($category) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($category . "$" . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
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
                        'desc' => self::ACTION['create'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                        'data' => $listConfig,
                    );
                }

            }
        }

        $this->response = $response;
        $this->logger->info('WebConfig.list',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));

        return $this;
    }

    public function show(): WebServiceConfig
    {
        $required = ['id', 'language'];
        $filter = Filter::filterInputDataIsArray($this->inputData, $required);

        if ($filter === false) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => self::MESSAGES['invalidParams'],
                'inputData' => $this->inputData
            );
        } else {
            $id = $this->inputData['id'] ?? null;
            $language = $this->inputData['language'] ?? null;
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);
            if (empty($id) || empty($language) || empty($signature) || empty($username)) {
                $response = array(
                    'result' => self::EXIT_CODE['paramsIsEmpty'],
                    'desc' => self::MESSAGES['invalidParams'],
                    'inputData' => $this->inputData
                );
            } else {
                // Request User Roles
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($id . '$' . $language . "$" . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $id = $this->slug->slugify($this->inputData['id'], '_');

                    $data = array(
                        'id' => $id,
                        'language' => $language,
                    );
                    $result = $this->db->showConfig($data);

                    if ($result) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => self::ACTION['read'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                            'data' => json_encode($result),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => self::MESSAGES['success'],
                            'inputData' => $this->inputData
                        );
                    }
                }
            }
        }

        $this->response = $response;
        $this->logger->info('WebConfig.show',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));

        return $this;
    }
}
