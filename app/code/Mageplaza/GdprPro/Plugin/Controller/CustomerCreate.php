<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Plugin\Controller;

use Closure;
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class CustomerCreate
 *
 * @package Mageplaza\GdprPro\Plugin\Controller
 */
class CustomerCreate
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var UrlInterface
     */
    protected $urlModel;

    /**
     * @var RedirectInterface
     */
    protected $_redirect;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CustomerCreate constructor.
     *
     * @param RedirectFactory $resultRedirectFactory
     * @param UrlFactory $urlFactory
     * @param RedirectInterface $redirect
     * @param ManagerInterface $messageManager
     * @param Data $helper
     */
    public function __construct(
        RedirectFactory $resultRedirectFactory,
        UrlFactory $urlFactory,
        RedirectInterface $redirect,
        ManagerInterface $messageManager,
        Data $helper
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->urlModel              = $urlFactory->create();
        $this->_redirect             = $redirect;
        $this->messageManager        = $messageManager;
        $this->helper                = $helper;
    }

    /**
     * @param CreatePost $object
     * @param Closure $proceed
     *
     * @return mixed|string
     */
    public function aroundExecute(CreatePost $object, Closure $proceed)
    {
        if ($this->helper->getEnableTermAndCondition() && !$object->getRequest()->getParam('agree_gdpr_tac', 0)) {
            $this->messageManager->addErrorMessage(__('Please agree to the term and condition.'));

            /**
             * @var Redirect $resultRedirect
             */
            $resultRedirect = $this->resultRedirectFactory->create();

            $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            $resultRedirect->setUrl($this->_redirect->error($url));

            return $resultRedirect;
        }

        return $proceed();
    }
}
