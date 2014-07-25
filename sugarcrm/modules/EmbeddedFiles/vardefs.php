<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$dictionary['EmbeddedFile'] = array(
    'table' => 'embedded_files',
    'audited' => false,
    'activity_enabled' => false,
    'comment' => 'Files for KBSContent body.',
    'fields' => array(
        'filename' =>
            array(
                'name' => 'filename',
                'vname' => 'LBL_FILENAME',
                'type' => 'file',
                'dbType' => 'varchar',
                'len' => '255',
                'importable' => false,
            ),
        'file_mime_type' =>
            array(
                'name' => 'file_mime_type',
                'vname' => 'LBL_FILE_MIME_TYPE',
                'type' => 'varchar',
                'len' => '100',
                'importable' => false,
            ),
    ),
    'relationships' => array(),
    'duplicate_check' => array(
        'enabled' => false,
    ),
);

VardefManager::createVardef(
    'EmbeddedFiles',
    'EmbeddedFile',
    array(
        'basic',
    )
);
