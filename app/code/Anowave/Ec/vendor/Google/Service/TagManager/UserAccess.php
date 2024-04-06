<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;
use Anowave\Ec\vendor\Google\Service\TagManager\AccountAccess as Google_Service_TagManager_AccountAccess;

class UserAccess extends Google_Collection
{
	protected $collection_key = 'containerAccess';
	protected $internal_gapi_mappings = array(
	);
	protected $accountAccessType = 'Google_Service_TagManager_AccountAccess';
	protected $accountAccessDataType = '';
	public $accountId;
	protected $containerAccessType = 'Google_Service_TagManager_ContainerAccess';
	protected $containerAccessDataType = 'array';
	public $emailAddress;
	public $permissionId;
	
	
	public function setAccountAccess(Google_Service_TagManager_AccountAccess $accountAccess)
	{
		$this->accountAccess = $accountAccess;
	}
	public function getAccountAccess()
	{
		return $this->accountAccess;
	}
	public function setAccountId($accountId)
	{
		$this->accountId = $accountId;
	}
	public function getAccountId()
	{
		return $this->accountId;
	}
	public function setContainerAccess($containerAccess)
	{
		$this->containerAccess = $containerAccess;
	}
	public function getContainerAccess()
	{
		return $this->containerAccess;
	}
	public function setEmailAddress($emailAddress)
	{
		$this->emailAddress = $emailAddress;
	}
	public function getEmailAddress()
	{
		return $this->emailAddress;
	}
	public function setPermissionId($permissionId)
	{
		$this->permissionId = $permissionId;
	}
	public function getPermissionId()
	{
		return $this->permissionId;
	}
}