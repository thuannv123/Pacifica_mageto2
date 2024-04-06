<?php

namespace Isobar\SocialLogin\Model\Provider\ServiceBuilder;

use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;
use Isobar\SocialLogin\Model\Provider\ServiceBuilder;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\OAuth1\Signature\Signature;

class OAuth1 extends ServiceBuilder
{
    /**
     * @return ServiceInterface
     */
    public function build()
    {
        $a = 1;
        /** @var ServiceInterface $service */
        $service = $this->objectManager->create($this->service, [
            'credentials'   => $this->credentials,
            'httpClient'    => new CurlClient(),
            'storage'       => $this->storage,
            'signature'     => new Signature($this->credentials),
            'baseApiUri'    => $this->config->getBaseUri()
        ]);
        return $service;
    }
}
