<?php

namespace Isobar\ImportExport\Ui\Component\Listing\Column;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Log
 * @package Isobar\ImportExport\Ui\Component\Listing\Column
 */
class Log extends \Firebear\ImportExport\Ui\Component\Listing\Column\Log
{
    /**
     * Log constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Filesystem $filesystem
     * @param UrlInterface $backendUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, Filesystem $filesystem, UrlInterface $backendUrl, array $components = [], array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $filesystem, $backendUrl, $components, $data);
        $this->directory = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
    }
}
