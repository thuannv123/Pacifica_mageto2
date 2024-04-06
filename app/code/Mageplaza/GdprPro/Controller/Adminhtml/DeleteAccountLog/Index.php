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
 * @category    Mageplaza
 * @package     Mageplaza_GdprPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Controller\Adminhtml\DeleteAccountLog;

use Magento\Backend\Model\View\Result\Page as PageResultModel;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\GdprPro\Controller\Adminhtml\AbstractDeleteLog;

/**
 * Class Index
 * @package Mageplaza\GdprPro\Controller\Adminhtml\DeleteAccountLog
 */
class Index extends AbstractDeleteLog
{
    /**
     * Execute the action
     *
     * @return PageResultModel|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Log Delete Your Account'));

        return $resultPage;
    }
}
