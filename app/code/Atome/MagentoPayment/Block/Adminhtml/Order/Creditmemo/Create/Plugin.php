<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Block\Adminhtml\Order\Creditmemo\Create;

use Atome\MagentoPayment\Services\Config\Atome;
use Closure;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items;

class Plugin
{
    /**
     * @param Items $subject
     * @param Closure $proceed
     * @param LayoutInterface $layout
     * @return mixed
     */
    public function aroundSetLayout($subject, Closure $proceed, $layout)
    {
        $ret = $proceed($layout);
//        $payment = $subject->getCreditmemo()->getOrder()->getPayment();
//        if ($payment->getMethod() === Atome::METHOD_CODE) {
////         remove the  "Refund Offline" button in the Invoice => Credit Memo page
//            $subject->unsetChild('submit_offline');
//        }
        return $ret;
    }
}
