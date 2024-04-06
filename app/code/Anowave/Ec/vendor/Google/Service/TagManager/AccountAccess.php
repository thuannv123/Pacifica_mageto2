<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;


class AccountAccess extends Google_Collection
{
	protected $collection_key = 'permission';
	protected $internal_gapi_mappings = array(
	);
	public $permission;
	
	
	public function setPermission($permission)
	{
		$this->permission = $permission;
	}
	public function getPermission()
	{
		return $this->permission;
	}
}