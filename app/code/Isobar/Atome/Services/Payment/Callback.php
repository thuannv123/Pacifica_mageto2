<?php
namespace Isobar\Atome\Services\Payment;

use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\OrderRepository;
use Isobar\LogDataPayment\Logger\LoggerPayment;

class Callback extends \Atome\MagentoPayment\Services\Payment\Callback
{
    /**
     * @var LoggerPayment
     */
    protected $_logger;

    public function __construct(
        PaymentGatewayConfig $paymentGatewayConfig,
        LoggerPayment $logger
    )
    {
        parent::__construct($paymentGatewayConfig);
        $this->_logger = $logger;
    }

    protected function whenPaymentPaid()
    {
        $connection = ObjectManager::getInstance()
            ->get(ResourceConnection::class)
            ->getConnection('sales');

        try {
            $connection->beginTransaction();

            if (!$this->hasInvoice()) {
                $this->createInvoice();
            }

            $status = $this->paymentGatewayConfig->getOrderStatus();
            $state = $this->paymentGatewayConfig->getOrderState();
            $this->order->setState($state)->setStatus($status);
            $this->order->addStatusToHistory($this->order->getStatus(), 'Successful payment with Atome');

            //add log payment success order
            $purchaseEvent = 'Payment with Atome';
            $order_id = $this->order->getIncrementId();
            $createdAt = $this->order->getCreatedAt();
            $storeCode = $this->order->getStore()->getCode();
            $customerName = $this->order->getCustomerFirstName().' '.$this->order->getCustomerLastName();
            $customerEmail = $this->order->getCustomerEmail();
            $totalOrder = $this->order->getGrandTotal();
            $this->_logger->info('Purchase event: '.$purchaseEvent.', Order id: '.$order_id.', Total order: '.$totalOrder.', Created at: '.$createdAt.', Store code: '.$storeCode.', Customer name: '.$customerName.', Customer email: '.$customerEmail);
            //end add log payment success order

            $this->order->setBaseCustomerBalanceInvoiced(null);
            $this->order->setCustomerBalanceInvoiced(null);

            ObjectManager::getInstance()
                ->get(OrderRepository::class)
                ->save($this->order);

            $connection->commit();
        } catch (\Throwable $e) {
            $connection->rollBack();
            Logger::instance()->error(
                $e->getMessage(),
                [
                    'method' => __METHOD__,
                    'trace' => $e->getTraceAsString()
                ]
            );

            throw $e;
        }

        $this->sendEmail();
    }
}
