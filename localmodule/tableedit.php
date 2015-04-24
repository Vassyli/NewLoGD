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
                case "edit":
                    $form = $this->getEditForm($id);
                    try {
                        $pageitem = $this->model->get("pages")->getById($id);
                        $this->error = $pageitem->isEditable() ? "": "noedit";
                    }
                    catch(\Exception $e) {
                        $this->error = "notfound";
                    }
                    
                    
                    if($this->model->get_postvalue("tableedit_submit") == 1 && empty($this->error)) {
                        try {
                            $sanitize = $form->sanitize($this->model->get_postarray(), true);

                            foreach($sanitize as $key => $val) {
                                $method = "set".$key;
                                $pageitem->$method($val);
                            }

                            $pageitem->save();
                        }
                        catch(\Exception $e) {
                            // Exception, do nothing
                        }
                    }
                    break;
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
                    if(empty($this->error)) {
                        $buffer = $this->getEditForm($id)->getHtml();
                    }
                    elseif($this->error == "notfound") {
                        $buffer = "The requested page with the id {$id} was not found.";
                    }
                    elseif($this->error == "noedit") {
                        $buffer = "The requested page with the id {$id} is not editable!";
                    }
                    else {
                        $buffer = "An unknown error occured.";
                    }
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
     * 
     * @return \Submodel\TableFields
     */
    protected function getFields() {
        $dbtablename = filter_var($this->getPageconfigField("table-to-edit"), FILTER_CALLBACK, array("options" => "filter_word"));
        return $this->model->get("TableFields")->getByTablename($dbtablename);
    }
    /**
     * Gets the generated Edit-Form with the appropriate title and action param
     * @return \Submodel\FormGenerator
     */
    protected function getEditForm($id) {
        return $this->getForm($id, "Editieren", $this->getModuleGameUri("edit", $id));
    }
	
    /**
     * Get a generated Form to edit a table entry or add a new one.
     * @param int $id ID of row which has to be edited.
     * @param string $title A descriptive form title, seen by the user
     * @param string $action The action url parameter that the form points to
     * @return \FormGenerator
     */
	protected function getForm($id = NULL, $title = "", $action = "") {
        if(empty($this->form)) {
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
            $this->form = $formgenerator;
        }
        else {
            $formgenerator = $this->form;
        }
        return $formgenerator;
	}
	
    /**
     * Creates a human readable, generated table of database entries
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