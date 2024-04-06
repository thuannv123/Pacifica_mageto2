<?php
namespace Isobar\SocialLogin\Model\Provider\RequestProcessor\Login\OAuth1;

use Isobar\SocialLogin\Model\Provider\RequestProcessor\Login;
use Isobar\SocialLogin\Model\Provider\RequestProcessor\Login\OAuth1;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;

/**
 * Class Kakao login processor
 */
class Kakao extends OAuth1
{
    /**
     * {@inheritdoc}
     */
    public function process(ServiceInterface $service, \Magento\Framework\App\RequestInterface $request)
    {
        /** @var \Isobar\SocialLogin\Model\Provider\Service\Kakao $service */
        $token = $service->requestRequestToken();
        $authUrl = $service->getAuthorizationUri([
            'oauth_token' => $token->getRequestToken()
        ]);
        return $this->buildRedirect($authUrl);
    }
}
