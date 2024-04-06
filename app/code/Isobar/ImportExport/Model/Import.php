<?php

namespace Isobar\ImportExport\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Firebear\ImportExport\Model\Source\Type\File\Config;
use Symfony\Component\Console\Output\ConsoleOutput;
use Firebear\ImportExport\Traits\General as GeneralTrait;

class Import extends \Firebear\ImportExport\Model\Import
{
    use \Firebear\ImportExport\Traits\Import\Entity;
    use GeneralTrait;

    protected $_importDataFirebear;

    public function __construct(
        \Firebear\ImportExport\Model\Source\ConfigInterface $config,
        \Firebear\ImportExport\Helper\Data $helper,
        \Firebear\ImportExport\Helper\Additional $additional,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Firebear\ImportExport\Model\Source\Import\Config $importConfig,
        \Magento\ImportExport\Model\Import\Entity\Factory $entityFactory,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvFactory,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\ImportExport\Model\Source\Import\Behavior\Factory $behaviorFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\ImportExport\Model\History $importHistoryModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $localeDate,
        \Firebear\ImportExport\Model\Source\Factory $factory,
        \Firebear\ImportExport\Model\Source\Platform\Config $configPlatforms,
        \Magento\Framework\FilesystemFactory $filesystemFactory,
        \Firebear\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWrite,
        \Firebear\ImportExport\Model\Output\Xslt $modelOutput,
        Config $typeConfig,
        ConsoleOutput $output,
        array $data = []
    ) {
        parent::__construct(
            $config,
            $helper,
            $additional,
            $timezone,
            $logger,
            $filesystem,
            $importExportData,
            $coreConfig,
            $importConfig,
            $entityFactory,
            $csvFactory,
            $httpFactory,
            $uploaderFactory,
            $behaviorFactory,
            $indexerRegistry,
            $importHistoryModel,
            $localeDate,
            $factory,
            $configPlatforms,
            $filesystemFactory,
            $importData,
            $file,
            $fileWrite,
            $modelOutput,
            $typeConfig,
            $output,
            $data
        );

        $this->_importDataFirebear = $importData;
    }

    /**
     * @param $file
     * @param $offset
     * @param $job
     * @param $show
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function importSourcePart($file, $offset, $job, $show)
    {
        $offset = $this->_importDataFirebear->getOffset() ?: $offset;
        $this->logDuplicateJob('First offset: ' . $offset);

        // if (!$this->validateJobByHistoyData($file, $offset, $job)) {
        //     return true;
        // }

        $this->setData('entity', $this->getEntity());
        $this->setData('behavior', $this->getBehavior());

        if (0 == $offset) {
            $this->addLogComment(
                __('Begin import of "%1" with "%2" behavior', $this->getEntity(), $this->getBehavior())
            );
        }

        if ($this->getEntity() == "catalog_product") {
            $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/import_product_duplicate.log");
            $logger = new \Laminas\Log\Logger();
            $logger->addWriter($writer);
            try {
                throw new \Exception("duplicate import product tracking");
            } catch (\Exception $exception) {
                $logger->info($exception->getTraceAsString());
            }
        }

        $status = $this->processImportPart($file, $offset, $job);
        $this->logDuplicateJob('Status: ' . $status);
        if ($status) {
            $this->showErrors();
            if (empty($this->getProcessedEntitiesCount())) {
                $status = false;
                $this->addLogComment(__('No data imported.'));
            } elseif (empty($this->getErrorAggregator()->getErrorsCount())) {
                $this->addLogComment(__('The import was successful.'));
            }
        }
        return $status;
    }

    /**
     * Validate current process to avoid duplicate import process
     * @param $file
     * @param $offset
     * @param $job
     * @return bool (false: have a same process is running)
     */
    public function validateJobByHistoyData($file, $offset, $job)
    {
        /** @var \Firebear\ImportExport\Model\ResourceModel\Import\History\Collection $jobHistoryCollection */
        $jobHistoryCollection = $this->helper->getHistoryFactory()->create()->getCollection();
        $jobHistoryCollection->addFieldToFilter('job_id', $job);
        $jobHistoryCollection->addFieldToFilter('file', $file);

        if ($jobHistoryCollection->getSize()) {
            $historyData = $jobHistoryCollection->getFirstItem();

            /*history content doesn't have any data*/
            if ($historyData && empty($historyData->getData('log_content'))) {
                $this->addLogComment(__('Import Job is running.'));
                $this->addLogComment(__('Job ID: ') . $job);
                $this->addLogComment(__('Log file: ') . $file);
                $this->addLogComment(__('Offset: ') . $offset);
                $this->logDuplicateJob('Second offset: ' . $offset);
            }

            if ($historyData && !empty($historyData->getData('log_content'))) {
                try {
                    $jobProcesses = $this->helper->getSerializer()->unserialize($historyData->getData('log_content'));
                    if (is_array($jobProcesses)) {
                        foreach ($jobProcesses as $jobProcess) {
                            $sameJobId = false;
                            $sameLogFile = false;
                            $sameOffset = false;
                            if (isset($jobProcess['job_id']) && $jobProcess['job_id'] == $job) {
                                $sameJobId = true;
                            }

                            if (isset($jobProcess['file']) && $jobProcess['file'] == $file) {
                                $sameLogFile = true;
                            }

                            if (isset($jobProcess['offset']) && $jobProcess['offset'] == $offset) {
                                $this->logDuplicateJob('Third offset: ' . $offset);
                                $sameOffset = true;
                            }

                            if ($sameJobId && $sameLogFile && $sameOffset) {
                                $this->addLogComment(__('Another Import Job is running.'));
                                $this->addLogComment(__('Job ID: ') . $job);
                                $this->addLogComment(__('Log file: ') . $file);
                                $this->addLogComment(__('Offset: ') . $offset);
                                $this->addLogComment(__('This process will run after current process completed'));
                                return false;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $this->addLogComment(__('Import Job Validate failed: '. $e->getMessage()));
                }
            }

            try {
                /*log current process data to history*/
                $this->logCurrentProcess($historyData, $file, $offset, $job);
                return true;
            } catch (\Exception $e) {
                $this->addLogComment(__('Import History Save failed: ') . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    /**
     * add current process to history - log content
     *
     * @param $historyData
     * @param $file
     * @param $offset
     * @param $job
     */
    public function logCurrentProcess($historyData, $file, $offset, $job)
    {
        if ($historyData->getData()) {
            if (empty($historyData->getData('log_content'))) {
                $jobProcesses = [];
            } else {
                $jobProcesses = $this->helper->getSerializer()->unserialize($historyData->getData('log_content'));
            }
            /*add current process to log content - to validate if duplicate process*/
            if (is_array($jobProcesses)) {
                $jobProcesses[] = [
                    'job_id' => $job,
                    'file' => $file,
                    'offset' => $offset
                ];

                $this->logDuplicateJob('Fourth offset: ' . $offset);

                $historyData->setData('log_content', $this->helper->getSerializer()->serialize($jobProcesses));
                $this->helper->getHistoryRepository()->save($historyData);
            }
        }
    }

    /**
     * Validates source file and returns validation result.
     *
     * @param \Magento\ImportExport\Model\Import\AbstractSource $source
     *
     * @return bool
     * @throws LocalizedException
     */
    public function validateSource(\Magento\ImportExport\Model\Import\AbstractSource $source)
    {
        $this->addLogWriteln(__('Begin data validation'), $this->output, 'comment');
        /** @var \Firebear\ImportExport\Traits\Import\Map $source */
        $source->setReplaceWithDefault(
            $this->getData('replace_default_value')
        );
        $source->setPlatform(
            $this->getPlatform(
                $this->getData('platforms'),
                $this->getData('entity')
            )
        );
        try {
            $this->prepareMap($this->getData('platforms'), $this->getData('entity'));
            if (!$source->getMap()) {
                $source->setMap($this->getData('map'));
            }
            $source->setReplacing($this->getData('replacing'));

            $adapter = $this->_getEntityAdapter()->setSource($source);
            $adapter->setLogger($this->_logger);
            $adapter->setOutput($this->output);

            $this->_importDataFirebear->setJobId($this->getData('job_id'));
            $this->_importDataFirebear->setOffset($this->getData('offset'));
            $this->_importDataFirebear->setFile($this->getData('file'));

            $errorAggregator = $adapter->validateData();
        } catch (\Exception $e) {
            $errorAggregator = $this->getErrorAggregator();
            $this->addLogWriteln($e->getMessage(), $this->output, 'error');
            $errorAggregator->addError(
                \Magento\ImportExport\Model\Import\Entity\AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION . '. '
                . $e->getMessage(),
                ProcessingError::ERROR_LEVEL_CRITICAL,
                null,
                null,
                null,
                $e->getMessage()
            );
        }

        $this->addLogComment(
            __(
                'Checked rows: %1, checked entities: %2, invalid rows: %3, total errors: %4',
                $this->getProcessedRowsCount(),
                $this->getProcessedEntitiesCount(),
                $this->getErrorAggregator()->getInvalidRowsCount(),
                $this->getErrorAggregator()->getErrorsCount()
            )
        );
        $this->showErrors();
        $result = !$errorAggregator->isErrorLimitExceeded();
        if ($result) {
            if ($this->isExistRowsForImport()) {
                if ($this->isImportAllowed()) {
                    $message = __('Import data validation is complete.');
                } else {
                    $message = __('The file is valid, but we can\'t import it for some reason.');
                }
                $this->addLogWriteln($message, $this->output, 'info');
            } else {
                $this->addLogComment(
                    __('Data validation failed. Please fix the errors and upload the file again.')
                );
            }
        }
        return $result;
    }

    /**
     * Return Platform PreSet Map
     *
     * @param string $name
     * @param string $entity
     * @return mixed[]
     */
    private function prepareMap($name, $entity)
    {
        $map = $this->getData('map') ?: [];
        $keys = array_column($map, 'system');
        $fields = $this->platforms[$entity][$name]['fields'] ?? [];
        if (is_array($fields)) {
            foreach ($fields as $field => $data) {
                /* skip predefined attributes if a custom rule is set */
                if (in_array($data['reference'], $keys)) {
                    continue;
                }
                $map[] = [
                    'system' => $data['reference'],
                    'import' => $field,
                    'default' => $data['default'] ?: null,
                ];
            }
        }
        $this->setData('map', $map);
    }

    public function logDuplicateJob($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/firebear/import_product_job.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }
}
