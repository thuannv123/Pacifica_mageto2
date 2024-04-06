<?php
namespace Marvelic\Export\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\Model\UrlInterface;

class Log extends \Firebear\ImportExport\Ui\Component\Listing\Column\Log
{
    /**
     * @var Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * Log constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Filesystem $filesystem
     * @param UrlInterface $backendUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Filesystem $filesystem,
        UrlInterface $backendUrl,
        array $components = [],
        array $data = []
    )
    {
        $this->directory = $filesystem->getDirectoryRead(DirectoryList::LOG);
        $this->rootDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        parent::__construct($context, $uiComponentFactory, $filesystem, $backendUrl, $components, $data);
    }

    /**
     * @param $item
     * @return string
     */
    public function prepareItem($item)
    {
        if (!empty($item['db_log_storage'])) {
            $urlPath = $this->getData('config/urlPathForDbStorage') ?: '#';
            $jobType = $this->getData('config/jobType') ?: 'import';
            $path = $this->backendUrl->getUrl($urlPath, ['history_id' => $item['history_id'], 'job_type' => $jobType]);
            return '<a href="' . $path . '">' . __('Download') . '</a>';
        } elseif ($this->directory->isFile('/firebear/' . $item['file'] . '.log')) {
            $urlPath = $this->getData('config/urlPath') ?: '#';
            $path = $this->backendUrl->getUrl($urlPath, ['file' => $item['file']]);
            $filePath = $this->rootDirectory->getAbsolutePath('/log/firebear/' . $item['file'] . '.log');
            if ($this->rootDirectory->isFile($filePath)) {
                return '<a href="' . $path . '">' . __('Download') . '</a>';
            } else {
                return __('File doesn\'t exist or not found');
            }
        } else {
            return __('File doesn\'t exist or not found');
        }
    }
}
