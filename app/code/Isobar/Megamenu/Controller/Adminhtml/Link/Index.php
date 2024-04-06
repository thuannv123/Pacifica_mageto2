<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Link;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Isobar\Megamenu\Controller\Adminhtml\Link
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Isobar_Megamenu::menu_links';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Custom Menu Items'));

        return $resultPage;
    }
}
