<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Plugin\Api;

use Magento\Framework\Event\ManagerInterface as EventManager;

class Refund
{
    /**
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $invoice;
    
    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;
    
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditMemoRepository;
    
    /**
     * @var EventManager
     */
    private $eventManager;
    
    /**
     * Constructor
     * 
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param EventManager $eventManager
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditMemoRepository
     */
    public function __construct
    (
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        EventManager $eventManager,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditMemoRepository
    ) 
    {
        /**
         * Set invoice model 
         * 
         * @var \Anowave\Ec\Plugin\Api\Refund $invoice
         */
        $this->invoice = $invoice;
        
        /**
         * Set invoice repository 
         * 
         * @var \Anowave\Ec\Plugin\Api\Refund $invoiceRepository
         */
        $this->invoiceRepository = $invoiceRepository;
        
        /**
         * Set order repository 
         * 
         * @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
         */
        $this->orderRepository = $orderRepository;
        
        /**
         * Set event manager 
         * 
         * @var \Anowave\Ec\Plugin\Api\Refund $eventManager
         */
        $this->eventManager = $eventManager;
        
        /**
         * Set credit memo repository
         * 
         * @var \Anowave\Ec\Plugin\Api\Refund $creditMemoRepository
         */
        $this->creditMemoRepository = $creditMemoRepository;
    }
    
    /**
     * Before execute 
     * 
     * @param \Magento\Sales\Model\RefundInvoice $subject
     * @return array
     */
    public function beforeExecute(\Magento\Sales\Model\RefundInvoice $subject, $invoiceId, array $items = [],$isOnline = false,$notify = false,$appendComment = false,\Magento\Sales\Api\Data\CreditmemoCommentCreationInterface $comment = null,\Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface $arguments = null)
    {
        return [$invoiceId, $items, $isOnline, $notify, $appendComment, $comment, $arguments];
    }
    
    /**
     * After execute 
     * 
     * @param \Magento\Sales\Model\RefundInvoice $subject
     * @param unknown $result
     * @throws \Exception
     */
    public function afterExecute(\Magento\Sales\Model\RefundInvoice $subject, $result)
    {
        if ($result)
        {
            /**
             * Get credit memo 
             * 
             * @var \Magento\Sales\Model\Order\Creditmemo $creditmemo
             */
            $creditmemo = $this->creditMemoRepository->get($result);
            
            /**
             * Get order
             *
             * @var \Magento\Sales\Model\Order $order
             */
            $order = $this->orderRepository->get($creditmemo->getOrderId());
            
            /**
             * Dispatch custom event and notify custom observer
             */
            $this->eventManager->dispatch('api_sales_order_payment_refund', ['order' => $order]);
        }
        
        return $result;
    }
}