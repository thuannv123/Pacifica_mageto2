<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class ListContainerVersionsResponse extends Google_Collection
{
	protected $collection_key = 'containerVersionHeader';
	protected $internal_gapi_mappings = array(
	);
	protected $containerVersionType = 'Google_Service_TagManager_ContainerVersion';
	protected $containerVersionDataType = 'array';
	protected $containerVersionHeaderType = 'Google_Service_TagManager_ContainerVersionHeader';
	protected $containerVersionHeaderDataType = 'array';
	
	
	public function setContainerVersion($containerVersion)
	{
		$this->containerVersion = $containerVersion;
	}
	public function getContainerVersion()
	{
		return $this->containerVersion;
	}
	public function setContainerVersionHeader($containerVersionHeader)
	{
		$this->containerVersionHeader = $containerVersionHeader;
	}
	public function getContainerVersionHeader()
	{
		return $this->containerVersionHeader;
	}
}