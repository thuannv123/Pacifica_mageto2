<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class ListMacrosResponse extends Google_Collection
{
	protected $collection_key = 'macros';
	protected $internal_gapi_mappings = array(
	);
	protected $macrosType = 'Google_Service_TagManager_Macro';
	protected $macrosDataType = 'array';
	
	
	public function setMacros($macros)
	{
		$this->macros = $macros;
	}
	public function getMacros()
	{
		return $this->macros;
	}
}
