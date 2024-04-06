<?php

namespace Isobar\ImportExport\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;

class CleanTemporaryImageFolder {

    const IMGS_PATH = "/media/import/";

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * @param Filesystem $fileSystem
     * @param File $file
     */
    public function __construct(Filesystem $fileSystem, File $file)
    {
        $this->fileSystem = $fileSystem;
        $this->file = $file;
        $this->directory = $this->fileSystem->getDirectoryRead(DirectoryList::PUB);
    }

    /**
     * Execute delete temporary images when Firebear imported.
     *
     * @return void
     */
    public function execute()
    {
        $importRootDir = $this->fileSystem->getDirectoryRead(DirectoryList::PUB)->getAbsolutePath();
        $listFiles = $this->directory->read(self::IMGS_PATH);
        foreach ($listFiles as $key => $filePath) {
            $file = explode("/", $filePath);

            if ($this->directory->isExist($filePath) 
                && $this->directory->isFile($filePath)
                && strpos($file[2], '.') !== 0) {
                $this->file->deleteFile($importRootDir . $filePath);
            } elseif ($this->directory->isExist($filePath) 
                && $this->directory->isDirectory($filePath)
                && strpos($file[2], '.') !== 0) {
                $this->file->deleteDirectory($importRootDir . $filePath);
            }
        }
        return $this;
    }
}
