<?php
namespace Isobar\SocialLogin\Model\Provider\RequestProcessor;

use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;

/**
 * Interface LoginInterface
 */
interface LoginInterface
{
    /**
     * @param ServiceInterface $service
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function process(ServiceInterface $service, \Magento\Framework\App\RequestInterface $request);
}
