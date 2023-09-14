<?php 

namespace Application\Block\BasicTest;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Asset\Asset;

defined('C5_EXECUTE') or die(_("Access Denied."));
	
class Controller extends BlockController {
	
	protected $btTable = "btBasicTest";
	protected $btInterfaceWidth = "350";
	protected $btInterfaceHeight = "300";

	public function getBlockTypeName() {
		return t('Basic Test');
	}

	public function getBlockTypeDescription() {
		return t('A simple testing block for developers');
	}

	public function on_start() {
		$al = AssetList::getInstance();
		$al->register(
			'javascript', 'datgui', 'blocks/basic_test/dat.gui-master/build/dat.gui.min.js', array('version' => '0.7.9', 'minify' => false, 'combine' => true)
		);
		$al->register(
			'javascript', 'datguimodule', 'blocks/basic_test/dat.gui-master/build/dat.gui.module.js', array('version' => '0.7.9', 'minify' => false, 'combine' => true)
		);
		$al->registerGroup('datgui', array (
			array('javascript', 'datgui'),
			array('javascript', 'datguimodule')
		));
	}
	
	public function registerViewAssets($outputContent = '') {
		$this->requireAsset('javascript', 'jquery');
		$this->requireAsset('datgui');
	}
	
}

