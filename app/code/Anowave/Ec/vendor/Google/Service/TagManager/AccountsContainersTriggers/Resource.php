<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager\AccountsContainersTriggers;

use Anowave\Ec\vendor\Google\Service\Resource as Google_Service_Resource;
use Anowave\Ec\vendor\Google\Service\TagManager\Trigger as Google_Service_TagManager_Trigger;


class Resource extends Google_Service_Resource
{
	
	/**
	 * Creates a GTM Trigger. (triggers.create)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param Google_Trigger $postBody
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Trigger
	 */
	public function create($accountId, $containerId, Google_Service_TagManager_Trigger $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('create', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\Trigger");
	}
	
	/**
	 * Deletes a GTM Trigger. (triggers.delete)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $triggerId The GTM Trigger ID.
	 * @param array $optParams Optional parameters.
	 */
	public function delete($accountId, $containerId, $triggerId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'triggerId' => $triggerId);
		$params = array_merge($params, $optParams);
		return $this->call('delete', array($params));
	}
	
	/**
	 * Gets a GTM Trigger. (triggers.get)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $triggerId The GTM Trigger ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Trigger
	 */
	public function get($accountId, $containerId, $triggerId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'triggerId' => $triggerId);
		$params = array_merge($params, $optParams);
		return $this->call('get', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\Trigger");
	}
	
	/**
	 * Lists all GTM Triggers of a Container.
	 * (triggers.listAccountsContainersTriggers)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ListTriggersResponse
	 */
	public function listAccountsContainersTriggers($accountId, $containerId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId);
		$params = array_merge($params, $optParams);
		return $this->call('list', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\ListTriggersResponse");
	}
	
	/**
	 * Updates a GTM Trigger. (triggers.update)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $triggerId The GTM Trigger ID.
	 * @param Google_Trigger $postBody
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param string fingerprint When provided, this fingerprint must match the
	 * fingerprint of the trigger in storage.
	 * @return Google_Service_TagManager_Trigger
	 */
	public function update($accountId, $containerId, $triggerId, Google_Service_TagManager_Trigger $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'triggerId' => $triggerId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('update', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\Trigger");
	}
}