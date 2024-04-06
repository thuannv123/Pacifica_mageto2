<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;
use Anowave\Ec\vendor\Google\Service\TagManager\Account as Google_Service_TagManager_Account;

class ListAccountsResponse extends Google_Collection
{
	protected $collection_key = 'accounts';
	protected $internal_gapi_mappings = array(
	);
	protected $accountsType = 'Google_Service_TagManager_Account';
	protected $accountsDataType = 'array';
	
	
	public function setAccounts($accounts)
	{
		$this->accounts = $accounts;
	}
	public function getAccounts()
	{
		return $this->accounts;
	}
}