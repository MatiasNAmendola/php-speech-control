<?php
include_once(__DIR__.'/weather.php');
include_once(__DIR__.'/tools.php');
class voice {
  private $_patterns = array();
  private $_debug = FALSE;
  
  public function __construct() {
    session_start();

    $thankyou = array(
      'Bitte, gern geschehen',
      'Keine Ursache.',
      'Bitte, dafür bin ich ja da.',
      'Kein Problem.',
      'Freut mich, dass ich dir helfen konnte.',
    );
    $hello = array(
      'Hallihallo.',
      'Na du?',
      'Hai du.',
      'Auch hallo.',
      'Hallöchen.',
    );      
  
    $weather = new weather();
    $tools = new tools();
    $this->_patterns = array(
      array('/^(Hi|Hallo|Hello|Servus)$/i', $hello[rand(0, count($hello)-1)]),
      array('/^Webseite$/i', 'redirect:http://bp.la'),
      array('/geht (?:.*) dir/i', 'Ich denke, mir geht es gut. Und dir?'),
      array('/Ich heiße (.*)/i', 'Hallo %s! Schön dich kennen zu lernen. Ich bin Suri.', array($tools, 'name')),
      array('/Mein Name ist (.*)/i', 'Hallo %s! Schön dich kennen zu lernen. Ich bin Suri.', array($tools, 'name')),
      array('/Ich bin (.*)/i', 'Hallo %s! Schön dich kennen zu lernen. Ich bin Suri.', array($tools, 'name')),            
      array('/wie heiße ich/i', '%s', array($tools, 'getname')),   
      array('/wer bin ich/i', '%s', array($tools, 'getname')),         
      array('/wie (?:.*) mein name/i', '%s', array($tools, 'getname')),            
      array('/wie heißt du/i', 'Ich bin Suri.'),
      array('/wie (?:.*) dein name/i', 'Ich bin Suri.'),  
      array('/vergess(?:.*) mich/i', 'Ich habe dich jetzt vergessen. Wer warst du nochmal?', array($tools, 'clearname')),            
      array('/mir (?:.*) gut/i', 'Das freut mich.'),      
      array('/mir (?:.*) schlecht/i', 'Das ist schade. Gute Besserung.'),                              
    
      array('/(wiederhol|noch einmal)/i', $_SESSION['last_callfunc']),
      array('/google(?:.*)nach (.*)/i', 'redirect:http://www.google.de/search?q=%s'),
      array('/Wie(?:.*)IP/i', 'Deine IP-Adresse lautet: %s', array($tools, 'ip')),
      array('/Danke/i', $thankyou[rand(0, count($thankyou)-1)]),
      array('/Verarsch mich nicht/i', 'Das würde ich niemals tun.'),

      array('/google nach (.*)/i', 'Die Suche nach %s wird gestartet.'),
      array('/wie spät/i', 'Es ist jetzt %s.', array($tools, 'timeoutput')),
      array('/was kannst du/i', 'Hallo ich bin Suri und kann das Wetter für dich herausfinden.'),
      array('/kannst du (?:.*) mehr/i', 'Ich kann deine IP-Adresse ermitteln und eine google-suche starten.'),

      array('/übermorgen wetter in (,,,([0-9]+),([0-9]+))/i', 'Das Wetter hier ist übermorgen: %2$s.|%1$s', array($weather, 'dayaftertomorrow')),
      array('/morgen wetter in (,,,([0-9]+),([0-9]+))/i', 'Das morgige Wetter hier ist: %2$s.|%1$s', array($weather, 'tomorrow')),
      array('/wetter in ([\,]{3}.*)/i', 'Das Wetter hier ist zur Zeit %2$s.|%1$s', array($weather, 'today')),

      array('/übermorgen (?:.*) wetter in (.*)/i', 'Das Wetter übermorgen in %s ist: %s.', array($weather, 'dayaftertomorrow')),
      array('/wetter in (.*) übermorgen/i', 'Das Wetter übermorgen in %s ist: %s.', array($weather, 'dayaftertomorrow')),
      array('/wetter übermorgen in (.*)/i', 'Das Wetter übermorgen in %s ist: %s.', array($weather, 'dayaftertomorrow')),
      array('/morgen (?:.*) wetter in (.*)/i', 'Das morgige Wetter in %s ist: %s angekündigt.', array($weather, 'tomorrow')),
      array('/wetter morgen in (.*)/i', 'Das morgige Wetter in %s ist: %s angekündigt.', array($weather, 'tomorrow')),      
      array('/wetter in (.*) morgen/i', 'Das morgige Wetter in %s ist: %s angekündigt.', array($weather, 'tomorrow')),
      array('/wetter in (.*)/i', 'Das Wetter in %s ist zur Zeit %s.', array($weather, 'today')),

      array('/übermorgen (?:.*)wetter/i', 'location:übermorgen wetter'),
      array('/wetter (?:.*)übermorgen/i', 'location:übermorgen wetter'),
      array('/wetter (?:.*)morgen/i', 'location:morgen wetter'),
      array('/morgen (?:.*)wetter/i', 'location:morgen wetter'),
      array('/wetter/i', 'location:wetter'),
      
      array('/kannst du/', 'Das hat man mir nicht beigebracht.'),
      array('/bist du (.*)/', 'Ich weiß nicht ob ich %s bin.'),
      array('/bist du/', 'Ich bin Suri. Und basiere auf Google Apis.'),      
    );
  }
  
  public function run() {
    $result = '';
    $str = $this->_query();
    if (!empty($str)) {
      foreach ($this->_patterns as $p) {
        if (preg_match($p[0], $str)) {
          $matches = array();
          preg_match_all($p[0], $str, &$matches);
          // matches
          if (!empty($matches[1])) {
            // special getters (e.g. weather)
            if (!empty($p[2]) && is_array($p[2])) {
              $callfunc = call_user_func_array($p[2], $matches[1]);
              $_SESSION['last_callfunc'] = $callfunc;
              array_push($matches[1], $callfunc);
            }
            $result = vsprintf($p[1], $matches[1]);
          } else {
            if (!empty($p[2])) {
              // special getters (e.g. weather)
              if (is_array($p[2])) {
                $callfunc = call_user_func_array($p[2], array());
                $_SESSION['last_callfunc'] = $callfunc;
                $result = vsprintf($p[1], array($callfunc));
              } else {
                $result = vsprintf($p[1], array($p[2]));
              }
            } else {
              $result = $p[1];
            }
          }
          break;
        }
      }
      if (empty($result)) {
        $sorry = array(
          'Bitte etwas deutlicher sprechen.',
          'Ich habe das nicht verstanden.',
          'Bitte wiederhole das.',
          'Entschuldigung, das war zu undeutlich.',
        );
        if (!isset($_SESSION['fail']) || $_SESSION['fail'] > 5) {
          $_SESSION['fail'] = 0;
        }
        $_SESSION['fail']++;
        $result = $sorry[rand(0, count($sorry)-1)];
        if ($_SESSION['fail'] > 5) {
          $result = 'Ich kann zur Zeit nur begrenzt kommunizieren. Für mehr wende dich bitte an meinen Entwickler.';
        }
      }
      if (rand(0, 10) == 4 && !empty($_SESSION['name'])) {
        $result = $_SESSION['name'].', '.$result;
      }
      echo $result;
      exit;
    }
  }

  private function _query() {
    $result = '';
    if ($this->_debug) {
      $result = @$_GET['v'];
    } else {
      $result = @$_POST['v'];
    }
    return urldecode($result);
  }
}