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

namespace Anowave\Ec\Model;

class Comment implements \Magento\Config\Model\Config\CommentInterface
{
	/**
	 * @var \Anowave\Ec\Model\Api
	 */
	protected $api = null;
	
	/**
	 * Block factory
	 * 
	 * @var \Magento\Framework\View\Element\BlockFactory
	 */
	protected $blockFactory;
	
	/**
	 * Context
	 *
	 * @var \Magento\Framework\App\Helper\Context
	 */
	protected $context = null;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Anowave\Ec\Model\Api $api
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\BlockFactory $blockFactory,
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Anowave\Ec\Model\Api $api
	)
	{
		/**
		 * Set block factory 
		 * 
		 * @var \Magento\Framework\View\Element\BlockFactory $blockFactory
		 */
		$this->blockFactory = $blockFactory;
		
		/**
		 * Set context 
		 * 
		 * @var \Magento\Framework\App\Helper\Context $context
		 */
		$this->context = $context;
		
		/**
		 * Set store manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set API 
		 * 
		 * @var \Anowave\Ec\Model\Api $api
		 */
		$this->api = $api;

	}
	
	public function getCommentText($currentValue)
	{
		$containers = [];
		
		$errors = [];
		
		try 
		{
			foreach($this->getContainers() as $container)
			{
				$containers[] = "Container: <strong>$container->publicId</strong>,  Container ID: <strong>$container->containerId</strong>";
			}
		}
		catch (\Exception $e)
		{
			$errors[] = $e->getMessage();
		}
		
		if (!$errors)
		{
			if (!$this->getApi()->getClient()->isAccessTokenExpired())
			{
				return nl2br(join(PHP_EOL, $containers));
			}
		}
		
		return $this->blockFactory->createBlock('Anowave\Ec\Block\Comment')->setTemplate('comment.phtml')->setData(['errors' => $errors])->toHtml();
	}
	
	protected function getContainers()
	{
		$account = $this->context->getScopeConfig()->getValue('ec/api/google_gtm_account_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getCode());
		
		if ($account)
		{
			return $this->getApi()->getContainers($account);
		}
			
		return array();
	}
	
	public function getStore()
	{
		if ($this->context->getRequest()->getParam('store'))
		{
			return $this->storeManager->getStore((int) $this->context->getRequest()->getParam('store'));
		}
	
		return $this->storeManager->getStore();
	}
	
	protected function getApi()
	{
		return $this->api;
	}
}