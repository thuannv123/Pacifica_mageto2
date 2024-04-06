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

namespace Anowave\Ec\vendor\Google;

use Anowave\Ec\vendor\Google\Client as Google_Client;

class Service
{
  public $batchPath;
  public $rootUrl;
  public $version;
  public $servicePath;
  public $serviceName;
  public $availableScopes;
  public $resource;
  
  /**
   * @var \Google_Client
   */
  private $client;

  public function __construct(Google_Client $client)
  {
    $this->client = $client;
  }

  /**
   * Return the associated Google_Client class.
   * @return Google_Client
   */
  public function getClient()
  {
    return $this->client;
  }

  /**
   * Create a new HTTP Batch handler for this service
   *
   * @return Google_Http_Batch
   */
  public function createBatch()
  {
    return new Google_Http_Batch
    (
      $this->client,
      false,
      $this->rootUrl,
      $this->batchPath
    );
  }
}