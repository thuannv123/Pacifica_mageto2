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

namespace Anowave\Ec\Model\Facebook;

use Anowave\Ec\vendor\FacebookAds\Api;
use Anowave\Ec\vendor\FacebookAds\Object\ServerSide\ActionSource;
use Anowave\Ec\vendor\FacebookAds\Object\ServerSide\Event;
use Anowave\Ec\vendor\FacebookAds\Object\ServerSide\EventRequest;
use Anowave\Ec\vendor\FacebookAds\Object\ServerSide\UserData;
use Anowave\Ec\vendor\FacebookAds\Object\ServerSide\CustomData;
use Anowave\Ec\vendor\FacebookAds\Logger\CurlLogger;
use Anowave\Ec\Model\Facebook\Conversions\Endpoint;

class ConversionsApi extends Endpoint
{
    /**
     * Default action source
     * 
     * @var string
     */
    const ACTION_SOURCE = 'website';
    
    /**
     * @var \Anowave\Ec\vendor\FacebookAds\Api
     */
    protected $api;
    
    /**
     * Set cookie 
     * 
     * @var \Anowave\Ec\Model\Cookie\Facebook
     */
    protected $cookie;
    
    /**
     * Check if Facebook Conversions API is enabled
     * 
     * @var string
     */
    private $enable = false;
    /**
     * Pixel ID
     *
     * @var string
     */
    private $pixel_id = null;
    
    /**
     * Test event code 
     * 
     * @var string
     */
    private $test_event_code = null;
    
    /**
     * User data 
     * 
     * @var array
     */
    private $user_data = [];
    
    /**
     * Is cookie directive support enabled
     * @var string
     */
    private $cookie_directive = false;
    
    /**
     * Is cookie consent granted
     * 
     * @var string
     */
    private $cookie_directive_constent_granted = false;
    
    /**
     * @var \Anowave\Ec\Model\Logger
     */
    private $logger;
    
    /**
     * Constructor 
     * 
     * @param unknown $pixel_id
     * @param unknown $access_token
     * @param \Anowave\Ec\Model\Cookie\Facebook $cookie
     */
    public function __construct($pixel_id, $access_token, $test_event_code, \Anowave\Ec\Model\Cookie\Facebook $cookie, array $user_data = [], $cookie_directive = false, $cookie_directive_constent_granted = false, \Anowave\Ec\Model\Logger $logger = null)
    {
        /**
         * Set pixel id 
         * 
         * @var string $pixel_id
         */
        $this->pixel_id = $pixel_id;
        
        /**
         * Instantiate API 
         * 
         * @var \Anowave\Ec\vendor\FacebookAds\Api
         */
        
        if ($this->pixel_id && $access_token)
        {
            /**
             * Instantiate API 
             * 
             * @var \Anowave\Ec\Model\Facebook\ConversionsApi $api
             */
            $this->api = Api::init(null, null, $access_token);
            
            /**
             * Set logger
             */
            $this->api->setLogger(new CurlLogger());
        }
        else 
        {
            $this->enable = false;
        }
        
        /**
         * Set test event code
         * 
         * @var \Anowave\Ec\Model\Facebook\ConversionsApi $test_event_code
         */
        if ($test_event_code)
        {
            $this->test_event_code = $test_event_code;   
        }
        
        /**
         * Set cookie 
         * 
         * @var \Anowave\Ec\Model\Cookie\Facebook $cookie
         */
        $this->cookie = $cookie;
        
        /**
         * Set user data 
         * 
         * @var [] $user_data
         */
        $this->user_data = $user_data;
        
        /**
         * Set cookie directive enabled flag 
         * 
         * @var \Anowave\Ec\Model\Facebook\ConversionsApi $cookie_directive
         */
        $this->cookie_directive = $cookie_directive;
        
        /**
         * Set granted flag 
         * 
         * @var \Anowave\Ec\Model\Facebook\ConversionsApi $cookie_directive_constent_granted
         */
        $this->cookie_directive_constent_granted = $cookie_directive_constent_granted;
        
        /**
         * Set logger
         * 
         * @var \Anowave\Ec\Model\Facebook\ConversionsApi $logger
         */
        $this->logger = $logger;
    }
    
    /**
     * Track view content 
     * 
     * @param string $content_ids
     * @param string $currency
     * @param float $value
     * @param string $category
     * @param string $content_name
     */
    public function trackViewContent($content_ids, $currency, float $value, $category, $content_name)
    {
        $custom_data = (new CustomData())
                            ->setContentType('product')
                            ->setContentIds($content_ids)
                            ->setNumItems(1)
                            ->setCurrency($currency)
                            ->setValue($value)
                            ->setContentCategory($category)
                            ->setContentName($content_name);
        
        $this->track('ViewContent', $custom_data, true);
    }
    
    /**
     * Track initiate checkout 
     * 
     * @param array $content_ids
     * @param string $currency
     * @param float $value
     */
    public function trackInitiateCheckout($content_ids, $currency, float $value = 0)
    {
        $custom_data = (new CustomData())
                            ->setContentType('product')
                            ->setContentIds($content_ids)
                            ->setNumItems(count($content_ids))
                            ->setCurrency($currency)
                            ->setValue($value)
                            ->setContentName('checkout');
                
        $this->track('InitiateCheckout', $custom_data, true);
    }
    
    /**
     * Track purchase
     * 
     * @param array $content_ids
     * @param string $currency
     * @param float $value
     */
    public function trackPurchase(\Magento\Sales\Model\Order $order, array $content_ids = [], $currency = 'EUR', float $value = 0)
    {
        $this->user_data = [];
        
        /**
         * Handle non-logged purchase
         */
        if (!$this->user_data)
        {
            try 
            {
                $address = $order->getShippingAddress();
                
                if ($address)
                {
                    if ($address->getEmail())
                    {
                        $this->user_data['email'] = strtolower(trim($address->getEmail()));
                    }
                    
                    if ($address->getFirstname())
                    {
                        $this->user_data['first_name'] = strtolower(trim($address->getFirstname()));
                    }
                    
                    if ($address->getLastname())
                    {
                        $this->user_data['last_name'] = strtolower(trim($address->getLastname()));
                    }
                    
                    if ($address->getTelephone())
                    {
                        $this->user_data['phone'] =  preg_replace('/[^0-9]+/i', '', $address->getTelephone());
                    }
                    
                    if ($address->getCity())
                    {
                        $this->user_data['city'] = strtolower(preg_replace('/[^a-zA-Z]/i','',$address->getCity()));
                    }
                    
                    if ($address->getPostcode())
                    {
                        $this->user_data['zip_code'] = $address->getPostcode();
                    }
                    
                    if ($address->getCountryId())
                    {
                        $this->user_data['country_code'] = strtolower($address->getCountryId());
                    }
                    
                    if ($address->getRegionCode())
                    {
                        $region = preg_replace('/[^a-z]/i','', $address->getRegionCode());
                        
                        if ($region)
                        {
                            $this->user_data['state'] = $region;
                        }
                    }
        
                    $this->user_data = array_map(function($data)
                    {
                        return hash('sha256', $data);
                        
                    }, $this->user_data);
                }
            }
            catch (\Exception $e)
            {
                
            }
        }
        
        $custom_data = (new CustomData())
                            ->setContentType('product')
                            ->setContentIds($content_ids)
                            ->setNumItems(count($content_ids))
                            ->setCurrency($currency)
                            ->setValue($value);
        
        $this->track('Purchase', $custom_data, true);
    }
    
    /**
     * Track add to cart event 
     * 
     * @param string $content_name
     * @param array $content_ids
     * @param string $currency
     * @param float $value
     */
    public function trackAddToCart($content_name, array $content_ids = [], $currency = 'EUR', float $value = 0)
    {
        $custom_data = (new CustomData())
                            ->setContentName($content_name)
                            ->setContentType('product')
                            ->setContentIds($content_ids)
                            ->setCurrency($currency)
                            ->setValue($value);
        
        $this->track('AddToCart', $custom_data);
    }
    
    /**
     * Track event 
     * 
     * @param string $eventName
     * @param CustomData $custom_data
     * @param string $cookie
     * @return boolean|\Anowave\Ec\vendor\FacebookAds\Object\ServerSide\EventResponse
     */
    public function track($eventName, $custom_data = null, $force = false, $cleanup = false)
    { 
        /**
         * Check if tracking is active
         */
        if (!$this->enable)
        {
            return false;
        }

        if (!$this->api)
        {
            return false;
        }
        
        try 
        {
            $user_data = (new UserData($this->user_data))->setClientIpAddress($_SERVER['REMOTE_ADDR'])->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);
            
            if (isset($_COOKIE['_fbp']))
            {
                $user_data->setFbp($_COOKIE['_fbp']);
            }
            
            if (isset($_COOKIE['_fbc']))
            {
                $user_data->setFbc($_COOKIE['_fbc']);
            }

            $event = (new Event())
                            ->setEventName($eventName)
                            ->setEventTime(time())
                            ->setUserData($user_data)
                            ->setActionSource(ActionSource::WEBSITE);
            
            if(php_sapi_name() !== "cli")
            {
                if (isset($_SERVER['HTTP_REFERER']))
                {
                    $event->setEventSourceUrl($_SERVER['HTTP_REFERER']);
                }
            }
            
            if ($custom_data)
            {
                 $event->setCustomData($custom_data);
            }

            if (!isset($_COOKIE['facebook_latest_uuid']) || $force)
            {
                $uuid = (object)
                [
                    'uuid' => $this->uuid(), 
                    'event' => $eventName
                ];
                
                if ($this->cookie_directive)
                {
                    if ($this->cookie_directive_constent_granted)
                    {
                        /**
                         * Set cookie
                         */
                        $this->cookie->set(json_encode($uuid));
                    }
                }
                else 
                {
                    /**
                     * Set cookie
                     */
                    $this->cookie->set(json_encode($uuid));
                }
                
            }
            else 
            {
                $uuid = json_decode($_COOKIE['facebook_latest_uuid']);
            }
                         
            if ($uuid && $uuid->uuid && $uuid->event === $eventName)
            {
                /**
                 * Set event id
                 */
                $event->setEventId($uuid->uuid);
            }
            
            $request = (new EventRequest($this->pixel_id))->setEvents([$event]);
            
            if ($this->test_event_code)
            {
                /**
                 * Set test mode
                 */
                $request->setTestEventCode($this->test_event_code);
            }
            
            /**
             * Execute response
             * 
             * @var \Anowave\Ec\vendor\FacebookAds\Object\ServerSide\EventResponse $response
             */
            $response = $request->execute();
            
            if ($cleanup)
            {
                $this->cookie->delete();
            }
            
            return $response;
        }
        catch (\Exception $e)
        {
            $this->logger->log("CAPI ERROR: {$e->getMessage()}");
        }
        
        return true;
        
    }
    
    /**
     * Enable 
     * 
     * @return \Anowave\Ec\Model\Facebook\ConversionsApi
     */
    public function enable()
    {
        $this->enable = true;
        
        return $this;
    }
    
    /**
     * Disable
     * 
     * @return \Anowave\Ec\Model\Facebook\ConversionsApi
     */
    public function disable()
    {
        $this->enable = false;
        
        return $this;
    }
    
    /**
     * Check if Facebook Conversions API is enabled 
     * 
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->enable;
    }
}