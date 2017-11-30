<?php

namespace Grapesc\GrapeFluid\AdminModule\Presenters;

use Grapesc\GrapeFluid\AdminModule\ComponentListControl\IComponentListControlFactory;
use Grapesc\GrapeFluid\ContentModule\Model\PageModel;
use Grapesc\GrapeFluid\ContentModule\PageForm;
use Grapesc\GrapeFluid\FluidFormControl\FluidFormControl;
use Grapesc\GrapeFluid\ContentModule\Grid\PageGrid;


class ContentPresenter extends BasePresenter
{

	/** @var PageForm @inject */
	public $pageForm;

	/** @var PageModel @inject */
	public $pageModel;

	/** @var IComponentListControlFactory @inject */
	public $componentListControlFactory;


	protected function createComponentPageForm()
	{
		return new FluidFormControl($this->pageForm);
	}


	protected function createComponentPageGrid()
	{
		return new PageGrid($this->pageModel, $this->translator);
	}


	/**
	 * @return \Grapesc\GrapeFluid\AdminModule\ComponentListControl\ComponentListControl
	 */
	protected function createComponentComponentList()
	{
		return $this->componentListControlFactory->create();
	}


	public function actionEdit($id = 0)
	{
		$page = $this->pageModel->getItem($id);

		if ($page) {
			$this->getComponent("pageForm")->setDefaults($page);
		} else {
			$this->flashMessage("Požadovaná stránka neexistuje", "warning");
			$this->redirect(":Admin:Content:default");
		}
	}

}