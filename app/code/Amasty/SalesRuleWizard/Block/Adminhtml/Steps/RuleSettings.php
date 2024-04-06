<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Sales Rules Wizard for Magento 2 (System)
 */

namespace Amasty\SalesRuleWizard\Block\Adminhtml\Steps;

class RuleSettings extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        // Step 2
        return __('Product Settings');
    }
}
