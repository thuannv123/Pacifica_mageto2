<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Observer;

use Amasty\Ogrid\Model\AttributeFactory;
use Amasty\Ogrid\Model\Indexer\Attribute\Processor;
use Magento\Framework\Event\ObserverInterface;

class CatalogEntityAttributeSaveAfterObserver implements ObserverInterface
{
    /**
     * @var AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @var Processor
     */
    protected $_productAttributesIndexerProcessor;

    public function __construct(
        AttributeFactory $attributeFactory,
        Processor $productAttributesIndexerProcessor
    ) {
        $this->_attributeFactory = $attributeFactory;
        $this->_productAttributesIndexerProcessor = $productAttributesIndexerProcessor;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $catalogAttribute = $observer->getEvent()->getData('data_object');
        if ($catalogAttribute) {
            $attribute = $this->_attributeFactory->create()->load($catalogAttribute->getId(), 'attribute_id');

            if ($catalogAttribute->getData('amasty_ogrid_use_in_index')) {
                $attribute->addData([
                    'attribute_id' => $catalogAttribute->getId(),
                    'attribute_code' => $catalogAttribute->getAttributeCode(),
                    'frontend_label' => $catalogAttribute->getFrontendLabel(),
                ]);

                $attribute->save();

                $this->_productAttributesIndexerProcessor->markIndexerAsInvalid();
            } else {
                $attribute->delete();
            }
        }
    }
}
