<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */
namespace Amasty\Ogrid\Model\Column\Product;

class Option extends \Amasty\Ogrid\Model\Column\Product
{
    public function getOrderOptions()
    {
        $result = [];

        if ($options = $this->getItem()->getProductOptions()) {
            $result = $this->combineProductOptions($options);
        }

        return $result;
    }

    public function modifyItem(&$item, $config = [])
    {
        parent::modifyItem($item, $config);

        if (array_key_exists('amasty_ogrid_product_product_options', $item)) {
            $options = $this->combineProductOptions(
                $this->serializer->unserialize($item['amasty_ogrid_product_product_options'])
            );

            if (is_array($options)) {
                foreach ($options as $idx => $vals) {
                    if (key_exists('option_id', $vals)) {
                        unset($vals['option_id']);
                    }

                    if (key_exists('option_value', $vals)) {
                        unset($vals['option_value']);
                    }

                    $vals = [$vals['label'], $vals['value']];
                    $options[$idx] = implode(': ', $vals);
                }
            }

            $item['amasty_ogrid_product_product_options'] = implode(', ', $options);
        }
    }

    protected function combineProductOptions($options)
    {
        $result = [];

        if (isset($options['options'])) {
            $result = array_merge($result, $options['options']);
        }

        if (isset($options['additional_options'])) {
            $result = array_merge($result, $options['additional_options']);
        }

        if (!empty($options['attributes_info'])) {
            $result = array_merge($options['attributes_info'], $result);
        }

        return $result;
    }
}
