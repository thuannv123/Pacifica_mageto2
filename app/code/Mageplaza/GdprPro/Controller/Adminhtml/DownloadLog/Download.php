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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GdprPro\Controller\Adminhtml\AbstractDownloadLog;
use Mageplaza\GdprPro\Model\ResourceModel\DownloadLogs\CollectionFactory;

/**
 * Class Download
 * @package Mageplaza\GdprPro\Controller\Adminhtml\DownloadLog
 */
class Download extends AbstractDownloadLog
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Download constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;

        parent::__construct($context, $resultPageFactory, $filter, $collectionFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ((int) $id) {
            $log = $this->collectionFactory->create()->addFieldToFilter('entity_id', $id)->getFirstItem();

            $fileType = $log->getFileType();
            $path     = $log->getPath();

            return $this->fileFactory->create(
                'customer_data.' . $fileType,
                [
                    'type'  => 'filename',
                    'value' => $path
                ],
                DirectoryList::VAR_DIR
            );
        }

        $this->messageManager->addErrorMessage(__('Something wrong when download file.'));

        return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
    }
}
