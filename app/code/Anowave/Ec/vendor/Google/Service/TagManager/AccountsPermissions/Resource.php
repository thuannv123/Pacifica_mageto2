<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager\AccountsPermissions;

use Anowave\Ec\vendor\Google\Service\Resource as Google_Service_Resource;
use Anowave\Ec\vendor\Google\Service\TagManager\UserAccess as Google_Service_TagManager_UserAccess;

class Resource extends Google_Service_Resource
{
	
	/**
	 * Creates a user's Account & Container Permissions. (permissions.create)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param Google_UserAccess $postBody
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_UserAccess
	 */
	public function create($accountId, Google_Service_TagManager_UserAccess $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('create', array($params), "Google_Service_TagManager_UserAccess");
	}
	
	/**
	 * Removes a user from the account, revoking access to it and all of its
	 * containers. (permissions.delete)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $permissionId The GTM User ID.
	 * @param array $optParams Optional parameters.
	 */
	public function delete($accountId, $permissionId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'permissionId' => $permissionId);
		$params = array_merge($params, $optParams);
		return $this->call('delete', array($params));
	}
	
	/**
	 * Gets a user's Account & Container Permissions. (permissions.get)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $permissionId The GTM User ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_UserAccess
	 */
	public function get($accountId, $permissionId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'permissionId' => $permissionId);
		$params = array_merge($params, $optParams);
		return $this->call('get', array($params), "Google_Service_TagManager_UserAccess");
	}
	
	/**
	 * List all users that have access to the account along with Account and
	 * Container Permissions granted to each of them.
	 * (permissions.listAccountsPermissions)
	 *
	 * @param string $accountId The GTM Account ID. @required
	 * tagmanager.accounts.permissions.list
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ListAccountUsersResponse
	 */
	public function listAccountsPermissions($accountId, $optParams = array())
	{
		$params = array('accountId' => $accountId);
		$params = array_merge($params, $optParams);
		return $this->call('list', array($params), "Google_Service_TagManager_ListAccountUsersResponse");
	}
	
	/**
	 * Updates a user's Account & Container Permissions. (permissions.update)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $permissionId The GTM User ID.
	 * @param Google_UserAccess $postBody
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_UserAccess
	 */
	public function update($accountId, $permissionId, Google_Service_TagManager_UserAccess $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'permissionId' => $permissionId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('update', array($params), "Google_Service_TagManager_UserAccess");
	}
}