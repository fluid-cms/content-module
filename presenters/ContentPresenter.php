<?php

namespace Grapesc\GrapeFluid\ContentModule\Presenters;

use Grapesc\GrapeFluid\ContentModule\Model\PageModel;
use Grapesc\GrapeFluid\CoreModule\Service\BreadCrumbService;
use Grapesc\GrapeFluid\MagicControl\Helper;
use Nette\Database\Table\ActiveRow;
use Nette\Http\IResponse;
use Nette\Utils\Strings;


class ContentPresenter extends BasePresenter
{

	/** @var PageModel @inject */
	public $pageModel;

	/** @var BreadCrumbService @inject */
	public $breadCrumbService;

	/** @var $page ActiveRow */
	private $page;


	public function actionDefault($id = null, $title = null) {
		$page = $this->pageModel->getItem($id);

		if (!$page) {
			$page = $this->pageModel->getItemBy([$id, "$id-$title"], 'slug');

			if (!$page) {
				$this->error("Stránka nenalezena");
			}
		}

		if (!$this->user->isAllowed('content.page.item', 'read.' . $page->id)) {
			$this->error("Nemáte oprávnění pro zobrazení požadované stránky", IResponse::S403_FORBIDDEN);
		}

		$this->page = $page;
	}


	public function renderDefault($id = null, $title = null)
	{
		$this->setMeta([
			"description" => $this->page->description,
			"keywords"    => $this->page->keywords
		]);

//            TODO: Pokud 'content' obsahuje obsah dalších komponent / boxů, je třeba ho odříznout a nahradit zpět magic makrem
//            $this->template->inlineEnabled = ($this->setting->getVal("content.edit.inline") && !Helper::containsMagicMacros($page->content));

		foreach ($this->getParents($this->page->parent_id, []) as $parent) {
			if ($parent->slug) {
				$this->breadCrumbService->addLink($parent->title, [':Content:Content:default', ['id' => $parent->slug]]);
			} else {
				$this->breadCrumbService->addLink($parent->title, [':Content:Content:default', ['id' => $parent->id, 'title' => Strings::webalize($parent->title)]]);
			}
		}

		$this->breadCrumbService->addLink($this->page->title);

		$this->template->inlineEnabled = $this->setting->getVal("content.edit.inline");
		$this->template->page          = $this->page;
		$this->template->content       = Helper::magicMacroCreator($this->page->content, $this);
	}


	public function handleInlineEdit($content)
	{
		if ($this->getUser()->isAllowed('backend:content') && $this->isAjax()) {
			/** @var ActiveRow $page */
			$page = $this->pageModel->getItem($this->getParameter('inlineEditId'));
			$page->update(["content" => Helper::createSafeEscapeString(Helper::magicMacroRecreator($content))]);
			$this->flashMessage("Uloženo", "success");
			$this->redrawControl("flashMessages");
		}
	}


	public function handleUploadImage()
	{
		if ($this->getUser()->isAllowed('backend:content')) {
			if ($process = $this->imageStorage->processImageFromRequest()) {
				$this->payload->path = $process;
			}
			$this->flashMessage($this->imageStorage->getLastState());
			$this->redrawControl("flashMessages");
		}
	}


	/**
	 * @param $parentId
	 * @param $parents
	 */
	private function getParents($parentId, $parents)
	{
		if (!$parentId) {
			return $parents;
		}

		$parent = $this->pageModel->getItem($parentId);
		if (array_key_exists($parent->id, $parents)) {
			return $parents;
		}

		$parents[$parent->id] = $parent;

		if($parent->parent_id) {
			$parents = $this->getParents($parent->parent_id, $parents);
		}

		return $parents;
	}

}