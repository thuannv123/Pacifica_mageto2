<?php

namespace Isobar\SocialLogin\Model\Provider\RequestProcessor\Callback\OAuth1;

use Isobar\SocialLogin\Model\Provider\RequestProcessor\Callback;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;
use Magento\Framework\App\RequestInterface;

class Naver extends Callback
{

    /**
     * @inheritDoc
     */
    protected function processRequest(ServiceInterface $service, RequestInterface $request)
    {
        $state = null;
        $code = $request->getParam('code');

        $service->requestAccessToken($code, $state);
    }
}
