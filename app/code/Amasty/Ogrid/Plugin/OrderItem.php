<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Plugin;

use Amasty\Ogrid\Model\Indexer\Attribute\Processor;

class OrderItem
{
    /**
     * @var Processor
     */
    protected $_productAttributesIndexerProcessor;

    public function __construct(
        Processor $productAttributesIndexerProcessor
    ) {
        $this->_productAttributesIndexerProcessor = $productAttributesIndexerProcessor;
    }

    public function afterAfterSave(
        \Magento\Sales\Model\Order\Item $item,
        $result
    ) {
        $this->_productAttributesIndexerProcessor->reindexRow($item->getId());
        return $result;
    }
}
