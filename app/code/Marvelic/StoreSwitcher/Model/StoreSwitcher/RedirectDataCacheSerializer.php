<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Marvelic\StoreSwitcher\Model\StoreSwitcher;

use InvalidArgumentException;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Throwable;

class RedirectDataCacheSerializer extends \Magento\Store\Model\StoreSwitcher\RedirectDataCacheSerializer
{
    protected $scopeConfig;
    private const CACHE_KEY_PREFIX = 'store_switch_';
    private const CACHE_LIFE_TIME = 10;
    private const CACHE_ID_LENGTH = 32;

    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var Random
     */
    private $random;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Json $json
     * @param Random $random
     * @param CacheInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        Json $json,
        Random $random,
        CacheInterface $cache,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig 
    ) {
        $this->cache = $cache;
        $this->json = $json;
        $this->random = $random;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }
    
    public function serialize(array $data): string
    {
        $token = $this->random->getRandomString(self::CACHE_ID_LENGTH);
        $cacheKey = self::CACHE_KEY_PREFIX . $token;
        $this->cache->save($this->json->serialize($data), $cacheKey, [], self::CACHE_LIFE_TIME);

        return $token;
    }

    public function unserialize(string $data): array
    {
        if (strlen($data) !== self::CACHE_ID_LENGTH) {
            throw new InvalidArgumentException("Invalid cache key '$data' supplied.");
        }

        $cacheKey = self::CACHE_KEY_PREFIX . $data;
        $json = $this->cache->load($cacheKey);
        if (!$json) {
            throw new InvalidArgumentException('Couldn\'t retrieve data from cache.');
        }
       
        $useCategoriesPath = $this->scopeConfig->getValue(
            'catalog/seo/product_use_categories',
            ScopeInterface::SCOPE_STORE
        );
        $result = $this->json->unserialize($json);
        
        if ($useCategoriesPath == 0 && isset($result['am_category_id'])) {
            $result = [];
        }
        try {
            $this->cache->remove($cacheKey);
        } catch (Throwable $exception) {
            $this->logger->error($exception);
        }

        return $result;
    }
}
