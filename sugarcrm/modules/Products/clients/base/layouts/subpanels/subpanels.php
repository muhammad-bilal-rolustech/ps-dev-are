<?php
$viewdefs['Products']['base']['layout']['subpanels'] = array (
  'components' => array (
    array (
      'layout' => 'subpanel',
      'label' => 'LBL_CONTACTS_SUBPANEL_TITLE',
      'context' => array (
        'link' => 'contact_link',
      ),
    ),
    array (
      'layout' => 'subpanel',
      'label' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
      'context' => array (
        'link' => 'documents',
      ),
    ),
    array (
      'layout' => 'subpanel',
      'label' => 'LBL_RELATED_PRODUCTS',
      'override_subpanel_list_view' => 'subpanel-for-products',
      'context' => array (
        'link' => 'related_products',
      ),
    ),
  ),
  'type' => 'subpanels',
  'span' => 12,
);
