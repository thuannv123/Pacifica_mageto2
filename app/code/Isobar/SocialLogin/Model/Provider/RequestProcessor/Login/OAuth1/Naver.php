<?php

namespace Isobar\SocialLogin\Model\Provider\RequestProcessor\Login\OAuth1;

use Isobar\SocialLogin\Model\Provider\RequestProcessor\Login\OAuth1;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;
use Magento\Framework\App\RequestInterface;

class Naver extends OAuth1
{
    /**
     * @inheritDoc
     */
    public function process(ServiceInterface $service, RequestInterface $request)
    {
        /** @var \Isobar\SocialLogin\Model\Provider\Service\Naver $service */
        $token = $service->requestRequestToken();
        $authUrl = $service->getAuthorizationUri([
            'oauth_token' => $token->getRequestToken()
        ]);
        return $this->buildRedirect($authUrl);
    }
}
