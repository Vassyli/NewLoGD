<?php

namespace Localmodule;

class Tableedit extends \LocalmoduleBasis {
	protected $model;
	
	private $form = NULL;
	
	public function __construct(\Model $model, array $row, $page = NULL) {
		parent::__construct($model, $row, $page);
	}
	
	public function execute() {		
		$arguments = $this->page->get_arguments();
		
		if(empty($arguments[0])) {
		}
		elseif(empty($arguments[1])) {	
			$this->page->block_output();
		}
		else {
			$this->page->block_output();
			switch($arguments[1]) {

			}
		}
	}
	
	public function output() {
		$arguments = $this->page->get_arguments();
		$buffer = "";
		
		if(empty($arguments[0])) {
			// Empty Argument - Show a table of all items.
			$buffer = $this->get_table()->getHtml();
		}
		elseif(empty($arguments[1])) {	
		}
		else {
			switch($arguments[1]) {

			}
		}
		
		return $buffer;
	}
	
	protected function get_form() {
		
	}
	
	protected function get_table() {
        $dbtablename = filter_var($this->get_pageconfig_field("table-to-edit"), FILTER_CALLBACK, array("options" => "filter_word"));
        //SELECT `table_fields`.* FROM `table_fields` INNER JOIN `tables` ON `tables`.id = `table_fields`.`table_id` and `tables`.`name` = 'pages'
        $fields = $this->model->get("TableFields")->getByTablename($dbtablename);
        $data = $this->model->get("Pages")->all();
        
		$table = new \TableGenerator();
        
        foreach($fields as $row) {
            $table->addCol($row["fieldname"], $row["description"]);
        }
          
        $table->addRows($data);
        return $table;
	}
}