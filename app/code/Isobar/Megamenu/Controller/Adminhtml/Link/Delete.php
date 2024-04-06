<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Link;

use Isobar\Megamenu\Model\Repository\LinkRepository;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Class Delete
 * @package Isobar\Megamenu\Controller\Adminhtml\Link
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Isobar_Megamenu::menu_links';

    /**
     * @var LinkRepository
     */
    private $linkRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param LinkRepository $linkRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        LinkRepository $linkRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->linkRepository = $linkRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $packId = (int)$this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($packId) {
            try {
                $this->linkRepository->deleteById($packId);
                $this->messageManager->addSuccessMessage(__('The link have been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $resultRedirect->setPath('*/*/edit', ['id' => $packId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
