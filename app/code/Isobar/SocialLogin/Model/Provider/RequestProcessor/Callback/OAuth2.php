<?php
namespace Isobar\SocialLogin\Model\Provider\RequestProcessor\Callback;

use Isobar\SocialLogin\Model\Provider\RequestProcessor\Callback;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class OAuth2 callback request processor
 */
class OAuth2 extends Callback
{
    /**
     * {@inheritdoc}
     */
    protected function processRequest(ServiceInterface $service, RequestInterface $request)
    {
        /** @var \Isobar\SocialLogin\Model\Provider\Service\Kakao $service */

        $state = $request->getParam('state');
        $code = $request->getParam('code');

        $service->requestAccessToken($code, $state);
    }
}
