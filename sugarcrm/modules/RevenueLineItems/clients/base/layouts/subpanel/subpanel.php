<?php

$subpanels = array(
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'contact_link',
//    'LBL_LEADS_SUBPANEL_TITLE' => 'leads', // not currently in var defs
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'documents',
    'LBL_NOTES_SUBPANEL_TITLE' => 'notes',
);
$layout = MetaDataManager::getLayout("SubPanelLayout", $subpanels);
$viewdefs['RevenueLineItems']['base']['layout']['subpanel'] = $layout->getLayout();
