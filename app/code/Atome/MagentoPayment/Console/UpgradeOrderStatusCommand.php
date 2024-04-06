<?php

namespace Atome\MagentoPayment\Console;


use Atome\MagentoPayment\Enum\ConfigFormName;
use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeOrderStatusCommand extends Command
{
    protected function configure()
    {
        $this->setName('atome:update-order-status');
        $this->setDescription('Fix the missing setting value brought by the upgrade');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configWriter = ObjectManager::getInstance()->create(WriterInterface::class);

        $config = ObjectManager::getInstance()->create(PaymentGatewayConfig::class);
        $orderStatus = $config->getOrderStatus();
        if (!$orderStatus || $orderStatus === 'default') {
            $configWriter->save(Atome::getScopePath(ConfigFormName::ORDER_STATUS), 'processing');
        }

        $orderStatusBeforePayment = $config->getNewOrderStatus();
        if (!$orderStatusBeforePayment) {
            $configWriter->save(Atome::getScopePath(ConfigFormName::NEW_ORDER_STATUS), 'pending');
        }

        $maxSpend = $config->getMaxSpend();
        if (!$maxSpend) {
            $configWriter->save(Atome::getScopePath(ConfigFormName::MAX_SPEND), '0');
        }

        $minSpend = $config->getMinSpend();
        if (!$minSpend) {
            $configWriter->save(Atome::getScopePath(ConfigFormName::MIN_SPEND), '0');
        }

        $cancelTimeout = $config->getCancelTimeout();
        if (!$cancelTimeout) {
            $configWriter->save(Atome::getScopePath(ConfigFormName::CANCEL_TIMEOUT), Atome::CANCEL_TIMEOUT_MAXIMUM_MINUTES);
        }
    }
}
