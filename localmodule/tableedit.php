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
        $subaction = isset($arguments[1]) ? $arguments[1] : "";
        $id = isset($arguments[2]) ? intval($arguments[2]) : "";
		
		if(empty($arguments[0])) {
		}
		else {
			$this->page->block_output();
			switch($subaction) {

			}
		}
	}
	
	public function output() {
		$arguments = $this->page->getArguments();
        $subaction = isset($arguments[1]) ? $arguments[1] : "";
        $id = isset($arguments[2]) ? intval($arguments[2]) : "";
		$buffer = "";
		
		if(empty($arguments[0])) {
			// Empty Argument - Show a table of all items.
			$buffer = $this->getTable()->getHtml();
		}
		else {
			switch($subaction) {
                case "edit":
                    $buffer = $this->getForm($id, "Editieren", $this->getModuleGameUri("edit", $id))->getHtml();
                    break;
                
                case "drop":
                    break;
                
                case "new":
                    $buffer = $this->getForm($id, "Neu", $this->getModuleGameUri("new"))->getHtml();
                    break;
			}
		}
		
		return $buffer;
	}
    
    /**
     * @return \Submodel\TableFields
     */
    protected function getFields() {
        $dbtablename = filter_var($this->getPageconfigField("table-to-edit"), FILTER_CALLBACK, array("options" => "filter_word"));
        return $this->model->get("TableFields")->getByTablename($dbtablename);
    }
	
    /**
     * 
     * @param int $id ID of row which has to be edited.
     * @return \FormGenerator
     */
	protected function getForm($id = NULL, $title = "", $action = "") {
        $fields = $this->getFields();
        if(!is_null($id)) {
            $page = $this->model->get("Pages")->getById($id);
        }
        
		$formgenerator = new \FormGenerator($title, $action);
        foreach($fields as $field) {
            $formgenerator->addInput(
                $field->getFieldtype(), 
                $field->getDescription(), 
                $field->getFieldname(), 
                $page->{"get".$field->getFieldname()}(), [
                    
                ], $field->getProperty("flags", [])
            );
        }
        $formgenerator->addSubmitButton("Bestätigen", "tableedit_submit", 1);
        return $formgenerator;
	}
	
    /**
     * 
     * @return \TableGenerator
     */
	protected function getTable() {
        $fields = $this->getFields();
        $data = $this->model->get("Pages")->all();
        
		$table = new \TableGenerator();
        $url_e = $this->getModuleGameUri("edit", "%s");
        $url_x = $this->getModuleGameUri("drop", "%s");
        $table->addCol(0, "Optionen", array(
                "custom-content" => array("[<a href=\"$url_e\">E</a>]", "[<a href=\"$url_x\">X</a>]"),
                "custom-variables" => array("id", "id"),
                "custom-check" => array("isEditable", "isDeletable"),
                "custom-alternate" => array("[E]", "[X]")
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