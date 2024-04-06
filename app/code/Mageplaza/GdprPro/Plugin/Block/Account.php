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

namespace Mageplaza\GdprPro\Plugin\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Mageplaza\Gdpr\Block\Address\Account as BlockAccount;
use Mageplaza\GdprPro\Helper\Data as GdprProData;

/**
 * Class GdprData
 *
 * @package Mageplaza\GdprPro\Plugin\Helper
 */
class Account
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var GdprProData
     */
    protected $_gdprProData;

    /**
     * @var AssetRepository
     */
    protected $_assetRepo;

    /***
     * @var RequestInterface
     */
    protected $_request;

    /**
     * GdprData constructor.
     *
     * @param Session $customerSession
     * @param GdprProData $gdprProdata
     * @param AssetRepository $assetRepo
     * @param RequestInterface $request
     */
    public function __construct(
        Session $customerSession,
        GdprProData $gdprProdata,
        AssetRepository $assetRepo,
        RequestInterface $request
    ) {
        $this->_customerSession = $customerSession;
        $this->_gdprProData     = $gdprProdata;
        $this->_assetRepo       = $assetRepo;
        $this->_request         = $request;
    }

    /**
     * @param BlockAccount $subject
     * @param $result
     *
     * @return string
     */
    public function afterGetDeleteAccountUrl(BlockAccount $subject, $result)
    {
        /**
         * get token customer
         */
        $customerId = $this->_customerSession->getCustomerId();
        if ($customerId) {
            $tokenCustomer = $this->_gdprProData->getTokencustomer($customerId);
            $result        = $subject->getUrl(
                'customer/account/delete',
                ['token' => $tokenCustomer, '_nosid' => true]
            );
        }

        return $result;
    }

    /**
     * @param BlockAccount $subject
     * @param $result
     *
     * @return string
     */
    public function afterGetExtraData(BlockAccount $subject, $result)
    {
        $extraData = [
            'lazyload'                => $this->_assetRepo->getUrlWithParams(
                'images/loader-1.gif',
                ['_secure' => $this->_request->isSecure()]
            ),
            'currentControllerAction' => $this->_gdprProData->allowVerifyPassword() ? 'account-edit' : null,
            'checkpasswordUrl'        => $subject->getUrl('customer/account/checkpassword')
        ];

        return GdprProData::jsonEncode($extraData);
    }
}
