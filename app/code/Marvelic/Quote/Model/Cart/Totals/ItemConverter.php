<?php

namespace Marvelic\Quote\Model\Cart\Totals;

use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

class ItemConverter extends \Magento\Quote\Model\Cart\Totals\ItemConverter
{
    private $configurationPool;

    private $eventManager;

    private $totalsItemFactory;

    private $dataObjectHelper;

    private $serializer;

    public function __construct(
        ConfigurationPool $configurationPool,
        EventManager $eventManager,
        \Magento\Quote\Api\Data\TotalsItemInterfaceFactory $totalsItemFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct(
            $configurationPool,
            $eventManager,
            $totalsItemFactory,
            $dataObjectHelper,
            $serializer
        );

        $this->configurationPool = $configurationPool;
        $this->eventManager = $eventManager;
        $this->totalsItemFactory = $totalsItemFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function modelToDataObject($item)
    {
        $this->eventManager->dispatch('items_additional_data', ['item' => $item]);
        $items = $item->toArray();
        $items['options'] = $this->getFormattedOptionValue($item);
        unset($items[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        $itemsData = $this->totalsItemFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemsData,
            $items,
            \Magento\Quote\Api\Data\TotalsItemInterface::class
        );
        return $itemsData;
    }

    private function getFormattedOptionValue($item)
    {
        $optionsData = [];

        /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
        $helper = $this->configurationPool->getByProductType('default');

        $options = $this->configurationPool->getByProductType($item->getProductType())->getOptions($item);
        foreach ($options as $index => $optionValue) {
            $params = [
                'max_length' => 55,
                'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
            ];
            $option = $helper->getFormattedOptionValue($optionValue, $params);
            $optionsData[$index] = $option;
            $optionsData[$index]['label'] = $optionValue['label'];
            $optionsData[$index]['option_id'] = $optionValue['option_id'];
        }
        return $this->serializer->serialize($optionsData);
    }
}
