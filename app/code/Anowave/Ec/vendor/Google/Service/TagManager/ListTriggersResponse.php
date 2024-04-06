<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class ListTriggersResponse extends Google_Collection
{
	protected $collection_key = 'triggers';
	protected $internal_gapi_mappings = array(
	);
	protected $triggersType = 'Anowave\Ec\\vendor\Google\Service\TagManager\Trigger';
	protected $triggersDataType = 'array';
	
	
	public function setTriggers($triggers)
	{
		$this->triggers = $triggers;
	}
	public function getTriggers()
	{
		return $this->triggers;
	}
}