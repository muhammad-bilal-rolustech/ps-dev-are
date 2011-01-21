<?php
$dictionary["documents_cases"] = array (
  'true_relationship_type' => 'many-to-many',
  'relationships' => 
  array (
    'documents_cases' => 
    array (
      'lhs_module' => 'Documents',
      'lhs_table' => 'documents',
      'lhs_key' => 'id',
      'rhs_module' => 'Cases',
      'rhs_table' => 'cases',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'documents_cases',
      'join_key_lhs' => 'document_id',
      'join_key_rhs' => 'case_id',
    ),
  ),
  'table' => 'documents_cases',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'id',
      'type' => 'varchar',
      'len' => 36,
    ),
    1 => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    2 => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
      'required' => true,
    ),
    3 => 
    array (
      'name' => 'document_id',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'case_id',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'documents_casesspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'documents_cases_case_id',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'case_id',
        1 => 'document_id',
      ),
    ),
    2 => 
    array (
      'name' => 'documents_cases_document_id',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'document_id',
        1 => 'case_id',
      ),
    ),
  ),
);

