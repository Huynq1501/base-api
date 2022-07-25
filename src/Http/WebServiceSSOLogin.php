<?php

namespace nguyenanhung\Backend\BaseAPI\Http;

use JetBrains\PhpStorm\ArrayShape;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Instagram;
use League\OAuth2\Client\Provider\LinkedIn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class WebServiceAccount
 *
 * @package   nguyenanhung\Backend\BaseAPI\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceSSOLogin extends BaseHttp
{
    protected const API_NAME = 'social login';
    protected const LIST_STATE = ['google', 'facebook', 'instagram', 'linkedin', 'github'];
    protected const MES = array(
        'stateNotFound' => "phuong thuc login khong ton tai, hay thu lai",
    );
    private string $state;
    private Session $session;
    private Request $request;

    /**
     * WebServiceSSOLogin constructor.
     *
     * @param array $options
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->session = new Session();
        $this->request = Request::createFromGlobals();
        $this->logger->setLoggerSubPath(__CLASS__);
    }

    public function setState($state): static
    {
        $this->state = $state;
        return $this;
    }

    public function login()
    {
        if (!in_array($this->state, self::LIST_STATE, true)) {
            $response = array(
                'result' => self::EXIT_CODE['notFound'],
                'desc' => self::MES['stateNotFound'],
                'login by' => $this->state
            );
            $this->response = $response;

            return $this;
        }

        if ($this->state === 'google') {
            return $this->googleLogin();
        }

        if ($this->state === 'facebook') {
            return $this->facebookLogin();
        }

        if ($this->state == 'instagram') {
            return $this->instagramLogin();
        }
        if ($this->state == 'linkedin') {
            return $this->linkedinLogin();
        }
        if ($this->state == 'github') {
            return $this->githubLogin();
        }


    }

    protected function githubLogin(): WebServiceSSOLogin
    {
        $config = $this->options[$this->state];
        $provider = new Github($config);
        $request = $this->request;
        $response = $this->oauth2Service($provider, $request);

        $this->response = $response;
        return $this;
    }

    protected function linkedinLogin(): WebServiceSSOLogin
    {
        $config = $this->options[$this->state];
        $provider = new LinkedIn($config);
        $request = $this->request;
        $response = $this->oauth2Service($provider, $request);

        $this->response = $response;
        return $this;
    }

    protected function instagramLogin(): WebServiceSSOLogin
    {
        $config = $this->options[$this->state];
        $provider = new Instagram($config);
        $request = $this->request;
        $response = $this->oauth2Service($provider, $request);

        $this->response = $response;
        return $this;
    }

    protected function facebookLogin(): WebServiceSSOLogin
    {
        $config = $this->options[$this->state];
        $provider = new Facebook($config);
        $request = $this->request;
        $response = $this->oauth2Service($provider, $request);

        $this->response = $response;
        return $this;
    }

    protected function googleLogin(): WebServiceSSOLogin
    {
        $config = $this->options[$this->state];
        $provider = new Google($config);
        $request = $this->request;
        $response = $this->oauth2Service($provider, $request);

        $this->response = $response;
        return $this;
    }

    #[ArrayShape(['result' => "int", 'desc' => "string", 'data' => "mixed|string"])]
    protected function oauth2Service($provider, $request): array
    {
        if (!empty($request->query->get('error'))) {
            $response = array(
                'result' => self::EXIT_CODE['failed'],
                'desc' => 'Got error: ' . htmlspecialchars($request->query->get('error'), ENT_QUOTES),
            );
        } elseif (empty($request->query->get('code'))) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $this->session->set('oauth2state', $provider->getState());

            $response = array(
                'result' => self::EXIT_CODE['success'],
                'desc' => self::API_NAME . '-' . self::MESSAGES['success'],
                'data' => $authUrl
            );
        } else {
            try {
                // Try to get an access token (using the authorization code grant)
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $request->query->get('code')
                ]);

                // Optional: Now you have a token you can look up a users profile data
                // We got an access token, let's now get the owner details
                $ownerDetails = $provider->getResourceOwner($token);
                $this->session->set('userLogin', serialize($ownerDetails));
                $response = array(
                    'result' => self::EXIT_CODE['success'],
                    'desc' => self::API_NAME . '-' . 'Success',
                    'data' => serialize($ownerDetails),
                );
            } catch (IdentityProviderException $e) {
                $response = array('desc' => 'Something went wrong: ' . $e->getMessage());
            }
        }

        return $response;
    }
}