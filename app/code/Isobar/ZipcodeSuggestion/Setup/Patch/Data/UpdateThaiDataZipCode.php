<?php

namespace Isobar\ZipcodeSuggestion\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Api\AddressRepositoryInterface;

class UpdateThaiDataZipCode implements DataPatchInterface
{
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceConnection;
    /**
     * @var AddressRepositoryInterface
     */
    protected AddressRepositoryInterface $addressRepository;

    /**
     * UpdateThaiDataZipCode constructor.
     * @param ResourceConnection $resourceConnection
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->addressRepository = $addressRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $listRemoveProvinces = ['PTN', 'YLA', 'NRT'];
        $connection = $this->resourceConnection->getConnection();
        $getListRegionSql = "SELECT entity_id FROM customer_address_entity WHERE region_id IN (SELECT region_id FROM directory_country_region WHERE code IN ('PTN', 'YLA', 'NRT'))";
        $listAddressIds = $connection->fetchAssoc($getListRegionSql);
        foreach ($listAddressIds as $key => $id) {
            $this->addressRepository->deleteById($key);
        }
        foreach ($listRemoveProvinces as $province) {
            $sql = "DELETE FROM directory_country_region WHERE code = '$province'";
            $connection->query($sql);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
