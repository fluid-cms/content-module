<?php

namespace Grapesc\GrapeFluid\ContentModule\Grid;

use Grapesc\GrapeFluid\FluidGrid;
use Nette\Database\Table\ActiveRow;


class PageGrid extends FluidGrid
{

	protected function build()
	{
		$this->setItemsPerPage(15);
		$this->skipColumns(["content"]);
		$this->enableFilters(['title', 'slug']);

		$this->addRowAction("edit", "Upravit", [$this, 'editPage']);
		$this->addRowAction("inline", "Upravit na stránce", [$this, 'inlineEditPage']);
		$this->addRowAction("duplicate", "Duplikovat", [$this, 'duplicatePage']);
		$this->addRowAction("delete", "Smazat", [$this, 'deletePage']);
		parent::build();
	}


	public function editPage(ActiveRow $record)
	{
		$this->getPresenter()->redirect(":Admin:Content:edit", ["id" => $record->id]);
	}


	public function inlineEditPage(ActiveRow $record)
	{
		$this->getPresenter()->redirect(":Content:Content:default", ["id" => $record->id]);
	}


	public function duplicatePage(ActiveRow $record)
	{
		$data = $record->toArray();
		unset($data['id']);
		$data['title'] .= " (kopie)";
		$this->model->insert($data);
		$this->getPresenter()->flashMessage("Stránka duplikována", "success");
		$this->getPresenter()->redrawControl("flashMessages");
	}


	public function deletePage(ActiveRow $record)
	{
		$record->delete();
		$this->getPresenter()->flashMessage("Stránka smazána", "success");
		$this->getPresenter()->redrawControl("flashMessages");
	}

}