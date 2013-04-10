<?php

  namespace Template;

  class Sandbox extends AbstractSandbox
  {
    public function execute(Template $template, $data) {
      // Extract data which may overwrite arguments
      extract($data);

      // Execute template
      ob_start();
      include func_get_arg(0)->getFilename();
      $output = ob_get_clean();

      return $output;
    }
  }

?>