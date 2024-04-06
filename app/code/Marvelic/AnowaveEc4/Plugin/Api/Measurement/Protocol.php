<?php

namespace Marvelic\AnowaveEc4\Plugin\Api\Measurement;

class Protocol extends \Anowave\Ec4\Plugin\Api\Measurement\Protocol
{
    /**
     * After purchase 
     * 
     * @param \Anowave\Ec\Model\Api\Measurement\Protocol $interceptor
     * @param callable $proceed
     * @param \Magento\Sales\Model\Order $order
     * @param string $reverse
     * @return array
     */
    public function aroundPurchase(\Anowave\Ec\Model\Api\Measurement\Protocol $interceptor, callable $proceed, \Magento\Sales\Model\Order $order, $reverse = false)
    {
        $measurement_id         = $this->getOrderMeasurementId($order);
        $measurement_api_secret = $this->getOrderMeasurementApiSecret($order);

        /**
         * Get client id 
         * 
         * @var Ambiguous $cid
         */
        $cid = $interceptor->getCID();

        $payload = function (array $events = []) use ($measurement_id, $measurement_api_secret, $reverse, $cid) {
            return
                [
                    'client_id' => $cid,
                    'events'    => $events
                ];
        };

        if ($measurement_id && $measurement_api_secret) {
            $items = [];

            /**
             * Default start position
             *
             * @var int
             */
            $index = 1;

            /**
             * Loop products
             */
            foreach ($interceptor->getProducts($order) as $product) {
                $item =
                    [
                        'index'         =>          $index,
                        'item_id'       =>          @$product['id'],
                        'item_name'     =>          @$product['name'],
                        'item_brand'    => (string) @$product['brand'],
                        'price'         => (float)  @$product['price'],
                        'quantity'      => (int)    @$product['quantity']
                    ];

                /**
                 * Check if reverse and reverse quantity
                 */

                if ($reverse) {
                    $item['quantity'] *= -1;
                    $item['price'] *= -1;
                }

                if (isset($product['category'])) {
                    $categories = explode(chr(47), @$product['category']);
                }


                if ($categories) {
                    $category = array_shift($categories);

                    if ($category) {
                        $item['item_category'] = $category;
                    }

                    foreach ($categories as $index => $category) {
                        $key = $index + 2;

                        $item["item_category{$index}"] = $category;
                    }
                }


                $items[] = $item;

                $index++;
            }

            $data = $payload(
                [
                    [
                        'name' => 'purchase',
                        'params' =>
                        [
                            'currency'       => $this->helper->getBaseHelper()->getCurrency(),
                            'transaction_id' => $order->getIncrementId(),
                            'value'          => $this->helper->getBaseHelper()->getRevenue($order),
                            'shipping'         => (float) $order->getShippingInclTax(),
                            'tax'             => (float) $order->getTaxAmount(),
                            'affiliation'    => $this->helper->getBaseHelper()->escape(
                                $order->getStore()->getName()
                            ),
                            'items' => $items,
                            'traffic_type' => $this->state->getAreaCode()
                        ]
                    ]
                ]
            );

            if ($reverse) {
                $data['events'][0]['params']['value']    *= -1;
                $data['events'][0]['params']['shipping'] *= -1;
                $data['events'][0]['params']['tax']      *= -1;
            }

            $analytics = curl_init("https://www.google-analytics.com/mp/collect?measurement_id={$measurement_id}&api_secret={$measurement_api_secret}");

            curl_setopt($analytics, CURLOPT_HEADER,         0);
            curl_setopt($analytics, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($analytics, CURLOPT_POST,             1);
            curl_setopt($analytics, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($analytics, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($analytics, CURLOPT_USERAGENT,        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($analytics, CURLOPT_POSTFIELDS,     utf8_encode(
                json_encode($data)
            ));

            try {
                $response = curl_exec($analytics);

                if ($this->helper->getBaseHelper()->useDebugMode()) {
                    $this->messageManager->addNoticeMessage(json_encode($data, JSON_PRETTY_PRINT));
                }

                if (!curl_error($analytics) && $response) {
                    return $interceptor;
                }
            } catch (\Exception $e) {
            }
        }

        return $proceed($order, $reverse);
    }
}
