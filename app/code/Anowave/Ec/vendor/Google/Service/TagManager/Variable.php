<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class Variable extends Google_Collection
{
	protected $collection_key = 'parameter';
	protected $internal_gapi_mappings = array();
	public $accountId;
	public $containerId;
	public $disablingTriggerId;
	public $enablingTriggerId;
	public $fingerprint;
	public $name;
	public $notes;
	protected $parameterType = 'Google_Service_TagManager_Parameter';
	protected $parameterDataType = 'array';
	public $scheduleEndMs;
	public $scheduleStartMs;
	public $type;
	public $variableId;
	
	
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
	public function setDisablingTriggerId($disablingTriggerId)
	{
		$this->disablingTriggerId = $disablingTriggerId;
	}
	public function getDisablingTriggerId()
	{
		return $this->disablingTriggerId;
	}
	public function setEnablingTriggerId($enablingTriggerId)
	{
		$this->enablingTriggerId = $enablingTriggerId;
	}
	public function getEnablingTriggerId()
	{
		return $this->enablingTriggerId;
	}
	public function setFingerprint($fingerprint)
	{
		$this->fingerprint = $fingerprint;
	}
	public function getFingerprint()
	{
		return $this->fingerprint;
	}
	public function setName($name)
	{
		$this->name = $name;
	}
	public function getName()
	{
		return $this->name;
	}
	public function setNotes($notes)
	{
		$this->notes = $notes;
	}
	public function getNotes()
	{
		return $this->notes;
	}
	public function setParameter($parameter)
	{
		$this->parameter = $parameter;
	}
	public function getParameter()
	{
		return $this->parameter;
	}
	public function setScheduleEndMs($scheduleEndMs)
	{
		$this->scheduleEndMs = $scheduleEndMs;
	}
	public function getScheduleEndMs()
	{
		return $this->scheduleEndMs;
	}
	public function setScheduleStartMs($scheduleStartMs)
	{
		$this->scheduleStartMs = $scheduleStartMs;
	}
	public function getScheduleStartMs()
	{
		return $this->scheduleStartMs;
	}
	public function setType($type)
	{
		$this->type = $type;
	}
	public function getType()
	{
		return $this->type;
	}
	public function setVariableId($variableId)
	{
		$this->variableId = $variableId;
	}
	public function getVariableId()
	{
		return $this->variableId;
	}
}