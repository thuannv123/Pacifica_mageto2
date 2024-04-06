<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Marvelic\Sales\Controller\Adminhtml\Order;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;

class MassUpdate extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Marvelic_Sales::update';

    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface|null $orderManagement
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        OrderManagementInterface $orderManagement = null
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->orderManagement = $orderManagement ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Sales\Api\OrderManagementInterface::class
        );
    }

    /**
     * Cancel selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $orderIdsList = [];

        foreach ($collection->getItems() as $order) {
            $orderIdsList[] = $order->getId();
        }

        $connection = $this->resourceConnection->getConnection();
        $tableSalesOrderGrid = $connection->getTableName('sales_order_grid');
        $tableSalesOrder = $connection->getTableName('sales_order');

        $query = "UPDATE `" . $tableSalesOrderGrid . "` LEFT JOIN `" . $tableSalesOrder . "` ON `" . $tableSalesOrderGrid . "`.entity_id = `" . $tableSalesOrder . "`.entity_id SET `" . $tableSalesOrderGrid . "`.status = `" . $tableSalesOrder . "`.status WHERE `" . $tableSalesOrderGrid . "`.status != `" . $tableSalesOrder . "`.status";
        $connection->query($query);

        $this->messageManager->addSuccessMessage(__('Update %1 order(s).', count($orderIdsList)));

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}
