<?php

  namespace Template;

  class Engine
  {
    protected $plugins;
    protected $filters;
    protected $executioner;

    public function __construct() {
      $this->plugins = new \stdClass;
      $this->filters = new \stdClass;
      $this->executioner = new Executioner($this);
      $this->plug('block', new Block);
    }

    public function plug($name, $plugin) {
      if(isset($this->plugins->$name)) {
        throw new \Exception('Plugin \'' . $name . '\' is already defined');
      }
      $this->plugins->$name = $plugin;
    }

    public function getPlugin($name) {
      if(!isset($this->plugins->$name)) {
        throw new \Exception('Plugin \'' . $name . '\' is not defined');
      }
      return $this->plugins->$name;
    }

    // Filters
    public function addFilter($name, $filter) {
      if(isset($this->filters->$name)) {
        throw new \Exception('Filter \'' . $name . '\' is already defined');
      }
      $this->filters->$name = $filter;
    }

    public function filter($var, $filter) {
      $filters = array_slice(func_get_args(), 1);
      foreach($filters as $filter) {
        if(!isset($this->filters->$filter)) {
          throw new \Exception('Filter \'' . $filter . '\' is not defined');
        }
        $var = call_user_func($this->filters->$filter, $var);
      }
      return $var;
    }

    public function execute($filename, array $data = array()) {
      $output =  $this->executioner->execute($this->getTemplate($filename), $data);
      return $output;
    }

    public function getTemplate($filename) {
      return new Template($filename);
    }
  }

?>