<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class Parameter extends Google_Collection
{
	protected $collection_key = 'map';
	protected $internal_gapi_mappings = array();
	public $key;
	protected $listType = 'Google_Service_TagManager_Parameter';
	protected $listDataType = 'array';
	protected $mapType = 'Google_Service_TagManager_Parameter';
	protected $mapDataType = 'array';
	public $type;
	public $value;
	
	
	public function setKey($key)
	{
		$this->key = $key;
	}
	public function getKey()
	{
		return $this->key;
	}
	public function setList($list)
	{
		$this->list = $list;
	}
	public function getList()
	{
		return $this->list;
	}
	public function setMap($map)
	{
		$this->map = $map;
	}
	public function getMap()
	{
		return $this->map;
	}
	public function setType($type)
	{
		$this->type = $type;
	}
	public function getType()
	{
		return $this->type;
	}
	public function setValue($value)
	{
		$this->value = $value;
	}
	public function getValue()
	{
		return $this->value;
	}
}