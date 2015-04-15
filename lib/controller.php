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
		// Load and start the Session
		$this->model->get("Session")->start();
		
		if($this->model->get("Session")->is_loggedin()) {
			debug("<b style=\"\">Account is logged in</b>");
			$this->model->get("Accounts")->set_active($this->model->get("Session")->get_active_account());
		}
		else {
			debug("<b style=\"\">Account is NOT logged in</b>");
		}
		
		// Load the page given by action and initialize it properly.
		$page = $this->model->get("Pages")->getby_action($this->model->get_res_action());
		
		// Check if user actually has access
        $page = $this->checkAccess($page);
		
		$page->initiate();
		$page->set_arguments($this->model->get_res_arguments());
		
		// Execute the page-code.
		$page->execute();
		
		// Stop the Session. Session can be read (and changed) afterwards, but not saved.
		$this->model->get("Session")->stop();
		
		// If the page has no output, it should redirect to somewhere.
		if($page->hasOutput() === false) {
			die("Page has no output, should have redirect. Script was stopped in controller.");
		}
	}
    
    protected function checkAccess(\Page\Api $page) {
        if($this->model->get("Session")->is_loggedin() == false) {
			if($page->checkAccess(\Page\api::ACCESS_ANONYMOUS)) {
				// Anonymous Access ok
			}
			else {
				$page = $this->model->get("Pages")->get_403page($this->model->get_res_action());
			}
		}
		else {
			if($page->checkAccess(\Page\api::ACCESS_ACCOUNT)) {
				// Account Access ok
			}
			else {
				$page = $this->model->get("Pages")->get_403page($this->model->get_res_action());
			}
		}
        
        return $page;
    }
}