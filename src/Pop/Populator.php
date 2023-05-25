<?php

namespace Civi\Pop;

class Populator {

  function __construct($entityStore, $optionStore){
    $this->entityStore = $entityStore;
    $this->optionStore = $optionStore;
  }

  function relationshipFields($entity, &$fields){

    // if no relationship type has been specified, get one
    if(!isset($fields['relationship_type_id'])){
      $relationshipType = $this->entityStore->getRandom('RelationshipType');
    }else{
      $relationshipType = $this->entityStore->getSpecific('RelationshipType', NULL, $fields['relationship_type_id']);
    }
    $fields['relationship_type_id']=$relationshipType['id'];
    $fields['contact_id_a'] = $this->entityStore->getRandom('Contact', array('return' => 'id', 'contact_type' => $relationshipType['contact_type_a']))['id'];
    $fields['contact_id_b'] = $this->entityStore->getRandom('Contact', array('return' => 'id', 'contact_type' => $relationshipType['contact_type_b']))['id'];
  }

  function eventId($entity, &$fields){

    // if no relationship type has been specified, get one
    if(!isset($fields['event_id'])){
      $fields['event_id'] = $this->entityStore->getRandom('Event', array('return' => 'id', 'is_template' => false))['id'];
    }
  }

  function groupId($entity, &$fields) {
    if(!isset($fields['group_id'])) {
      $fields['group_id'] = $this->entityStore->getRandom('Group', array('return' => 'id'))['id'];
    }
  }

}
