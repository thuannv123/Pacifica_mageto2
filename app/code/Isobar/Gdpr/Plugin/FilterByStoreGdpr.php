<?php

namespace Isobar\Gdpr\Plugin;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;

class FilterByStoreGdpr
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * FilterByStoreGdpr constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    private function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param $subject
     * @param $collection AbstractCollection
     * @return mixed
     */
    public function afterFilterByPolicyVersionAndLinkType(
        $subject,
        $collection
    ) {
        $collection->addFieldToFilter('s.store_id', $this->getStoreId());
        return $collection;
    }
}
