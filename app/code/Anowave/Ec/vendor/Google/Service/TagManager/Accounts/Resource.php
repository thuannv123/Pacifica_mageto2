<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager\Accounts;

use Anowave\Ec\vendor\Google\Service\Resource as Google_Service_Resource;
use Anowave\Ec\vendor\Google\Service\TagManager\Account as Google_Service_TagManager_Account;

class Resource extends Google_Service_Resource
{
	
	/**
	 * Gets a GTM Account. (accounts.get)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Account
	 */
	public function get($accountId, $optParams = array())
	{
		$params = array('accountId' => $accountId);
		$params = array_merge($params, $optParams);
		return $this->call('get', array($params), "Google_Service_TagManager_Account");
	}
	
	/**
	 * Lists all GTM Accounts that a user has access to. (accounts.listAccounts)
	 *
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ListAccountsResponse
	 */
	public function listAccounts($optParams = array())
	{
		$params = array();
		$params = array_merge($params, $optParams);
		return $this->call('list', array($params), "Google_Service_TagManager_ListAccountsResponse");
	}
	
	/**
	 * Updates a GTM Account. (accounts.update)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param Google_Account $postBody
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param string fingerprint When provided, this fingerprint must match the
	 * fingerprint of the account in storage.
	 * @return Google_Service_TagManager_Account
	 */
	public function update($accountId, Google_Service_TagManager_Account $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('update', array($params), "Google_Service_TagManager_Account");
	}
}