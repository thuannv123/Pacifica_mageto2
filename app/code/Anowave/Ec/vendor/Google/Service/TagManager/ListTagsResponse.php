<?php
namespace Anowave\Ec\vendor\Google\Service\TagManager;

use Anowave\Ec\vendor\Google\Collection as Google_Collection;

class ListTagsResponse extends Google_Collection
{
	protected $collection_key = 'tags';
	protected $internal_gapi_mappings = array(
	);
	protected $tagsType = 'Anowave\Ec\\vendor\Google\Service\TagManager\Tag';
	protected $tagsDataType = 'array';
	
	
	public function setTags($tags)
	{
		$this->tags = $tags;
	}
	public function getTags()
	{
		return $this->tags;
	}
}
