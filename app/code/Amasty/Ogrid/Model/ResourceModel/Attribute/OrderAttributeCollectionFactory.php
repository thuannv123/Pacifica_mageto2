<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\ResourceModel\Attribute;

use Magento\Framework\ObjectManagerInterface;

class OrderAttributeCollectionFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    public function create()
    {
        return $this->objectManager->create(\Amasty\Orderattr\Model\ResourceModel\Order\Attribute\Collection::class);
    }
}
