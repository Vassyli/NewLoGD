<?php

namespace Submodel;

class Navigations implements SubmodelInterface, EditableSubmodel {
	use \lazy;
	
	private $model;
	
	public function __construct(\Model $model) {
		$this->model = $model;
		$this->set_lazy_keys();
		$this->set_lazyset_keys(array("page_id"));
	}
    
    public function getById($id) {
		if($this->has_lazy("id", $id) === false) {
			$query = $this->model->from("navigations")
				->where("id", $id);
            
            if(count($query) > 0) {
				$row = $query->fetchObject("\Navigation\Item", array($this->model));
				$this->set_lazy($row);
				return $row;
			}
			else {
				return false;
			}
		}
		else {
			return $this->get_lazy("id", $id);
		}
	}
	
	public function getByPageId($page_id) {
		if($this->has_lazyset("page_id")) {
			$navs = $this->get_lazyset("page_id");
			return array();
		}
		else {
			//$result = $this->model->from("navigation")->where("page_id", $page_id)->orderby("parentid")->orderby("action", \Query\Select::ORDER_ASC, true);
			$result = $this->model->from("navigations")->where("page_id", $page_id)->orderby("parentid")->orderByCondition("action", NULL, \Query\Select::OPERATOR_EQ, "action", "sort")->orderby("sort");
			$instances = array();
		
			while($row = $result->fetchObject("\Navigation\Item", array($this->model))) {
				$i = $this->set_lazyset("page_id", $row);
				array_push($instances, $i);
			}
			
			return $instances;
		}
	}
    
    public function all() {
        $query = $this->model->from("navigations");
        
        $instances = array();
        
        while($row = $query->fetchObject("\Navigation\Item", array($this->model))) {
            array_push($instances, $row);
            $this->set_lazy($row);
        }
        
        return $instances;
    }
    
    public function create(array $sanitize) {
        $query = $this->model->insertInto("navigations")
            ->addFields("parentid", "page_id", "action", "title", "sort", "flags")
            ->addValues(
                $sanitize["parentid"], 
                $sanitize["page_id"], 
                $sanitize["action"], 
                $sanitize["title"], 
                $sanitize["sort"], 
                3
            );
        $result = $query->execute();
        // var_dump($result, $sanitize);
    }
    
    public function save(\Navigation\Item $item) {
        $query = $this->model->update("Navigations");
        $query->addPair("parentid", $item->getParentid())
            ->addPair("page_id", $item->getPage_Id())
            ->addPair("action", $item->getAction())
            ->addPair("title", $item->getTitle())
            ->addPair("sort", $item->getSort())
            ->where("id", $item->getId());
        $query->execute();
    }
    
    public function dropById($id) {
        $query = $this->model->delete("Navigations")
            ->where("id", $id);
        $query->execute();
        return $query->getAffectedRows();
    }
}