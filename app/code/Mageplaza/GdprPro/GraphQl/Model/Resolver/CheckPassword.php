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


namespace Mageplaza\GdprPro\GraphQl\Model\Resolver;

use Exception;
use Magento\Customer\Model\CustomerRegistry;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\Gdpr\Helper\Data;

/**
 * Class CheckPassword
 * @package Mageplaza\GdprPro\GraphQl\Model\Resolver
 */
class CheckPassword implements ResolverInterface
{
    /**
     * @var CustomerRegistry
     */
    protected $_customerRegistry;

    /**
     * @var Encryptor
     */
    protected $_encryptor;
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var GetCustomer
     */
    protected $_getCustomer;

    /**
     * @param Encryptor $encryptor
     * @param CustomerRegistry $customerRegistry
     * @param Data $helperData
     * @param GetCustomer $getCustomer
     */
    public function __construct(
        Encryptor $encryptor,
        CustomerRegistry $customerRegistry,
        Data $helperData,
        GetCustomer $getCustomer
    ) {
        $this->_encryptor        = $encryptor;
        $this->_customerRegistry = $customerRegistry;
        $this->_helperData       = $helperData;
        $this->_getCustomer      = $getCustomer;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return bool
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     * @throws GraphQlNoSuchEntityException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        if (!$this->_helperData->isEnabled()) {
            throw new GraphQlAuthorizationException(__('The Gdpr is disabled.'));
        }
        if (empty($args['password'])) {
            throw new GraphQlInputException(__('Specify the "password" value.'));
        }
        if (empty($args['email'])) {
            throw new GraphQlInputException(__('Specify the "email" value.'));
        }
        try {
            $customer = $this->_getCustomer->execute($context);
            if ($args['email'] !== $customer->getEmail()) {
                return false;
            }
            $customerSecure       = $this->_customerRegistry->retrieveSecureData($customer->getId());
            $customerPasswordHash = $customerSecure->getPasswordHash();
            if ($this->_encryptor->validateHash($args['password'], $customerPasswordHash)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }
    }
}
