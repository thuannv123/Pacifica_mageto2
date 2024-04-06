<?php

namespace Isobar\ImportExport\Plugin\Model\Job;

use Firebear\ImportExport\Model\Job\Processor;
use Firebear\ImportExport\Api\Data\ImportHistoryInterfaceFactory;
use Firebear\ImportExport\Model\ResourceModel\Import\History as ImportHistoryResource;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;

class ProcessorPlugin
{
    private ImportHistoryInterfaceFactory $historyFactory;

    private ImportHistoryResource $importHistoryResource;

    private TimezoneInterface $timezone;

    private LoggerInterface $logger;

    /**
     * @param ImportHistoryInterfaceFactory $historyFactory
     * @param ImportHistoryResource $importHistoryResource
     * @param TimezoneInterface $timezone
     * @param LoggerInterface $logger
     */
    public function __construct(
        ImportHistoryInterfaceFactory $historyFactory,
        ImportHistoryResource $importHistoryResource,
        TimezoneInterface $timezone,
        LoggerInterface $logger
    ) {
        $this->historyFactory = $historyFactory;
        $this->importHistoryResource = $importHistoryResource;
        $this->timezone = $timezone;
        $this->logger = $logger;
    }

    /**
     * @param Processor $subject
     * @param array $result
     * @param $file
     * @param $job
     * @param $offset
     * @param $error
     * @param int $show
     * @return array
     * @throws AlreadyExistsException
     */
    public function afterProcessImport(Processor $subject, array $result, $file, $job, $offset, $error, $show = 1): array
    {
        if (isset($result[1]) && $result[1] === false) {
            $history = $this->historyFactory->create();
            $this->importHistoryResource->load($history, $file, 'file');

            if ($history->getId()) {
                $date = $this->timezone->date();
                $timeStamp = $date->getTimestamp();
                $history->setFinishedAt($timeStamp);

                //Status history: 1: created, 2: processing, 3:success, 4:failed
                $history->setStatus(4);
                $this->logger->info(
                    "History id #".$history->getId()." - Change status to failed"
                );
                $this->importHistoryResource->save($history);
            }

            $this->logger->info(
                "Isobar\ImportExport\Plugin\Model\Job\ProcessorPlugin was fired!,
                    file_id: " . $file . ', status: ' . $result[1],
                $result
            );
        }

        return $result;
    }
}
