<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;
use Anowave\Ec\vendor\Google\Service\TagManager\Container as Google_Service_TagManager_Container;

class ListContainersResponse extends Google_Collection
{
	protected $collection_key = 'containers';
	protected $internal_gapi_mappings = array();
	protected $containersType = 'Anowave\Ec\\vendor\Google\Service\TagManager\Container';
	protected $containersDataType = 'array';
	
	
	public function setContainers($containers)
	{
		$this->containers = $containers;
	}
	
	public function getContainers()
	{
		return $this->containers;
	}
}
