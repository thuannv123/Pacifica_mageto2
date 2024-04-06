<?php

namespace Isobar\ZipcodeSuggestion\Model\ResourceModel\DirectoryDistrict;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Isobar\ZipcodeSuggestion\Model\DirectoryDistrict',
            'Isobar\ZipcodeSuggestion\Model\ResourceModel\DirectoryDistrict'
        );
    }
}
