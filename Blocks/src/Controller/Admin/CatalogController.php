<?php

namespace Croogo\Blocks\Controller\Admin;

use Croogo\Blocks\Catalog;
use Croogo\Blocks\Model\Table\RegionsTable;
use Croogo\Core\Controller\CroogoAppController;

/**
 * @property RegionsTable Regions
 */
class CatalogController extends CroogoAppController
{

	public function initialize()
	{
		parent::initialize();

		$this->loadModel('Croogo/Blocks.Regions');
	}


	public function index()
	{
		if (isset($this->request->params['named']['links']) || isset($this->request->query['chooser'])) {
//			$this->layout = 'Croogo/Core.admin_popup';
			$this->view = 'chooser';
		}

		if ($this->request->query('region')) {
			$region = $this->Regions->get($this->request->query('region'));

			$this->set('region', $region);
		}

		$items = Catalog::getItems();

		$this->set(compact('items'));
	}

}
