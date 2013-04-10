<?php

  namespace Template;

  class Parser
  {
    protected $tmpFile;
    protected $escape = '\\\\';

    protected $lBrack = '\\{';
    protected $rBrack = '\\}(?!\\})';

    protected $tokens = array(
      'echo'          => array('\\{','\\}'),
      'comment'        => array('\\#','$'),

      'extend'        => array('extends?\s','$'),
      'import'        => array('import\s','$'),
                      
      'hookblock'     => array('block\s', '/$'),
      'block'         => array('block\s','$'),
      'endblock'      => array('/','block'),
      'fullblock'     => array('fullblock\s','$'),
      'endfullblock'  => array('/','fullblock'),

      'if'            => array('if\s','$'),
      'endif'         => array('/','if'),

      'for'           => array('for\s','$'),
      'endfor'        => array('/','for'),

      'foreach'       => array('foreach\s','$'),
      'endforeach'    => array('/','foreach'),

      'while'         => array('while\s','$'),
      'endwhile'      => array('/','while')
    );

    protected $patterns = array();


    public function __construct() {
      $this->tmpFile = __DIR__.'/builder.tmp.php';
      foreach($this->tokens as $name => $tokens) {
        $this->patterns[$name] = '#^' . $this->getPattern($tokens[0], $tokens[1], $tokens[0] !== '/') . '#imu';
      }
    }

    public function parse($string) {
      $output = preg_replace_callback(
        '#' . $this->getPattern($this->lBrack, $this->rBrack, true) . '#imu',
        array($this, 'parseBracket'),
        $string
      );

      // Unescape, escaped brackets
      $output = preg_replace(sprintf('#((?<!%2$s)(?:%2$s%2$s)*)%2$s(%1$s)#imu', $this->lBrack, $this->escape), '$1$2', $output);
      return $output;
    }

    protected function parse_echo($string) {
      if(preg_match('#^\\s*((?:[a-z0-9_-]+)(?:\\s*[\\s|]\\s*[a-z0-9_-]+)*)\\s+(.+)$#imu', $string, $match)) {
        $filters = preg_split('#[\\s|]+#imu', $match[1]);
        return 'echo $this->filter(' . $match[2] .', \'' . implode('\',\'', $filters) . '\');';
      }
      return 'echo ' . $string . ';';
    }
    protected function parse_comment() {
      return NULL;
    }

    protected function parse_extend($string) {
      return '$this->extend(' . $string . ');';
    }
    protected function parse_import($string) {
      return '$this->import(' . $string . ');';
    }

    protected function parse_block($string) {
      return 'if($this->block->open(' . $string . ')):';
    }
    protected function parse_endblock() {
      return 'endif;$this->block->close();';
    }
    protected function parse_hookblock($string) {
      return '$this->block->hook(' . $string . ');';
    }
    protected function parse_fullblock($string) {
      return 'if($this->block->isFilled(' . $string . ')):';
    }
    protected function parse_endfullblock($string) {
      return 'endif;';
    }

    protected function parse_if($string) {
      return 'if(' . $string . '):';
    }
    protected function parse_endif($string) {
      return 'endif;';
    }

    protected function parse_for($string) {
      return 'for(' . $string . '):';
    }
    protected function parse_endfor($string) {
      return 'endfor;';
    }

    protected function parse_foreach($string) {
      return 'foreach(' . $string . '):';
    }
    protected function parse_endforeach($string) {
      return 'endforeach;';
    }

    protected function parse_while($string) {
      return 'while(' . $string . '):';
    }
    protected function parse_endwhile($string) {
      return 'endwhile;';
    }

    protected function parseBracket($bracket) {
      foreach($this->patterns as $name => $pattern) {
        if(preg_match($pattern, $bracket[1], $match)) {
          $instructions = call_user_func(array($this, 'parse_' . $name), isset($match[1]) ? $match[1] : '');
          if($instructions) {
            return '<?php ' . $instructions . ' ?>';
          } else {
            return '';
          }
        }
      }
      return $bracket[0];
    }

    protected function getPattern($l, $r, $block = true) {
      if($block) {
        $regex = '(?<!%3$s)(?:%3$s%3$s)*%1$s\s*((?:.(?!(?<!%3$s)(%3$s%3$s)*%2$s))*.(?<!%3$s)(?:%3$s%3$s)*)(?<!\s)\s*%2$s';
      } else {
        $regex = '(?<!%3$s)(?:%3$s%3$s)*%1$s\s*%2$s';
      }
      return sprintf($regex, $l, $r, $this->escape);
    }
    
  }

?>