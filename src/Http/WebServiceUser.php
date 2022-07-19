<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use Exception;
use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Libraries\Password\Hash;
use nguyenanhung\Validation\Validation;


/**
 * Class WebServiceUser
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceUser extends BaseHttp
{
    /**
     * WebServiceAccount constructor.
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

    protected const API_NAME = 'user';
    protected const DEFAULT_ID = 0;

    /**
     * @throws Exception
     */
    public function createOrUpdate(): WebServiceUser
    {
//        $value = $this->inputData['email'];
//
//        Validation::add_validator("email_beetsoft", static function () use ($value) {
//            return str_contains($value, '@beetsoft.com.vn');
//        }, 'please login with beetsoft email');

        $isValid = Validation::is_valid($this->inputData, [
            'email' => 'required|valid_email|email_beetsoft',
            'fullname' => 'required',
            'address' => 'required',
            'phone' => 'required|between_len,10;11|numeric',
        ], [
            'email' => [
                'required' => 'Fill the email field please.',
//                'valid_email' => 'Email is incorrect, please try again.'
            ],
            'fullname' => ['required' => 'Fill the fullname field please.',],
            'address' => ['required' => 'Fill the address field please.'],
            'phone' => [
                'required' => 'Fill the phone field please.',
                'between_len' => '{field} must be between {param[0]} and {param[1]} characters.',
                'numeric' => 'please enter a valid phone number'
            ],
        ]);

        if ($isValid !== true) {
            $response = array(
                'result' => self::EXIT_CODE['invalidParams'],
                'desc' => json_encode($isValid),
                'inputData' => $this->inputData
            );
        } else {
            $departmentID = (isset($this->inputData['department_id']) && is_int($this->inputData['department_id'])) ? $this->inputData['department_id'] : self::DEFAULT_ID;
            $parentID = (isset($this->inputData['parent']) && is_int($this->inputData['parent'])) ? $this->inputData['parent'] : self::DEFAULT_ID;
            $userName = empty($this->inputData['user_name']) ? implode('@',
                explode('@', $this->inputData['email'], -1)) : $this->inputData['user_name'];
            $fullName = $this->inputData['fullname'] ?? null;
            $address = $this->inputData['address'] ?? null;
            $email = $this->inputData['email'] ?? null;
            $avatar = $this->formatInputNull('avatar');
            $groupID = (isset($this->inputData['group_id']) && is_int($this->inputData['group_id'])) ? $this->inputData['group_id'] : self::DEFAULT_ID;
            $password = $this->inputData['password'];
            $resetPassword = (isset($this->inputData['reset_password']) && is_int($this->inputData['reset_password'])) ? $this->inputData['reset_password'] : 0;
            $phone = $this->inputData['phone'] ?? null;
            $note = $this->formatInputNull('note');
            $photo = $this->formatInputNull('photo');
            $thumb = $this->formatInputNull('thumb');
            $remember_token = $this->formatInputNull('remember_token');
            $status = $this->formatStatus($this->inputData);
            $google_token = empty($this->inputData['token']) ? '' : $this->inputData['google_token'];
            $google_refresh_token = empty($this->inputData['token']) ? '' : $this->inputData['google_token'];
            $username = $this->formatInputUsername($this->inputData);
            $signature = $this->formatInputSignature($this->inputData);

            // get user role
            $user = $this->db->getUserSignature($username);
            $validSignature = !empty($user) ? md5($userName . '$' . $fullName . '$' . $address . '$' . $email . '$' . $phone . '$' . $username . "$" . $user->signature) : "";

            if ($signature !== $validSignature || empty($user)) {
                $response = array(
                    'result' => self::EXIT_CODE['invalidSignature'],
                    'desc' => self::MESSAGES['invalidSignature'],
                    'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                );
            } else {
                $salt = Hash::generateUserSaltKey();
                $password = Hash::generateHashValue($password . $salt);
                $checkExitDepartment = $this->db->checkExitsRecords(['id' => $departmentID],
                    'department_structure');
                $checkExitGroup = $this->db->checkExitsRecords(['id' => $groupID],
                    'user_group');
                $checkExitParent = $this->db->checkExitsRecords(['id' => $parentID],
                    'beetsoft_user');
                $data = array(
                    'department_id' => $checkExitDepartment ? $departmentID : self::DEFAULT_ID,
                    'parent' => $checkExitParent ? $parentID : self::DEFAULT_ID,
                    'username' => $userName,
                    'fullname' => $fullName,
                    'address' => $address,
                    'email' => $email,
                    'status' => $status,
                    'avatar' => $avatar,
                    'group_id' => $checkExitGroup ? $groupID : self::DEFAULT_ID,
                    'reset_password' => $resetPassword,
                    'updated_pass' => Date('Y-m-d H:i:s'),
                    'phone' => $phone,
                    'note' => $note,
                    'photo' => $photo,
                    'thumb' => $thumb,
                    'remember_token' => $remember_token,
                    'salt' => $salt,
                    'token' => Hash::generateUserToken(),
                    'activation_key' => Hash::generateOTPCode(),
                    'created_at' => Date('Y-m-d H:i:s'),
                    'updated_at' => Date('Y-m-d H:i:s'),
                    'google_token' => $google_token,
                    'google_refresh_token' => $google_refresh_token,
                );

                if (isset($this->inputData['id'])) {
                    $id = $this->inputData['id'];
                    $checkCategoryExits = $this->db->checkUserExists(['id' => $id]);
                    if ($checkCategoryExits) {
                        $data['id'] = $id;
                        $data['updated_at'] = date("Y/m/d H:i:s");
                        $result = $this->db->updateUser($data);
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
                    $isValidPassword = Validation::is_valid($this->inputData,
                        [
                            'password' => 'required|between_len,6;32',
                        ],
                        [
                            'required' => 'Fill the password field please.',
                            'password' => ['between_len' => 'Password must be between {param[0]} and {param[1]} characters.'],
                        ]
                    );
                    if ($isValidPassword !== true) {
                        $response = array(
                            'result' => self::EXIT_CODE['invalidParams'],
                            'desc' => json_encode($isValidPassword),
                            'inputData' => $this->inputData
                        );
                    } else {
                        $uniEmail = $this->db->checkExitsRecords(['email' => $email], 'beetsoft_user');
                        $uniUserName = $this->db->checkExitsRecords(['username' => $userName], 'beetsoft_user');
                        if ($uniEmail || $uniUserName) {
                            $response = array(
                                'result' => self::EXIT_CODE['notUnique'],
                                'desc' => 'Email or username ' . self::MESSAGES['notUnique'],
                                'data' => $this->inputData,
                            );
                        } else {
                            $data['password'] = $password;
                            $id = $this->db->createUser($data);
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
        }
        $this->logger->info('WebUser.createOrUpdate',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));
        $this->response = $response;

        return $this;

    }

    public function list(): WebServiceUser
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
                $listConfig = $this->db->listUser($data);

                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => self::ACTION['create'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                    'data' => $listConfig,
                );
            }

        }

        $this->response = $response;
        $this->logger->info('WebUser.list',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));

        return $this;
    }

    public function show(): WebServiceUser
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
                $validSignature = !empty($user) ? md5($id . "$" . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $result = $this->db->showUser(['id' => $id]);

                    if ($result) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => self::ACTION['read'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                            'data' => json_encode($result),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => self::MESSAGES['notFound'],
                            'inputData' => $this->inputData
                        );
                    }
                }
            }
        }

        $this->response = $response;
        $this->logger->info('WebUser.show',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));

        return $this;
    }

    public function delete(): WebServiceUser
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
                $validSignature = !empty($user) ? md5($id . "$" . $username . "$" . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $result = $this->db->deleteUser(['id' => $id]);

                    if ($result) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => self::ACTION['delete'] . ' ' . self::API_NAME . ' - ' . self::MESSAGES['success'],
                            'data' => json_encode($result),
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => self::MESSAGES['notFound'],
                            'inputData' => $this->inputData
                        );
                    }
                }
            }
        }

        $this->response = $response;
        $this->logger->info('WebUser.delete',
            'Input data: ' . json_encode($this->inputData) . ' -> Response: ' . json_encode($response));

        return $this;
    }
}
