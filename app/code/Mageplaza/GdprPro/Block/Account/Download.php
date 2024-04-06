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
 * @package   Mageplaza_Gdpr
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Block\Account;

use Magento\Framework\View\Element\Template;
use Mageplaza\GdprPro\Helper\Data as HelperData;

/**
 * Class Account
 * @package Mageplaza\Gdpr\Block\Address
 */
class Download extends Template
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Account constructor.
     *
     * @param Template\Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function allowDeleteCustomer()
    {
        return $this->_helperData->allowDeleteAccount();
    }

    /**
     * @return bool
     */
    public function allowDownload()
    {
        return $this->_helperData->allowDownload();
    }

    /**
     * @return mixed
     */
    public function getDownloadMessage()
    {
        return $this->_helperData->getDownloadMessage();
    }

    /**
     * @return string
     */
    public function getDownloadFileUrl()
    {
        return $this->getUrl('customer/account/download');
    }
}
