<?php

namespace Isobar\Sales\Plugin\Order\Totals;

class Tax
{
    public function beforeToHtml(\Magento\Sales\Block\Adminhtml\Order\Totals\Tax $subject)
    {
        $subject->setTemplate('Isobar_Sales::order/totals/tax.phtml');
    }
}
