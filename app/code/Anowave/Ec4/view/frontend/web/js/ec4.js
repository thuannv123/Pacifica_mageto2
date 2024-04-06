if ('undefined' !== typeof AEC && 'undefined' !== typeof AEC.EventDispatcher)
{	
	AEC.GA4 = (() => 
	{
		return {
			enabled: false,
			transformCategories: function(category)
			{
				let map = {}, categories = category.split('/');
				
				if (categories)
				{
					map['item_category'] = categories.shift();
					
					if (categories.length)
					{
						let index = 1;
						
						categories.forEach(category => 
						{
							map['item_category' + (++index)] = category;
						});
					}
				}
				
				return map;
			},
			augmentCategories: function(product) 
			{
				let category = null;
				
				if (product.hasOwnProperty('category'))
				{
					category = product.category;
					
					if (AEC.localStorage)
		            {
		                let reference = AEC.Storage.reference().get();
		                
		                if (reference)
		                {
			                for (var a = 0, b = reference.length; a < b; a++)
		    				{
		    					if (product.id.toString().toLowerCase() === reference[a].id.toString().toLowerCase())
		    					{
		    						category = reference[a].category;
		    					}
		    				}
		                }
		            }
				}
				
				return this.transformCategories(category);
			},
			augmentItem: function(product)
			{
				let map = {};
				
				map['google_business_vertical'] = 'retail';
				
				Object.entries(product).forEach(([key, value]) => 
				{
					if (-1 === ['id','name','price','category','currency','variant','brand'].indexOf(key))
					{
						map[key] = value;
					}
				});
				
				return map;
			}
			
		}
	})();
	
	/**
	 * Modify product impressions payload
	 */
	AEC.EventDispatcher.on('ec.cookie.impression.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		var items = [];
		
		data.ecommerce.impressions.forEach(product => 
		{
			let item = 
			{	
				item_id: 		product.id,
				item_name: 		product.name,
				item_list_name: data.ecommerce.actionField.list,
				item_list_id:	data.ecommerce.actionField.list,
				item_brand: 	product.brand,
				price: 			product.price,
				quantity: 		product.quantity ? product.quantity : 1,
				index: 			product.position,
				currency:		AEC.GA4.currency
			};
			
			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));

			items.push(item);
		});
		
		data['event'] = 'view_item_list';
		data.ecommerce['items'] = items;
	});
	
	AEC.EventDispatcher.on('ec.widget.view.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		var items = [];
		
		data.ecommerce.impressions.forEach(product => 
		{
			let item = 
			{
					
				item_id: 		product.id,
				item_name: 		product.name,
				item_list_name: data.ecommerce.actionField.list,
				item_list_id:	data.ecommerce.actionField.list,
				item_brand: 	product.brand,
				price: 			product.price,
				quantity: 		product.quantity ? impression.quantity : 1,
				index: 			product.position,
				currency:		AEC.GA4.currency
			};
			
			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));

			items.push(item);
		});
		
		data['event'] = 'view_item_list';
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify product click payload
	 */
	AEC.EventDispatcher.on('ec.cookie.click.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.click.products.forEach(product => 
		{
			let item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        item_list_name: data.ecommerce.click.actionField.list,
		        quantity: 		product.quantity,
		        index: 			product.position,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});

		data['event'] = 'select_item';
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify product detail payload
	 */
	AEC.EventDispatcher.on('ec.cookie.detail.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.detail.products.forEach(product => 
		{
			let item = 
			{
				item_name: 		product.name,
				item_id: 		product.id,
		        item_brand: 	product.brand,
		        item_list_name: data.ecommerce.detail.actionField.list,
				item_list_id:	data.ecommerce.detail.actionField.list,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		data['event'] = 'view_item';
		
		data.ecommerce['items'] = items;
		data.ecommerce['value'] = items[0].price;
	});
	
	
	/**
	 * Modify add to cart payload
	 */
	AEC.EventDispatcher.on('ec.cookie.add.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.add.products.forEach(product => 
		{
			let item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        item_list_id:   data.ecommerce.add.actionField.list,
		        item_list_name: data.ecommerce.add.actionField.list,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};

			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}
			
			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		data['event'] = 'add_to_cart';
		
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify remove from cart payload
	 */
	AEC.EventDispatcher.on('ec.cookie.remove.item.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.remove.products.forEach((product) => 
		{
			var item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        item_list_id: 	data.ecommerce.remove.actionField.list,
		        item_list_name: data.ecommerce.remove.actionField.list,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}
		
			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		data['event'] = 'remove_from_cart';
		
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify remove from cart payload
	 */
	AEC.EventDispatcher.on('ec.cookie.update.item.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		if ('addToCart' === data.event)
		{
			data.ecommerce.add.products.forEach(product => 
			{
				var item = 
				{
					item_id: 		product.id,
					item_name: 		product.name,
			        item_brand: 	product.brand,
			        item_list_id:   data.ecommerce.add.actionField.list,
			        item_list_name: data.ecommerce.add.actionField.list,
			        quantity: 		product.quantity,
			        price: 			product.price,
			        currency:		AEC.GA4.currency
				};
				
				if (product.hasOwnProperty('variant'))
				{
					item['item_variant'] = product.variant;
				}
			
				Object.assign(item, item, AEC.GA4.augmentItem(product));
				Object.assign(item, item, AEC.GA4.augmentCategories(product));
				
				items.push(item);
			});
			
			data['event'] = 'add_to_cart';
		}
		else 
		{
			data.ecommerce.remove.products.forEach((product) => 
			{
				var item = 
				{
					item_id: 		product.id,
					item_name: 		product.name,
			        item_brand: 	product.brand,
			        item_list_id:   data.ecommerce.remove.actionField.list,
			        item_list_name: data.ecommerce.remove.actionField.list,
			        quantity: 		product.quantity,
			        price: 			product.price,
			        currency:		AEC.GA4.currency
				};
				
				if (product.hasOwnProperty('variant'))
				{
					item['item_variant'] = product.variant;
				}
			
				Object.assign(item, item, AEC.GA4.augmentItem(product));
				Object.assign(item, item, AEC.GA4.augmentCategories(product));
				
				items.push(item);
			});
			
			data['event'] = 'remove_from_cart';
		}
		
		data.ecommerce['items'] = items;
	});
	
	
	
	/**
	 * Modify checkout step payload
	 */
	

	AEC.EventDispatcher.on('ec.checkout.step.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
	});
	
	AEC.EventDispatcher.on('ec.cookie.checkout.step.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		let value = 0;
		
		data.ecommerce.checkout.products.forEach(product => 
		{
			var item = 
			{
				item_id: 			product.id,
				item_name: 			product.name,
		        item_brand: 		product.brand,
		        item_list_id:       product.list,
		        item_list_name:     product.list,
		        quantity: 			product.quantity,
		        price: 				product.price,
		        currency:			AEC.GA4.currency
			};

			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}
			
			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
			
			value += (product.price * product.quantity);
		});
		
		if (1 == Number(data.ecommerce.checkout.actionField.step))
		{
			data['event'] = 'begin_checkout';
		}
		else
		{
			data['event'] = 'checkout';
		}
		
		data.ecommerce['items'] = items;
		data.ecommerce['value'] = window.hasOwnProperty('checkoutConfig') ? window.checkoutConfig.totalsData.base_grand_total : value;
	});
	
	/**
	 * Modify checkout step option payloasd
	 */
	AEC.EventDispatcher.on('ec.checkout.step.option.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}

		switch(parseInt(data.ecommerce.checkout_option.actionField.step))
		{
			case AEC.Const.CHECKOUT_STEP_SHIPPING:
				
				data['event'] = 'add_shipping_info';
				
				if (AEC.GA4.quote.hasOwnProperty('coupon'))
				{
					data.ecommerce['coupon'] = AEC.GA4.quote.coupon;
				}
				
				data.ecommerce['currency'] = AEC.GA4.currency;
				
				data.ecommerce['items'] = AEC.Checkout.data.ecommerce.items;
				
				data.ecommerce['shipping_tier'] = data.ecommerce.checkout_option.actionField.option;
				
				delete data.ecommerce.checkout_option;
				
				break;
				
			case AEC.Const.CHECKOUT_STEP_PAYMENT:
				
				data['event'] = 'add_payment_info';
				
				if (AEC.GA4.quote.hasOwnProperty('coupon'))
				{
					data.ecommerce['coupon'] = AEC.GA4.quote.coupon;
				}

				data.ecommerce['currency'] = AEC.GA4.currency;
				
				data.ecommerce['items'] = AEC.Checkout.data.ecommerce.items;
				
				data.ecommerce['payment_type'] = data.ecommerce.checkout_option.actionField.option;
				
				delete data.ecommerce.checkout_option;
				
				break;
		}
	});
	
	/**
	 * Modify purchase payload
	 */
	AEC.EventDispatcher.on('ec.cookie.purchase.data', data => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.purchase.products.forEach(product => 
		{
			let item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}

			Object.assign(item, item, AEC.GA4.augmentItem(product));
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		data['event'] = AEC.GA4.conversion_event;
		
		data.ecommerce.purchase['items'] 			= items;
		data.ecommerce.purchase['transaction_id'] 	= data.ecommerce.purchase.actionField.id;
		data.ecommerce.purchase['affiliation'] 		= data.ecommerce.purchase.actionField.id;
		data.ecommerce.purchase['value'] 			= data.ecommerce.purchase.actionField.revenue;
		data.ecommerce.purchase['tax'] 				= data.ecommerce.purchase.actionField.tax;
		data.ecommerce.purchase['shipping'] 		= data.ecommerce.purchase.actionField.shipping;
		data.ecommerce.purchase['currency'] 		= data.ecommerce.currencyCode;
		data.ecommerce.purchase['coupon'] 			= data.ecommerce.purchase.actionField.coupon;
	});
	
	/**
	 * Add to wishlist (Google Analytics 4)
	 */
	AEC.EventDispatcher.on('ec.add.wishlist', (data, options) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let attributes = JSON.parse(options.attributes);
		
		data['event']     = 'add_to_wishlist';
		data['ecommerce'] = 
		{
			items: attributes.items
		};
	});
	
	AEC.EventDispatcher.on('ec.add.compare', (data, options) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let attributes = JSON.parse(options.attributes);
		
		data['event']     = 'add_to_compare';
		data['ecommerce'] = 
		{
			items: attributes.items
		};
	});
	
	AEC.EventDispatcher.on('ec.cookie.promotion.entity', (data, options) => 
	{	
		if (!AEC.GA4.enabled)
		{
			return true;
		}
	});
	
	AEC.EventDispatcher.on('ec.cookie.promotion.click', (data, options) => 
	{	
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		const promotion = Object.fromEntries(Object.entries(Object.assign({}, options.element.dataset)).filter(([key, val]) => ['creative_name','creative_slot','location_id','promotion_id','promotion_name'].includes(key)));
		
		promotion['items'] = [];
		
		if (data['ecommerce'].hasOwnProperty('promoClick'))
		{
			data['ecommerce']['promoClick']['promotions'].forEach(entry => 
			{
				promotion['items'].push(
				{
					'item_id': 			entry.id,
					'item_name': 		entry.name,
					'promotion_id': 	entry.id,
					'promotion_name':   entry.name,
					'creative_name': 	entry.creative,
					'creative_slot': 	entry.position
				});
			});
		}
		
		Object.entries(promotion).forEach(([key, value]) => 
		{
			data['ecommerce'][key] = value;
		});
		
		data['event'] = 'select_promotion';
	});
	
	AEC.EventDispatcher.on('ec.cookie.promotion.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}

		const promotion = {};
		
		promotion['items'] = [];
		
		if (data['ecommerce'].hasOwnProperty('promoView'))
		{
			data['ecommerce']['promoView']['promotions'].forEach(entry => 
			{
				promotion['items'].push(
				{
					'item_id': 			entry.id,
					'item_name': 		entry.name,
					'promotion_id': 	entry.id,
					'promotion_name':   entry.name,
					'creative_name': 	entry.creative,
					'creative_slot': 	entry.position
				});
			});
		}

		Object.entries(promotion).forEach(([key, value]) => 
		{
			data['ecommerce'][key] = value;
		});
		
		data['event'] = 'view_promotion';
	});
}