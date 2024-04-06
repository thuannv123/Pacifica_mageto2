<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking GA4
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
 * @package 	Anowave_Ec4
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Model;

class Api extends \Anowave\Ec\Model\Api
{ 
    const TRIGGER_GA4_SELECT_ITEM 		= 'GA4 Event Equals Select Item';
    const TRIGGER_GA4_VIEW_ITEM 		= 'GA4 Event Equals View Item';
    const TRIGGER_GA4_VIEW_ITEM_LIST 	= 'GA4 Event Equals View Item List';
    const TRIGGER_GA4_ADD_TO_CART 	    = 'GA4 Event Equals Add To Cart';
    const TRIGGER_GA4_ADD_TO_WISHLIST 	= 'GA4 Event Equals Add To Wishlist';
    const TRIGGER_GA4_ADD_TO_COMPARE 	= 'GA4 Event Equals Add To Compare';
    const TRIGGER_GA4_REMOVE_FROM_CART 	= 'GA4 Event Equals Remove From Cart';
    const TRIGGER_GA4_VIEW_PROMOTION 	= 'GA4 Event Equals View Promotion';
    const TRIGGER_GA4_SELECT_PROMOTION 	= 'GA4 Event Equals Select Promotion';
    const TRIGGER_GA4_BEGIN_CHECKOUT 	= 'GA4 Event Equals Begin Checkout';
    const TRIGGER_GA4_PURCHASE          = 'GA4 Event Equals Purchase';
    const TRIGGER_GA4_REFUND            = 'GA4 Event Equals Refund';
    const TRIGGER_GA4_VIEW_CART         = 'GA4 Event Equals View Cart';
    const TRIGGER_GA4_ADD_SHIPPING_INFO = 'GA4 Event Equals Add Shipping Info';
    const TRIGGER_GA4_ADD_PAYMENT_INFO  = 'GA4 Event Equals Add Payment Info';
    
    
    const GA4_EVENT_ADD_TO_CART         = 'add_to_cart';
    const GA4_EVENT_ADD_TO_WISHLIST     = 'add_to_wishlist';
    const GA4_EVENT_ADD_TO_COMPARE      = 'add_to_compare';
    const GA4_EVENT_REMOVE_FROM_CART    = 'remove_from_cart';
    const GA4_EVENT_SELECT_ITEM         = 'select_item';
    const GA4_EVENT_VIEW_ITEM           = 'view_item';
    const GA4_EVENT_VIEW_ITEM_LIST      = 'view_item_list';
    const GA4_EVENT_VIEW_PROMOTION      = 'view_promotion';
    const GA4_EVENT_SELECT_PROMOTION    = 'select_promotion';
    const GA4_EVENT_BEGIN_CHECKOUT      = 'begin_checkout';
    const GA4_EVENT_PURCHASE            = 'purchase';
    const GA4_EVENT_REFUND              = 'refund';
    const GA4_EVENT_VIEW_CART           = 'view_cart';
    const GA4_EVENT_PAYMENT_INFO        = 'add_payment_info';
    const GA4_EVENT_SHIPPING_INFO       = 'add_shipping_info';
    
    /**
     * Tag name
     *
     * @var string
     */
    const TAG_GA4_CONFIGURATION     = 'GA4 Configuration';
    const TAG_GA4_ADD_TO_CART       = 'EE4 Add To Cart';
    const TAG_GA4_ADD_TO_WISHLIST   = 'EE4 Add To Wishlist';
    const TAG_GA4_ADD_TO_COMPARE    = 'EE4 Add To Compare';
    const TAG_GA4_REMOVE_FROM_CART  = 'EE4 Remove From Cart';
    const TAG_GA4_SELECT_ITEM       = 'EE4 Select Item';
    const TAG_GA4_VIEW_ITEM         = 'EE4 View Item';
    const TAG_GA4_VIEW_ITEM_LIST    = 'EE4 View Item List';
    const TAG_GA4_VIEW_PROMOTION    = 'EE4 View Promotion';
    const TAG_GA4_SELECT_PROMOTION  = 'EE4 Select Promotion';
    const TAG_GA4_BEGIN_CHECKOUT    = 'EE4 Begin Checkout';
    const TAG_GA4_PURCHASE          = 'EE4 Purchase';
    const TAG_GA4_REFUND            = 'EE4 Refund';
    const TAG_GA4_VIEW_CART         = 'EE4 View Cart';
    const TAG_GA4_ADD_PAYMENT_INFO  = 'EE4 Add Payment Info';
    const TAG_GA4_ADD_SHIPPING_INFO = 'EE4 Add Shipping Info';
    
    /**
     * Tag type
     *
     * @var integer
     */
    const TAG_MANAGER_TAG_TYPE_GA4_CONFIGURATION = 'gaawc';
    
    /**
     * Tag type event
     * @var string
     */
    const TAG_MANAGER_TAG_TYPE_GA4_EVENT = 'gaawe';
    
    /**
     * Variable type / Measurement protocol
     *
     * @var string
     */
    const TAG_MANAGER_VARIABLE_TYPE_GA4_MEASUREMENT_ID = 'mp';
    
    /**
     * Created variables (Google Analytics 4)
     * 
     * @param string $account
     * @param string $container
     * @return string[]|NULL[]
     */
    public function ec_api_variables_ga4($account, $container)
    {
        $schema =
        [
            'ee4 ecommerce items' =>
            [
                'name' 		=> 'ee4 ecommerce items',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.items'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase items' =>
            [
                'name' 		=> 'ee4 ecommerce purchase items',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.items'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase transaction id' =>
            [
                'name' 		=> 'ee4 ecommerce purchase transaction id',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.transaction_id'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase value' =>
            [
                'name' 		=> 'ee4 ecommerce purchase value',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.value'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase currency' =>
            [
                'name' 		=> 'ee4 ecommerce purchase currency',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.currency'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase shipping' =>
            [
                'name' 		=> 'ee4 ecommerce purchase shipping',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.shipping'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase tax' =>
            [
                'name' 		=> 'ee4 ecommerce purchase tax',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.tax'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase coupon' =>
            [
                'name' 		=> 'ee4 ecommerce purchase coupon',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.coupon'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce purchase affiliation' =>
            [
                'name' 		=> 'ee4 ecommerce purchase affiliation',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.purchase.actionField.affiliation'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce shipping tier' =>
            [
                'name' 		=> 'ee4 ecommerce shipping tier',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.shipping_tier'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee4 ecommerce payment type' =>
            [
                'name' 		=> 'ee4 ecommerce payment type',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'ecommerce.payment_type'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ],
            'ee conversion id' => 
            [
                'name' 		=> 'ee conversion id',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_CONSTANT_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'value',
                        'value' => $this->getAdwordsConversionId()
                    ]
                ]
            ],
            'ee conversion label' =>
            [
                'name' 		=> 'ee conversion label',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_CONSTANT_VARIABLE,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'value',
                        'value' => $this->getAdWordsConversionLabel()
                    ]
                ]
            ],
            'google_tag_params' => 
            [
                'name' 		=> 'google_tag_params',
                'type'		=> \Anowave\Ec\Model\Api::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
                'parameter' => 
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'name',
                        'value' => 'google_tag_params'
                    ],
                    [
                        'type' 	=> 'integer',
                        'key' 	=> 'dataLayerVersion',
                        'value' => 2
                    ]
                ]
            ]
        ];
        
        return $this->generate_variables($schema, $account, $container);
    }
    
    /**
     * Created triggers (Google Analytics 4)
     *
     * @param string $account
     * @param string $container
     * @return string[]|NULL[]
     */
    public function ec_api_triggers_ga4($account, $container)
    {
        $schema = 
        [
            static::TRIGGER_GA4_SELECT_ITEM =>
            [
                'name' 				=> static::TRIGGER_GA4_SELECT_ITEM,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_SELECT_ITEM
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_VIEW_ITEM =>
            [
                'name' 				=> static::TRIGGER_GA4_VIEW_ITEM,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_VIEW_ITEM
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_VIEW_ITEM_LIST =>
            [
                'name' 				=> static::TRIGGER_GA4_VIEW_ITEM_LIST,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_VIEW_ITEM_LIST
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_ADD_TO_CART =>
            [
                'name' 				=> static::TRIGGER_GA4_ADD_TO_CART,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_ADD_TO_CART
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_REMOVE_FROM_CART =>
            [
                'name' 				=> static::TRIGGER_GA4_REMOVE_FROM_CART,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_REMOVE_FROM_CART
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_VIEW_PROMOTION =>
            [
                'name' 				=> static::TRIGGER_GA4_VIEW_PROMOTION,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_VIEW_PROMOTION
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_SELECT_PROMOTION =>
            [
                'name' 				=> static::TRIGGER_GA4_SELECT_PROMOTION,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_SELECT_PROMOTION
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_BEGIN_CHECKOUT =>
            [
                'name' 				=> static::TRIGGER_GA4_BEGIN_CHECKOUT,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_BEGIN_CHECKOUT
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_PURCHASE =>
            [
                'name' 				=> static::TRIGGER_GA4_PURCHASE,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_PURCHASE
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_REFUND =>
            [
                'name' 				=> static::TRIGGER_GA4_REFUND,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_REFUND
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_VIEW_CART =>
            [
                'name' 				=> static::TRIGGER_GA4_VIEW_CART,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_VIEW_CART
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_ADD_TO_WISHLIST =>
            [
                'name' 				=> static::TRIGGER_GA4_ADD_TO_WISHLIST,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_ADD_TO_WISHLIST
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_ADD_TO_COMPARE =>
            [
                'name' 				=> static::TRIGGER_GA4_ADD_TO_COMPARE,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_ADD_TO_COMPARE
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_ADD_SHIPPING_INFO =>
            [
                'name' 				=> static::TRIGGER_GA4_ADD_SHIPPING_INFO,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_SHIPPING_INFO
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_GA4_ADD_PAYMENT_INFO =>
            [
                'name' 				=> static::TRIGGER_GA4_ADD_PAYMENT_INFO,
                'type'				=> \Anowave\Ec\Model\Api::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' =>
                [
                    [
                        'type' => 'equals',
                        'parameter' =>
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => static::GA4_EVENT_PAYMENT_INFO
                            ]
                        ]
                    ]
                ]
            ],
            static::TRIGGER_REMARKETING_TAG => array
            (
                'name' 				=> static::TRIGGER_REMARKETING_TAG,
                'type'				=> static::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
                'customEventFilter' => 
                [
                    [
                        'type' => 'equals',
                        'parameter' => 
                        [
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' 	=> 'template',
                                'key' 	=> 'arg1',
                                'value' => 'fireRemarketingTag'
                            ]
                        ]
                    ]
                ]
            )
        ];
        
        return $this->generate_triggers($schema, $account, $container);
    }
    
    /**
     * Created tags (Google Analytics 4)
     *
     * @param string $account
     * @param string $container
     * @return string[]|NULL[]
     */
    public function ec_api_tags_ga4($account, $container)
    {
        /**
         * Reset current triggers map 
         * 
         * @var \Anowave\Ec4\Model\Api $currentTriggers
         */
        $this->currentTriggers = [];
        
        /**
         * Get triggers
         * 
         * @var array $triggers
         */
        $triggers = $this->getTriggersMap($account, $container);

        $schema =
        [
            static::TAG_GA4_CONFIGURATION =>
            [
                'name' 				=> static::TAG_GA4_CONFIGURATION,
                'firingTriggerId' 	=>
                [
                    \Anowave\Ec\Model\Api::TRIGGER_ALL_PAGES
                ],
                'type' => static::TAG_MANAGER_TAG_TYPE_GA4_CONFIGURATION,
                'parameter' =>
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'measurementId',
                        'value' => $this->getMeasurementId()
                    ],
                    [
                        'type' 	=> 'boolean',
                        'key' 	=> 'sendPageView',
                        'value' => 'true'
                    ]
                ]
            ],
            static::TAG_GA4_ADD_TO_CART =>
            [
                'name' 				=> static::TAG_GA4_ADD_TO_CART,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_ADD_TO_CART]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_ADD_TO_CART
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=> 
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=> 
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_REMOVE_FROM_CART =>
            [
                'name' 				=> static::TAG_GA4_REMOVE_FROM_CART,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_REMOVE_FROM_CART]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_REMOVE_FROM_CART
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_SELECT_ITEM =>
            [
                'name' 				=> static::TAG_GA4_SELECT_ITEM,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_SELECT_ITEM]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_SELECT_ITEM
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_VIEW_ITEM =>
            [
                'name' 				=> static::TAG_GA4_VIEW_ITEM,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_VIEW_ITEM]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_VIEW_ITEM
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_VIEW_ITEM_LIST =>
            [
                'name' 				=> static::TAG_GA4_VIEW_ITEM_LIST,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_VIEW_ITEM_LIST]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_VIEW_ITEM_LIST
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_PURCHASE =>
            [
                'name' 				=> static::TAG_GA4_PURCHASE,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_PURCHASE]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_PURCHASE
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase items}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'transaction_id'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase transaction id}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'value'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase value}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'currency'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase currency}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'shipping'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase shipping}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'tax'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase tax}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'affiliation'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase affiliation}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'coupon'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce purchase coupon}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_REFUND =>
            [
                'name' 				=> static::TAG_GA4_REFUND,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_REFUND]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_REFUND
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_BEGIN_CHECKOUT =>
            [
                'name' 				=> static::TAG_GA4_BEGIN_CHECKOUT,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_BEGIN_CHECKOUT]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_BEGIN_CHECKOUT
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_VIEW_PROMOTION =>
            [
                'name' 				=> static::TAG_GA4_VIEW_PROMOTION,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_VIEW_PROMOTION]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_VIEW_PROMOTION
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_SELECT_PROMOTION =>
            [
                'name' 				=> static::TAG_GA4_SELECT_PROMOTION,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_SELECT_PROMOTION]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_SELECT_PROMOTION
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_VIEW_CART =>
            [
                'name' 				=> static::TAG_GA4_VIEW_CART,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_VIEW_CART]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_VIEW_CART
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_ADD_TO_WISHLIST =>
            [
                'name' 				=> static::TAG_GA4_ADD_TO_WISHLIST,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_ADD_TO_WISHLIST]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_ADD_TO_WISHLIST
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_ADD_TO_COMPARE =>
            [
                'name' 				=> static::TAG_GA4_ADD_TO_COMPARE,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_ADD_TO_COMPARE]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_ADD_TO_COMPARE
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_ADD_SHIPPING_INFO =>
            [
                'name' 				=> static::TAG_GA4_ADD_SHIPPING_INFO,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_ADD_SHIPPING_INFO]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_SHIPPING_INFO
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'shipping_tier'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce shipping tier}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_GA4_ADD_PAYMENT_INFO =>
            [
                'name' 				=> static::TAG_GA4_ADD_PAYMENT_INFO,
                'firingTriggerId' 	=>
                [
                    $triggers[static::TRIGGER_GA4_ADD_PAYMENT_INFO]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_GA4_EVENT,
                'parameter' 		=>
                [
                    [
                        'type' 	=> 'tagReference',
                        'key' 	=> 'measurementId',
                        'value' => static::TAG_GA4_CONFIGURATION
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'eventName',
                        'value' => static::GA4_EVENT_PAYMENT_INFO
                    ],
                    [
                        'type' 	=> 'list',
                        'key' 	=> 'eventParameters',
                        'list' 	=>
                        [
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce items}}'
                                    ]
                                ]
                            ],
                            [
                                'type' 	=> 'map',
                                'map' 	=>
                                [
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'name',
                                        'value' => 'payment_type'
                                    ],
                                    [
                                        'type' 	=> 'template',
                                        'key' 	=> 'value',
                                        'value' => '{{ee4 ecommerce payment type}}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            static::TAG_ADWORDS_DYNAMIC_REMARKETING => 
            [
                'name' 				=> static::TAG_ADWORDS_DYNAMIC_REMARKETING,
                'firingTriggerId' 	=> 
                [
                    $triggers[static::TRIGGER_REMARKETING_TAG]
                ],
                'type' 				=> static::TAG_MANAGER_TAG_TYPE_ADWORDS_DYNAMIC_REMARKETING,
                'parameter' 		=> 
                [
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'googleScreenName',
                        'value' => self::TAG_ADWORDS_DYNAMIC_REMARKETING
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'conversionId',
                        'value' => '{{ee conversion id}}'
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'conversionLabel',
                        'value' => '{{ee conversion label}}'
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'customParamsFormat',
                        'value' => 'DATA_LAYER'
                    ],
                    [
                        'type' 	=> 'template',
                        'key' 	=> 'dataLayerVariable',
                        'value' => '{{google_tag_params}}'
                    ]
                ]
            ]
        ];
        
        return $this->generate_tags($schema, $account, $container);
    }
    
    /**
     * Get Google Analytics 4 Measurement Id
     *
     * @return string
     */
    protected function getMeasurementId() : string
    {
        return (string) $this->helper->getConfig('ec/api/google_gtm_ua4_measurement_id');
    }
}