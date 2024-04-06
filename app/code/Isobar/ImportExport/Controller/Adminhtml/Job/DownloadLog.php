<?php

namespace Isobar\ImportExport\Controller\Adminhtml\Job;

use Firebear\ImportExport\Controller\Adminhtml\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Class DownloadLog
 * @package Isobar\ImportExport\Controller\Adminhtml\Job
 */
class DownloadLog extends \Firebear\ImportExport\Controller\Adminhtml\Job\DownloadLog
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ReadInterface
     */
    protected $directory;

    /**
     * DownloadLog constructor.
     * @param Context $context
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        FileFactory $fileFactory
    )
    {
        parent::__construct($context, $filesystem, $fileFactory);
        $this->fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
    }
    /**
     * @param $file
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function downloadFile($file)
    {
        $file = $this->directory->getAbsolutePath(). 'firebear/' . $file .".log" ;
        return $this->fileFactory->create(basename($file), file_get_contents($file), DirectoryList::VAR_DIR);
    }
}
