<?php
namespace Isobar\SocialLogin\Model\Provider\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Uri\Uri;

/**
 * Class Odnoklassniki
 */
class Kakao extends \OAuth\OAuth2\Service\AbstractService implements ServiceInterface
{
    /**
     * Kakao constructor.
     * @param CredentialsInterface $credentials
     * @param ClientInterface $httpClient
     * @param TokenStorageInterface $storage
     * @param array $scopes
     * @param UriInterface|null $baseApiUri
     * @throws \OAuth\OAuth2\Service\Exception\InvalidScopeException
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
            $this->baseApiUri = new Uri('https://kapi.kakao.com/v2/');
        }
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
        return static::AUTHORIZATION_METHOD_QUERY_STRING;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://kauth.kakao.com/oauth/authorize');
    }

    /**
     * Returns the access token API endpoint.
     *
     * @return UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://kauth.kakao.com/oauth/token');
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
     * @throws \OAuth\Common\Token\Exception\ExpiredTokenException
     */
    public function requestWithParams(
        $path,
        array $params,
        $method = 'GET',
        $body = null,
        array $extraHeaders = []
    ) {
        $token = $this->storage->retrieveAccessToken($this->service());

        $params = array_merge(
            $params,
            ['application_key' => $this->getApplicationKey()]
        );
        $params['sig'] = $this->generateSignature(
            $params,
            $token->getAccessToken(),
            $this->credentials->getConsumerSecret()
        );

        $path = $this->preparePathWithParams($path, $params);

        return $this->request($path, $method, $body, $extraHeaders);
    }
}
