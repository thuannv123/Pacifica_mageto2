<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Model as Google_Model;

class CreateContainerVersionRequestVersionOptions extends Google_Model
{
	protected $internal_gapi_mappings = array();
	public $name;
	public $notes;
	public $quickPreview;
	
	
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
	public function setQuickPreview($quickPreview)
	{
		$this->quickPreview = $quickPreview;
	}
	public function getQuickPreview()
	{
		return $this->quickPreview;
	}
}