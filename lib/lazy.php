<?php

// This trait can only operate classes that implements Modelitem.

trait lazy {
	private $lazy_keys = array();
	private $lazyset_keys = array();
	
	private $lazy = array();
	private $lazyset = array();
	
	private function set_lazy_keys(array $keys = array(), $noid = false) {
		if(!in_array("id", $keys) and $noid === false) {
			array_push($keys, "id");
		}
		
		$this->lazy_keys = $keys;
		$this->lazy = array();
		
		foreach($keys as $key) {
			$this->lazy[$key] = array();
		}
	}
	
	private function set_lazyset_keys(array $set_keys) {
		$this->lazyset_keys = $set_keys;
		$this->lazysets = array();
		foreach($set_keys as $key) {
			$this->lazyset[$key] = array();
		}
	}
	
	private function set_lazy(Modelitem $instance) {
		foreach($this->lazy_keys as $key) {
			$method = "get".$key;
			$this->lazy[$key][$instance->$method()] = $instance;
		}
	}
	
	private function set_lazyset($set_key, $instances) {
		if(is_array($instances)) {
			foreach($instances as $arrkey => $instance) {
				$ret = $this->set_lazyset($set_key, $instance);
				$instances[$arrkey] = $ret;
			}
			
			return $instances;
		}
		elseif($instances instanceof Modelitem) {
			if(in_array($instances, $this->lazyset[$set_key]) == false) {
				// Check if there is already an instance of this object
				if($this->has_lazy("id", $instances->getId())) {
					debug("LazyLoading: Get from Memory Storage");
					$otherinstance = $this->get_lazy("id", $instances->getId());
					array_push($this->lazyset[$set_key], $otherinstance);
					return $otherinstance;
				}
				else {
					array_push($this->lazyset[$set_key], $instances);
					return $instances;
				}
			}
		}
		else {
			throw new UnexpectedValueException("trait lazy->set_lazyset(\$set_key, $\instances): \$instances has to be a Modelitem or an array of Modelitems.");
		}
	}
	
	private function get_lazy($key, $val) {
		return $this->lazy[$key][$val];
	}
	
	private function get_lazyset($set_key) {
		return $this->lazyset[$set_key];
	}
	
	private function has_lazy($key, $val) {
		if(isset($this->lazy[$key]) and isset($this->lazy[$key][$val])) {
			return true;
		}
		else {
			return false;
		}
	}
	
	private function has_lazyset($set_key) {
		if(!empty($this->lazyset[$set_key])) {
			return true;
		}
		else {
			return false;
		}
	}
}