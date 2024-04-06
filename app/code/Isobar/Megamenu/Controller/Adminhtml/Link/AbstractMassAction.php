<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Controller\Adminhtml\Link;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Menu\Link;
use Isobar\Megamenu\Model\Menu\LinkFactory;
use Isobar\Megamenu\Model\Repository\LinkRepository;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link\Collection;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractMassAction
 * @package Isobar\Megamenu\Controller\Adminhtml\Link
 */
abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Isobar_Megamenu::menu_links';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var LinkRepository
     */
    protected $repository;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var LinkFactory
     */
    protected $modelFactory;

    /**
     * AbstractMassAction constructor.
     * @param Action\Context $context
     * @param Filter $filter
     * @param LoggerInterface $logger
     * @param LinkRepository $repository
     * @param CollectionFactory $collectionFactory
     * @param LinkFactory $modelFactory
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        LinkRepository $repository,
        CollectionFactory $collectionFactory,
        LinkFactory $modelFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
    }

    /**
     * Execute action for group
     *
     * @param LinkInterface $link
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    abstract protected function itemAction(LinkInterface $link);

    /**
     * Mass action execution
     * @throws LocalizedException
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                /** @var Link $model */
                foreach ($collection->getItems() as $model) {
                    $this->itemAction($model);
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
