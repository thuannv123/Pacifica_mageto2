<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class Rule extends Google_Collection
{
	protected $collection_key = 'condition';
	protected $internal_gapi_mappings = array();
	public $accountId;
	protected $conditionType = 'Google_Service_TagManager_Condition';
	protected $conditionDataType = 'array';
	public $containerId;
	public $fingerprint;
	public $name;
	public $notes;
	public $ruleId;
	
	
	public function setAccountId($accountId)
	{
		$this->accountId = $accountId;
	}
	public function getAccountId()
	{
		return $this->accountId;
	}
	public function setCondition($condition)
	{
		$this->condition = $condition;
	}
	public function getCondition()
	{
		return $this->condition;
	}
	public function setContainerId($containerId)
	{
		$this->containerId = $containerId;
	}
	public function getContainerId()
	{
		return $this->containerId;
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
	public function setRuleId($ruleId)
	{
		$this->ruleId = $ruleId;
	}
	public function getRuleId()
	{
		return $this->ruleId;
	}
}