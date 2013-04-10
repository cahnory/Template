<?php

  namespace Template;

  class Executioner
  {
    protected $engine;
    protected $sandbox;
    protected $block;

    protected $data = array(array());

    protected $extendFile = array();
    protected $extendData = array();

    public function __construct(Engine $engine) {
      $this->engine = $engine;
      $this->sandbox = new SandBox($this);
      $this->block = new Block;
    }

    public function execute(Template $template, array $data) {
      // Create execution context
      array_unshift($this->data, array_merge($this->data[0], $data));
      array_unshift($this->extendFile, array());
      array_unshift($this->extendData, array());

      // Execute template
      $output = $this->sandbox->execute($template, $this->data[0]);

      // Execute template extends
      while($this->extendFile[0]) {
        $filename = array_shift($this->extendFile[0]);
        $override = array_shift($this->extendData[0]);
        $output   = $this->execute(
          $this->engine->getTemplate($filename),
          array_merge($this->data[0], $override)
        );
      }

      // Remove execution context
      array_shift($this->extendData);
      array_shift($this->extendFile);
      array_shift($this->data);

      return $output;
    }

    // !Templating tools

    public function __get($name) {
      return $this->engine->getPlugin($name);
    }

    public function __call($name, $args) {
      return call_user_func_array($this->engine->getPlugin($name), $args);
    }

    public function filter($var, $filter) {
      return call_user_func_array(array($this->engine, 'filter'), func_get_args());
    }

    public function extend($filename, array $data = array()) {
      if(in_array($filename, $this->extendFile[0])) {
        throw new \Exception(sprintf('Extend loop on \'%s\' template', $filename));
      }
      $this->extendFile[0][] = $filename;
      $this->extendData[0][] = $data;
    }

    public function import($filename, array $data = array()) {
      $executioner = new Executioner($this->engine);
      echo $executioner->execute($this->engine->getTemplate($filename), array_merge($this->data[0], $data));
    }
/*
    public function block($name) {
      return $this->block->open(func_get_args());
    }
    
    public function endBlock() {
      $this->block->close();
      if(!$this->extendFile) {
        echo $this->block->getString();
      }
    }

    public function hookBlock($name) {
      $opened = $this->block->open(func_get_args());
      $this->endBlock();
      return $opened;
    }*/
  }

?>