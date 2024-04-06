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
use Magento\Framework\UrlInterface;

class ConsentActions extends Column
{
	/**
	 * Delete route
	 * 
	 * @var string
	 */
	const LOG_URL_PATH_DELETE = 'track/consent/delete';
	
	/** 
	 * @var UrlInterface 
	 */
	protected $urlBuilder;
	
	/**
	 * @var string
	 */
	private $editUrl;
	
	/**
	 * Constructor 
	 * 
	 * @param ContextInterface $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param UrlInterface $urlBuilder
	 * @param array $components
	 * @param array $data
	 * @param string $editUrl
	 */
	public function __construct
	(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = []
	) 
	{
		$this->urlBuilder = $urlBuilder;
		
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	
	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) 
		{
			foreach ($dataSource['data']['items'] as & $item) 
			{
				$name = $this->getData('name');
				
				if (isset($item['consent_id'])) 
				{
					$item[$name]['delete'] = 
					[
					    'href'  => $this->urlBuilder->getUrl(self::LOG_URL_PATH_DELETE, ['id' => $item['consent_id']]),
						'label' => __('Delete'),
						'confirm' => 
						[
							'title'   => __("Delete {$item['consent_id']}"),
							'message' => __("Are you sure you want to delete {$item['consent_id']} consent?")
						]
					];
				}
			}
		}
		
		return $dataSource;
	}
}