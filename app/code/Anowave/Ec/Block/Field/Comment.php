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
 
namespace Anowave\Ec\Block\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Comment extends \Magento\Config\Block\System\Config\Form\Field
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
	 * Scope helper 
	 * 
	 * @var \Anowave\Ec\Helper\Scope
	 */
	protected $scope;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Anowave\Ec\Helper\Scope $scope
	 * @param \Anowave\Ec\Model\Api $api
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\BlockFactory $blockFactory,
		\Magento\Backend\Block\Template\Context $context,
		\Anowave\Ec\Helper\Scope $scope,
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
		 * Set api 
		 * 
		 * @var \Anowave\Ec\Model\Api $api
		 */
		$this->api = $api;
		
		/**
		 * Set scope 
		 * 
		 * @var \Anowave\Ec\Helper\Scope $scope
		 */
		$this->scope = $scope;
		
		parent::__construct($context);
	}
	
	/**
	 * Get element HTML
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Config\Block\System\Config\Form\Field::_getElementHtml()
	 */
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		return parent::_getElementHtml($element) . $this->getCommentText('');
	}

	/**
	 * Get comment text 
	 * 
	 * @param string $currentValue
	 * @return string
	 */
    public function getCommentText($currentValue = '')
    {
    	if (!$this->getApi()->getClient()->isAccessTokenExpired())
    	{
    		return '<p class="note">' . __('Select Container ID to configure. Dropdown will populate automatically once Account ID is set.') . '</p>';
    	}
    	
    	return $this->blockFactory->createBlock('Anowave\Ec\Block\Comment')->setTemplate('comment.phtml')->toHtml();
    }
    
    /**
     * Get containers 
     * 
     * @return array
     */
    public function getContainers()
    {
    	$account = $this->scope->getConfig('ec/api/google_gtm_account_id');

    	if ($account)
    	{
    		return $this->getApi()->getContainers($account);
    	}
    	
    	return array();
    }

    /**
     * Get API 
     * 
     * @return \Anowave\Ec\Model\Api
     */
    public function getApi()
    {
    	return $this->api;
    }
}