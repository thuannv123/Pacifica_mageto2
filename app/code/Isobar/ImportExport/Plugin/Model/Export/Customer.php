<?php
declare(strict_types=1);

namespace Isobar\ImportExport\Plugin\Model\Export;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Customer
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;
    /**
     * @var DataObject
     */
    public $list;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Customer constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObject $list DataObject
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        DataObject $list = null,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->list = $list;
        $this->logger = $logger;
    }

    /**
     * @param \Firebear\ImportExport\Model\Export\Customer $subject
     * @param $result
     * @param $item
     * @return mixed
     */
    public function afterExportItem(
        \Firebear\ImportExport\Model\Export\Customer $subject,
        $result,
        $item
    ) {
        try {
            if (!empty($item->getExported())) {
                return $result;
            }
            if ($this->list == null) {
                $this->list = new DataObject();
            }
            if ($this->list->hasData('customers')) {
                $data = $this->list->getData('customers');
                $data[] = $item->getId();
                $this->list->setData('customers', $data);
            } else {
                $this->list->setData('customers', [$item->getId()]);
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
        return $result;
    }

    public function afterExport(
        \Firebear\ImportExport\Model\Export\Customer $subject,
        $result
    ) {
        if ($this->list && $this->list->hasData('customers')) {
            $customerExported = $this->list->getData('customers');
            foreach ($customerExported as $customerId) {
                $customer = $this->customerRepository->getById($customerId);
                $customer->setCustomAttribute('exported', 1);
                $this->customerRepository->save($customer);
            }
        }
        return $result;
    }
}
