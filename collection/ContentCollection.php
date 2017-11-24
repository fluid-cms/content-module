<?php
namespace Grapesc\GrapeFluid\ContentModule\Collection;

use Grapesc\GrapeFluid\ContentModule\Model\PageModel;
use Grapesc\GrapeFluid\LinkCollector\ILinkCollection;
use Nette\Database\Table\ActiveRow;


/**
 * @author Kulíšek Patrik <kulisek@grapesc.cz>
 */
class ContentCollection implements ILinkCollection
{

	/** @var PageModel @inject */
	public $pages;


	/**
	 * @return array
	 */
	public function getLinks()
	{
		$collection = [];
		/** @var ActiveRow $page */
		foreach ($this->pages->getAllItems() as $page) {
			$collection[$page->title] = [":Content:Content:default", json_encode(["id" => $page->id])];
		}
		return $collection;
	}

}