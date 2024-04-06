<?php
namespace Isobar\P2c2p\Controller\Payment;
use Exception;
use Isobar\LogDataPayment\Logger\LoggerPayment;
use Magento\Catalog\Model\Session as catalogSession;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\Service\InvoiceService;
use P2c2p\P2c2pPayment\Helper\Checkout;
use P2c2p\P2c2pPayment\Helper\P2c2pHash;
use P2c2p\P2c2pPayment\Helper\P2c2pMeta;
use P2c2p\P2c2pPayment\Helper\P2c2pRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\CatalogInventory\Model\StockManagement;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor as StockProcessor;

class Response extends \P2c2p\P2c2pPayment\Controller\Payment\Response
{
    /**
     * @var LoggerPayment
     */

	const STATUS_RESPONSE_SUCCESSFUL    = "000";
    const STATUS_RESPONSE_PENDING       = "001";
    const STATUS_RESPONSE_REJECTED      = "002";
    const STATUS_RESPONSE_CANCEL        = "003";
    const STATUS_RESPONSE_FAILED        = "999";

    const PENDING_2C2P                  = "Pending_2C2P";
    const REJECTED_2C2P                 = "Rejected_2C2P";
    const PAYMENT_FAIL_2C2P             = "Payment_Fail_2C2P";

	protected $_logger;
    protected $_transaction;
    protected $_transactionBuilder;
    protected $_stockManagement;
    protected $_stockIndexerProcessor;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Customer $customer,
        Checkout $checkoutHelper,
        P2c2pRequest $p2c2pRequest,
        P2c2pMeta $p2c2pMeta,
        P2c2pHash $p2c2pHash,
        ScopeConfigInterface $configSettings,
        catalogSession $catalogSession,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        OrderManagementInterface $orderManagement,
        LoggerPayment $logger,
        StockManagement $stockManagement,
        StockProcessor $stockIndexerProcessor,
        BuilderInterface $transactionBuilder,
        \Magento\Framework\DB\Transaction $transaction
    ) {
        parent::__construct(
			$context,
			$checkoutSession, 
			$orderFactory, 
			$customer, 
			$checkoutHelper, 
			$p2c2pRequest, 
			$p2c2pMeta, 
			$p2c2pHash, 
			$configSettings, 
			$catalogSession, 
			$invoiceService, 
			$invoiceSender, 
			$orderManagement);
        $this->_logger = $logger;
		$this->_transaction = $transaction;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_stockManagement = $stockManagement;
        $this->_stockIndexerProcessor = $stockIndexerProcessor;
    }

	public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

	public function execute()
	{
        $this->log('2C2P NOTIFY REQUEST : ['. json_encode($_REQUEST) .']');

        # If payment getway response is empty then redirect to home page directory.
        if (empty($_REQUEST) || empty($_REQUEST['order_id'])) {
			$this->_redirect('');
			return;
		}

        # Extract the Payment getaway resposne object.
        extract($_REQUEST, EXTR_OVERWRITE);

        $order_id = $_REQUEST['order_id'];
        # Get the object of current order.
		$order = $this->getOrderDetailByIncrementId($order_id);

        # If order is empty then redirect to home page. Because order is not avaialbe.
        if (empty($order)) {
            $this->_redirect('');
            return; 
        }

        # Ignore request from 2C2P when customer paid money
        $payment_status_code = $_REQUEST['payment_status'];

        # Get detail payment response to variable.
        $paid_channel           = array_key_exists('paid_channel', $_REQUEST) ? $_REQUEST['paid_channel'] : '';
        $paid_agent             = array_key_exists('paid_agent', $_REQUEST) ? $_REQUEST['paid_agent'] : '';
        $hash_value			    = $_REQUEST['hash_value'];
        $request_timestamp	    = $_REQUEST['request_timestamp'];
        $merchant_id		    = $_REQUEST['merchant_id'];
        $invoice_no			    = $_REQUEST['invoice_no'];
        $amount 			    = $_REQUEST['amount'];
        $transaction_ref 	    = empty($_REQUEST['transaction_ref']) ? __('None'): $_REQUEST['transaction_ref'];
        $transaction_datetime 	= $_REQUEST['transaction_datetime'];
        $payment_channel 	    = $_REQUEST['payment_channel'];
        $channel_response_code 	= $_REQUEST['channel_response_code'];

		$hashHelper   = $this->getHashHelper();
		$configHelper = $this->getConfigSettings();
		$isValidHash  = $hashHelper->isValidHashValue($_REQUEST, $configHelper['secretKey']);

		switch ($channel_response_code) {
			case '001':
				$channel_response_code = $channel_response_code.' - Credit and debit cards';
				break;
			case '002':
				$channel_response_code = $channel_response_code.' - Cash payment channel';
				break;
			case '003':
				$channel_response_code = $channel_response_code.' - Direct debit';
				break;
			case '004':
				$channel_response_code = $channel_response_code.' - Others';
				break;
			default:
				$channel_response_code = $channel_response_code.' - IPP transaction';
				break;
		}

		# Check whether hash value is valid or not If not valid then redirect to home page when hash value is wrong.
		if (!$isValidHash) {
            $this->log('2C2P ORDER_ID : ['. $order_id .'][FALSE HASH]');
			$order->setState(Order::STATUS_FRAUD);
			$order->setStatus(Order::STATUS_FRAUD);
			$order->save();
            $this->_redirect('');
			return;
		}

		$metaDataHelper = $this->getMetaDataHelper();
		$metaDataHelper->savePaymentGetawayResponse($_REQUEST, $order->getCustomerId());

		# Check payment status according to payment response.
		if (strcasecmp($payment_status_code, '000') == 0) {
			# IF payment status code is success
			if (!empty($order->getCustomerId()) && !empty($_REQUEST['stored_card_unique_id'])) {
				$boolIsFound = false;
				$intCustomerId = $order->getCustomerId();

				# Fetch data from database by using the customer ID.
				$objTokenData = $metaDataHelper->getUserToken($intCustomerId);

                $arrayTokenData = array(
                    'user_id' => $intCustomerId,
                    'masked_pan' => $_REQUEST['masked_pan'],
                    'stored_card_unique_id' => $_REQUEST['stored_card_unique_id'],
                    'created_time' => date("Y-m-d H:i:s")
                );

                /**
                 * Iterate foreach and check whether token key is present into p2c2p_token table or not.
                 * If token key is already present into database then prevent insert entry otherwise insert token entry into database.
                 */
                foreach ($objTokenData as $key => $value) {
					if (strcasecmp($value->getData('masked_pan'), $_REQUEST['masked_pan']) == 0 && 
					   strcasecmp($value->getData('stored_card_unique_id'), $_REQUEST['stored_card_unique_id']) == 0
                    ) {
						$boolIsFound = true;
						break;
					}
				}

				if (!$boolIsFound) {
					$metaDataHelper->saveUserToken($arrayTokenData);					
				}
			}

			$payment = $order->getPayment();
			$payment->setLastTransId($transaction_ref);
			$payment->setTransactionId($transaction_ref);
			$invoice = $this->invoice($order);
			$invoice->setTransactionId($transaction_ref);

			# Add transaction.
			$payment->addTransactionCommentsToOrder(
				$payment->addTransaction(Transaction::TYPE_CAPTURE),
				__(
					'Amount of %1 has been paid via 2C2P payment',
					$order->getBaseCurrency()->formatTxt($order->getBaseGrandTotal())
				)
			);
			$order->save();

			$order_id = $order->getId();
			$payment_id = $payment->getId();
            
            # Write log debug cant create invoice
            $this->log('2C2P DATA_ID : ['. $payment_id .']['. $order_id .']');
            $this->log('2C2P CONFIG : ['. json_encode($configHelper) .']');

            # Save Transaction
            $detailData = [
				Transaction::RAW_DETAILS => [
					'Request Timestamp'		=> $request_timestamp,
					'Merchant Id' 			=> $merchant_id,
					'Order Id'				=> $_REQUEST['order_id'],
					'Invoice No'			=> $invoice_no,
					'Amount'				=> $amount,
					'Transaction REF'		=> $transaction_ref,
					'Transaction Datetime'	=> $transaction_datetime,
					'Payment Channel'		=> $payment_channel,
					'Payment Status'		=> '000 - '.__('Payment Successful'),
					'Channel Response Code' => $channel_response_code,
					'Paid Channel'			=> $paid_channel,
					'Paid Agent'			=> $paid_agent,
					'Hash Value'			=> $hash_value
				]
			];

			$detailJson		= \GuzzleHttp\json_encode($detailData);
	        $resource 		= $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
	        $connection 	= $resource->getConnection();
	        $tableName 		= $resource->getTableName('sales_payment_transaction');
	        $sql 			= "UPDATE " . $tableName . " SET additional_information = '". $detailJson ."' WHERE order_id = '". $order_id ."' AND payment_id = '". $payment_id ."'";
            $connection->query($sql);

            try {
                if ($configHelper['auto_invoice'] == 1) {
                    if ($order->canInvoice()) {
                        # Set the complete status when payment is completed.
                        $order->setState(Order::STATE_PROCESSING);
                        $order->setStatus(Order::STATE_PROCESSING);
                        $this->log('2C2P CAN_CREATE_INVOICE : ['. gettype($order->canInvoice()) .']');
                        $this->prepareInvoice($order);
                    } 
                    else {
                        if($order->getStatus() != 'processing'){
                            # Handle when order auto cancle
                            if ($this->unCancleOrder($order)) {
                                $this->prepareInvoice($order);
                            }
                        }
                    }
                }
            }
            catch (Exception $exception) {
                throw new CouldNotSaveException(__($exception->getMessage()));
            }
            $this->executeSuccessAction($_REQUEST);
            $order->save();
            return;
		} 
        else if (strcasecmp($payment_status_code, self::STATUS_RESPONSE_PENDING) == 0) {
            if (!$order->isCanceled()) {
                $order->setState(self::PENDING_2C2P);
                $order->setStatus(self::PENDING_2C2P);
            }
            $this->executeSuccessAction($_REQUEST);
		} 
        else if (strcasecmp($payment_status_code, self::STATUS_RESPONSE_REJECTED) == 0) {
            $order->setState(self::REJECTED_2C2P);
            $order->setStatus(self::REJECTED_2C2P);
            $order->addStatusHistoryComment($_REQUEST['channel_response_desc']);
			$this->executeCancelAction($_REQUEST['channel_response_desc']);

		} 
        else if (strcasecmp($payment_status_code, self::STATUS_RESPONSE_CANCEL) == 0) {
            $this->cancelOrder($order);
			$order->addStatusHistoryComment($_REQUEST['channel_response_desc']);
			$this->executeCancelAction($_REQUEST['channel_response_desc']);
        } 
        else if (strcasecmp($payment_status_code, self::STATUS_RESPONSE_FAILED) == 0) {
            $this->cancelOrder($order);
			$order->addStatusHistoryComment($_REQUEST['channel_response_desc']);
			$this->executeCancelAction($_REQUEST['channel_response_desc']);
        } 
        else {
            $this->cancelOrder($order);
			$order->addStatusHistoryComment($_REQUEST['channel_response_desc']);
			$this->executeCancelAction($_REQUEST['channel_response_desc']);
        }
        return;
	}

	protected function invoice($order) 
    {
		return $order->getInvoiceCollection()->getLastItem();
	}

    public function unCancleOrder($order, $comment = "", $forceUnCancle = true) 
    {
        if (!($order)) {
            throw new LocalizedException(__('Invalid Order'));
        }

        if ($order->isCanceled() || $forceUnCancle) {
            $state = Order::STATE_PROCESSING;
            $productStockQty = [];

            foreach ($order->getAllVisibleItems() as $item) {
                $productStockQty[$item->getProductId()] = $item->getQtyCanceled();
                foreach ($item->getChildrenItems() as $child) {
                    $productStockQty[$child->getProductId()] = $item->getQtyCanceled();
                    $child->setQtyCanceled(0);
                    $child->setTaxCanceled(0);
                    $child->setDiscountTaxCompensationCanceled(0);
                }
                $item->setQtyCanceled(0);
                $item->setTaxCanceled(0);
                $item->setDiscountTaxCompensationCanceled(0);
            }

            $order->setSubtotalCanceled(0);
            $order->setBaseSubtotalCanceled(0);
            $order->setTaxCanceled(0);
            $order->setBaseTaxCanceled(0);
            $order->setShippingCanceled(0);
            $order->setBaseShippingCanceled(0);
            $order->setDiscountCanceled(0);
            $order->setBaseDiscountCanceled(0);
            $order->setTotalCanceled(0);
            $order->setBaseTotalCanceled(0);
            $order->setState($state)
                  ->setStatus($order->getConfig()->getStateDefaultStatus($state));
            if (!empty($comment)) {
                $order->addStatusHistoryComment($comment, false);
            }

            # Reverting inventory
            $itemsForReindex = $this->_stockManagement->registerProductsSale(
                $productStockQty,
                $order->getStore()->getWebsiteId()
            );
            $productIds = [];
            foreach ($itemsForReindex as $item) {
                $item->save();
                $productIds[] = $item->getProductId();
            }
            if (!empty($productIds)) {
                $this->_stockIndexerProcessor->reindexList($productIds);
            }
            $order->setInventoryProcessed(true);
            $order->save();
            return true;
        } 
        else {
            throw new LocalizedException(__('We cannot un-cancel this order.'));
        }
    }
}
