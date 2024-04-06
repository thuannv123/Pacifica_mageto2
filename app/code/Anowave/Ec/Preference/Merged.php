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

namespace Anowave\Ec\Preference;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Merged extends \Magento\Framework\View\Asset\Merged
{
    const XML_PATH_DEV_MOVE_JS_TO_BOTTOM = 'dev/js/move_script_to_bottom';
    
    /**
     * Disable recustion 
     * 
     * @var string
     */
    private $bootstrap = false;

	/**
     * Attempt to merge assets, falling back to original non-merged ones, if merging fails
     *
     * @return void
     */
    protected function initialize()
    {
        if (!$this->bootstrap)
        {
            $this->bootstrap = true;
            
            $map = [];
            
            if ($this->isDeferEnabled())
            {
                foreach ($this->assets as $key => $asset)
                {
                    if ('Anowave_Ec' === $asset->getModule() && false !== strrpos($asset->getUrl(), 'ec.js'))
                    {
                        $map[] = $asset;
                        
                        unset($this->assets[$key]);
                    }
                    
                    if ('Anowave_Ec4' === $asset->getModule() && false !== strrpos($asset->getUrl(), 'ec4.js'))
                    {
                        $map[] = $asset;
                        
                        unset($this->assets[$key]);
                    }
                }
            }
            
            parent::initialize();

            if ($map)
            {
                foreach ($map as $asset)
                {
                    $this->assets[] = $asset;
                }
            }
        }
    }
    
    /**
     * Returns information whether moving JS to footer is enabled
     *
     * @return bool
     */
    private function isDeferEnabled(): bool
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Framework\App\Config\ScopeConfigInterface')->isSetFlag(static::XML_PATH_DEV_MOVE_JS_TO_BOTTOM,ScopeInterface::SCOPE_STORE);
    }
}