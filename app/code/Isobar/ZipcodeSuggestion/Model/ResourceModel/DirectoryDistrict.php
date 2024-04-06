<?php

namespace Isobar\ZipcodeSuggestion\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DirectoryDistrict extends AbstractDb
{


    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('directory_district', 'district_id');
    }
}
