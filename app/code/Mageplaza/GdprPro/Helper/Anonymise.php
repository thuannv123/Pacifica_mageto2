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

namespace Mageplaza\GdprPro\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Anonymise
 *
 * @package Mageplaza\GdprPro\Helper
 */
class Anonymise extends Data
{
    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var OrderResourceModel
     */
    protected $_orderResourceModel;

    /**
     * @var QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * Anonymise constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Request $request
     * @param Serialize $serialize
     * @param TokenFactory $tokenFactory
     * @param OrderFactory $orderFactory
     * @param OrderResourceModel $orderResourceModel
     * @param QuoteFactory $quoteFactory
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Request $request,
        Serialize $serialize,
        TokenFactory $tokenFactory,
        OrderFactory $orderFactory,
        OrderResourceModel $orderResourceModel,
        QuoteFactory $quoteFactory,
        AddressFactory $addressFactory
    ) {
        $this->_orderFactory       = $orderFactory;
        $this->_orderResourceModel = $orderResourceModel;
        $this->_quoteFactory       = $quoteFactory;
        $this->_addressFactory     = $addressFactory;
        parent::__construct($context, $objectManager, $storeManager, $request, $serialize, $tokenFactory);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAnonymiseFirstname($storeId)
    {
        $lastname = $this->generateRandomString();
        if ($this->getModuleConfig('anonymise_account/firstname', $storeId)) {
            $lastname = $this->getModuleConfig('anonymise_account/firstname', $storeId);
        }

        return $lastname;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAnonymiseLastName($storeId)
    {
        $lastname = $this->generateRandomString();
        if ($this->getModuleConfig('anonymise_account/lastname', $storeId)) {
            $lastname = $this->getModuleConfig('anonymise_account/lastname', $storeId);
        }

        return $lastname;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAnonymiseEmail($storeId)
    {
        $email = $this->generateRandomString();
        if ($this->getModuleConfig('anonymise_account/email', $storeId)) {
            $email = $this->getModuleConfig('anonymise_account/email', $storeId);
        }

        return $email . '@anonymise.com';
    }

    /**
     * @param $customerEmail
     *
     * @return mixed
     */
    public function getOrderByEmail($customerEmail)
    {
        $orderModel = $this->_orderFactory->create()->getCollection()
            ->addFieldToFilter('customer_email', $customerEmail);

        return $orderModel;
    }

    /**
     * @param $order
     */
    public function anonymiseAccountInOrder($order)
    {
        $storeId = $order->getStoreId();
        $order->addData([
            'customer_id'        => null,
            'customer_firstname' => $this->getAnonymiseFirstname($storeId),
            'customer_lastname'  => $this->getAnonymiseLastName($storeId),
            'customer_email'     => $this->getAnonymiseEmail($storeId)
        ])->save();
    }

    /**
     * @param $customerEmail
     *
     * @return mixed
     */
    public function getAbandonedcartByEmail($customerEmail)
    {
        $quoteModel = $this->_quoteFactory->create()->getCollection()
            ->addFieldToFilter('customer_email', $customerEmail);

        return $quoteModel;
    }

    /**
     * @param $quoteAccount
     *
     * @throws Exception
     */
    public function deleAbandonedcart($quoteAccount)
    {
        $quoteId     = $quoteAccount->getId();
        $quoteDelete = $this->_quoteFactory->create()->load($quoteId);
        $quoteDelete->setIsActive(false);
        $quoteDelete->delete();
    }

    /**
     * @param Order $order
     *
     * @throws Exception
     */
    public function anonymiseAddressInOrder($order)
    {
        $this->saveDataforAddressAnonymise($order->getShippingAddress(), $order->getStoreId());
        $this->saveDataforAddressAnonymise($order->getBillingAddress(), $order->getStoreId());
    }

    /**
     * @param $shippingAddress
     * @param $storeId
     */
    public function saveDataforAddressAnonymise($shippingAddress, $storeId)
    {
        $anonymiseOption        = $this->getAnonymiseAddressOption($storeId);
        $anonymiseAddressOption = explode(',', $anonymiseOption);

        foreach ($anonymiseAddressOption as $key => $value) {
            switch ($value) {
                case 'street':
                    $shippingAddress->setStreet($this->generateRandomString(30));
                    break;
                case 'city':
                    $shippingAddress->setCity($this->generateRandomString());
                    break;
                case 'state_province':
                    $shippingAddress->setRegion($this->generateRandomString());
                    break;
                case 'postcode':
                    $shippingAddress->setPostcode($this->generateRandomString(5));
                    break;
                case 'telephone':
                    $shippingAddress->setTelephone($this->generateRandomString());
                    break;
                case 'first_name':
                    $shippingAddress->setFirstname($this->generateRandomString());
                    break;
                case 'last_name':
                    $shippingAddress->setLastname($this->generateRandomString());
                    break;
                default:
                    break;
            }
        }

        $shippingAddress->save();
    }
}
