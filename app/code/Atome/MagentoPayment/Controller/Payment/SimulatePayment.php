<?php

namespace Atome\MagentoPayment\Controller\Payment;


use Atome\MagentoPayment\Services\Config\Atome;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DirectoryList;

trait SimulatePayment
{

    protected function isSimulationEnvironment()
    {
        $objectManager = ObjectManager::getInstance();
        $directory = $objectManager->get(DirectoryList::class);

        return file_exists($directory->getRoot() . '/' . Atome::SIMULATE_FILE_NAME);
    }

}
