<?php

namespace Meetanshi\OrderUpload\Controller\Adminhtml\OrderUpload;

use Meetanshi\OrderUpload\Model\OrderUpload\FileUploaderFactory;
use Meetanshi\OrderUpload\Model\OrderUpload;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Upload
 * @package Meetanshi\OrderUpload\Controller\Adminhtml\OrderUpload
 */
class Upload extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var FileUploaderFactory
     */
    private $fileUploaderFactory;
	
	protected $_timezoneInterface;
    
    /**
     * Upload constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Filesystem $filesystem
     * @param FileUploaderFactory $fileUploaderFactory
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, Filesystem $filesystem, FileUploaderFactory $fileUploaderFactory, TimezoneInterface $timezone)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->_timezoneInterface = $timezone;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $uploadFiles = $this->getRequest()->getFiles('file');
        $result = [];
	
	    $today = $this->_timezoneInterface->date()->format("Y-m-d H:i:s");
        
        try {
            $i = 0;
            foreach ($uploadFiles as $file) {
                $fileUploader = $this->fileUploaderFactory->create(['fileId' => 'file[' . $i . ']'])->setAllowRenameFiles(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $result[$i] = $fileUploader->save($mediaDirectory->getAbsolutePath(OrderUpload::ORDERUPLOAD_TMP_PATH));
                $result[$i]['currentDate'] = $today;
                // $result[$i]['currentDate'] = date('Y-m-d h:i:s', $today);
                $i++;
            }
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
