<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->customRepositoryClassName = 'AppBundle\Repository\OOPsRepository';
$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
$metadata->mapField(array(
   'fieldName' => 'id',
   'type' => 'integer',
   'id' => true,
   'columnName' => 'id',
  ));
$metadata->mapField(array(
   'columnName' => 'OOPsID',
   'fieldName' => 'oOPsID',
   'type' => 'integer',
   'unique' => true,
  ));
$metadata->mapField(array(
   'columnName' => 'binSerial',
   'fieldName' => 'binSerial',
   'type' => 'string',
   'length' => '10',
  ));
$metadata->mapField(array(
   'columnName' => 'problemType',
   'fieldName' => 'problemType',
   'type' => 'string',
   'length' => '20',
  ));
$metadata->mapField(array(
   'columnName' => 'status',
   'fieldName' => 'status',
   'type' => 'string',
   'length' => '20',
  ));
$metadata->mapField(array(
   'columnName' => 'description',
   'fieldName' => 'description',
   'type' => 'string',
   'length' => '250',
   'nullable' => true,
  ));
$metadata->mapField(array(
   'columnName' => 'image',
   'fieldName' => 'image',
   'type' => 'object',
   'nullable' => true,
   'unique' => true,
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);