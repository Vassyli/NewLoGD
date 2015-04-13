<?php

namespace Localmodule;

class Tableedit extends \LocalmoduleBasis {
	protected $model;
	
	private $form = NULL;
	
	public function __construct(\Model $model, array $row, $page = NULL) {
		parent::__construct($model, $row, $page);
	}
	
	public function execute() {		
		$arguments = $this->page->getArguments();
		
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
		$arguments = $this->page->getArguments();
		$buffer = "";
		
		if(empty($arguments[0])) {
			// Empty Argument - Show a table of all items.
			$buffer = $this->getTable()->getHtml();
		}
		elseif(empty($arguments[1])) {	
		}
		else {
			switch($arguments[1]) {

			}
		}
		
		return $buffer;
	}
	
	protected function getForm() {
		
	}
	
	protected function getTable() {
        $dbtablename = filter_var($this->getPageconfigField("table-to-edit"), FILTER_CALLBACK, array("options" => "filter_word"));
        //SELECT `table_fields`.* FROM `table_fields` INNER JOIN `tables` ON `tables`.id = `table_fields`.`table_id` and `tables`.`name` = 'pages'
        $fields = $this->model->get("TableFields")->getByTablename($dbtablename);
        $data = $this->model->get("Pages")->all();
        
		$table = new \TableGenerator();
        $url_e = get_gameuri($this->page->getAction(), array("edit", "%s"));
        $url_x = get_gameuri($this->page->getAction(), array("drop", "%s"));
        var_dump($url_e, $url_x);
        $table->addCol(0, "Optionen", array(
                "custom-content" => "[<a href=\"$url_e\">E</a>] [<a href=\"$url_x\">X</a>]",
                "custom-variables" => array("id", "id"),
        ));
        
        foreach($fields as $row) {
            $table->addCol(
                $row->getFieldname(), 
                $row->getDescription(), 
                array(
                    "type" => $row->getFieldtype(),
                )
            );
        }
          
        $table->addRows($data);
        return $table;
	}
}