<?php

namespace Mageplaza\GdprPro\Model\Api;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\File\WriteFactory;
use Magento\Framework\Filesystem\File\WriteInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Webapi\Rest\Response as RestResponse;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GdprPro\Model\DownloadLogsFactory;

/**
 * Class DownLoad
 * @package Mageplaza\GdprPro\Model\Api\DownLoad
 */
class DownLoad
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var ExcelFactory
     */
    protected $excelFactory;

    /**
     * @var WriteFactory
     */
    protected $fileWriteFactory;

    /**
     * @var File
     */
    protected $driverFile;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RestResponse
     */
    protected $_response;

    /**
     * @var ReadFactory
     */
    protected $_readFactory;

    /**
     * @var array
     */
    private $mimet = [
        'csv' => 'application/csv',
        'xml' => 'application/xml',
    ];
    /**
     * @var DownloadLogsFactory
     */
    protected $downloadLogsFactory;
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;
    /**
     * @var
     */
    protected $customerId;

    /**
     * Download CustomerData constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     * @param Csv $csvProcessor
     * @param ExcelFactory $excelFactory
     * @param WriteFactory $fileWriteFactory
     * @param File $driverFile
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ManagerInterface $messageManager
     * @param TimezoneInterface $timeZone
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param RestResponse $response
     * @param ReadFactory $readFactory
     * @param DownloadLogsFactory $downloadLogsFactory
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        Csv $csvProcessor,
        ExcelFactory $excelFactory,
        WriteFactory $fileWriteFactory,
        File $driverFile,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ManagerInterface $messageManager,
        TimezoneInterface $timeZone,
        DateTime $date,
        StoreManagerInterface $storeManager,
        RestResponse $response,
        ReadFactory $readFactory,
        DownloadLogsFactory $downloadLogsFactory,
        CustomerFactory $customerFactory
    ) {
        $this->fileFactory         = $fileFactory;
        $this->filesystem          = $filesystem;
        $this->driverFile          = $driverFile;
        $this->excelFactory        = $excelFactory;
        $this->csvProcessor        = $csvProcessor;
        $this->_messageManager     = $messageManager;
        $this->fileWriteFactory    = $fileWriteFactory;
        $this->resultPageFactory   = $resultPageFactory;
        $this->resultPageFactory   = $resultPageFactory;
        $this->_customerRepository = $customerRepositoryInterface;
        $this->timeZone            = $timeZone;
        $this->date                = $date;
        $this->storeManager        = $storeManager;
        $this->_response           = $response;
        $this->_readFactory        = $readFactory;
        $this->downloadLogsFactory = $downloadLogsFactory;
        $this->_customerFactory    = $customerFactory;
    }

    /**
     * @param $customerId
     * @param $fileType
     *
     * @throws FileSystemException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function downLoadCustomerData($customerId, $fileType)
    {
        try {
            $customer = $this->_customerRepository->getById($customerId);
        } catch (Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()));
        }
        $this->customerId           = $customerId;
        $customerData               = $customer->__toArray();
        $customerData['created_at'] = $this->formatDate($customerData['created_at']);
        $customerData['updated_at'] = $this->formatDate($customerData['updated_at']);
        $customerCollection         = $this->getCustomerCollection();

        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('mp_gdpr');
        $path   = DirectoryList::VAR_DIR . '/mp_gdpr/customer_data' . '.' . $fileType;
        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($path, 'w+');
        $stream->lock();
        $fileName = 'customer_data.' . $fileType;

        if ($fileType === 'csv') {
            $this->exportToCSV($stream, $customerData);
        } else {
            $this->exportToXML($stream, $customerCollection);
        }
        $stream->unlock();
        $stream->close();

        $storeId      = $this->storeManager->getStore()->getId();
        $downloadData = [
            'customer_name'     => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'customer_id'       => $customer->getId(),
            'customer_email'    => $customer->getEmail(),
            'store_id'          => $storeId,
            'customer_group_id' => $customer->getGroupId(),
            'file_type'         => $fileType,
            'path'              => $path,
            'created_at'        => $this->date->date(),
            'updated_at'        => $this->date->date()
        ];
        $downloadLog  = $this->downloadLogsFactory->create();
        $downloadLog->setData($downloadData)->save();

        $mediaPath        = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath();
        $directoryRead    = $this->_readFactory->create($mediaPath);
        $fileAbsolutePath = $path;
        if (!$directoryRead->isFile($fileAbsolutePath)) {
            throw new \Magento\Framework\Webapi\Exception(__('%1 not a file', $path));
        }
        /** @var RestResponse $resultRaw */
        $resultRaw = $this->_response;
        $resultRaw->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-Length', strlen($directoryRead->readFile($fileAbsolutePath)))
            ->setHeader('Content-Description', 'File Transfer')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Expires', '0')
            ->setHeader('Pragma', 'public')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
            ->setHeader('Content-type', $this->getContentType($fileName), true)
            ->setHeader('Last-Modified', date('r'), true);
        $resultRaw->setContent($directoryRead->readFile($path));
        $resultRaw->sendResponse();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|AbstractCollection|null
     */
    public function getCustomerCollection()
    {
        return $this->_customerFactory->create()->getCollection();
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public function formatDate($date)
    {
        return $this->timeZone->formatDate($date, 2, true);
    }

    /**
     * @param WriteInterface $stream
     * @param array $customerData
     *
     * @throws FileSystemException
     */
    public function exportToCSV($stream, $customerData)
    {
        $data        = $this->formatCustomerData($customerData);
        $addressData = [];
        if (count($customerData['addresses']) > 0) {
            $addressData = $this->formatAddressData($customerData['addresses'][0]);
        }
        $data = array_merge($data, $addressData);
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }
    }

    /**
     * @param WriteInterface $stream
     * @param AbstractCollection $collection
     */
    public function exportToXML($stream, $collection)
    {
        $iterator    = $collection->getIterator();
        $rowCallback = [$this, 'getCustomerData'];
        $excel       = $this->excelFactory->create(compact('iterator', 'rowCallback'));
        $excel->write($stream);
    }

    /**
     * @param DataObject $data
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerData(DataObject $data)
    {
        $arr = [];
        if ($data->getData('entity_id') === $this->customerId) {
            $customerData               = $this->_customerRepository->getById($data->getData('entity_id'))->__toArray();
            $customerData['created_at'] = $this->formatDate($customerData['created_at']);
            $customerData['updated_at'] = $this->formatDate($customerData['updated_at']);
            $addressArr                 = [];
            if (count($customerData['addresses']) > 0) {
                $addressArr = $this->formatAddressData($customerData['addresses'][0], true);
            }
            $arr = $this->formatCustomerData($customerData, true);
            $arr = array_merge($arr, $addressArr);
        }

        return $arr;
    }

    /**
     * @param array $addressData
     * @param bool $checkXmlFile
     *
     * @return array
     */
    public function formatAddressData($addressData, $checkXmlFile = false)
    {
        $arr = [];
        if (is_array($addressData)) {
            foreach ($addressData as $key => $value) {
                if (!is_array($value)) {
                    if ($value !== null) {
                        $key  = str_replace('_', ' ', $key);
                        $data = ('Address ' . ucfirst($key) . ',' . $value);
                        if ($checkXmlFile) {
                            $arr [] = $data;
                        } else {
                            $arr [] = [$data];
                        }
                    }
                } else {
                    $data = ('Address ' . ucfirst($key) . ',' . implode(',', $value));
                    if ($checkXmlFile) {
                        $arr [] = $data;
                    } else {
                        $arr [] = [$data];
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * @param array $customerData
     * @param bool $checkXmlFile
     *
     * @return array
     */
    public function formatCustomerData($customerData, $checkXmlFile = false)
    {
        $arr = [];
        if (is_array($customerData)) {
            foreach ($customerData as $key => $value) {
                if ($value !== null && !is_array($value)) {
                    $label = str_replace('_', ' ', $key);
                    if ($checkXmlFile) {
                        $arr[] = (ucfirst($label) . ',' . $value);
                    } else {
                        $arr[] = [ucfirst($label), $value];
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * @param string $name
     *
     * @return string
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getContentType($name)
    {
        $fileNameInfo = explode('.', $name);

        if (count($fileNameInfo) === 2 && array_key_exists($fileNameInfo[1], $this->mimet)) {
            return $this->mimet[$fileNameInfo[1]];
        }

        throw new \Magento\Framework\Webapi\Exception(__('Only support xml and csv type'));
    }
}