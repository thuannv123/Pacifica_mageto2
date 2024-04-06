<?php

namespace Isobar\ImportExport\Model\Email;

use Magento\Framework\App\Filesystem\DirectoryList;

class Sender extends \Firebear\ImportExport\Model\Email\Sender
{
    protected function getDirectory()
    {
        if (null === $this->dir) {
            $this->dir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        }
        return $this->dir;
    }
}
