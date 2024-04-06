<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Model as Google_Model;
use Anowave\Ec\vendor\Google\Service\TagManager\ContainerVersion as Google_Service_TagManager_ContainerVersion;


class CreateContainerVersionResponse extends Google_Model
{
	protected $internal_gapi_mappings = array();
	public $compilerError;
	protected $containerVersionType = 'Google_Service_TagManager_ContainerVersion';
	protected $containerVersionDataType = '';
	
	
	public function setCompilerError($compilerError)
	{
		$this->compilerError = $compilerError;
	}
	public function getCompilerError()
	{
		return $this->compilerError;
	}
	public function setContainerVersion(Google_Service_TagManager_ContainerVersion $containerVersion)
	{
		$this->containerVersion = $containerVersion;
	}
	public function getContainerVersion()
	{
		return $this->containerVersion;
	}
}