<?php

namespace Atome\MagentoPayment\Controller\Payment;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;

trait Response
{

    /**
     * @param $url
     * @return RedirectFactory
     */
    public function redirectResponse($url)
    {
        return ObjectManager::getInstance()
            ->create(RedirectFactory::class)
            ->create()
            ->setUrl($url);
    }

    /**
     * @param $data
     * @param int $httpCode
     * @return Json
     */
    public function jsonResponse($data, $httpCode = 200)
    {
        return ObjectManager::getInstance()
            ->create(JsonFactory::class)
            ->create()
            ->setHttpResponseCode($httpCode)
            ->setData($data);
    }


    /**
     * @return PageFactory
     */
    public function pageResponse()
    {
        return ObjectManager::getInstance()
            ->create(PageFactory::class);
    }


}
