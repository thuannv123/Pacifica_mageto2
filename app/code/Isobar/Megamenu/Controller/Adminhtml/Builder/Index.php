<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Builder;

use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Index
 * @package Isobar\Megamenu\Controller\Adminhtml\Builder
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Isobar_Megamenu::menu_builder';

    /**
     * @var Position
     */
    private $positionResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Index constructor.
     * @param Action\Context $context
     * @param Position $positionResource
     * @param StoreManagerInterface $storeManager
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        Position $positionResource,
        StoreManagerInterface $storeManager,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->positionResource = $positionResource;
        $this->storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', $this->storeManager->getDefaultStoreView()->getId());
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->positionResource->importCategoryPositions($storeId);

        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Menu Builder'));

        return $resultPage;
    }
}
