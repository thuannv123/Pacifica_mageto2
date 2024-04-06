<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Sales Rules Wizard for Magento 2 (System)
 */

namespace Amasty\SalesRuleWizard\Plugin\SalesRule\Block\Adminhtml\Promo;

class QuotePlugin
{
    /**
     * @param \Magento\SalesRule\Block\Adminhtml\Promo\Quote $subject
     */
    public function beforeSetLayout(\Magento\SalesRule\Block\Adminhtml\Promo\Quote $subject)
    {
        $subject->addButton(
            'addWizard',
            [
                'label' => __('Free Gift Rules Wizard'),
                'onclick' => 'setLocation(\'' . $subject->getUrl('amasty_promowizard/wizard') . '\')',
                'class' => 'add primary'
            ]
        );
    }
}
