<?php

namespace Grapesc\GrapeFluid\ContentModule;

use Grapesc\GrapeFluid\ContentModule\Model\PageModel;
use Grapesc\GrapeFluid\CoreModule\AclFormTrait;
use Grapesc\GrapeFluid\FluidFormControl\FluidForm;
use Grapesc\GrapeFluid\MagicControl\Helper;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class PageForm extends FluidForm
{

	use AclFormTrait;

	/** @var PageModel @inject */
	public $model;


	protected function build(Form $form)
	{
		$form->addHidden("id");

		$form->addText("title", "Titulek stránky")
			->setAttribute("cols", 6)
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 200)
			->setRequired("Titulek je povinný");

		$form->addText("slug", "Vlastní slug")
			->setRequired(false)
			->setAttribute("cols", 6)
			->setAttribute("help", "Titulek zobrazený v url - výchozí je <id>-<titulek>")
			->addCondition(Form::FILLED)
				->addRule(Form::PATTERN, "Slug může obsahovat pouze znaky a-z, 0-9, _ a -", "[a-z0-9_\-]+\w")
				->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 200);

		$form->addText("description", "Popis stránky (SEO description)")
			->setRequired(false)
			->setAttribute("cols", 6)
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 200);

		$form->addText("keywords", "Klíčová slova (SEO keywords)")
			->setRequired(false)
			->setAttribute("cols", 6)
			->setAttribute("help", "Jednotlivá slova (oddělujte čárkou)")
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 200);

		$form->addSelect('parent_id', 'Předek stránky', $this->model->getTableSelection()->fetchPairs('id', 'title'))
			->setPrompt('-- žádný --')
			->setAttribute("cols", 6)
			->setAttribute("help", 'Předek stránky slouží pro zobrazení v drobečkové navigaci');

		$form->addTextArea("content", "Obsah stránky")
			->setAttribute("class", "form-summernote");

		$this->addAclInput('content.page.item');

	}


	/**
	 * @param Form $form
	 */
	protected function addButtons(Form $form)
	{
		parent::addButtons($form);
		$form->addSubmit("save", "Uložit & Zůstat")
			->setAttribute('class', 'btn btn-info');
	}


	protected function submit(Control $control, Form $form)
	{
		$presenter = $control->getPresenter();
		$values = $form->getValues('array');
		$values['content'] = Helper::createSafeEscapeString($values['content']);

		if (isset($values['id']) && (int) $values['id'] > 0) {
			$this->model->getTableSelection()->where("id", $values['id'])->update($this->model->clearingValues($values));
			$presenter->flashMessage("Změny uloženy", "success");
		} else {
			unset($values['id']);
			$this->createdId = $this->model->getTableSelection()->insert($this->model->clearingValues($values))->id;
			$presenter->flashMessage("Stránka vytvořena", "success");
		}

		$this->saveAcl();
	}


	/**
	 * @param Control $control
	 * @param Form $form
	 */
	protected function afterSucceedSubmit(Control $control, Form $form)
	{
		if ($form->isSubmitted()->getName() == 'save') {
			if ($this->isEditMode()) {
				$control->getPresenter()->redirect('this');
			} else {
				$control->getPresenter()->redirect('edit', ['id' => $this->getCreatedId()]);
			}
		} else {
			$control->getPresenter()->redirect('default');
		}
	}

}