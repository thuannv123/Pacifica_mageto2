<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager\AccountsContainersVariables;

use Anowave\Ec\vendor\Google\Service\Resource as Google_Service_Resource;
use Anowave\Ec\vendor\Google\Service\TagManager\Variable as Google_Service_TagManager_Variable;

class Resource extends Google_Service_Resource
{
	
	/**
	 * Creates a GTM Variable. (variables.create)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param Google_Variable $postBody
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Variable
	 */
	public function create($accountId, $containerId, Google_Service_TagManager_Variable $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('create', array($params), 'Anowave\Ec\vendor\Google\Service\TagManager\Variable');
	}
	
	/**
	 * Deletes a GTM Variable. (variables.delete)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $variableId The GTM Variable ID.
	 * @param array $optParams Optional parameters.
	 */
	public function delete($accountId, $containerId, $variableId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'variableId' => $variableId);
		$params = array_merge($params, $optParams);
		return $this->call('delete', array($params));
	}
	
	/**
	 * Gets a GTM Variable. (variables.get)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $variableId The GTM Variable ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Variable
	 */
	public function get($accountId, $containerId, $variableId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'variableId' => $variableId);
		$params = array_merge($params, $optParams);
		return $this->call('get', array($params), 'Anowave\Ec\vendor\Google\Service\TagManager\Variable');
	}
	
	/**
	 * Lists all GTM Variables of a Container.
	 * (variables.listAccountsContainersVariables)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ListVariablesResponse
	 */
	public function listAccountsContainersVariables($accountId, $containerId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId);
		$params = array_merge($params, $optParams);
		return $this->call('list', array($params), 'Anowave\Ec\vendor\Google\Service\TagManager\ListVariablesResponse');
	}
	
	/**
	 * Updates a GTM Variable. (variables.update)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $variableId The GTM Variable ID.
	 * @param Google_Variable $postBody
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param string fingerprint When provided, this fingerprint must match the
	 * fingerprint of the variable in storage.
	 * @return Google_Service_TagManager_Variable
	 */
	public function update($accountId, $containerId, $variableId, Google_Service_TagManager_Variable $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'variableId' => $variableId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('update', array($params), 'Anowave\Ec\vendor\Google\Service\TagManager\Variable');
	}
}