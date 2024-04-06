<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Ui\Component;

use Amasty\Ogrid\Helper\Data as Helper;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Component\Container;

class Columns extends Container
{
    /**
     * @var BookmarkManagementInterface
     */
    protected $bookmarkManagement;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var array
     */
    protected $typeToFilter = [
        'text' => 'text',
        'select' => 'select',
        'multiselect' => 'select',
        'boolean' => 'select'
    ];

    public function __construct(
        ContextInterface $context,
        BookmarkManagementInterface $bookmarkManagement,
        FilterBuilder $filterBuilder,
        Helper $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->bookmarkManagement = $bookmarkManagement;
        $this->helper = $helper;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();

        $columnsConfiguration = $this->getData('config');

        if (array_key_exists('productColsData', $columnsConfiguration)) {
            $bookmark = $this->bookmarkManagement->getByIdentifierNamespace(
                'current',
                'sales_order_grid'
            );
            $config = $bookmark ? $bookmark->getConfig() : null;
            $bookmarksCols = [];
            if (is_array($config) && isset($config['current']) && isset($config['current']['columns'])) {
                $bookmarksCols = $config['current']['columns'];
            }

            foreach ($this->getAttributeCollection() as $attribute) {
                $inputType = $attribute->getFrontendInput();
                $columnConfig = [
                    'visible' => false,
                    'filter' => null,
                    'label' => $attribute->getFrontendLabel(),
                    'productAttribute' => true,
                    'frontendInput' => $inputType
                ];

                if (array_key_exists($inputType, $this->typeToFilter)) {
                    $columnConfig['filter'] = $this->typeToFilter[$inputType];

                    if ($attribute->getSource()) {
                        $columnConfig['dataType'] = 'text';
                        $columnConfig['options'] = $this->prepareFilterOptions(
                            $attribute->getSource()->getAllOptions()
                        );
                    }
                }

                $productAttributeDbAlias = $this->getProductAttributeDbAlias($attribute->getAttributeCode());
                $columnsConfiguration['productColsData'][$productAttributeDbAlias] = $columnConfig;
            }

            foreach ($columnsConfiguration['productColsData'] as $id => &$config) {
                if (isset($bookmarksCols[$id]['amogrid_label'])) {
                    $config['amogrid_label'] = $bookmarksCols[$id]['amogrid_label'];
                    $config['default_label'] = $config['label'];
                } elseif (isset($config['label'])) {
                    $config['amogrid_label'] = $config['default_label'] = $config['label'];
                }

                if (isset($bookmarksCols[$id]['visible'])) {
                    $config['visible'] = $bookmarksCols[$id]['visible'];
                } elseif (isset($config['visible'])) {
                    $config['visible'] = $config['visible'];
                }

            }

            $this->setData('config', $columnsConfiguration);
        }
    }

    public function getAttributeCollection(): Collection
    {
        return $this->helper->getProductAttributesDataCollection();
    }

    private function prepareFilterOptions(array $options): array
    {
        $options = array_map(function ($option) {
            $option['value'] = (string)$option['label'];
            return $option;
        }, $options);

        return $options;
    }

    private function getProductAttributeDbAlias(string $attributeCode): string
    {
        return 'amasty_ogrid_product_attrubute_' . $attributeCode;
    }
}
