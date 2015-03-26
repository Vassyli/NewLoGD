<?php
/**
 * @author	Basilius Sauter <basilius.sauter@hispeed.ch>
 *
 * Provides the Controller module.
 */

class Controller {
	private $model = NULL;
	
	public function __construct(Model $model) {
		$this->model = $model;
	}
	
	public function execute() {
		$page = $this->model->get("Pages")->getby_action($this->model->get_res_action());
		$page->set_arguments($this->model->get_res_arguments());
		
		$page->execute();
	}
}