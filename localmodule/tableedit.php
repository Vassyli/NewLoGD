<?php

namespace Localmodule;

class Tableedit extends \LocalmoduleBasis {
	protected $model;
	
	private $form = NULL;
    private $dbtablename = "";
	
	public function __construct(\Model $model, array $row, $page = NULL) {
		parent::__construct($model, $row, $page);
        
        // Prepare the Name for the Database/Database-Model (which sould be the same..)
        $this->dbtablename = filter_var($this->getPageconfigField("table-to-edit"), FILTER_CALLBACK, array("options" => "filter_word"));
	}
	
    //
    //  CONTROLLER
    //
	public function execute() {		
        list($arguments, $subaction, $id, $module) = $this->getURLParams();
		
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
        try {
            $form = $this->getEditForm($id);
            $pageitem = $this->model->get($this->dbtablename)->getById($id);
            $this->error = $pageitem->isEditable() ? "": "noedit";
        }
        catch(\Exception $e) {
            $this->error = "notfound";
        }


        if($this->model->getPostvalue("tableedit_submit") == 1 && empty($this->error)) {
            try {
                $sanitize = $form->sanitize($this->model->getPostarray(), true);

                foreach($sanitize as $key => $val) {
                    $method = "set".$key;
                    $pageitem->$method($val);
                }

                $pageitem->save();
            } catch(\Exception $e) {
                // Exception, do nothing
            }
        }
		
		// Module-Specific Edits
		$module = $this->model->getPostvalue("module");
		if($module && empty($this->error)) {
			$form = $this->getModuleFormByModulename($id, $module);
			if(!empty($form)) {
				$sanitize = $form->sanitize($this->model->getPostarray(), true);
				
				// Get reference to module
				$module = $this->model->get($this->dbtablename)->getById($id)->getLocalmodule($module);
				$module->setPageconfig($sanitize);
				$module->save();
			}
		}
    }
    
    protected function executeNew() {
        $form = $this->getNewForm();

        if($this->model->getPostvalue("tableedit_submit") == 1 && empty($this->error)) {
            try {
                $sanitize = $form->sanitize($this->model->getPostarray(), true);
                $this->model->get($this->dbtablename)->create($sanitize);
                $this->error = "newlycreated";
            } catch(\Exception $e) {
                $this->error = "error";
            }
        }
    }
    
    protected function executeDrop($id) {
        try {
            $pageitem = $this->model->get($this->dbtablename)->getById($id);
            $this->error = ($pageitem === false) ? "notfound" : ($pageitem->isDeletable() ? "": "nodrop");
        }
        catch(\Exception $e) {
            $this->error = "notfound";
        }
        
        if(empty($this->error)) {
            try {
                $ret = $this->model->get($this->dbtablename)->dropById($id);
                $this->error = $ret > 0 ? "" : "notdropped";
            } catch (Exception $e) {
                // 
            }
        }
    }
    
    //
    //  NAVIGATION
    //
    public function navigationHook(\Navigation\Container $navigation) {
        list($arguments, $subaction, $id) = $this->getURLParams();
        
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
	
    //
    //  VIEW
    //
	public function output() {
        list($arguments, $subaction, $id) = $this->getURLParams();
		$buffer = "";
		
		if(empty($arguments[0])) {
			// Empty Argument - Show a table of all items.
			$buffer = $this->getTable()->getHtml();
		} else {
            if(empty($this->error)) {
                $this->error = "";
            }
        
			switch($subaction) {
                case "edit": $buffer = $this->outputEdit($id); break;
                case "drop": $buffer = $this->outputDrop($id); break;
                case "new": $buffer = $this->outputNew(); break;
			}
		}
		
		return $buffer;
	}
    
    protected function outputEdit($id) {  
        switch($this->error) {
            case "":
                $buffer = $this->getEditForm($id)->getHtml();
                $buffer.= $this->addModuleForms($id);
                break;
            case "notfound":
                $buffer = "The requested page with the id {$id} was not found.";
                $buffer.= $this->getTable()->getHtml();
                break;
            case "noedit":
                $buffer = "The requested page with the id {$id} is not editable!";
                break;
            default:
                $buffer = "An unknown error occured.";
                $buffer.= $this->getTable()->getHtml();
                break;
        }
        
        return $buffer;
    }
    
    protected function outputDrop($id) {
        switch($this->error) {
            case "":
                $buffer = "The entry was successfully deleted.";
                break;
            case "notfound":
                $buffer = "The requested page with the id {$id} was not found.";
                break;
            case "nodrop":
                $buffer = "The requested page with the id {$id} is not deletable!";
                break;
            case "notdropped":
                $buffer = "Database query was successfull, but nothing was deleted.";
                break;
            default:
                $buffer = "An unknown error occured.";
                break;
        }
        $buffer .= $this->getTable()->getHtml();
        return $buffer;
    }
    
    protected function outputNew() {
        switch($this->error) {
            case "":
                $buffer = $this->getNewForm()->getHtml();
                break;
            case "error":
                $buffer = "An unknown error occured";
                break;
            case "newlycreated":
                $buffer = "The page was successfully created.";
                        $buffer .= $this->getTable()->getHtml();
                break;
            default:
                $buffer = "An unknown error occured.";
                break;
        }
        return $buffer;
    }
    
    //
    //  HELPER FUNCTIONS
    //
    protected function addModuleForms($id) {
        if($id === NULL) {
            return "";
        }

		$buffer = "";
		
		$forms = $this->getModuleForms($id);
		foreach($forms as $form) {
			$buffer .= $form->getHtml();
		}
        
        return $buffer;
    }
	
	protected function getModuleFormByModulename($id, $modulename = NULL) {
		if(empty($modulename)) {
			return NULL;
		}
		
		$forms = $this->getModuleForms($id);
		foreach($forms as $form) {
			if($form->getField("module")["value"]) {
				return $form;
			}
		}
		
		return NULL;
	}
	
	protected function getModuleForms($id) {
		$dbitem = $this->model->get($this->dbtablename)->getById($id);

        if(in_array("hasModules", class_implements($dbitem))) {
            $forms = $dbitem->getLocalmodulesForm($this->getModuleGameUri("edit", $id, "modules"));
			return $forms;
		}
		
		return [];
	}
    
    /**
     * 
     */
    protected function getURLParams() {
        $arguments = $this->page->getArguments();

        return [
            $arguments, 
            isset($arguments[1]) ? $arguments[1] : "", 
            isset($arguments[2]) ? intval($arguments[2]) : "",
            isset($arguments[3]) ? $arguments[3] : "",
        ];
    }
    
    /**
     * 
     * @return \Submodel\TableFields
     */
    protected function getFields() {
        //$dbtablename = filter_var($this->getPageconfigField("table-to-edit"), FILTER_CALLBACK, array("options" => "filter_word"));
        $fields = $this->model->get("TableFields")->getByTablename($this->dbtablename);
        return $fields;
    }
    /**
     * Gets the generated Edit-Form with the appropriate title and action param
     * @return \Submodel\FormGenerator
     */
    protected function getEditForm($id) {
        return $this->getForm($id, "Editieren", $this->getModuleGameUri("edit", $id));
    }
    /**
     * Gets the generated New-Entry-Form with the appropriate title and action param
     * @return \Submodel\FormGenerator
     */
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
                $page = $this->model->get($this->dbtablename)->getById($id);
            }

            $formgenerator = new \FormGenerator($title, $action);
            $formgenerator->setModel($this->model);
            foreach($fields as $field) {
                $formgenerator->addInput(
                    $field->getFieldtype(), 
                    $field->getDescription(), 
                    $field->getFieldname(), 
                    (!is_null($id) ? $page->{"get".$field->getFieldname()}() : $field->getDefaultValue()), 
                    $field->getProperty("validator", []),
                    $field->getProperty("options", [])
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
        $data = $this->model->get($this->dbtablename)->all();
        
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
                [
                    "type" => $row->getFieldtype(),
                    "nullifempty" => empty($row->getProperty("validator", [])["nullifempty"]) ? false : true
                ]
            );
        }
          
        $table->addRows($data);
        return $table;
	}
    
    public function getPageconfigForm($action) {
        $formgenerator = new \FormGenerator($this->getName(), $action);
        $formgenerator->addLine("Which Table to Edit", "table-to-edit", $this->getPageconfigField("table-to-edit"));
        $formgenerator->addSubmitButton("Submit", "module", $this->getClass());
        return $formgenerator;
    }
}