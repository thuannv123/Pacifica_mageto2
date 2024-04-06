<?php
declare(strict_types=1);

namespace Isobar\ImportExport\Setup\Patch\Data;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Psr\Log\LoggerInterface;

class InitExport implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * InitExport constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $list = $this->customerRepository->getList($searchCriteria);
        foreach ($list->getItems() as $customer) {
            $customer->setCustomAttribute('exported', 0);
            try {
                $this->customerRepository->save($customer);
            } catch (InputException $e) {
                $this->logger->error($e->getLogMessage());
            } catch (InputMismatchException $e) {
                $this->logger->error($e);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.2';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
