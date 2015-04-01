<?php
/**
 * NewLoGD
 *
 * @author      Basilius Sauter <basilius.sauter@hispeed.ch>
 * @copyright   Copyright (c) 2015, Basilius Sauter
 * @licence     https://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPL 3.0
 */
 /**
  * The Controller-Class which does all the processing of user-committed data or delegates it.
  */
class Controller {
	/** @var Model Contains a reference to the Model class */
	private $model = NULL;
	
	/**
	 * The constructor.
	 *
	 * @param Model $model A reference to a instance of the Model-Class
	 */
	public function __construct(Model $model) {
		$this->model = $model;
	}
	
	/**
	 * Runs the processing code of all loaded components.
	 */
	public function execute() {
		// Load the page given by action and initialize it properly.
		$page = $this->model->get("Pages")->getby_action($this->model->get_res_action());
		$page->initiate();
		$page->set_arguments($this->model->get_res_arguments());
		
		// Execute the page-code.
		$page->execute();
	}
}