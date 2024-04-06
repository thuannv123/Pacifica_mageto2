<?php

namespace Isobar\ImportExport\Plugin;

use Firebear\ImportExport\Api\Data\ImportHistoryInterfaceFactory;
use Firebear\ImportExport\Api\HistoryRepositoryInterface;
use Firebear\ImportExport\Controller\Adminhtml\Job\Terminate;
use Firebear\ImportExport\Model\ResourceModel\Import\History as ImportHistoryResource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class TerminatePlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyFactory;

    /**
     * @var ImportHistoryResource
     */
    private $importHistoryResource;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param RequestInterface $request
     * @param ImportHistoryInterfaceFactory $historyFactory
     * @param ImportHistoryResource $importHistoryResource
     * @param TimezoneInterface $timezone
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        ImportHistoryInterfaceFactory $historyFactory,
        ImportHistoryResource $importHistoryResource,
        TimezoneInterface $timezone,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->historyFactory = $historyFactory;
        $this->importHistoryResource = $importHistoryResource;
        $this->timezone = $timezone;
        $this->logger = $logger;
    }

    /**
     * @param Terminate $subject
     * @param ResultInterface $result
     * @return ResultInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function afterExecute(Terminate $subject, ResultInterface $result): ResultInterface
    {
        $fileId = $this->request->getParam('file');
        $status = $this->request->getParam('status');
        $history = $this->historyFactory->create();
        $this->importHistoryResource->load($history, $fileId, 'file');

        if ($history->getId()) {
            $date = $this->timezone->date();
            $timeStamp = $date->getTimestamp();
            $history->setFinishedAt($timeStamp);
            //Status history: 1: created, 2: processing, 3:success, 4:falied
            if ($status == 1) {
                $history->setStatus(3);
                $this->logger->info(
                    "History id #".$history->getId()." - Change status to success"
                );
            } else {
                $history->setStatus(4);
                $this->logger->info(
                    "History id #".$history->getId()." - Change status to failed"
                );
            }
            $this->importHistoryResource->save($history);
        }
        $this->logger->info(
            "Isobar\ImportExport\Plugin\TerminatePlugin was fired!, file_id: " . $fileId . ', status: ' . $status
        );
        return $result;
    }
}