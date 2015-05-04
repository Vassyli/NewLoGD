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
			$this->page->blockOutput();
			switch($subaction) {
                case "edit": $this->executeEdit($id); break;     
                case "drop": $this->executeDrop($id); break;
                case "new": $this->executeNew(); break;
			}
		}
	}
    
    protected function executeEdit($id) {
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
            } catch(\Exception $e) {
                // Exception, do nothing
            }
        }
    }
    
    protected function executeNew() {
        $form = $this->getNewForm();

        if($this->model->get_postvalue("tableedit_submit") == 1 && empty($this->error)) {
            try {
                $sanitize = $form->sanitize($this->model->get_postarray(), true);
                $this->model->get("pages")->create(
                    $sanitize["type"],
                    $sanitize["action"],
                    $sanitize["title"],
                    $sanitize["subtitle"],
                    $sanitize["content"],
                    $sanitize["access"]
                );
                $this->error = "newlycreated";
            } catch(\Exception $e) {
                $this->error = "error";
            }
        }
    }
    
    protected function executeDrop($id) {
        try {
            $pageitem = $this->model->get("pages")->getById($id);
            $this->error = $pageitem->isDeletable() ? "": "nodrop";
        }
        catch(\Exception $e) {
            $this->error = "notfound";
        }
        
        if(empty($this->error)) {
            try {
                $ret = $this->model->get("pages")->dropById($id);
                $this->error = $ret > 0 ? "" : "notdropped";
            } catch (Exception $e) {
                // 
            }
        }
    }
    
    public function navigationHook(\Navigation\Container $navigation) {
        $arguments = $this->page->getArguments();
        $subaction = isset($arguments[1]) ? $arguments[1] : "";
        $id = isset($arguments[2]) ? intval($arguments[2]) : "";
        
        if(empty($arguments[0])) {
            $navid = $navigation->addCustomItem("Editor");
            $navigation->addCustomItem("Neu", $this->getModuleGameUri("new"), $navid);
        } else {
            $navid = $navigation->addCustomItem("Editor");
            $navigation->addCustomItem("Zurück zur Übersicht", $this->getPageGameUri(), $navid);
            
            switch($subaction) {
                case "new":
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
		} else {
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
                    if(empty($this->error)) {
                        $buffer = "The entry was successfully deleted.";
                    }
                    elseif($this->error == "notfound") {
                        $buffer = "The requested page with the id {$id} was not found.";
                    }
                    elseif($this->error == "nodrop") {
                        $buffer = "The requested page with the id {$id} is not deletable!";
                    }
                    elseif($this->error == "notdropped") {
                        $buffer = "Database query was successfull, but nothing was deleted.";
                    }
                    else {
                        $buffer = "An unknown error occured.";
                    }
                    
                    $buffer .= $this->getTable()->getHtml();
                    break;
                
                case "new":
                    if(empty($this->error)) {
                        $buffer = $this->getNewForm()->getHtml();    
                    }
                    elseif($this->error == "error") {
                        $buffer = "An unknown error occured";
                    }
                    elseif($this->error == "newlycreated") {
                        $buffer = "The page was successfully created.";
                        $buffer .= $this->getTable()->getHtml();
                    }
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
    
    protected function getNewForm() {
        return $this->getForm(NULL, "Neu", $this->getModuleGameUri("new"));
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
                    (!is_null($id) ? $page->{"get".$field->getFieldname()}() : ""), 
                    [], 
                    $field->getProperty("flags", [])
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