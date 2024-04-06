<?php
namespace Marvelic\AmastyLabel\Controller\Adminhtml\Label;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\LabelRepositoryInterface;
use Amasty\Label\Model\Label\Save\Adminhtml\SaveFromEditForm;
use Amasty\Label\Model\LabelRegistry;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Throwable as Throwable;
use Amasty\Label\Controller\Adminhtml\Label\Save as AmastyLabelSave;

class Save extends AmastyLabelSave
{
    public const ADMIN_RESOURCE = \Amasty\Label\Controller\Adminhtml\Label\Edit::ADMIN_RESOURCE;

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    /**
     * @var SaveFromEditForm
     */
    private $saveFromEditForm;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        LabelRepositoryInterface $labelRepository,
        SaveFromEditForm $saveFromEditForm,
        LabelRegistry $labelRegistry,
        LoggerInterface $logger
    ) {
        $this->labelRepository = $labelRepository;
        $this->labelRegistry = $labelRegistry;
        $this->saveFromEditForm = $saveFromEditForm;
        $this->logger = $logger;
        parent::__construct($context, $labelRepository, $saveFromEditForm, $labelRegistry, $logger);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        // var_dump(123);die;
        $postData = $this->getRequest()->getPostValue();
        $currentLabel = $this->getCurrentLabel();
        $this->labelRegistry->setCurrentLabel($currentLabel);
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $postData[LabelInterface::LABEL_ID] = $currentLabel->getId() === null ? null : $currentLabel->getLabelId();
            $currentLabel = $this->saveFromEditForm->execute($postData);
            $this->_getSession()->unsAmLabelPageData($postData);
            $this->messageManager->addSuccessMessage(__('You saved the label'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            // $this->_getSession()->setAmLabelPageData($postData);
        } catch (Throwable $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the item data. Please review the error log.')
            );
            $this->logger->critical($e);
            // $this->_getSession()->setAmLabelPageData($postData);
        }

        $needGoBack = (bool) $this->getRequest()->getParam('back', false);
        $labelId = $currentLabel->getLabelId();
        $redirectParams = $needGoBack ? ['amasty_label/*/edit', ['id' => $labelId]] : ['amasty_label/label/index'];

        return $resultRedirect->setPath(...$redirectParams);
    }

    private function getCurrentLabel(): LabelInterface
    {
        $labelId = (int) $this->getRequest()->getParam(LabelInterface::LABEL_ID);

        try {
            $currentLabel = $this->labelRepository->getById((int) $labelId);
        } catch (NoSuchEntityException $e) {
            $currentLabel = $this->labelRepository->getModelLabel();
        }

        return $currentLabel;
    }
}
