<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class ListVariablesResponse extends Google_Collection
{
	protected $collection_key = 'variables';
	protected $internal_gapi_mappings = array(
	);
	protected $variablesType = 'Anowave\Ec\vendor\Google\Service\TagManager\Variable';
	protected $variablesDataType = 'array';
	
	
	public function setVariables($variables)
	{
		$this->variables = $variables;
	}
	public function getVariables()
	{
		return $this->variables;
	}
}