<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager\AccountsContainersRules;

use Anowave\Ec\vendor\Google\Service\Resource as Google_Service_Resource;
use Anowave\Ec\vendor\Google\Service\TagManager\Rule as Google_Service_TagManager_Rule;


class Resource extends Google_Service_Resource
{
	
	/**
	 * Creates a GTM Rule. (rules.create)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param Google_Rule $postBody
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Rule
	 */
	public function create($accountId, $containerId, Google_Service_TagManager_Rule $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('create', array($params), "Google_Service_TagManager_Rule");
	}
	
	/**
	 * Deletes a GTM Rule. (rules.delete)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $ruleId The GTM Rule ID.
	 * @param array $optParams Optional parameters.
	 */
	public function delete($accountId, $containerId, $ruleId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'ruleId' => $ruleId);
		$params = array_merge($params, $optParams);
		return $this->call('delete', array($params));
	}
	
	/**
	 * Gets a GTM Rule. (rules.get)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $ruleId The GTM Rule ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Rule
	 */
	public function get($accountId, $containerId, $ruleId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'ruleId' => $ruleId);
		$params = array_merge($params, $optParams);
		return $this->call('get', array($params), "Google_Service_TagManager_Rule");
	}
	
	/**
	 * Lists all GTM Rules of a Container. (rules.listAccountsContainersRules)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ListRulesResponse
	 */
	public function listAccountsContainersRules($accountId, $containerId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId);
		$params = array_merge($params, $optParams);
		return $this->call('list', array($params), "Google_Service_TagManager_ListRulesResponse");
	}
	
	/**
	 * Updates a GTM Rule. (rules.update)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $ruleId The GTM Rule ID.
	 * @param Google_Rule $postBody
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param string fingerprint When provided, this fingerprint must match the
	 * fingerprint of the rule in storage.
	 * @return Google_Service_TagManager_Rule
	 */
	public function update($accountId, $containerId, $ruleId, Google_Service_TagManager_Rule $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'ruleId' => $ruleId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('update', array($params), "Google_Service_TagManager_Rule");
	}
}