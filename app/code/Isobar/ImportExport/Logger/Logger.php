<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Isobar\ImportExport\Logger;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Web UI Logger
 *
 * @package Magento\Setup\Model
 */
class Logger extends \Firebear\ImportExport\Logger\Logger
{
    /**
     * Logger constructor.
     * @param Filesystem $filesystem
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param null $logFile
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        $logFile = null)
    {
        parent::__construct(
            $filesystem,
            $timezone,
            $logFile);
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }
}
