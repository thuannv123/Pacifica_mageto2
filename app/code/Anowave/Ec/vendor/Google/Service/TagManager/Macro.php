<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class Macro extends Google_Collection
{
	protected $collection_key = 'parameter';
	protected $internal_gapi_mappings = array(
	);
	public $accountId;
	public $containerId;
	public $disablingRuleId;
	public $enablingRuleId;
	public $fingerprint;
	public $macroId;
	public $name;
	public $notes;
	protected $parameterType = 'Google_Service_TagManager_Parameter';
	protected $parameterDataType = 'array';
	public $scheduleEndMs;
	public $scheduleStartMs;
	public $type;
	
	
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
	public function setDisablingRuleId($disablingRuleId)
	{
		$this->disablingRuleId = $disablingRuleId;
	}
	public function getDisablingRuleId()
	{
		return $this->disablingRuleId;
	}
	public function setEnablingRuleId($enablingRuleId)
	{
		$this->enablingRuleId = $enablingRuleId;
	}
	public function getEnablingRuleId()
	{
		return $this->enablingRuleId;
	}
	public function setFingerprint($fingerprint)
	{
		$this->fingerprint = $fingerprint;
	}
	public function getFingerprint()
	{
		return $this->fingerprint;
	}
	public function setMacroId($macroId)
	{
		$this->macroId = $macroId;
	}
	public function getMacroId()
	{
		return $this->macroId;
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
}