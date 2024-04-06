<?php

namespace Marvelic\Core\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Index extends Action
{
    protected $jsonResultFactory;
    protected $orderRepository;
    protected $request;
    protected $productRepositoryInterface;
    protected $quoteItemCollectionFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        RequestInterface $request,
        ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->request = $request;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $itemId = $this->request->getParam('id');

        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItem           = $quoteItemCollection
            ->addFieldToSelect('*')
            ->addFieldToFilter('item_id', $itemId)
            ->getData();
        foreach ($quoteItem as $item) {
            $product = $this->productRepositoryInterface->get($item['sku']);
            $data = [
                'style' => $product->getStyle(),
                'price' => $product->getPrice(),
                'labelStype' => $product->getResource()->getAttribute('style')->getStoreLabel($product),
                'color' => $product->getResource()->getAttribute('color')->getFrontend()->getValue($product)
            ];
            return $result->setData(
                $data
            );
        }
        
       
    }
}
