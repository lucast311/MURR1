<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->customRepositoryClassName = 'AppBundle\Repository\CommunicationRepository';
$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
$metadata->mapField(array(
   'fieldName' => 'id',
   'type' => 'integer',
   'id' => true,
   'columnName' => 'id',
  ));
$metadata->mapField(array(
   'columnName' => 'date',
   'fieldName' => 'date',
   'type' => 'date',
  ));
$metadata->mapField(array(
   'columnName' => 'type',
   'fieldName' => 'type',
   'type' => 'string',
   'length' => '15',
  ));
$metadata->mapField(array(
   'columnName' => 'medium',
   'fieldName' => 'medium',
   'type' => 'string',
   'length' => '8',
  ));
$metadata->mapField(array(
   'columnName' => 'contact',
   'fieldName' => 'contact',
   'type' => 'integer',
   'nullable' => true,
  ));
$metadata->mapField(array(
   'columnName' => 'property',
   'fieldName' => 'property',
   'type' => 'integer',
   'nullable' => true,
  ));
$metadata->mapField(array(
   'columnName' => 'category',
   'fieldName' => 'category',
   'type' => 'string',
   'length' => '25',
  ));
$metadata->mapField(array(
   'columnName' => 'description',
   'fieldName' => 'description',
   'type' => 'text',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);