<?php

  namespace Template\Filter;
  use Template\Engine;

  class String
  {
    static public function filter(Engine $engine) {
      $engine->addFilter('escape', array(__CLASS__, 'escape'));
      $engine->addFilter('upper',  array(__CLASS__, 'upper'));
      $engine->addFilter('lower',  array(__CLASS__, 'lower'));
      $engine->addFilter('br',     array(__CLASS__, 'br'));
    }
    static public function escape($string) {
      return htmlspecialchars($string);
    }
    static public function upper($string) {
      return mb_strtoupper($string);
    }
    static public function lower($string) {
      return mb_strtolower($string);
    }
    static public function br($string) {
      return nl2br($string);
    }
  }

?>