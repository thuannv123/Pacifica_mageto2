<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\Payment\Adapter\Http;

use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;
use GuzzleHttp\Client;

class ApiCall
{
    protected $paymentGatewayConfig;
    protected $magentoProductMetadata;
    protected $storageManager;
    protected $logger;

    public function __construct(
        PaymentGatewayConfig                            $paymentGatewayConfig,
        \Magento\Framework\App\ProductMetadataInterface $magentoProductMetadata,
        \Magento\Store\Model\StoreManagerInterface      $storageManager
    )
    {
        $this->paymentGatewayConfig = $paymentGatewayConfig;
        $this->magentoProductMetadata = $magentoProductMetadata;
        $this->storageManager = $storageManager;
    }

    /**
     * @param string $url
     * @param bool $body
     * @param string $method
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function send($url, $body = null, $method = 'GET')
    {
        $website = $this->storageManager->getStore()->getBaseUrl();
        $phpVersion = phpversion();
        $phpOs = PHP_OS;
        $moduleVersion = Atome::version();
        $ua1 = "AtomeMagento2/{$moduleVersion} ($phpOs; PHP/$phpVersion)";
        $ua2 = "{$this->magentoProductMetadata->getName()}/{$this->magentoProductMetadata->getVersion()} ({$this->magentoProductMetadata->getEdition()})";
        $ua3 = "Merchant=" . rawurlencode($this->paymentGatewayConfig->getMerchantApiKey() ?: '') . '&Website=' . rawurlencode($website ?: '');

        $userAgent = "$ua1 $ua2 $ua3";
        Logger::instance()->info('api call request: ' . json_encode([
                'type' => 'Request',
                'method' => $method,
                'url' => $url,
                'userAgent' => $userAgent,
                'body' => $this->maskSensitiveData($body),
            ], JSON_UNESCAPED_SLASHES));

        try {

            $apiKey = trim($this->paymentGatewayConfig->getMerchantApiKey() ?? '');
            $password = trim($this->paymentGatewayConfig->getMerchantApiSecret() ?? '');

            $client = new Client();

            $options = [
                'timeout' => 60,
                'headers' => [
                    'User-Agent' => $userAgent,
                    'Accept' => 'application/json',
                    'Authorization' => 'basic' . base64_encode("{$apiKey}:{$password}"),
                    'Content-Type' => 'application/json'
                ],
            ];
            if ($body) {
                $options['body'] = json_encode($body);
            }

            $response = $client->request($method, $url, $options);

            Logger::instance()->info('api call response: ' . json_encode([
                    'type' => 'Response',
                    'method' => $method,
                    'url' => $url,
                    'status' => $response->getStatusCode(),
                    'body' => $this->maskSensitiveData(json_decode((string)$response->getBody()))
                ]));
        } catch (\Exception $e) {
            Logger::instance()->error('api call exception: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__('Gateway error: %1', $e->getMessage()));
        }
        return $response;
    }

    private function maskSensitiveData($data)
    {
        $sensitiveFields = ["shippingAddress", "billingAddress", "customerInfo"];
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $isSensitiveField = in_array($key, $sensitiveFields);
                    foreach ($val as $k => $v) {
                        if (substr($k, 0, 1) === '_') {
                            unset($data[$key][$k]);
                        } elseif (is_array($v)) {
                            $data[$key][$k] = '[array (' . count($v) . ')]';
                        } elseif ($isSensitiveField) {
                            $data[$key][$k] = (strlen("$v") <= 2) ? $v : (substr($v, 0, 2) . str_repeat("*", strlen($v) - 2));
                        }
                    }
                }
                if (substr($key, 0, 1) === '_') {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }
}
