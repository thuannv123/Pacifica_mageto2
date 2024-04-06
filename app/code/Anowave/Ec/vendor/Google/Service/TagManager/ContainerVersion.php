<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;
use Anowave\Ec\vendor\Google\Service\TagManager\Container as Google_Service_TagManager_Container;


class ContainerVersion extends Google_Collection
{
	protected $collection_key = 'variable';
	protected $internal_gapi_mappings = array();
	public $accountId;
	protected $containerType = 'Anowave\Ec\\vendor\Google\Service\TagManager\Container';
	protected $containerDataType = '';
	public $containerId;
	public $containerVersionId;
	public $deleted;
	public $fingerprint;
	protected $macroType = 'Anowave\Ec\vendor\Google\Service\TagManager\Macro';
	protected $macroDataType = 'array';
	public $name;
	public $notes;
	protected $ruleType = 'Anowave\Ec\vendor\Google\Service\TagManager\Rule';
	protected $ruleDataType = 'array';
	protected $tagType = 'Anowave\Ec\vendor\Google\Service\TagManager\Tag';
	protected $tagDataType = 'array';
	protected $triggerType = 'Anowave\Ec\vendor\Google\Service\TagManager\Trigger';
	protected $triggerDataType = 'array';
	protected $variableType = 'Anowave\Ec\vendor\Google\Service\TagManager\Variable';
	protected $variableDataType = 'array';
	
	
	public function setAccountId($accountId)
	{
		$this->accountId = $accountId;
	}
	public function getAccountId()
	{
		return $this->accountId;
	}
	public function setContainer(Google_Service_TagManager_Container $container)
	{
		$this->container = $container;
	}
	public function getContainer()
	{
		return $this->container;
	}
	public function setContainerId($containerId)
	{
		$this->containerId = $containerId;
	}
	public function getContainerId()
	{
		return $this->containerId;
	}
	public function setContainerVersionId($containerVersionId)
	{
		$this->containerVersionId = $containerVersionId;
	}
	public function getContainerVersionId()
	{
		return $this->containerVersionId;
	}
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted;
	}
	public function getDeleted()
	{
		return $this->deleted;
	}
	public function setFingerprint($fingerprint)
	{
		$this->fingerprint = $fingerprint;
	}
	public function getFingerprint()
	{
		return $this->fingerprint;
	}
	public function setMacro($macro)
	{
		$this->macro = $macro;
	}
	public function getMacro()
	{
		return $this->macro;
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
	public function setRule($rule)
	{
		$this->rule = $rule;
	}
	public function getRule()
	{
		return $this->rule;
	}
	public function setTag($tag)
	{
		$this->tag = $tag;
	}
	public function getTag()
	{
		return $this->tag;
	}
	public function setTrigger($trigger)
	{
		$this->trigger = $trigger;
	}
	public function getTrigger()
	{
		return $this->trigger;
	}
	public function setVariable($variable)
	{
		$this->variable = $variable;
	}
	public function getVariable()
	{
		return $this->variable;
	}
}