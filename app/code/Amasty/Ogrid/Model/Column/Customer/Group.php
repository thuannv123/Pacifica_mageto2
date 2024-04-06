<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\Column\Customer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection;

class Group extends \Amasty\Ogrid\Model\Column
{
    public function addField(Collection $collection, $mainTableAlias = 'main_table')
    {
        $customerColumn = ObjectManager::getInstance()->create(\Amasty\Ogrid\Model\Column\Customer::class);
        $customerColumn->addField($collection);

        parent::addField($collection, $customerColumn->getAlias());
    }
}
