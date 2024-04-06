<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Controller\Account;

use IntlDateFormatter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\File\WriteFactory;
use Magento\Framework\Filesystem\File\WriteInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GdprPro\Model\DownloadLogsFactory;

/**
 * Class Download
 * @package Mageplaza\GdprPro\Controller\Account
 */
class Download extends Action
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
     * @var CustomerSession
     */
    protected $_customerSession;

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
     * @var DownloadLogsFactory
     */
    protected $downloadLogsFactory;

    /**
     * Download constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     * @param Csv $csvProcessor
     * @param ExcelFactory $excelFactory
     * @param WriteFactory $fileWriteFactory
     * @param File $driverFile
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ManagerInterface $messageManager
     * @param TimezoneInterface $timeZone
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param DownloadLogsFactory $downloadLogsFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        Csv $csvProcessor,
        ExcelFactory $excelFactory,
        WriteFactory $fileWriteFactory,
        File $driverFile,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ManagerInterface $messageManager,
        TimezoneInterface $timeZone,
        DateTime $date,
        StoreManagerInterface $storeManager,
        DownloadLogsFactory $downloadLogsFactory
    ) {
        $this->fileFactory         = $fileFactory;
        $this->filesystem          = $filesystem;
        $this->driverFile          = $driverFile;
        $this->excelFactory        = $excelFactory;
        $this->csvProcessor        = $csvProcessor;
        $this->_messageManager     = $messageManager;
        $this->fileWriteFactory    = $fileWriteFactory;
        $this->_customerSession    = $customerSession;
        $this->resultPageFactory   = $resultPageFactory;
        $this->_customerRepository = $customerRepositoryInterface;
        $this->timeZone            = $timeZone;
        $this->date                = $date;
        $this->storeManager        = $storeManager;
        $this->downloadLogsFactory = $downloadLogsFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws FileSystemException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->_customerSession->isLoggedIn()) {
            $fileType = $this->_request->getParam('file_type');
            if ($fileType !== null) {
                $customerData               = $this->_customerSession->getCustomerData()->__toArray();
                $customerData['created_at'] = $this->formatDate($customerData['created_at']);
                $customerData['updated_at'] = $this->formatDate($customerData['updated_at']);
                $customerCollection         = $this->_customerSession->getCustomer()->getCollection();

                $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('mp_gdpr');
                $path   = DirectoryList::VAR_DIR . '/mp_gdpr/' . time() . '.' . $fileType;
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

                $customer = $this->_customerSession->getCustomer();
                $storeId  = $this->storeManager->getStore()->getId();

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

                $downloadLog = $this->downloadLogsFactory->create();
                $downloadLog->setData($downloadData)->save();

                return $this->fileFactory->create(
                    $fileName,
                    [
                        'type'  => 'filename',
                        'value' => $path
                    ],
                    DirectoryList::VAR_DIR
                );
            }

            $this->_messageManager->addErrorMessage(__('Invalid Form Key.'));
            $resultRedirect->setPath('/');

            return $resultRedirect;
        }

        $this->_messageManager->addErrorMessage(__('You need to login to download your person data!'));
        $resultRedirect->setPath('*/*/login');

        return $resultRedirect;
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public function formatDate($date)
    {
        return $this->timeZone->formatDate($date, IntlDateFormatter::MEDIUM, true);
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
        if ($data->getData('entity_id') === $this->_customerSession->getCustomerId()) {
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
}
