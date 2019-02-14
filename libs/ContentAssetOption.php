<?php
namespace Grapesc\GrapeFluid\ContentModule;

use Grapesc\GrapeFluid\CoreModule\Model\SettingModel;
use Grapesc\GrapeFluid\Options\IAssetOptions;
use Nette\Utils\Strings;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class ContentAssetOption implements IAssetOptions
{

	private const PREFIX = "content.";

	/** @var SettingModel */
	private $settingModel;


	public function __construct(SettingModel $settingModel)
	{
		$this->settingModel = $settingModel;
	}


	/**
	 * @param string $option
	 * @return bool
	 */
	public function getOption($option)
	{
		if (!Strings::startsWith($option, self::PREFIX)) {
			return false;
		}

		return (bool) $this->settingModel->getVal($option) ?? false;
	}

}