<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager\AccountsContainersTags;

use Anowave\Ec\vendor\Google\Service\Resource as Google_Service_Resource;
use Anowave\Ec\vendor\Google\Service\TagManager\Tag as Google_Service_TagManager_Tag;

class Resource extends Google_Service_Resource
{
	
	/**
	 * Creates a GTM Tag. (tags.create)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param Google_Tag $postBody
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Tag
	 */
	public function create($accountId, $containerId, Google_Service_TagManager_Tag $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('create', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\Tag");
	}
	
	/**
	 * Deletes a GTM Tag. (tags.delete)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $tagId The GTM Tag ID.
	 * @param array $optParams Optional parameters.
	 */
	public function delete($accountId, $containerId, $tagId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'tagId' => $tagId);
		$params = array_merge($params, $optParams);
		return $this->call('delete', array($params));
	}
	
	/**
	 * Gets a GTM Tag. (tags.get)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $tagId The GTM Tag ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_Tag
	 */
	public function get($accountId, $containerId, $tagId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'tagId' => $tagId);
		$params = array_merge($params, $optParams);
		return $this->call('get', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\Tag");
	}
	
	/**
	 * Lists all GTM Tags of a Container. (tags.listAccountsContainersTags)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param array $optParams Optional parameters.
	 * @return Google_Service_TagManager_ListTagsResponse
	 */
	public function listAccountsContainersTags($accountId, $containerId, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId);
		$params = array_merge($params, $optParams);
		return $this->call('list', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\ListTagsResponse");
	}
	
	/**
	 * Updates a GTM Tag. (tags.update)
	 *
	 * @param string $accountId The GTM Account ID.
	 * @param string $containerId The GTM Container ID.
	 * @param string $tagId The GTM Tag ID.
	 * @param Google_Tag $postBody
	 * @param array $optParams Optional parameters.
	 *
	 * @opt_param string fingerprint When provided, this fingerprint must match the
	 * fingerprint of the tag in storage.
	 * @return Google_Service_TagManager_Tag
	 */
	public function update($accountId, $containerId, $tagId, Google_Service_TagManager_Tag $postBody, $optParams = array())
	{
		$params = array('accountId' => $accountId, 'containerId' => $containerId, 'tagId' => $tagId, 'postBody' => $postBody);
		$params = array_merge($params, $optParams);
		return $this->call('update', array($params), "Anowave\Ec\\vendor\Google\Service\TagManager\Tag");
	}
}