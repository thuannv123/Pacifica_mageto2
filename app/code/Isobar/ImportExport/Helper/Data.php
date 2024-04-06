<?php

namespace Isobar\ImportExport\Helper;

use Firebear\ImportExport\Api\Export\History\CreateInterface as ExportHistoryCreate;
use Firebear\ImportExport\Api\Export\HistoryRepositoryInterface as ExportHistoryRepository;
use Firebear\ImportExport\Api\HistoryRepositoryInterface;
use Firebear\ImportExport\Model\Email\Sender;
use Firebear\ImportExport\Model\ExportJob\Processor as ExportProcessor;
use Firebear\ImportExport\Model\Import\HistoryFactory;
use Firebear\ImportExport\Model\Job\Processor;
use Firebear\ImportExport\Model\ResourceModel\Import\DataFactory;
use Firebear\ImportExport\Model\ResourceModel\Import\History\CollectionFactory as ImportHistoryCollectionFactory;
use Firebear\ImportExport\Model\Source\Config;
use Firebear\ImportExport\Model\Source\Factory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Data extends \Firebear\ImportExport\Helper\Data
{
    /**
     * @var ImportHistoryCollectionFactory
     */
    protected $importHistoryCollectionFactory;


    /**
     * @param Context $context
     * @param Factory $sourceFactory
     * @param Config $configSource
     * @param \Firebear\ImportExport\Logger\Logger $logger
     * @param HistoryRepositoryInterface $historyRepository
     * @param ExportHistoryRepository $historyExRepository
     * @param HistoryFactory $historyFactory
     * @param ExportHistoryCreate $exportHistoryCreate
     * @param Processor $processor
     * @param ExportProcessor $exProcessor
     * @param TimezoneInterface $timezone
     * @param Filesystem $filesystem
     * @param DataFactory $dataFactory
     * @param \Firebear\ImportExport\Model\Source\Platform\Config $configPlatforms
     * @param Factory $factory
     * @param SerializerInterface $serializer
     * @param Sender $sender
     * @param ImportHistoryCollectionFactory $importHistoryCollectionFactory
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        Factory $sourceFactory,
        Config $configSource,
        \Firebear\ImportExport\Logger\Logger $logger,
        HistoryRepositoryInterface $historyRepository,
        ExportHistoryRepository $historyExRepository,
        HistoryFactory $historyFactory,
        ExportHistoryCreate $exportHistoryCreate,
        Processor $processor,
        ExportProcessor $exProcessor,
        TimezoneInterface $timezone,
        Filesystem $filesystem,
        DataFactory $dataFactory,
        \Firebear\ImportExport\Model\Source\Platform\Config $configPlatforms,
        Factory $factory,
        SerializerInterface $serializer,
        Sender $sender,
        ImportHistoryCollectionFactory $importHistoryCollectionFactory
    ) {
        $this->importHistoryCollectionFactory = $importHistoryCollectionFactory;
        parent::__construct(
            $context,
            $sourceFactory,
            $configSource,
            $logger,
            $historyRepository,
            $historyExRepository,
            $historyFactory,
            $exportHistoryCreate,
            $processor,
            $exProcessor,
            $timezone,
            $filesystem,
            $dataFactory,
            $configPlatforms,
            $factory,
            $serializer,
            $sender
        );
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * @return \Firebear\ImportExport\Model\Import\HistoryFactory
     */
    public function getHistoryFactory(): \Firebear\ImportExport\Model\Import\HistoryFactory
    {
        return $this->historyFactory;
    }

    /**
     * @return \Firebear\ImportExport\Api\HistoryRepositoryInterface
     */
    public function getHistoryRepository(): \Firebear\ImportExport\Api\HistoryRepositoryInterface
    {
        return $this->historyRepository;
    }

    /**
     * @param $jobId
     * @return \Magento\Framework\DataObject
     */
    public function getLastHistory($jobId)
    {
        return $this->importHistoryCollectionFactory->create()
            ->addFieldToFilter('job_id', $jobId)
            ->addFieldToFilter('type', 'admin')
            ->addFieldToFilter('status', 2)
            ->setOrder(
                'history_id',
                'DESC'
            )
            ->getFirstItem();
    }

    /**
     * @param $startedAt
     * @return bool
     */
    private function isTimeHasExpired($startedAt)
    {
        $now = $this->timeZone->date()->getTimestamp();
        $startedAtTimestamp = $this->timeZone->date($startedAt)->add(
            \DateInterval::createFromDateString("+30 minutes")
        )->getTimestamp();

        return $startedAtTimestamp < $now ? true : false;
    }

    /**
     * @param $id
     * @param $file
     * @return bool
     * @throws AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function runImport($id, $file)
    {
        $result = false;
        $this->processor->debugMode = $this->getDebugMode();
        $this->processor->setLogger($this->logger);
        $this->processor->prepareJob($id);
        $lastHistory = $this->getLastHistory($id);

        if ($lastHistory->getId() && $lastHistory->getStartedAt() && !$this->isTimeHasExpired(
                $lastHistory->getStartedAt()
            ) && $lastHistory->getStatus() == 2) {
            $this->logger->setFileName($file);
            $this->addLogComment(
                'The same import process is running and has not been finished yet.
         Please wait or come back after 30 minutes.',
                'info'
            );
            return false;
        }

        try {
            $history = $this->createHistory($id, $file, 'admin');
            $this->addLogComment(
                "Start running import job #". $id. ", History id #".$history->getId().", log file: ". $file
            );
            $result = $this->processor->processScope($id, $file);
            $this->setResultProcessor($result);
        } catch (\Exception $e) {
            $result = false;
            $history->setLogContent($e->getMessage());
            //Status: 1: created, 2: processing, 3:success, 4:falied
            $history->setStatus(4);
            $this->saveFinishHistory($history);
            $this->addLogComment(
                "History id #".$history->getId()." - Change status to failed"
            );
            $this->addLogComment(
                'Job #' . $id . ' can\'t be imported. Check if job exist',
                'error'
            );
            $this->addLogComment(
                $e->getMessage(),
                'error'
            );
        }
        return $result;
    }

    /**
     * @param $id
     * @param $file
     * @param $type
     * @return \Firebear\ImportExport\Api\Data\ImportHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createHistory($id, $file, $type)
    {
        $history = $this->historyFactory->create();
        $history->setJobId($id);
        $history->setType($type);
        $history->setStatus(2);
        $date = $this->timeZone->date();
        $timeStamp = $date->getTimestamp();
        $history->setStartedAt($timeStamp);
        $this->logger->setFileName($file);
        $history->setFile($file);
        if ($this->isEnableDbLogStorage()) {
            $history->setDbLogStorage(true);
        }
        $history = $this->historyRepository->save($history);

        return $history;
    }
}
