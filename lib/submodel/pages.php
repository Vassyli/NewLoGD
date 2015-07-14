<?php

namespace Submodel;

class Pages implements SubmodelInterface, EditableSubmodel {
	use \lazy;
	
	private $model;
	
	public function __construct(\Model $model) {
		$this->model = $model;
		$this->set_lazy_keys(array("id", "action"));
	}
	
	public function getById($id) {
		if($this->has_lazy("id", $id) === false) {
			$query = $this->model->from("pages")
				->where("id", $id);
				
			$row = $query->fetch();
			
			$page = NULL;
			
			if($row === false) {
				throw new \Exception(sprintf("Page(id=%i) was not found in database.", $id));
			}
			else {
				$classname = $this->get_classname($row["type"]);
				
				if(class_exists($classname)) {
					$page = new $classname($this->model, $row);
					$this->set_lazy($page);
				}
                
                return $page;
			}
		}
		else {
			return $this->get_lazy("id", $id);
		}
	}
	
	public function getbyAction($action) {
		//if(!isset($this->lazy["action"][$action])) {
		if($this->has_lazy("action", $action) === false) {
			$query = $this->model->from("pages")
				->where("action", $action);
			$row = $query->fetch();
			
			$page = NULL;
			
			if($row === false) {
				// Page not found
				$page = new \Page\Error404($this->model, array($action));
				$this->set_lazy($page);
			}
			else {
				$classname = $this->get_classname($row["type"]);
                
				try {
					$page = new $classname($this->model, $row);
					$this->set_lazy($page);
				}
				catch(LogicException $e) {
					// Lalelu..
				}
			}
			
			return $page;
		}
		else {
			return $this->get_lazy("action", $action);
		}
	}
	
	public function get_403page($action) {
		if($this->has_lazy("id", -403)) {
			$page = $this->get_lazy("id", -403);
		}
		else {
			$page = new \Page\Error403($this->model, array($action));
			$this->set_lazy($page);
		}
		
		return $page;
	}
    
	
	protected function get_classname($type) {
        try {
            $classname = "\\Page\\".filter_var($type, FILTER_CALLBACK, array("options" => "filter_nonalpha"));
            if(class_exists($classname)) {
                return $classname;
            }
            else {
                return "\\Page\\Node";
            }
        } catch (LogicException $ex) {
            return "\\Page\\Node";
        } catch(Exception $ex) {
            return "\\Page\\Node";
        }
	}
    
    public function all() {
        $query = $this->model->from("pages");
        $instances = array();
        while($row = $query->fetch()) {     
            $classname = $this->get_classname($row["type"]);
            
            $page = new $classname($this->model, $row);
            array_push($instances, $page);
            $this->set_lazy($page);
        }
        
        return $instances;
    }
    
    public function create(array $sanitize) {
        $query = $this->model->insertInto("pages")
            ->addFields("type", "action", "title", "subtitle", "content", "access", "flags")
            ->addValues(
                $sanitize["type"], 
                $sanitize["action"], 
                $sanitize["title"], 
                $sanitize["subtitle"], 
                $sanitize["content"], 
                $sanitize["access"], 
                3
            );
        $query->execute();
    }
    
    public function save(\Page\Base $page) {
        $query = $this->model->update("pages");
        $query->addPair("type", $page->getType())
            ->addPair("action", $page->getAction())
            ->addPair("title", $page->getTitle())
            ->addPair("subtitle", $page->getSubtitle())
            ->addPair("content", $page->getContent())
            ->addPair("access", $page->getAccess())
            ->addPair("flags", $page->getFlags())
            ->where("id", $page->getId());
        $query->execute();
    }
    
    public function dropById($id) {
        $query = $this->model->delete("pages")
            ->where("id", $id);
        $query->execute();
    }
}