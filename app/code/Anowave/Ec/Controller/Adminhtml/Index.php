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

namespace Anowave\Ec\Controller\Adminhtml;

abstract class Index extends \Magento\Backend\App\Action implements \Magento\Framework\App\ActionInterface
{
    /**
     * Admin resource 
     * 
     * @var string
     */
    protected $admin_resource = 'Anowave_Ec::ec';
    
    /**
     * Admin breadcrumb 
     * 
     * @var string
     */
    protected $admin_breadcrumb = 'Index';
    
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct
	(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		
		$this->resultPageFactory = $resultPageFactory;
	}
	
	/**
	 * Logs grid
	 *
	 * @return void
	 */
	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		
		/**
		 * Set active menu
		 */
		$resultPage->setActiveMenu($this->admin_resource);
		
		/**
		 * Add breadcrumbs
		 */
		$resultPage->addBreadcrumb(__($this->admin_breadcrumb), __($this->admin_breadcrumb));
		$resultPage->addBreadcrumb(__($this->admin_breadcrumb), __($this->admin_breadcrumb));
		
		/**
		 * Set title
		 */
		$resultPage->getConfig()->getTitle()->prepend(__($this->admin_breadcrumb));
		
		return $resultPage;
	}
}