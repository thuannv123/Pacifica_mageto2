<?php
/**
 * Anowave Package
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Package
 * @copyright 	Copyright (c) 2021 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Package\Model\Plugin;

class Builder
{
	/**
	 * @var \Magento\Backend\Model\Menu\Item\Factory
	 */
	protected $_itemFactory;
	
	/**
	 * @param \Magento\Backend\Model\Menu\Item\Factory $menuItemFactory
	 */
	public function __construct(\Magento\Backend\Model\Menu\Item\Factory $menuItemFactory)
	{
		$this->_itemFactory = $menuItemFactory;
	}
	
	/**
	 * After get result 
	 * 
	 * @param \Magento\Backend\Model\Menu\Builder\Interceptor $interceptor
	 * @param \Magento\Backend\Model\Menu $menu
	 * @return \Magento\Backend\Model\Menu
	 */
	public function afterGetResult(\Magento\Backend\Model\Menu\Builder\Interceptor $interceptor, \Magento\Backend\Model\Menu $menu) 
	{
		try 
		{	
			$package = $menu->get('Anowave_Package::package');
			
			/**
			 * Example of adding dynamic values to menu 
			 */
			if ($package && !$menu->get('Anowave_Package::package_marketplace'))
			{
				$item = $this->_itemFactory->create(array
				(
					'type' 		=> 'add',
					'id'   		=> 'Anowave_Package::package_marketplace',
					'title' 	=> 'Visit Marketplace',
					'module' 	=> 'Anowave_Package',
					'sortOrder' => 999,
					'parent' 	=> 'Anowave_Package::package',
					'resource' 	=> 'Anowave_Package::package'
				));
				
				$package->getChildren()->add($item, null, 999);
			}
			
		}
		catch (\Exception $e)
		{
			
		}

		return $menu;
	}
}