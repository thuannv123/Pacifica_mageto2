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
 * @category    Mageplaza
 * @package     Mageplaza_GdprPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Controller\Adminhtml\DownloadLog;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GdprPro\Controller\Adminhtml\AbstractDownloadLog;
use Mageplaza\GdprPro\Model\ResourceModel\DownloadLogs\CollectionFactory;

/**
 * Class Clear
 * @package Mageplaza\GdprPro\Controller\Adminhtml\DownloadLog
 */
class Clear extends AbstractDownloadLog
{
    /**
     * @var File
     */
    protected $file;

    /**
     * Clear constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param File $file
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        File $file
    ) {
        $this->file = $file;

        parent::__construct($context, $resultPageFactory, $filter, $collectionFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $this->file->deleteDirectory(DirectoryList::VAR_DIR . '/var/mp_gdpr/');
        $collection = $this->collectionFactory->create();
        $count      = $collection->getSize();

        $collection->getConnection()->truncateTable($collection->getMainTable());

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $count));

        return $this->_redirect('*/*/');
    }
}
