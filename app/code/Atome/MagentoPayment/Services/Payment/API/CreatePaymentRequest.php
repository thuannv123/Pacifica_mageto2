<?php

namespace Atome\MagentoPayment\Services\Payment\API;

use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Logger\Logger;
use Atome\MagentoPayment\Services\Price\PriceService;
use Exception;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;

class CreatePaymentRequest extends Request
{

    /**
     * @var Order
     */
    protected $order;

    protected $payload;

    /**
     * @param Order $order
     * @return CreatePaymentRequest
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }

    public function getMethod()
    {
        return 'POST';
    }

    public function getUrl()
    {
        return $this->paymentGatewayConfig->getApiUrl('payments');
    }

    public function getPayload()
    {
        if (!$this->payload) {
            $this->payload = $this->buildFromOrder($this->order);
        }

        return $this->payload;
    }


    protected function buildFromOrder(Order $order)
    {
        if ($this->paymentGatewayConfig->getCountry() === 'tw' && round($order->getGrandTotal()) != $order->getGrandTotal()) {
            throw new Exception('The order total amount must be integer');
        }

        $params['callbackUrl'] = $order->getStore()->getBaseUrl() . 'atome/payment/callback?orderId=' . $order->getEntityId();
        $params['paymentResultUrl'] = $order->getStore()->getBaseUrl() . 'atome/payment/result?type=result&orderId=' . $order->getEntityId();
        $params['paymentCancelUrl'] = $order->getStore()->getBaseUrl() . 'atome/payment/result?type=cancel&orderId=' . $order->getEntityId();
        $params['expirationTime'] = $this->paymentGatewayConfig->getCancelTimeout(true);

        $billingAddress = $order->getBillingAddress() ?: $this->objectManager->create(Address::class);
        $shippingAddress = $order->getShippingAddress() ?: $this->objectManager->create(Address::class);

        $customerNameParts = [];
        $firstName = $order->getCustomerFirstname() ?: $billingAddress->getFirstname();
        $middleName = $order->getCustomerMiddlename() ?: $billingAddress->getMiddlename();
        $lastName = $order->getCustomerLastname() ?: $billingAddress->getLastname();
        if ($firstName) {
            $customerNameParts[] = $firstName;
        }
        if ($middleName) {
            $customerNameParts[] = $middleName;
        }
        if ($lastName) {
            $customerNameParts[] = $lastName;
        }
        $customerFullName = join(' ', $customerNameParts);

        // leave referenceId empty, server will generate new id every time.
        // pass merchantReferenceId to payment gateway
        $params['merchantReferenceId'] = $order->getIncrementId();

        $params['customerInfo'] = [
            'fullName' => $customerFullName,
            'email' => $order->getCustomerEmail(),
            'mobileNumber' => $shippingAddress->getTelephone() ?: $billingAddress->getTelephone(),
        ];


        $priceService = ObjectManager::getInstance()->get(PriceService::class);
        foreach ($order->getAllVisibleItems() as $item) {
            if (!$item->getParentItem()) {
                $product = $item->getProduct();
                $category_ids = $product->getCategoryIds();
                /** @var Image $imageHelper */
                $imageHelper = $this->objectManager->get(Image::class);

                $categories = [];
                if (count($category_ids) > 0) {
                    foreach ($category_ids as $category) {
                        $cat = $this->objectManager->create(Category::class)->load($category);
                        $categories[] = $cat->getName();
                    }
                }
                $params['items'][] = [
                    '_raw' => $item->getData(),
                    'itemId' => $item->getId(),
                    'name' => $item->getName(),
                    'quantity' => $item->getQty(),
                    'price' => $priceService->format($item->getPrice()),
                    'originalPrice' => $priceService->format($item->getOriginalPrice()),
                    'sku' => $item->getSku(),
                    'pageUrl' => $product->getProductUrl(),
                    'imageUrl' => $imageHelper->init($product, 'product_page_image_small')->setImageFile($product->getImage())->getUrl(),
                    'categories' => $categories,
                ];
            }
        }

        /*  base_grand_total is the base currency total for the order grand_total will be the grand total of the currency used to checkout.
            The store currency is GBP, base_grand_total = 10.00 in GBP
            Customer checks out in USD, grand_total is 15.00
            sub_total is pre-tax.
        */
        $params['shippingAddress'] = [
            '_raw' => $shippingAddress->getData(),
            'lines' => $shippingAddress->getStreet(),
            'postCode' => $shippingAddress->getPostcode(),
            'countryCode' => $shippingAddress->getCountryId(),
        ];

        $params['billingAddress'] = [
            '_raw' => $billingAddress->getData(),
            'lines' => $billingAddress->getStreet(),
            'postCode' => $billingAddress->getPostcode(),
            'countryCode' => $billingAddress->getCountryId(),
        ];

        $params['currency'] = $order->getOrderCurrencyCode();
        $params['amount'] = $priceService->format($order->getGrandTotal());
        $params['taxAmount'] = $priceService->format($shippingAddress->getTaxAmount());
        $params['shippingAmount'] = $priceService->format($shippingAddress->getShippingAmount());
        $params['_raw'] = $order->getData();
        $params['_model'] = get_class($order);
        return $params;
    }

    /**
     * @return CreatePaymentResponse
     */
    public function getSimulateCreatePaymentResponse()
    {
        $id = $this->order->getId();

        Logger::instance()->info(__METHOD__ . json_encode([
                'grand_total' => $this->order->getData('grand_total'),
                'intFactor' => Atome::getIntFactor()
            ]));

        $response = new CreatePaymentResponse();
        $response->setData([
            'referenceId' => $id,
            'amount' => $this->order->getData('grand_total') * Atome::getIntFactor(),
            'currency' => $this->order->getData('order_currency_code'),
            'message' => 'You are using Atome to simulate the payment environment, this is only intended as a local test purpose. Delete the `.atome_simulation` file located in the root directory to create real payment transactions.',
            'redirectUrl' => $this->order->getStore()->getBaseUrl() . "atome/payment/result?type=result&orderId={$id}",
        ]);

        return $response;
    }

}
