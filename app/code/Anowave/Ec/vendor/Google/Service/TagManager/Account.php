<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Model as Google_Model;

class Account extends Google_Model
{
	protected $internal_gapi_mappings = array();
	
	public $accountId;
	public $fingerprint;
	public $name;
	public $shareData;
	
	
	public function setAccountId($accountId)
	{
		$this->accountId = $accountId;
	}
	public function getAccountId()
	{
		return $this->accountId;
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
	public function setShareData($shareData)
	{
		$this->shareData = $shareData;
	}
	public function getShareData()
	{
		return $this->shareData;
	}
}