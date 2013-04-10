<?php

  namespace Template;

  class Template
  {
    protected $filename;

    public function __construct($filename) {
      if(!is_file($filename) || !is_readable($filename)) {
        throw new \Exception('Template file \'%s\' does not exist or is not readable');
      }
      $this->filename = $filename;
    }

    public function getFilename() {
      return $this->filename;
    }
  }

?>