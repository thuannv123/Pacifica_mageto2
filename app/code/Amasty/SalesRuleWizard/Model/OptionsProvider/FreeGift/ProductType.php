<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Sales Rules Wizard for Magento 2 (System)
 */

namespace Amasty\SalesRuleWizard\Model\OptionsProvider\FreeGift;

class ProductType extends \Magento\Catalog\Model\Product\Type implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = parent::getOptionArray();
        foreach ($options as $typeId => $type) {
            if (!in_array($typeId, [
                'simple',
                'configurable',
                'virtual',
                'downloadable',
                'bundle',
                'giftcard',
            ])) {
                unset($options[$typeId]);
            }
        }

        return $options;
    }
}
