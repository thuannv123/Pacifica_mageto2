<?php

/*
 * Created by 2C2P
 * Date 20 June 2017
 * AbstractCheckoutRedirectAction is used for intermediate for request and reponse.
 */

namespace P2c2p\P2c2pPayment\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Catalog\Model\Session as catalogSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as Customer;
use P2c2p\P2c2pPayment\Controller\AbstractCheckoutAction;
use P2c2p\P2c2pPayment\Helper\Checkout;
use P2c2p\P2c2pPayment\Helper\P2c2pRequest;
use P2c2p\P2c2pPayment\Helper\P2c2pMeta;
use P2c2p\P2c2pPayment\Helper\P2c2pHash;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Api\OrderManagementInterface;
// use Magento\Sales\Model\Order\Email\Sender;

abstract class AbstractCheckoutRedirectAction extends AbstractCheckoutAction
{
    protected $objCheckoutHelper, $objCustomer;
    protected $objP2c2pRequestHelper, $objP2c2pMetaHelper;
    protected $objP2c2pHashHelper, $objConfigSettings;
    protected $objCatalogSession;

    /**
     * @var InvoiceService
     */
    private $invoiceService;
    protected $_objectManager;
    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    protected $_messageManager;

    /**
     * Email sender model.
     *
     * @var \Custom\Sale\Plugin\Model\Order\Email\Sender\OrderSender
     */
    protected $emailSender;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    public function __construct(
        Context $context,
        Session $checkoutSession, OrderFactory $orderFactory,
        Customer $customer, Checkout $checkoutHelper,
        P2c2pRequest $p2c2pRequest, P2c2pMeta $p2c2pMeta,
        P2c2pHash $p2c2pHash, ScopeConfigInterface $configSettings ,
        catalogSession $catalogSession,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        OrderManagementInterface $orderManagement
        // \Custom\Sale\Plugin\Model\Order\Email\Sender\OrderSender $emailSender
    ) {

        parent::__construct($context, $checkoutSession, $orderFactory);
        $this->objCheckoutHelper = $checkoutHelper;
        $this->objCustomer = $customer;
        $this->objP2c2pRequestHelper = $p2c2pRequest;
        $this->objP2c2pMetaHelper = $p2c2pMeta;
        $this->objP2c2pHashHelper = $p2c2pHash;
        $this->objConfigSettings = $this->objConfigSettings = $configSettings->getValue('payment/p2c2ppayment', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
        $this->objCatalogSession = $catalogSession;
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->_messageManager = $context->getMessageManager();
        $this->orderManagement = $orderManagement;
        // $this->emailSender = $emailSender;
    }

    //This object is hold the custom filed data for payment method like selected store Card's, other setting, etc.
    protected function getCatalogSession() {
        return $this->objCatalogSession;
    }

    //Get the Magento configuration setting object that hold global setting for Merchant configuration
    protected function getConfigSettings() {
        return $this->objConfigSettings;
    }

    //Get the P2c2p plugin Hash helper class object to check hash value is valid or not. Also generate the hash for any request.
    protected function getHashHelper() {
        return $this->objP2c2pHashHelper;
    }

    //Get the Meta helper object. It is responsible for storing the data into database. like p2c2p_meta, p2c2p_token table.
    protected function getMetaDataHelper() {
        return $this->objP2c2pMetaHelper;
    }

    //Get the p2c2p request helper class. It is responsible for construct the current user request for 2c2p Payment Gateway.
    protected function getP2c2pRequest($paramter,$isloggedIn) {
        return $this->objP2c2pRequestHelper->p2c2p_construct_request($paramter,$isloggedIn);
    }

    //This is magento object to get the customer object.
    protected function getCustomerSession() {
        return $this->objCustomer;
    }

    //Get the P2c2p cehckout object. It is reponsible for hold the current users cart detail's
    protected function getCheckoutHelper() {
        return $this->objCheckoutHelper;
    }

    //This function is used to redirect to customer message action method after make successfully payment / 123 payment type.
    protected function executeSuccessAction($request){
        if ($this->getCheckoutSession()->getLastRealOrderId() || isset($request['order_id'])) {
            // $this->_forward('Success','Payment','p2c2p', $request);
            $this->_redirect('checkout/onepage/success');
            // TODO change redirection to checkout success page, and check for the session being gone
        }
    }

    //This function is redirect to cart after customer is cancel the payment.
    protected function executeCancelAction($message = ""){
        $this->_messageManager->addNoticeMessage($message);
//        $this->getCheckoutHelper()->cancelCurrentOrder('');
        $this->getCheckoutHelper()->restoreQuote();
        $this->redirectToCheckoutCart();
    }

    protected function prepareInvoice($order)
    {
        $invoice = $this->invoiceService->prepareInvoice($order, []);
        if (!$invoice) {
            return;
        }
        if (!$invoice->getTotalQty()) {
            return;
        }
        $invoice->setRequestedCaptureCase('online');
        //$this->invoiceService->setCapture($invoice->getEntityId());
        $invoice->register();
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = $this->_objectManager->create(
                \Magento\Framework\DB\Transaction::class
            )->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
        $transactionSave->save();
        // $this->emailSender->send($order);
        $this->invoiceSender->send($invoice);
        return $this;
    }

    /**
     * @param $order Order
     */
    public function cancelOrder($order)
    {
        try {
            $this->orderManagement->cancel($order->getEntityId());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
