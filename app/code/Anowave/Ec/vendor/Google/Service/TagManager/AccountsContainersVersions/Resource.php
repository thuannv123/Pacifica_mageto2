<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager\AccountsContainersVersions;

use Anowave\Ec\vendor\Google\Service\Resource as Google_Service_Resource;
use Anowave\Ec\vendor\Google\Service\TagManager\ContainerVersion as Google_Service_TagManager_ContainerVersion;

class Resource extends Google_Service_Resource
{
	
	/**
	 * Creates a Container Version. (versions.create)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param Google_CreateContainerVersionRequestVersionOptions $postBody
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_CreateContainerVersionResponse
	 */
	public function create($accountId, $containerId, Google_Service_TagManager_CreateContainerVersionRequestVersionOptions $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('create', array($params), "Google_Service_TagManager_CreateContainerVersionResponse");
	}
	
	/**
	 * Deletes a Container Version. (versions.delete)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $containerVersionId The GTM Container Version ID.
	 * @param array $optParams Optional parameters.
	 */
	public function delete($accountId, $containerId, $containerVersionId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'containerVersionId' => $containerVersionId);
		$params = array_merge($params, $optParams);
		return $this->call('delete', array($params));
	}
	
	/**
	 * Gets a Container Version. (versions.get)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $containerVersionId The GTM Container Version ID. Specify
	 * published to retrieve the currently published version.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ContainerVersion
	 */
	public function get($accountId, $containerId, $containerVersionId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'containerVersionId' => $containerVersionId);
		$params = array_merge($params, $optParams);
		return $this->call('get', array($params), "Google_Service_TagManager_ContainerVersion");
	}
	
	/**
	 * Lists all Container Versions of a GTM Container.
	 * (versions.listAccountsContainersVersions)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param bool headers Retrieve headers only when true.
	 * @return Google_Service_TagManager_ListContainerVersionsResponse
	 */
	public function listAccountsContainersVersions($accountId, $containerId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId);
		$params = array_merge($params, $optParams);
		return $this->call('list', array($params), "Google_Service_TagManager_ListContainerVersionsResponse");
	}
	
	/**
	 * Publishes a Container Version. (versions.publish)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $containerVersionId The GTM Container Version ID.
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param string fingerprint When provided, this fingerprint must match the
	 * fingerprint of the container version in storage.
	 * @return Google_Service_TagManager_PublishContainerVersionResponse
	 */
	public function publish($accountId, $containerId, $containerVersionId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'containerVersionId' => $containerVersionId);
		$params = array_merge($params, $optParams);
		return $this->call('publish', array($params), "Google_Service_TagManager_PublishContainerVersionResponse");
	}
	
	/**
	 * Restores a Container Version. This will overwrite the container's current
	 * configuration (including its macros, rules and tags). The operation will not
	 * have any effect on the version that is being served (i.e. the published
	 * version). (versions.restore)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $containerVersionId The GTM Container Version ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ContainerVersion
	 */
	public function restore($accountId, $containerId, $containerVersionId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'containerVersionId' => $containerVersionId);
		$params = array_merge($params, $optParams);
		return $this->call('restore', array($params), "Google_Service_TagManager_ContainerVersion");
	}
	
	/**
	 * Undeletes a Container Version. (versions.undelete)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $containerVersionId The GTM Container Version ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ContainerVersion
	 */
	public function undelete($accountId, $containerId, $containerVersionId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'containerVersionId' => $containerVersionId);
		$params = array_merge($params, $optParams);
		return $this->call('undelete', array($params), "Google_Service_TagManager_ContainerVersion");
	}
	
	/**
	 * Updates a Container Version. (versions.update)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $containerVersionId The GTM Container Version ID.
	 * @param Google_ContainerVersion $postBody
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param string fingerprint When provided, this fingerprint must match the
	 * fingerprint of the container version in storage.
	 * @return Google_Service_TagManager_ContainerVersion
	 */
	public function update($accountId, $containerId, $containerVersionId, Google_Service_TagManager_ContainerVersion $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'containerVersionId' => $containerVersionId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('update', array($params), "Google_Service_TagManager_ContainerVersion");
	}
}