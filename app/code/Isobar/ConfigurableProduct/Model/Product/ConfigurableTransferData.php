<?php

namespace Isobar\ConfigurableProduct\Model\Product;

class ConfigurableTransferData
{
    private array $variationAttributes = [];

    /**
     * @return array
     */
    public function getVariationAttributes(): array
    {
        return $this->variationAttributes;
    }

    /**
     * @param array $variationAttributes
     */
    public function setVariationAttributes(array $variationAttributes): void
    {
        $this->variationAttributes = $variationAttributes;
    }
}
