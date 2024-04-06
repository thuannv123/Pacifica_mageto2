<?php

namespace Isobar\SocialLogin\Model\Provider\Service;

use Isobar\SocialLogin\Model\Provider\Service\Credentials\AdditionalCredentialsInterface;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Exception\Exception;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\Exception\ExpiredTokenException;
use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Service\Exception\InvalidScopeException;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Naver extends AbstractService implements ServiceInterface
{

    /**
     * Defined scopes.
     */
    const SCOPE_VALUABLE_ACCESS = 'VALUABLE_ACCESS';

    /**
     * @param CredentialsInterface $credentials
     * @param ClientInterface $httpClient
     * @param TokenStorageInterface $storage
     * @param array $scopes
     * @param UriInterface|null $baseApiUri
     * @throws InvalidScopeException
     */
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = [],
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://openapi.naver.com/v1/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://nid.naver.com/oauth2.0/authorize');
    }

    /**
     * Returns the access token API endpoint.
     *
     * @return UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://nid.naver.com/oauth2.0/token');
    }

    /**
     * Request with params.
     *
     * @param $path
     * @param array $params
     * @param string $method
     * @param null $body
     * @param array $extraHeaders
     * @return string
     * @throws Exception
     * @throws TokenNotFoundException
     * @throws ExpiredTokenException
     */
    public function requestWithParams(
        $path,
        array $params,
        $method = 'GET',
        $body = null,
        array $extraHeaders = []
    ) {
        $path =  $this->preparePathWithParams($path, $params);

        return $this->request($path, $method, $body, $extraHeaders);
    }

    /**
     * Get application key.
     *
     * @return null|string
     */
    private function getApplicationKey()
    {
        if (!($this->credentials instanceof AdditionalCredentialsInterface)) {
            return null;
        }

        return $this->credentials->getPublicKey();
    }

    /**
     * Generates a signature.
     *
     * @param array $params
     * @param string $accessToken
     * @param string $secret
     * @return string
     */
    private function generateSignature($params, $accessToken, $secret)
    {
        ksort($params);
        $paramsStr = '';
        foreach ($params as $key => $value) {
            if (in_array($key, ['sig', 'access_token'])) {
                continue;
            }
            $paramsStr .= ($key . '=' . $value);
        }
        return md5($paramsStr . md5($accessToken . $secret));
    }

    /**
     * Prepare path with params.
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    private function preparePathWithParams($path, array $params)
    {

        return $path . '?' . http_build_query($params);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        $token->setLifetime($data['expires_in']);

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token']);
        unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }
}
