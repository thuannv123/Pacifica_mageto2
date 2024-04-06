<?php
namespace Isobar\SocialLogin\Model\Provider\RequestProcessor\Callback\OAuth1;

use Isobar\SocialLogin\Model\Provider\RequestProcessor\Callback;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Line callback request processor
 */
class Line extends Callback
{
    /**
     * {@inheritdoc}
     */
    protected function processRequest(ServiceInterface $service, RequestInterface $request)
    {
        /** @var \Isobar\SocialLogin\Model\Provider\Service\Line $service */

        $token = $service->getStorage()->retrieveAccessToken('Line');

        $service->requestAccessToken(
            $request->getParam('oauth_token'),
            $request->getParam('oauth_verifier'),
            $token->getRequestTokenSecret()
        );
    }
}
