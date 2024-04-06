<?php

namespace Isobar\Megamenu\Model\Config\Backend;

class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * The tail part of directory path for uploading
     *
     */
    const UPLOAD_DIR = 'config'; // Folder save image
    const PATH_DEFAULT = 'default';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */

    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }
    protected function _appendScopeInfo($path)
    {
        return  $path .= '/' . self::PATH_DEFAULT;
    }
}
