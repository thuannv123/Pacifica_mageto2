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

namespace Anowave\Ec\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\Event\ManagerInterface as EventManager;

class JsFooterPlugin
{
    const XML_PATH_DEV_MOVE_JS_TO_BOTTOM = 'dev/js/move_script_to_bottom';
    
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;
    
    /**
     * Constructor 
     * 
     * @param ScopeConfigInterface $scopeConfig
     * @param EventManager $eventManager
     */
    public function __construct
    (
        ScopeConfigInterface $scopeConfig,
        EventManager $eventManager
    )
    {
       
        /**
         * Set scope config 
         * 
         * @var \Anowave\Ec\Plugin\JsFooterPlugin $scopeConfig
         */
        $this->scopeConfig = $scopeConfig;
        
        /**
         * Set event manager 
         * 
         * @var \Magento\Framework\Event\ManagerInterface $eventManager
         */
        $this->eventManager = $eventManager;
    }
    
    /**
     * Around after render 
     * 
     * @param \Magento\Theme\Controller\Result\JsFooterPlugin $plugin
     * @param callable $proceed
     * @param Layout $subject
     * @param Layout $result
     * @param ResponseInterface $httpResponse
     * @return Layout
     */
    public function aroundAfterRenderResult(\Magento\Theme\Controller\Result\JsFooterPlugin $plugin, callable $proceed, Layout $subject, Layout $result, ResponseInterface $httpResponse)
    {   
        if (!$this->isActive())
        {
            return $proceed($subject, $result, $httpResponse);
        }
        
        if ($this->isDeferEnabled())
        {
            $content = (string) $httpResponse->getContent();
            
            $httpResponse->setContent
            (
                $this->transform($content)
            );
        }
        
        /**
         * Create transport object
         *
         * @var \Magento\Framework\DataObject $transport
         */
        $transport = new \Magento\Framework\DataObject
        (
            [
                'result'    => $result,
                'response'  => $httpResponse
            ]
        );
        
        /**
         * Notify others for schema
         */
        $this->eventManager->dispatch('footer_after_get_result', ['transport' => $transport]);
        
        return $transport->getResult();
    }
    
    /**
     * Around before 
     * 
     * @param \Magento\Theme\Controller\Result\JsFooterPlugin $plugin
     * @param callable $proceed
     * @param Http $subject
     */
    public function aroundBeforeSendResponse(\Magento\Theme\Controller\Result\JsFooterPlugin $plugin, callable $proceed, Http $subject)
    {
        if (!$this->isActive())
        {
            return $proceed($subject);
        }
        
        /**
         * Response content 
         * 
         * @var string $content
         */
        $content = $subject->getContent();
        
        $subject->setContent
        (
            $this->transform($content)
        );
    }

    /**
     * Transform content 
     * 
     * @param string $content
     * @return string
     */
    private function transform($content)
    {
        if (!$content)
        {
            return $content;
        }

        if (strpos($content, '</body') !== false)
        {
            if ($this->isDeferEnabled())
            {
                $scripts = $this->getScripts($content);
                
                foreach ($scripts as $script)
                {
                    $content = str_replace($script, '', $content);
                }
                
                $content = str_replace('</body', implode("\n", $scripts) . "\n</body", $content);
            }
        }
        
        return $content;
    }
    
    private function getScripts($content) : array
    {
        $scripts = [];
        
        /**
         * Skip scripts criteria
         *
         * @var array $skip
         */
        $skip =
        [
            'data-ommit',
            'www.googletagmanager.com/gtm.js',
            'ec.js',
            'ec.min.js',
            'ec4.js',
            'ec4.min.js'
        ];
        
        /**
         * Match scripts in response
         *
         * @var string $pattern
         */
        $pattern = '#<script[^>]*+(?<!text/x-magento-template.)>.*?</script>#is';
        
        $content = preg_replace_callback($pattern,function ($matchPart) use (&$scripts, &$skip, &$content)
        {
            foreach ($skip as $value)
            {
                /**
                 * Prevent script from being moved to bottom
                 */
                if (false !== strpos($matchPart[0], $value))
                {
                    return $matchPart[0];
                }
            }
            
            $scripts[] = $matchPart[0];
            
            /**
             * Remove script from original content
             * 
             * @var string $content
             */
             return '';
            
        },$content);
        
        return $scripts;
    }
    
    /**
     * Returns information whether moving JS to footer is enabled
     *
     * @return bool
     */
    private function isDeferEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(static::XML_PATH_DEV_MOVE_JS_TO_BOTTOM,ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Returns information whether moving JS to footer is enabled
     *
     * @return bool
     */
    private function isActive(): bool
    {
        return $this->scopeConfig->isSetFlag('ec/general/active',ScopeInterface::SCOPE_STORE);
    }
}