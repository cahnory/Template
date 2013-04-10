<?php

  namespace Template;

  abstract class AbstractSandbox
  {
    private $executioner;

    abstract public function execute(Template $template, $data);

    final function __construct(Executioner $executioner) {
      $this->executioner = $executioner;
    }

    final public function __get($name) {
      return $this->executioner->$name;
    }

    final public function __set($name, $value) {
      return $this->executioner->$name = $value;
    }

    final public function __isset($name) {
      return isset($this->executioner->$name);
    }

    final public function __unset($name) {
      unset($this->executioner->$name);
    }

    final public function __call($name, $args) {
      return call_user_func_array(array($this->executioner, $name), $args);
    }
  }

?>