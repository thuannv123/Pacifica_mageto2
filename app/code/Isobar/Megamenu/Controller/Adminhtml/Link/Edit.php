<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Link;

use Isobar\Megamenu\Api\LinkRepositoryInterface;
use Isobar\Megamenu\Model\Menu\Link;
use Isobar\Megamenu\Model\Menu\LinkFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Isobar\Megamenu\Controller\Adminhtml\Link
 */
class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Isobar_Megamenu::menu_links';

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var LinkRepositoryInterface
     */
    private $linkRepository;

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param LinkRepositoryInterface $linkRepository
     * @param LinkFactory $linkFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context,
        LinkRepositoryInterface $linkRepository,
        LinkFactory $linkFactory,
        DataPersistorInterface $dataPersistor,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->linkRepository = $linkRepository;
        $this->linkFactory = $linkFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $linkId = (int)$this->getRequest()->getParam('id');
        if ($linkId) {
            try {
                $model = $this->linkRepository->getById($linkId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Custom Menu Item no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/index');
            }
        } else {
            /** @var Link $model */
            $model = $this->linkFactory->create();
        }

        $data = $this->dataPersistor->get(Link::PERSIST_NAME);
        if (!empty($data) && !$model->getEntityId()) {
            $model->addData($data);
        }

        $this->coreRegistry->register(Link::PERSIST_NAME, $model);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $text = $model->getEntityId() ?
            __('Edit Custom Menu Item # %1', $model->getEntityId())
            : __('New Custom Menu Item');
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend($text);

        return $resultPage;
    }
}
