<?php

namespace Isobar\ZipcodeSuggestion\Model;

use \Magento\Framework\Model\AbstractModel;

class DirectoryDistrict extends AbstractModel
{


    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Isobar\ZipcodeSuggestion\Model\ResourceModel\DirectoryDistrict');
    }
}
