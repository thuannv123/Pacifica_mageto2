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

namespace Anowave\Ec\Observer\Cart;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Update implements ObserverInterface
{
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $session;
	
	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $sessionCheckout;
	
	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Customer\Model\SessionFactory $sessionFactory
	 * @param \Magento\Checkout\Model\Session $sessionCheckout
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 * @param \Anowave\Ec\Helper\Data $helper
	 */
	public function __construct
	(
		\Magento\Customer\Model\SessionFactory $sessionFactory,
		\Magento\Checkout\Model\Session $sessionCheckout,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Anowave\Ec\Helper\Data $helper
	)
	{
		$this->session = $sessionFactory->create();	
		
		/**
		 * Set checkout session
		 * 
		 * @var \Magento\Checkout\Model\Session $sessionCheckout
		 */
		$this->sessionCheckout = $sessionCheckout;
		
		/**
		 * Set product factory 
		 * 
		 * @var \Magento\Catalog\Model\ProductFactory $productFactory
		 */
		$this->productFactory = $productFactory;
		
		/**
		 * Set category respository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $helper
		 */
		$this->helper = $helper;
	}
	
	/**
	 * Execute (non-PHPdoc)
	 * 
	 * @see \Magento\Framework\Event\ObserverInterface::execute()
	 */
	public function execute(EventObserver $observer)
	{
		$push = [];
		
		/**
		 * Get cart parameters 
		 * 
		 * @var [] $cart
		 */
		$cart = $observer->getRequest()->getParam('cart');
		
		if ($cart)
		{
			foreach ($cart as $key => $data)
			{
				if (isset($data['qty']))
				{
					/**
					 * Get quantity 
					 * 
					 * @var Ambiguous $quantity
					 */
					$quantity = (int) $data['qty'];
					
					/**
					 * Get item by id 
					 * 
					 * @var \Magento\Quote\Model\Quote\Item
					 */
					$item = $this->sessionCheckout->getQuote()->getItemById($key);
					
					if ($item && $item->getId())
					{
						/**
						 * Prevent event tracking for quantities that did not change
						 */
						if($quantity === (int)$item->getQty())
						{
							continue;
						}
						
						$product = 
						[
							'id' 	=> $this->helper->getIdentifier($item->getProduct()),
							'price' => $item->getPriceInclTax(),
							'name' 	=> $item->getName(),
							'brand'	=> $this->helper->getBrand
							(
								$item->getProduct()
							)
						];
						
						$categories = $this->helper->getCurrentStoreProductCategories($item->getProduct());
						
						if (!$categories)
						{
							if (null !== $root = $this->helper->getStoreRootDefaultCategoryId())
							{
								$categories[] = $root;
							}
						}
						
						if ($categories)
						{
							/**
							 * Load last category
							 */
							$category = $this->categoryRepository->get
							(
								end($categories)
							);
							
							$product['category'] = $this->helper->getCategoryDetailList($item->getProduct(), $category);
						}
						
						if ($quantity > $item->getQty())
						{
							$product['quantity'] = ($quantity - $item->getQty()); 
							
							$push =
							[
								'event' => 'addToCart',
								'ecommerce' =>
								[
									'add' =>
									[
										'products' => 
										[
											$product
										]
									]
								]
							];
						}
						else 
						{
							$product['quantity'] = ($item->getQty() - $quantity); 
							
							$push =
							[
								'event' => 'removeFromCart',
								'ecommerce' =>
								[
									'remove' =>
									[
										'products' => 
										[
											$product
										]
									]
								]
							];
						}
					}
				}
			}
		}
		
		if ($push)
		{
			$this->session->setCartUpdateEvent($this->helper->getJsonHelper()->encode($push));
		}
		
		return true;
	}
}