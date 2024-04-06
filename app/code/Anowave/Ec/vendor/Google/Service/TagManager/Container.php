<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class Container extends Google_Collection
{
	protected $collection_key = 'usageContext';
	protected $internal_gapi_mappings = array(
	);
	public $accountId;
	public $containerId;
	public $domainName;
	public $enabledBuiltInVariable;
	public $fingerprint;
	public $name;
	public $notes;
	public $publicId;
	public $timeZoneCountryId;
	public $timeZoneId;
	public $usageContext;
	
	
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
	public function setDomainName($domainName)
	{
		$this->domainName = $domainName;
	}
	public function getDomainName()
	{
		return $this->domainName;
	}
	public function setEnabledBuiltInVariable($enabledBuiltInVariable)
	{
		$this->enabledBuiltInVariable = $enabledBuiltInVariable;
	}
	public function getEnabledBuiltInVariable()
	{
		return $this->enabledBuiltInVariable;
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
	public function setPublicId($publicId)
	{
		$this->publicId = $publicId;
	}
	public function getPublicId()
	{
		return $this->publicId;
	}
	public function setTimeZoneCountryId($timeZoneCountryId)
	{
		$this->timeZoneCountryId = $timeZoneCountryId;
	}
	public function getTimeZoneCountryId()
	{
		return $this->timeZoneCountryId;
	}
	public function setTimeZoneId($timeZoneId)
	{
		$this->timeZoneId = $timeZoneId;
	}
	public function getTimeZoneId()
	{
		return $this->timeZoneId;
	}
	public function setUsageContext($usageContext)
	{
		$this->usageContext = $usageContext;
	}
	public function getUsageContext()
	{
		return $this->usageContext;
	}
}