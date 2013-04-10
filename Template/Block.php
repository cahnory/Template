<?php

  namespace Template;
  use stdClass;

  class Block
  {
    protected $opened = array();

    protected $parent;
    protected $children;
    protected $output;
    protected $locked = false;

    public function __construct(Block $parent = NULL) {
      if($parent !== NULL) {
        $this->parent = $parent;
        $this->locked = $parent->locked;
        $this->opened = &$parent->opened;
      } else {
        $this->opened = array($this);
      }
      $this->children = new stdClass;
    }

    public function isLocked() {
      return $this->opened[0]->getBlock(func_get_args())->locked;
    }

    public function isFilled() {
      return $this->opened[0]->getBlock(func_get_args())->output !== NULL;
    }

    public function hook($name) {
      $this->openPath(func_get_args());
      return self::close();
    }

    public function open($name) {
      return $this->openPath(func_get_args());
    }

    protected function openPath($path) {
      $block = $this->opened[0]->getBlock($path);
      array_unshift($this->opened, $block);
      ob_start();
      return !$block->locked;
    }

    public function close() {
      if(sizeof($this->opened) < 2) {
        throw new \Exception('Trying to close a block which was not opened');
      }

      $block = array_shift($this->opened);
      if(!$block->locked) {
        $block->output = ob_get_clean();
        $block->lock();
      } else {
        ob_end_clean();
      }
      echo $block->output;
      return $block->output;
    }

    public function getString() {
      return $this->output !== NULL ? $this->output : '';
    }

    protected function lock() {
      if(!$this->locked) {
        $this->locked = true;
        foreach($this->children as $child) {
          $child->lock();
        }
      }
    }

    protected function getBlock($path) {
      $block = $this;
      foreach($path as $name) {
        if(!isset($block->children->$name)) {
          $block->children->$name = new Block($this);
        }
        $block = $block->children->$name;
      }
      return $block;
    }
  }

?>