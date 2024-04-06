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

namespace Anowave\Ec\Controller\Adminhtml\Log;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Anowave\Ec\Model\ResourceModel\Log\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Magento\Backend\App\Action
{
	/**
	 * @var Filter
	 */
	protected $filter;
	
	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param Context $context
	 * @param Filter $filter
	 * @param CollectionFactory $collectionFactory
	 */
	public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
	{
		/**
		 * Set filter 
		 * @var Filter $filter
		 */
		$this->filter = $filter;

		/**
		 * Set collection factory
		 * 
		 * @var CollectionFactory $collectionFactory
		 */
		$this->collectionFactory = $collectionFactory;
		
		parent::__construct($context);
	}
	
	/**
	 * Execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 * @throws \Magento\Framework\Exception\LocalizedException|\Exception
	 */
	public function execute()
	{
		/**
		 * Get collection
		 * 
		 * @var \Magento\Framework\Data\Collection\AbstractDb $collection
		 */
		$collection = $this->filter->getCollection($this->collectionFactory->create());

		/**
		 * Get collection size 
		 * 
		 * @var int $collectionSize
		 */
		$collectionSize = $collection->getSize();
		
		foreach ($collection as $item) 
		{
			$item->delete();
		}
		
		$this->messageManager->addSuccessMessage(__('A total of %1 Log(s) have been deleted.', $collectionSize));
		
		/** 
		 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
		 */
		
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		
		return $resultRedirect->setPath('*/*/');
	}
}