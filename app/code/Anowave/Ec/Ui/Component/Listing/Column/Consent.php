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

namespace Anowave\Ec\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;


class Consent extends Column
{
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
	public function __construct
	(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
	{
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) 
    {
        if (isset($dataSource['data']['items'])) 
        {
            foreach ($dataSource['data']['items'] as &$item)
            {
                $consent = json_decode($item['consent'], true);
                
                $value = [];
                
                foreach ($consent as $cookie => $state)
                {
                    $value[] = "<strong>$cookie</strong>";
                    $value[] = $state ? __('Accepted') : __('Declined');
                }
                
                /**
                 * Rebuild consent line 
                 * 
                 * @var Ambiguous $item
                 */
                $item['consent'] = nl2br(join(PHP_EOL, $value));
                
                /**
                 * Revert IP address
                 */
                $item['consent_ip'] = long2ip($item['consent_ip']);
            }
        }
        
        return $dataSource;
    }
}