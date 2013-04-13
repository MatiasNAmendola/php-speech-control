<?php
class tools {
  public function ip() {
    return $_SERVER['REMOTE_ADDR'];
  }
  
  public function timeoutput() {
    return date('G').' Uhr und '.(date('i')*1).' Minuten';
  }
  
  public function name($name) {
    $_SESSION['name'] = $name;
  }
  
  public function getname() {
    $name = $_SESSION['name'];
    if (!empty($name)) {
      return 'Ich bin nicht vergesslich. Dein Name ist '.$name.'.';
    } else {
      return 'Du hast mir deinen Namen noch nicht gesagt.';
    }
  }
  
  public function clearname() {
    $_SESSION['name'] = NULL;
    unset($_SESSION['name']);
  }
}
