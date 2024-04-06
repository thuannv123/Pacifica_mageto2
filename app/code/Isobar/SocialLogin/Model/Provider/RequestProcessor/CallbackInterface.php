<?php
namespace Isobar\SocialLogin\Model\Provider\RequestProcessor;

use Isobar\SocialLogin\Model\Provider\AccountInterface;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;

/**
 * Interface CallbackInterface
 */
interface CallbackInterface
{
    /**
     * @param ServiceInterface $service
     * @param \Magento\Framework\App\RequestInterface $request
     * @return AccountInterface
     */
    public function process(ServiceInterface $service, \Magento\Framework\App\RequestInterface $request);
}
