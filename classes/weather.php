<?php
class weather {
  public function today($location) {
    return "Die Wetter API ist nicht mehr verfügbar.";
    $location = urlencode($location);
    $api = simplexml_load_string(utf8_encode(file_get_contents('http://www.google.com/ig/api?weather='.$location.'&hl=de')));
    return $api->weather->current_conditions->condition->attributes()->data.' mit '.$api->weather->current_conditions->temp_c->attributes()->data.' Grad Celsius';
  }
  
  public function tomorrow($location) {
    return "Die Wetter API ist nicht mehr verfügbar.";
    $location = urlencode($location);
    $api = simplexml_load_string(utf8_encode(file_get_contents('http://www.google.com/ig/api?weather='.$location.'&hl=de')));
    $conditions = array();
    $temp = array();
    foreach ($api->weather->forecast_conditions as $weather) {
      $conditions[] = $weather->condition->attributes()->data;
      $temp[] = $weather->low->attributes()->data.' bis maximal '.$weather->high->attributes()->data.' Grad Celsius';
    }
    return $conditions[0].' mit '.$temp[0];
  }

  public function dayaftertomorrow($location) {
    return "Die Wetter API ist nicht mehr verfügbar.";
    $location = urlencode($location);
    $api = simplexml_load_string(utf8_encode(file_get_contents('http://www.google.com/ig/api?weather='.$location.'&hl=de')));
    $conditions = array();
    $temp = array();
    foreach ($api->weather->forecast_conditions as $weather) {
      $conditions[] = $weather->condition->attributes()->data;
      $temp[] = $weather->low->attributes()->data.' bis maximal '.$weather->high->attributes()->data.' Grad Celsius';
    }
    return $conditions[1].' mit '.$temp[1];
  }
}