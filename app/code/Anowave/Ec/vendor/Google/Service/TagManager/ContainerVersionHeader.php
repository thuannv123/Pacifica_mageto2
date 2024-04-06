<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Model as Google_Model;


class ContainerVersionHeader extends Google_Model
{
	protected $internal_gapi_mappings = array();
	public $accountId;
	public $containerId;
	public $containerVersionId;
	public $deleted;
	public $name;
	public $numMacros;
	public $numRules;
	public $numTags;
	public $numTriggers;
	public $numVariables;
	
	public function setAccountId($accountId)
	{
		$this->accountId = $accountId;
	}
	public function getAccountId()
	{
		return $this->accountId;
	}
	public function setContainerId($containerId)
	{
		$this->containerId = $containerId;
	}
	public function getContainerId()
	{
		return $this->containerId;
	}
	public function setContainerVersionId($containerVersionId)
	{
		$this->containerVersionId = $containerVersionId;
	}
	public function getContainerVersionId()
	{
		return $this->containerVersionId;
	}
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted;
	}
	public function getDeleted()
	{
		return $this->deleted;
	}
	public function setName($name)
	{
		$this->name = $name;
	}
	public function getName()
	{
		return $this->name;
	}
	public function setNumMacros($numMacros)
	{
		$this->numMacros = $numMacros;
	}
	public function getNumMacros()
	{
		return $this->numMacros;
	}
	public function setNumRules($numRules)
	{
		$this->numRules = $numRules;
	}
	public function getNumRules()
	{
		return $this->numRules;
	}
	public function setNumTags($numTags)
	{
		$this->numTags = $numTags;
	}
	public function getNumTags()
	{
		return $this->numTags;
	}
	public function setNumTriggers($numTriggers)
	{
		$this->numTriggers = $numTriggers;
	}
	public function getNumTriggers()
	{
		return $this->numTriggers;
	}
	public function setNumVariables($numVariables)
	{
		$this->numVariables = $numVariables;
	}
	public function getNumVariables()
	{
		return $this->numVariables;
	}
}