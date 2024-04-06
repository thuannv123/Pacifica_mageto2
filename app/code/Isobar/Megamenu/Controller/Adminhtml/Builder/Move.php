<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Builder;

use Isobar\Megamenu\Api\PositionRepositoryInterface;
use Isobar\Megamenu\Model\Menu\Item\Position;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\LayoutFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Move
 * @package Isobar\Megamenu\Controller\Adminhtml\Builder
 */
class Move extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Isobar_Megamenu::menu_builder';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var PositionRepositoryInterface
     */
    private $positionRepository;

    /**
     * Move constructor.
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $layoutFactory
     * @param PositionRepositoryInterface $positionRepository
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory,
        PositionRepositoryInterface $positionRepository
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->positionRepository = $positionRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /**
         * Category id after which we have put our category
         */
        $prevNodeId = $this->getRequest()->getPost('aid', false);

        /** @var $block Messages */
        $block = $this->layoutFactory->create()->getMessagesBlock();
        $error = false;

        try {
            /** @var Position $positionItem */
            $positionItem = $this->positionRepository->getById($this->getRequest()->getParam('id'));
            if ($positionItem === false) {
                throw new LocalizedException(__('Item is not available for requested store.'));
            }
            $positionItem->move($prevNodeId);
        } catch (LocalizedException $e) {
            $error = true;
            $this->messageManager->addExceptionMessage($e);
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addErrorMessage(__('There was a item move error.'));
            $this->logger->critical($e);
        }

        if (!$error) {
            $this->messageManager->addSuccessMessage(__('You moved the item.'));
        }

        $block->setMessages($this->messageManager->getMessages(true));
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'messages' => $block->getGroupedHtml(),
            'error' => $error
        ]);
    }
}
