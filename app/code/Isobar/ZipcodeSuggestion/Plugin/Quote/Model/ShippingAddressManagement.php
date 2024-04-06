<?php

namespace Isobar\ZipcodeSuggestion\Plugin\Quote\Model;

class ShippingAddressManagement
{
    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * BeforeAssign
     *
     * @param \Magento\Quote\Model\ShippingAddressManagement $subject
     * @param integer $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     */
    public function beforeAssign(
        \Magento\Quote\Model\ShippingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
    ) {
        $extAttributes = $address->getExtensionAttributes();

        $attributeObject = $address->getCustomAttribute('subdistrict');
        if ($attributeObject && $attributeObject->getValue()) {
            $address->setCustomAttribute('subdistrict', $attributeObject->getValue());
        }

        if (!empty($extAttributes)) {
            try {
                $address->setSubdistrict($extAttributes->getSubdistrict());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * Format customAddress
     *
     * @param string $address
     * @return false|string
     */
    protected function formatCustomAddress($address)
    {
        $breakLinePosition = strpos($address, PHP_EOL);
        if ($breakLinePosition !== false) {
            return substr($address, $breakLinePosition + 1);
        }
        return $address;
    }
}
