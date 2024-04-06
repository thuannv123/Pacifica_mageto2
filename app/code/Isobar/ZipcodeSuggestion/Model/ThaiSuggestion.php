<?php

namespace Isobar\ZipcodeSuggestion\Model;

use Isobar\ZipcodeSuggestion\Api\DirectoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Json\Helper\Data;
use Isobar\ZipcodeSuggestion\Model\DirectorySubdistrictFactory;
use Magento\Framework\Locale\Resolver as LocalResolver;

class ThaiSuggestion implements DirectoryInterface
{
    const COUNTRY_ID_TH = 'TH';
    const REGION_LOCAL_TH = 'th_TH';
    protected \Isobar\ZipcodeSuggestion\Model\DirectorySubdistrictFactory $directorySubdistrictFactory;
    /**
     * @var Data
     */
    protected Data $jsonHelper;
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resource;
    /**
     * @var LocalResolver
     */
    protected LocalResolver $localeResolver;

    /**
     * ThaiSuggestion constructor.
     * @param \Isobar\ZipcodeSuggestion\Model\DirectorySubdistrictFactory $directorySubdistrictFactory
     * @param Data $jsonHelper
     * @param ResourceConnection $resource
     * @param LocalResolver $localeResolver
     */
    public function __construct(
        DirectorySubdistrictFactory $directorySubdistrictFactory,
        Data $jsonHelper,
        ResourceConnection $resource,
        LocalResolver $localeResolver
    ) {
        $this->directorySubdistrictFactory = $directorySubdistrictFactory;
        $this->jsonHelper = $jsonHelper;
        $this->resource = $resource;
        $this->localeResolver = $localeResolver;
    }

    public function getDataZipCode($data)
    {
        $items = [];
        $collection = $this->directorySubdistrictFactory->create()->getCollection()
            ->addFieldToSelect('zipcode')
            ->addFieldToSelect('district_id');
        $nameField = 'name';
        $currentLocale = 'en_US';
        $locale = $this->localeResolver->getLocale();
        if ($locale == self::REGION_LOCAL_TH) {
            $nameField = 'th_name';
            $currentLocale = self::REGION_LOCAL_TH;
        }
        $collection->addFieldToSelect($nameField, 'subdistrict_name');
        $collection->getSelect()
            ->join(
                array(
                    'district' => $this->resource->getTableName('directory_district')),
                'main_table.district_id = district.district_id',
                [
                    " district.".$nameField." as district_name",
                    "district.region_id as region_id",
                    "district.country_id as country_id"
                ]
            )
            ->join(
                array(
                    'region' => $this->resource->getTableName('directory_country_region_name')),
                'district.region_id = region.region_id',
                [
                    " region.name as region_name"
                ]
            )
            ->where('region.locale = ?', $currentLocale)
            ->where('district.country_id = ?', self::COUNTRY_ID_TH)
            ->where('main_table.zipcode LIKE "%' . $data . '%"')
            ->orWhere('region.name LIKE "%' . $data . '%"')
            ->orWhere('district.'.$nameField.' LIKE "%' . $data . '%"')
            ->orWhere('main_table.'.$nameField.' LIKE "%' . $data . '%"');
        $collection->getSelect()->limit(30);
        $collection->getSelect()->limit(30);
        $items = $collection->getData();
        return $this->jsonHelper->jsonEncode($items);
    }
}
