<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\Attribute\Product\Weee;

use Amasty\Base\Model\Serializer;
use Magento\Directory\Model\Config\Source\Country as CountrySource;
use Magento\Directory\Model\Config\Source\Allregion as RegionSource;
use Magento\Directory\Model\Currency;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;

class Formatter
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var CountrySource
     */
    private $countrySource;

    /**
     * @var RegionSource
     */
    private $regionSource;

    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $countryOptionHash;

    /**
     * @var array
     */
    private $stateOptionHash;

    /**
     * @var array
     */
    private $websiteOptionHash;

    public function __construct(
        Serializer $serializer,
        CountrySource $countrySource,
        RegionSource $regionSource,
        DirectoryHelper $directoryHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->serializer = $serializer;
        $this->countrySource = $countrySource;
        $this->regionSource = $regionSource;
        $this->directoryHelper = $directoryHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * Formats weee tax attribute value to display at order grid
     */
    public function format(string $value): array
    {
        $data = $this->serializer->unserialize($value);

        foreach ($data as &$item) {
            $item['country'] = $this->toCountyLabel($item['country']);
            if (isset($item['state'])) {
                $item['state'] = $this->toStateLabel($item['state']);
            }
            $item['website_id'] = $this->toWebsiteLabel($item['website_id']);
        }

        return $data;
    }

    private function toCountyLabel($value): ?string
    {
        if (!isset($this->countryOptionHash)) {
            $this->countryOptionHash = $this->toOptionHash($this->countrySource->toOptionArray());
        }

        return $this->countryOptionHash[$value] ?? null;
    }

    private function toStateLabel($value): ?string
    {
        if (!isset($this->stateOptionHash)) {
            $this->stateOptionHash = $this->toOptionHash($this->regionSource->toOptionArray());
        }

        return $this->stateOptionHash[$value] ?? null;
    }

    private function toWebsiteLabel($value): ?string
    {
        if (!isset($this->websiteOptionHash)) {
            $this->websiteOptionHash = [
                0 => __('All Websites') . ' ' . $this->directoryHelper->getBaseCurrencyCode()
            ];

            /** @var Website $website */
            foreach ($this->storeManager->getWebsites() as $website) {
                $this->websiteOptionHash[$website->getId()] = $website->getName()
                    . ' ' . $website->getConfig(Currency::XML_PATH_CURRENCY_BASE);
            }
        }

        return $this->websiteOptionHash[$value] ?? null;
    }

    private function toOptionHash(array $optionArray, array &$hash = []): array
    {
        foreach ($optionArray as $optionItem) {
            if (is_array($optionItem['value'])) {
                $this->toOptionHash($optionItem['value'], $hash);
            } else {
                $hash[$optionItem['value']] = $optionItem['label'];
            }
        }

        return $hash;
    }
}
