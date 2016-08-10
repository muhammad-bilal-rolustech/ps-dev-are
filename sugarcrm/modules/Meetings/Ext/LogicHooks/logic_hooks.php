<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$hook_array['after_relationship_update'][] = array(
    1,
    'AfterMeetingsRelationshipsUpdate',
    'include/CalendarEvents/CalendarEventsHookManager.php',
    'CalendarEventsHookManager',
    'afterRelationshipUpdate',
);
$hook_array['after_relationship_add'][] = array(
    1,
    'AfterMeetingsRelationshipsAdd',
    'include/CalendarEvents/CalendarEventsHookManager.php',
    'CalendarEventsHookManager',
    'afterRelationshipAdd',
);
$hook_array['after_relationship_delete'][] = array(
    1,
    'AfterMeetingsRelationshipsDelete',
    'include/CalendarEvents/CalendarEventsHookManager.php',
    'CalendarEventsHookManager',
    'afterRelationshipDelete',
);
