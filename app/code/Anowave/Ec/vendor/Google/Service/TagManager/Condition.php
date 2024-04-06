<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;
use Anowave\Ec\vendor\Google\Service\TagManager\Parameter as Google_Service_TagManager_Parameter;

class Condition extends Google_Collection
{
	protected $collection_key = 'parameter';
	protected $internal_gapi_mappings = array(
	);
	protected $parameterType = 'Google_Service_TagManager_Parameter';
	protected $parameterDataType = 'array';
	public $type;
	
	
	public function setParameter($parameter)
	{
		$this->parameter = $parameter;
	}
	public function getParameter()
	{
		return $this->parameter;
	}
	public function setType($type)
	{
		$this->type = $type;
	}
	public function getType()
	{
		return $this->type;
	}
}
