<?php

namespace Civi\Pop;

/**
 * A place to cache CiviCRM entitiy field options to reduce API calls
 */
class OptionStore {

  private $store = array();

  function getRandomId($entity, $field){
    if(!isset($this->store[$entity][$field])){
      $this->store[$entity][$field] = Connection::api3($entity, 'getoptions', array(
        'sequential' => 1,
        'field' => $field,
      ))['values'];
    }
    return $this->store[$entity][$field][array_rand($this->store[$entity][$field])]['key'];
  }
}
