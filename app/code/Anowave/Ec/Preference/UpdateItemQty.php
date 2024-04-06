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

class UpdateItemQty extends \Magento\Checkout\Controller\Sidebar\UpdateItemQty
{
	/**
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $cart = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $dataHelper = null;
	
	/**
	 * @var \Magento\Catalog\Model\ProductRepository
	 */
	protected $productRepository;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * @var int
	 */
	private $previousQuantity = 0;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Checkout\Model\Sidebar $sidebar
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Anowave\Ec\Helper\Data $dataHelper
	 * @param \Magento\Catalog\Model\ProductRepository $productRepository
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Checkout\Model\Sidebar $sidebar,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Checkout\Model\Cart $cart,
		\Anowave\Ec\Helper\Data $dataHelper,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository
	) 
	{
		parent::__construct($context, $sidebar, $logger, $jsonHelper);
		
		$this->cart = $cart;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $dataHelper
		 */
		$this->dataHelper = $dataHelper;
		
		/**
		 * Set product repository 
		 * 
		 * @var \Magento\Catalog\Model\ProductRepository $productRepository
		 */
		$this->productRepository = $productRepository;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
	}
	
	public function execute()
	{
		/**
		 * Get originally updated item
		 * 
		 * @var \Anowave\Ec\Preference\UpdateItemQty $item
		 */
		$item_id = (int) (int) $this->getRequest()->getParam('item_id');
		
		try 
		{
			$item = $this->cart->getQuote()->getItemById($item_id);
			
			if ($item && $item->getId())
			{
				$this->previousQuantity = $item->getQty();
			}
		}
		catch (\Exception $e) 
		{
			$this->previousQuantity = 0;
		}

		/**
		 * Execute parent
		 */
		return parent::execute();
	}
	
	/**
     * Compile JSON response
     *
     * @param string $error
     * @return \Magento\Framework\App\Response\Http
     */
    protected function jsonResponse($error = '')
    {
    	/**
    	 * Get response
    	 * 
    	 * @var array $response
    	 */
    	$response = $this->sidebar->getResponseData($error);
    	
    	/**
    	 * Get changed item quantity 
    	 * 
    	 * @var int $itemQty
    	 */
    	$quantity = (int) $this->getRequest()->getParam('item_qty');
    	
    	/**
    	 * Get quote item 
    	 * 
    	 * @var \Magento\Quote\Model\Quote\Item $item
    	 */
    	$item = $this->cart->getQuote()->getItemById((int) $this->getRequest()->getParam('item_id'));
    	
    	if ($item instanceof \Magento\Quote\Api\Data\CartItemInterface)
    	{
    		/**
    		 * Load product
    		 *
    		 * @var \Magento\Catalog\Api\Data\ProductInterface $product
    		 */
    		$product = $this->productRepository->getById
    		(
    			$item->getProductId()
    		);
    		
    		$products = 
    		[
    			'id'  		=> $this->dataHelper->getIdentifier($product),
    			'name' 		=> $item->getName(),
    			'quantity' 	=> $item->getQty(),
    			'price'		=> $item->getPrice(),
    			'brand'		=> $this->dataHelper->getBrand($product)
    		];

    		/**
    		 * Get all product categories
    		 */
    		$categories = $this->dataHelper->getCurrentStoreProductCategories($product);
    		
    		if (!$categories)
    		{
    		    $categories = array_map('intval', $product->getCategoryIds());
    		}
    		
    		$list = null;
    		
    		if ($categories)
    		{
    			/**
    			 * Load last category
    			 */
    			$category = $this->categoryRepository->get
    			(
    				end($categories)
    			);
    			
    			/**
    			 * Set category name
    			 */
    			$products['category'] = $this->dataHelper->getCategory($category);
    			
    			/**
    			 * Get category 
    			 * 
    			 * @var unknown $list
    			 */
    			$list = $this->dataHelper->getCategoryList($category);
    		}
    		
    		/**
    		 * Get current cart quantity 
    		 * 
    		 * @var int $current
    		 */
    		$current = (int) $this->previousQuantity;
    		
    		/**
    		 * Determine whether product is added or deducted
    		 */
    		if ($current > $quantity)
    		{
    			$products['quantity'] = ($current - $quantity);
    			
    			$data =
    			[
    				'event' 	=> 'removeFromCart',
    				'ecommerce' =>
    				[
    					'remove' =>
    					[
    					    'actionField' => 
    					    [
    					        'list' => $list ? $list : __('Default list')
    					    ],
    						'products' =>
    						[
    							$products
    						]
    					]
    				]
    			];
    		}
    		else 
    		{
    			$products['quantity'] = ($quantity - $current);
    			
    			$data =
    			[
    				'event' 	=> 'addToCart',
    				'ecommerce' =>
    				[
    					'add' =>
    					[
    					    'actionField' =>
    					    [
    					        'list' => $list ? $list : __('Default list')
    					    ],
    						'products' =>
    						[
    							$products
    						]
    					]
    				]
    			];
    		}
    		
    		/**
    		 * Set response push
    		 */
    		$response['dataLayer'] = $data;
    		
    		if ($this->dataHelper->useSummary())
    		{
    		    $items = [];
    		    
    		    foreach ($this->cart->getQuote()->getItems() as $item)
    		    {
    		        $items[] =
    		        [
    		            'id'       => $this->dataHelper->getIdentifier($item->getProduct()),
    		            'price'    => $this->dataHelper->getPrice($item->getProduct()),
    		            'name'     => $item->getProduct()->getName(),
    		            'quantity' => $item->getQty()
    		        ];
    		    }
    		    
    		    
    		    $response['dataLayer']['current_cart_items'] = $items;
    		}
    	}
    	
    	/**
    	 * Create transport object
    	 *
    	 * @var \Magento\Framework\DataObject $transport
    	 */
    	$transport = new \Magento\Framework\DataObject
    	(
    	    [
    	        'response' => $response
    	    ]
    	);
    	
    	/**
    	 * Notify others
    	 */
    	$this->dataHelper->getEventManager()->dispatch('ec_get_update_quantity_attributes', ['transport' => $transport]);
    	
    	$response = $transport->getResponse();
    	
    	return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($response));
    }
}