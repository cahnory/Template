<?php

  ini_set('display_errors', 1); error_reporting(E_ALL);

  require_once '../Template/Engine.php';
  require_once '../Template/Executioner.php';
  require_once '../Template/AbstractSandbox.php';
  require_once '../Template/SandBox.php';
  require_once '../Template/Template.php';
  require_once '../Template/Block.php';
  require_once '../Template/Parser.php';

  require_once '../Template/Filter/String.php';

  // Instantiate a new engine
  $t = new Template\Engine;
  Template\Filter\String::filter($t);

  // Execute
  /*echo $t->execute('tpl/view.html', array(
    'test' => 'ok',
    'foo'  => 'foo'
  ));
  /* */
  
  $b = new Template\Parser;
  
  $templates = array(
    'theme-1.html',
    'theme-2.html',
    'demo.html'
  );

  // Src to bin
  foreach($templates as $file) {
    $src = file_get_contents('tpl/src/'.$file);
    $bin = $b->parse($src);

    file_put_contents('tpl/bin/'.$file, $bin);
  }

  echo $t->execute('tpl/bin/demo.html');

?>