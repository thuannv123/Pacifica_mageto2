<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Free Gift Base for Magento 2
 */

namespace Amasty\Promo\Plugin\Checkout\Model;

use Amasty\Promo\Model\Storage;

class TotalsInformationManagementPlugin
{
    /**
     * @var Storage
     */
    private $registry;

    public function __construct(Storage $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Avoid auto add items on estimation, only via popup
     *
     * @param \Magento\Checkout\Api\TotalsInformationManagementInterface $subject
     */
    public function beforeCalculate(
        \Magento\Checkout\Api\TotalsInformationManagementInterface $subject
    ) {
        $this->registry->setIsAutoAddAllowed(false);
    }
}
