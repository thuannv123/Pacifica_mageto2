<?php

namespace Isobar\ZipcodeSuggestion\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Driver\File;

class InstallThaiDataZipCode implements DataPatchInterface
{
    const MODULE_NAME = 'Isobar_ZipcodeSuggestion';
    const PATH_FILE_SAMPLE = '/Files/Sample/';
    const FILE_DISTRICT = 'th_district.csv';
    const FILE_SUB_DISTRICT = 'th_subdistrict.csv';
    const FILE_REGION = 'th_directory_region.csv';
    /**
     * @var Reader
     */
    protected Reader $reader;
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceConnection;

    /** @var File */
    protected $file;

    /**
     * InstallThaiDataZipCode constructor.
     * @param Reader $reader
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Reader $reader,
        ResourceConnection $resourceConnection,
        File $file
    ) {
        $this->reader = $reader;
        $this->resourceConnection = $resourceConnection;
        $this->file = $file;
    }

    public function apply()
    {
        $this->addDataRegion();
        $this->addDataDirectoryDistrict();
        $this->addDataDirectorySubDistrict();
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * Add data for table region
     */
    public function addDataRegion()
    {
        $connection = $this->resourceConnection->getConnection();
        $moduleDir = $this->reader->getModuleDir('', self::MODULE_NAME);
        $file = $this->file->fileOpen($moduleDir . self::PATH_FILE_SAMPLE . self::FILE_REGION, "r");

        while (($data = $this->file->fileGetCsv($file, 1000, ",")) !== false) {
            $sql = "INSERT INTO directory_country_region (country_id, code, default_name) VALUES ('TH','" . $data[1] . "','" . $data[2] . "')";
            $connection->query($sql);

            $sqlGetRegion = "SELECT region_id FROM directory_country_region WHERE (country_id = 'TH' AND default_name = '" . $data[2] . "')";
            $regionId = $connection->query($sqlGetRegion)->fetchColumn();

            $sqlRegionNameTH = "INSERT INTO directory_country_region_name (locale, region_id, name) VALUES ('" . $data[0] . "','" . $regionId . "','" . $data[3] . "')";
            $connection->query($sqlRegionNameTH);
            $sqlRegionName = "INSERT INTO directory_country_region_name (locale, region_id, name) VALUES ('en_US','" . $regionId . "','" . $data[2] . "')";
            $connection->query($sqlRegionName);
        }
        fclose($file);
    }

    /**
     * Add data for table directory_city
     */
    public function addDataDirectoryDistrict()
    {
        $connection = $this->resourceConnection->getConnection();
        $moduleDir = $this->reader->getModuleDir('', self::MODULE_NAME);

        $file = $this->file->fileOpen($moduleDir . self::PATH_FILE_SAMPLE . self::FILE_DISTRICT, "r");

        while (($data = $this->file->fileGetCsv($file, 1000, ",")) !== false) {
            $sql = "INSERT INTO directory_district (region_id, country_id, name, th_name) VALUES ((SELECT region_id FROM directory_country_region WHERE default_name='"
                . $data[0] . "'), 'TH', '" . $data[1] . "', '". $data[2] ."')";
            $connection->query($sql);
        }
        fclose($file);
    }

    /**
     * Add data for table directory_town
     */
    public function addDataDirectorySubDistrict()
    {
        $connection = $this->resourceConnection->getConnection();
        $moduleDir = $this->reader->getModuleDir('', self::MODULE_NAME);

        $file = $this->file->fileOpen($moduleDir . self::PATH_FILE_SAMPLE . self::FILE_SUB_DISTRICT, "r");

        while (($data = $this->file->fileGetCsv($file, 1000, ",")) !== false) {
            $zipcode = $data[4];
            $sqlGetRegion = "SELECT region_id FROM directory_country_region WHERE (country_id = 'TH' AND default_name = '" . $data[0] . "')";
            $regionId = $connection->query($sqlGetRegion)->fetchColumn();
            $sql = "INSERT INTO directory_subdistrict (district_id, zipcode , name, th_name) VALUES ((SELECT district_id FROM directory_district WHERE th_name='"
                . $data[1] . "' AND region_id='".$regionId."'), '" . $zipcode . "','" . $data[2] . "','" . $data[3] . "')";
            $connection->query($sql);
        }
        fclose($file);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
