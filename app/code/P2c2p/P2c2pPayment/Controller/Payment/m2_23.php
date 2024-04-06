<?php
namespace P2c2p\P2c2pPayment\Controller\Payment;

class Response extends \P2c2p\P2c2pPayment\Controller\AbstractCheckoutRedirectAction implements \Magento\Framework\App\CsrfAwareActionInterface
{

	public function log($data){
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/p2c2p_23.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info($data);
	}

	public function createCsrfValidationException(\Magento\Framework\App\RequestInterface $request): ? \Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return true;
    }


	public function execute()
	{
        $this->log('execute called');

		//If payment getway response is empty then redirect to home page directory.
		$this->log(json_encode($_REQUEST));
		if(empty($_REQUEST) || empty($_REQUEST['order_id'])){
            $this->log('empty request');
			$this->_redirect('');
			return;
		}

		$hashHelper   = $this->getHashHelper();
		$configHelper = $this->getConfigSettings();
		$objCustomerData = $this->getCustomerSession();
		$isValidHash  = $hashHelper->isValidHashValue($_REQUEST,$configHelper['secretKey']);

		//Get Payment getway response to variable.
		$payment_status_code = $_REQUEST['payment_status'];
		$transaction_ref 	 = $_REQUEST['transaction_ref'];
		$approval_code   	 = $_REQUEST['approval_code'];
		$payment_status  	 = $_REQUEST['payment_status'];
		$order_id 		 	 = $_REQUEST['order_id'];

		//Get the object of current order.
		// $order = $this->getOrderDetailByOrderId($order_id);
        $order = $this->getOrderDetailByIncrementId($order_id);

		//If order is empty then redirect to home page. Because order is not avaialbe.
		if(empty($order)) {
            $this->log('empty order');
            $this->log($order_id);
			$this->_redirect('');
			return;
		}

		//Check whether hash value is valid or not If not valid then redirect to home page when hash value is wrong.
		if(!$isValidHash) {
            $this->log('invalid hash');
			$order->setState(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->setStatus(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->save();

			$this->_redirect('');
			return;
		}

		$metaDataHelper = $this->getMetaDataHelper();
		$metaDataHelper->savePaymentGetawayResponse($_REQUEST,$order->getCustomerId());

        $this->log('payment status: ' . $payment_status_code);

		//check payment status according to payment response.
		if(strcasecmp($payment_status_code, "000") == 0) {
			//IF payment status code is success
            $this->log('success');

			if(!empty($order->getCustomerId()) && !empty($_REQUEST['stored_card_unique_id'])) {
				$intCustomerId = $order->getCustomerId();
				$boolIsFound = false;

				// Fetch data from database by using the customer ID.
				$objTokenData = $metaDataHelper->getUserToken($intCustomerId);

				$arrayTokenData = array('user_id' => $intCustomerId,
					'stored_card_unique_id' => $_REQUEST['stored_card_unique_id'],
					'masked_pan' => $_REQUEST['masked_pan'],
					'created_time' =>  date("Y-m-d H:i:s"));

				/*
				   Iterate foreach and check whether token key is present into p2c2p_token table or not.
				   If token key is already present into database then prevent insert entry otherwise insert token entry into database.
				*/
				foreach ($objTokenData as $key => $value) {
					if(strcasecmp($value->getData('masked_pan'), $_REQUEST['masked_pan']) == 0 &&
					   strcasecmp($value->getData('stored_card_unique_id'), $_REQUEST['stored_card_unique_id']) == 0) {
						$boolIsFound = true;
						break;
					}
				}

				if(!$boolIsFound) {
					$metaDataHelper->saveUserToken($arrayTokenData);
				}
			}
            // create invoice
            $this->prepareInvoice($order);
			//Set the complete status when payment is completed.
			$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
			$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
			$order->addStatusHistoryComment($_REQUEST['channel_response_desc']);
			$order->save();

			$this->executeSuccessAction($_REQUEST);
			return;
            // $this->_redirect('checkout/onepage/success');

		} else if(strcasecmp($payment_status_code, "001") == 0) {
            //Set the Pending payment status when payment is pending. like 123 payment type.
            if (!$order->isCanceled()) {
                $order->setState("Pending_2C2P");
                $order->setStatus("Pending_2C2P");
            }
            $order->addStatusHistoryComment($_REQUEST['channel_response_desc']);
            $order->save();
            $this->executeSuccessAction($_REQUEST);
            return;
		} else if(strcasecmp($payment_status_code, "002") == 0) {
			//Set the Pending payment status when payment is canceled. like 123 payment type.
            $this->cancelOrder($order);

			//$order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
			//$order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
			$order->addStatusHistoryComment($_REQUEST['channel_response_desc']);

			$this->executeCancelAction($_REQUEST['channel_response_desc']);
			return;

		} else {
			//If payment status code is cancel/Error/other.
            $this->cancelOrder($order);

			//$order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
			//$order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
			$order->addStatusHistoryComment($_REQUEST['channel_response_desc']);

			$this->executeCancelAction($_REQUEST['channel_response_desc']);

			return;
		}
	}


}
