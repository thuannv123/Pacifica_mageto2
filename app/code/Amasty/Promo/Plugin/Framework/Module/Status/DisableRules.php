<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Free Gift Base for Magento 2
 */

namespace Amasty\Promo\Plugin\Framework\Module\Status;

use Amasty\Promo\Model\ResourceModel\Rule;
use Magento\Framework\Module\Status;

class DisableRules
{
    public const MODULE_NAME = 'Amasty_Promo';

    /**
     * @var Rule
     */
    private $ruleResource;

    public function __construct(Rule $ruleResource)
    {
        $this->ruleResource = $ruleResource;
    }

    /**
     * @param string[] $modules
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetIsEnabled(Status $subject, bool $isEnabled, array $modules): array
    {
        if (!$isEnabled && in_array(self::MODULE_NAME, $modules)) {
            $this->ruleResource->disablePromoRules();
        }

        return [$isEnabled, $modules];
    }
}
