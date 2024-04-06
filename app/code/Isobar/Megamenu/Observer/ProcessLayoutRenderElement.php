<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Isobar\Megamenu\Observer;

use Magento\Framework\Serialize\Serializer\Base64Json;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\EntitySpecificHandlesList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class ProcessLayoutRenderElement extends \Magento\PageCache\Observer\ProcessLayoutRenderElement
{
    private $_config;
    private $isVarnishEnabled;
    private $isFullPageCacheEnabled;
    private $entitySpecificHandlesList;
    private $base64jsonSerializer;
    private $jsonSerializer;
    private $scopeConfigInterface;
    private $cacheList;
    private $cacheFontend;

    public function __construct(
        \Magento\PageCache\Model\Config $config,
        EntitySpecificHandlesList $entitySpecificHandlesList = null,
        Json $jsonSerializer = null,
        Base64Json $base64jsonSerializer = null,
        ScopeConfigInterface $scopeConfigInterface,
        TypeListInterface $cacheList,
        Pool $cacheFontend
    ) {
        parent::__construct($config,$entitySpecificHandlesList,$jsonSerializer,$base64jsonSerializer);
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->_config = $config;
        $this->entitySpecificHandlesList = $entitySpecificHandlesList;
        $this->jsonSerializer = $jsonSerializer;
        $this->base64jsonSerializer = $base64jsonSerializer;
        $this->cacheList = $cacheList;
        $this->cacheFontend = $cacheFontend;
    }

    private function _wrapEsi(
        \Magento\Framework\View\Element\AbstractBlock $block,
        \Magento\Framework\View\Layout $layout
    ) {
        $handles = $layout->getUpdate()->getHandles();
        $pageSpecificHandles = $this->entitySpecificHandlesList->getHandles();
        $url = $block->getUrl(
            'page_cache/block/esi',
            [
                'blocks' => $this->jsonSerializer->serialize([$block->getNameInLayout()]),
                'handles' => $this->base64jsonSerializer->serialize(
                    array_values(array_diff($handles, $pageSpecificHandles))
                )
            ]
        );
        // Varnish does not support ESI over HTTPS must change to HTTP
        $url = ($url) ? 'http' . substr($url, 5) : $url;
        return sprintf('<esi:include src="%s" />', $url);
    }

    private function isFullPageCacheEnabled()
    {
        if ($this->isFullPageCacheEnabled === null) {
            $this->isFullPageCacheEnabled = $this->_config->isEnabled();
        }
        return $this->isFullPageCacheEnabled;
    }

    private function isVarnishEnabled()
    {
        if ($this->isVarnishEnabled === null) {
            $this->isVarnishEnabled = ($this->_config->getType() === \Magento\PageCache\Model\Config::VARNISH);
        }
        return $this->isVarnishEnabled;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();
        if ($this->isFullPageCacheEnabled() && $layout->isCacheable()) {
            $name = $event->getElementName();
            /** @var \Magento\Framework\View\Element\AbstractBlock $block */
            $block = $layout->getBlock($name);
            $transport = $event->getTransport();
            if ($block instanceof \Magento\Framework\View\Element\AbstractBlock) {
                $blockTtl = $block->getTtl();
                $output = $transport->getData('output');
                if (isset($blockTtl) && $this->isVarnishEnabled()) {
                    $output = $this->_wrapEsi($block, $layout);
                } elseif ($block->isScopePrivate()) {
                    $output = sprintf(
                        '<!-- BLOCK %1$s -->%2$s<!-- /BLOCK %1$s -->',
                        $block->getNameInLayout(),
                        $output
                    );
                }
                if($name == 'catalog.topnav.fix'){
                    if($this->getConfig()){
                        $_types = [
                            'layout',
                            'block_html',
                            'collections',
                            'full_page',
                        ];
                
                        foreach ($_types as $type) {
                            $this->cacheList->cleanType($type);
                        }
    
                        $content = $layout->createBlock('Isobar\Megamenu\Block\Html\Topmenu')
                            ->setTemplate('Isobar_Megamenu::html/topmenu.phtml')
                            ->setData('cache_lifetime', null)
                            ->setData('cache_tags', [])
                            ->toHtml();
                        $output = $content;
                    }
                }
                $transport->setData('output', $output);
            }
        }
    }
    public function getConfig() {
        return $this->scopeConfigInterface->getValue('megamenu/config/megamenu_general_active_mobile', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
}
