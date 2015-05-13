<?php

abstract class LocalmoduleBasis implements \LocalmoduleAPI, \Basicmodelitem {
    /** @var \Model Reference to the main model */
	protected $model;
    /** @var \page\api Reference to the page or NULL */
	protected $page;
	
    /** @var int primary id of the database entry */
	protected $id;
    /** @var string Classname (without namespace) of the Module */
	protected $class;
    /** @var string user readable name of the module */
    protected $name;
    /** @var string user readable short description of the module */
    protected $description;
    /** @var bool true if module is active on global stage, false if not */
    protected $active;
    /** @var array containts module-specific configuration per page */
    protected $pageconfig;
	
	public function __construct(\Model $model, array $row,  \page\api $page = NULL) {
		$this->model = $model;
		$this->page = $page;
		
		$this->id = (int)$row["id"];
		$this->class = $row["class"];
		$this->name = $row['name'];
		$this->description = $row['description'];
		$this->active = (bool)$row['active'];
		
		$this->setPageconfig($row['pageconfig']);
	}
	
    /**
     * Returns the primary ID
     * @return int Primary ID
     */
	public function getId() {return $this->id;}
    /**
     * Returns the classname
     * @return string classname
     */
	public function getClass() {return $this->class; }
    /**
     * Returns the user-readable name of the module
     * @return string Name of the Module
     */
	public function getName() {return $this->name;}
    /**
     * Returns the user-readable description of the module
     * @return string Description of the module
     */
	public function getDescription() {return $this->description;}
    /**
     * Default implementation of getPageconfigForm(): Return NULL
     * @return NULL
     */
    public function getPageconfigForm() { return NULL; }
    /**
     * Decodes the json-encoded page-config and stores it in the instance
     * @param string json-encoded config string
     */
	protected function setPageconfig($pageconfig) {
		$this->pageconfig = json_decode($pageconfig, true);
	}
    /**
     * Returns a value from the page config
     * @param string $key
     * @param mixed $default returned if key is not set
     * @return mixed returns either the value stored by $key or returns $default.
     */
	public function getPageconfigField($key, $default = "") {
		if(isset($this->pageconfig[$key])) {
			return $this->pageconfig[$key];
		}
        else {
            return $default;
        }
	}
    /**
     * Gets called from a page after the inialization of the Navigation in order
     * to manipulate it.
     * @param Navigation\Container $navigation The prepared and filled Navigation Container
     */
    public function navigationHook(Navigation\Container $navigation) {} 
    /**
     * Returns an url which leads to the module namespace of the page.
     * @param string $arg... Additional URL argument (value only)
     * @return string the url string.
     */
    final protected function getModuleGameUri() {
        return get_gameuri($this->page->getAction(), array_merge(array($this->getClass()), func_get_args()));
    }
    final protected function getPageGameUri() {
        return get_gameuri($this->page->getAction(), func_get_args());
    }
}