<?php

namespace Isobar\ConfigurableProduct\Plugin\Product\Type;

use Isobar\ConfigurableProduct\Model\Product\ConfigurableTransferData;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ConfigurablePlugin
{
    private ConfigurableTransferData $configurableTransferData;

    /**
     * @param ConfigurableTransferData $configurableTransferData
     */
    public function __construct(ConfigurableTransferData $configurableTransferData)
    {
        $this->configurableTransferData = $configurableTransferData;
    }

    /**
     * @param Configurable $subject
     * @param $product
     * @return array
     */
    public function beforeBeforeSave(Configurable $subject, $product)
    {
        $variationData = [];
        foreach ($subject->getConfigurableAttributes($product) as $attribute)
        {
            $variationData[$attribute->getProductAttribute()->getAttributeCode()]
                = $product->getData($attribute->getProductAttribute()->getAttributeCode());
        }
        $this->configurableTransferData->setVariationAttributes($variationData);

        return [$product];
    }

    /**
     * @param Configurable $subject
     * @param $result
     * @param $product
     * @return mixed
     */
    public function afterBeforeSave(Configurable $subject, $result, $product)
    {
        foreach ($this->configurableTransferData->getVariationAttributes() as $attribute => $value)
        {
            $product->setData($attribute, $value);
        }

        return $result;
    }

}
