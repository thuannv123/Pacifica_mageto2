<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;
use Anowave\Ec\vendor\Google\Service\TagManager\UserAccess as Google_Service_TagManager_UserAccess;

class ListAccountUsersResponse extends Google_Collection
{
	protected $collection_key = 'userAccess';
	protected $internal_gapi_mappings = array(
	);
	protected $userAccessType = 'Google_Service_TagManager_UserAccess';
	protected $userAccessDataType = 'array';
	
	
	public function setUserAccess($userAccess)
	{
		$this->userAccess = $userAccess;
	}
	public function getUserAccess()
	{
		return $this->userAccess;
	}
}
