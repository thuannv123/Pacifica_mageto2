<?php

namespace Marvelic\Export\Ui\Component\Listing\Column;

use Exception;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class SourceCode extends AbstractSource
{
    /**
     * @var SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     * @var SourceRepositoryInterface
     */
    public $sourceRepository;

    /**
     * @var LoggerInterface
     */
    public $logger;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->logger = $logger;
    }

    /**
     * Get All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [];
        $sourceList = $this->getSourcesList();
        if (isset($sourceList) && !empty($sourceList)) {
            foreach ($sourceList as $source) {
                if ($source->isEnabled()) {
                    $options[] = [
                        'label' => $source->getName(),
                        'value' => $source->getSourceCode()
                    ];
                }
            }
        }
        return $options;
    }

    /**
     * Get All source list
     *
     * @return SourceInterface[]|null
     */
    public function getSourcesList()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        try {
            $sourceData = $this->sourceRepository->getList($searchCriteria);
            if ($sourceData->getTotalCount()) {
                return $sourceData->getItems();
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
    }
}
