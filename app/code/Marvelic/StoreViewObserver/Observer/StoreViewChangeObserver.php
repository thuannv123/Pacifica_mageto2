<?php
namespace Marvelic\StoreViewObserver\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class StoreViewChangeObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $cacheTypeList;
    protected $cacheFrontendPool;

    public function __construct(
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute(Observer $observer){
        if(strpos($observer->getRequest()->getUriString(), 'checkout') !== false){
            $this->clearCheckoutPageCache();
        }
    }

    protected function clearCheckoutPageCache(){
        $_types = [
            'eav',
            'full_page',
            'translate',
            'config_webservice'
        ];
 
        foreach ($_types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }
}