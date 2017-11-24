<?php
namespace Grapesc\GrapeFluid\ContentModule\RouteFilter;

use Grapesc\GrapeFluid\ContentModule\Model\PageModel;
use Nette\Utils\Strings;


/**
 * @author Jiri Novy <novy@grapesc.cz>
 */
class Content
{

	/** @var PageModel */
	private $pageModel;


	public function __construct(PageModel $pageModel)
	{
		$this->pageModel = $pageModel;
	}


	/**
	 * @param int $arg
	 * @return string
	 */
	public function filterOut($arg)
	{
		$page = $this->pageModel->getItem($arg);
		if (!$page) {
			return $arg;
		}

		return $page->slug ? $page->slug : ($arg . '-' . Strings::webalize($page->title));
	}


	/**
	 * @param string $arg
	 * @return int
	 */
	public function filterIn($arg)
	{
		$var = explode('-', $arg)[0];
		return is_int($var) ? $var : $arg;
	}

}