<?php 

namespace Concrete\Package\MyPackage;

use AssetList;
	
class MyPackage extends \Concrete\Core\Page\Controller\DashboardPageController {

	public function on_start() {
		AssetList::getInstance()->register(
			'javascript',
			'three',
			'packages/my_package/three.js-dev',
			array(),
			'mypackage'
		);
	}
}
