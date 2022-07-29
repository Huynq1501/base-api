<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use Exception;
use nguyenanhung\Classes\Helper\Filter;
use nguyenanhung\Libraries\Password\Hash;
use nguyenanhung\Libraries\Password\Password;
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

    protected const MES_AUTH = array(
        'notFound' => 'Account does not exist, please try again',
        'inCorrect' => 'Account or password is incorrect, please try again',
        'success' => ' successfully',
    );

    /**
     * Function format username
     * @param array $inputData
     * @return mixed|string
     */
    protected function formatUserName(array $inputData = array()): mixed
    {
        if (empty($inputData['user_name'])) {
            return implode('@', explode('@', $inputData['email'], -1));
        }
        return $inputData['user_name'];
    }

    /**
     * Function create or update user
     * @return $this
     */
    public function createOrUpdate(): WebServiceUser
    {
//        $value = $this->inputData['email'];
//
//        Validation::add_validator("email_beetsoft", static function () use ($value) {
//            return str_contains($value, '@beetsoft.com.vn');
//        }, 'please login with beetsoft email');
        try {
            $isValid = Validation::is_valid($this->inputData, [
                'email' => 'required|valid_email',
                'fullname' => 'required',
                'address' => 'required',
                'phone' => 'required|between_len,10;11|numeric',
            ], [
                'email' => [
                    'required' => 'Fill the email field please.',
                    'valid_email' => 'Email is incorrect, please try again.'
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
                $userName = $this->formatUserName($this->inputData);
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
                $google_token = empty($this->inputData['google_token']) ? '' : $this->inputData['google_token'];
                $google_refresh_token = empty($this->inputData['google_refresh_token']) ? '' : $this->inputData['google_refresh_token'];
                $username = $this->formatInputUsername($this->inputData);
                $signature = $this->formatInputSignature($this->inputData);

                // get user role
                $user = $this->db->getUserSignature($username);
                $validSignature = !empty($user) ? md5($userName . self::PREFIX_AUTH . $fullName . self::PREFIX_AUTH . $address . self::PREFIX_AUTH . $email . self::PREFIX_AUTH . $phone . self::PREFIX_AUTH . $username . self::PREFIX_AUTH . $user->signature) : "";

                if ($signature !== $validSignature || empty($user)) {
                    $response = array(
                        'result' => self::EXIT_CODE['invalidSignature'],
                        'desc' => self::MESSAGES['invalidSignature'],
                        'valid' => (isset($this->options['showSignature']) && $this->options['showSignature'] === true) ? $validSignature : null
                    );
                } else {
                    $salt = Hash::generateUserSaltKey();
                    $password = Password::hashPassword($password . $salt);
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
        } catch (Exception $e) {
            $this->logger->error('WebUser.createOrUpdate', $e->getMessage());
            $this->response = null;

            return $this;
        }
    }

    /**
     * Function list user with paginate
     * @return $this
     */
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

    /**
     * Function view detail user
     * @return $this
     */
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
                $validSignature = !empty($user) ? md5($id . self::PREFIX_AUTH . $username . self::PREFIX_AUTH . $user->signature) : "";

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

    /**
     * Function delete user
     * @return $this
     */
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
                $validSignature = !empty($user) ? md5($id . self::PREFIX_AUTH . $username . self::PREFIX_AUTH . $user->signature) : "";

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

    /**
     * Function login user
     * @return $this
     */
    public function userLogin(): WebServiceUser
    {

        $inputData = $this->inputData;
        try {
            $isValid = Validation::is_valid($inputData, [
                'user' => 'required',
                'password' => 'required',
            ], [
                'user' => ['required' => 'Fill the account field please.'],
                'password' => ['required' => 'Fill the password field please.'],
            ]);

            if ($isValid !== true) {
                $response = array(
                    'result' => self::EXIT_CODE['invalidParams'],
                    'desc' => json_encode($isValid),
                    'inputData' => $inputData
                );
            } else {
                $result = $this->db->checkUserLogin(['account' => $inputData['user']])? $this->db->checkUserLogin(['account' => $inputData['user']])[0]:false;
                // check account exists in the database
                if (!$result) {
                    $response = array(
                        'result' => self::EXIT_CODE['notFound'],
                        'desc' => self::MES_AUTH['notFound'],
                        'inputData' => $inputData
                    );
                } else {
                    $password = $inputData['password'] . $result->salt;

                    if (Password::verifyPassword($password, $result->password)) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => self::ACTION['login'] . self::MES_AUTH['success'],
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => self::MES_AUTH['inCorrect'],
                            'inputData' => $inputData
                        );
                    }
                }
            }

            $this->logger->info('WebAuth.login',
                'Input data: ' . json_encode($inputData) . ' -> Response: ' . json_encode($response));
            $this->response = $response;

            return $this;
        } catch (Exception $e) {
            $this->logger->error('WebAuth.login', $e->getMessage());
            $this->response = null;

            return $this;
        }
    }

    /**
     * Function register user
     * @return $this
     */
    public function userRegister(): WebServiceUser
    {
        $inputData = $this->inputData;
        try {
            $isValid = Validation::is_valid($inputData, [
                'fullname' => 'required',
                'email' => 'required|valid_email',
                'password' => 'required|between_len,6;32',
                'confirm_password' => 'required|equalsfield,password',
                'phone' => 'required|between_len,10;11|numeric',
            ], [
                'fullname' => ['required' => 'Fill the fullname field please.'],
                'email' => [
                    'required' => 'Fill the email field please.',
                    'valid_email' => 'Email is incorrect, please try again.'
                ],
                'password' => [
                    'required' => 'Fill the password field please.',
                    'between_len' => 'Password must be between {param[0]} and {param[1]} characters.'
                ],
                'confirm_password' => [
                    'required' => 'Fill the confirm password field please.',
                ],
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
                    'inputData' => $inputData
                );
            } else {
                $salt = Hash::generateUserSaltKey();
                $userName = $this->formatUserName($inputData);
                $data = array(
                    'department_id' => empty($inputData['department_id']) ? self::DEFAULT_ID : $inputData['department_id'],
                    'parent' => empty($inputData['parent']) ? self::DEFAULT_ID : $inputData['parent'],
                    'username' => $userName,
                    'fullname' => $inputData['fullname'],
                    'address' => empty($inputData['address']) ? '' : $inputData,
                    'email' => $inputData['email'],
                    'status' => self::STATUS['wait_active'],
                    'avatar' => null,
                    'group_id' => self::DEFAULT_ID,
                    'password' => Password::hashPassword($inputData['password'] . $salt),
                    'reset_password' => 0,
                    'updated_pass' => Date('Y-m-d H:i:s'),
                    'phone' => $inputData['phone'],
                    'note' => null,
                    'photo' => null,
                    'thumb' => null,
                    'remember_token' => null,
                    'salt' => $salt,
                    'token' => Hash::generateUserToken(),
                    'activation_key' => Hash::generateOTPCode(),
                    'created_at' => Date('Y-m-d H:i:s'),
                    'updated_at' => Date('Y-m-d H:i:s'),
                    'google_token' => empty($inputData['google_token']) ? '' : $inputData['google_token'],
                    'google_refresh_token' => empty($inputData['google_refresh_token']) ? '' : $inputData['google_refresh_token'],
                );

                $uniEmail = $this->db->checkExitsRecords(['email' => $inputData['email']], 'beetsoft_user');
                $uniUserName = $this->db->checkExitsRecords(['username' => $userName], 'beetsoft_user');

                // Check if the login account matches any username or email in the DB.
                if ($uniEmail || $uniUserName) {
                    $response = array(
                        'result' => self::EXIT_CODE['notUnique'],
                        'desc' => 'Email or username ' . self::MESSAGES['notUnique'],
                        'data' => $inputData,
                    );
                } else {
                    $id = $this->db->createUser($data);
                    if ($id > 0) {
                        $response = array(
                            'result' => self::EXIT_CODE['success'],
                            'desc' => self::ACTION['register'] . self::MES_AUTH['success'],
                        );
                    } else {
                        $response = array(
                            'result' => self::EXIT_CODE['notFound'],
                            'desc' => self::MESSAGES['failed'],
                            'inputData' => $inputData,
                        );
                    }
                }
            }

            $this->logger->info('WebAuth.register',
                'Input data: ' . json_encode($inputData) . ' -> Response: ' . json_encode($response));
            $this->response = $response;

            return $this;
        } catch (Exception $e) {
            $this->logger->error('WebAuth.register', $e->getMessage());
            $this->response = null;

            return $this;
        }
    }
}
