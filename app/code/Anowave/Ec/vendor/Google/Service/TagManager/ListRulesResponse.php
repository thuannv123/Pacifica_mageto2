<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class ListRulesResponse extends Google_Collection
{
	protected $collection_key = 'rules';
	protected $internal_gapi_mappings = array(
	);
	protected $rulesType = 'Google_Service_TagManager_Rule';
	protected $rulesDataType = 'array';
	
	
	public function setRules($rules)
	{
		$this->rules = $rules;
	}
	public function getRules()
	{
		return $this->rules;
	}
}
