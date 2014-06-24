<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

 // $Id: MyMeetingsDashlet.data.php 57148 2010-06-25 16:23:33Z kjing $

global $current_user;

$dashletData['MyMeetingsDashlet']['searchFields'] = array('name'             => array('default' => ''),
                                                          'status'           => array('default' => array('Planned')),
                                                          'date_start'       => array('default' => ''),
                                                          'date_entered'     => array('default' => ''),                                                         
                                                          //BEGIN SUGARCRM flav=pro ONLY
                                                          'team_id'          => array('default' => '', 'label'=>'LBL_TEAMS'),
                                                          //END SUGARCRM flav=pro ONLY

														  //BEGIN SUGARCRM flav!=com ONLY
                                                          'type'  => array('default' => array('Sugar')),
														  //END SUGARCRM flav!=com ONLY

                                                          'assigned_user_id' => array('type'    => 'assigned_user_name', 
                                                                                      'default' => $current_user->name,
																					  'label'   => 'LBL_ASSIGNED_TO'),);
$dashletData['MyMeetingsDashlet']['columns'] = array('set_complete' => array('width'    => '1', 
                                                                             'label'    => 'LBL_LIST_CLOSE',
                                                                             'default'  => true,
                                                                             'sortable' => false,
                                                                             'related_fields' => array('status','recurring_source')),
													//BEGIN SUGARCRM flav!=com ONLY
													'join_meeting' => array('width'    => '1', 
                                                                             'label'    => 'LBL_LIST_JOIN_MEETING',
                                                                             'default'  => true,
                                                                             'sortable' => false,
                                                                             'noHeader' => true,
                                                                             'related_fields' => array('host_url')),
												    //END SUGARCRM flav!=com ONLY
                                                   'name' => array('width'   => '40', 
                                                                   'label'   => 'LBL_SUBJECT',
                                                                   'link'    => true,
                                                                   'default' => true),
                                                   'parent_name' => array('width' => '29', 
                                                                          'label' => 'LBL_LIST_RELATED_TO',
                                                                          'sortable' => false,
                                                                          'dynamic_module' => 'PARENT_TYPE',
                                                                          'link' => true,
                                                                          'id' => 'PARENT_ID',
                                                                          'ACLTag' => 'PARENT',
                                                                          'related_fields' => array('parent_id', 'parent_type'),
																		  'default' => true),
                                                   'duration' => array('width'    => '15', 
                                                                       'label'    => 'LBL_DURATION',
                                                                       'sortable' => false,
                                                                       'related_fields' => array('duration_hours', 'duration_minutes')),
                                                   'date_start' => array('width'   => '15', 
                                                                         'label'   => 'LBL_DATE',
                                                                         'default' => true,
                                                                         'related_fields' => array('time_start')),
											   'set_accept_links'=> array('width'    => '10', 
																		   'label'    => 'LBL_ACCEPT_THIS',
																		   'sortable' => false,
																		   'default' => true,
																		   'related_fields' => array('status')),
                                                   'status' => array('width'   => '8', 
                                                                     'label'   => 'LBL_STATUS'),
												   //BEGIN SUGARCRM flav!=com ONLY
                                                   'type' => array('width'   => '8', 
                                                                     'label'   => 'LBL_TYPE'),
												   //END SUGARCRM flav!=com ONLY
                                                   'date_entered' => array('width'   => '15', 
                                                                           'label'   => 'LBL_DATE_ENTERED'),
                                                   'date_modified' => array('width'   => '15', 
                                                                           'label'   => 'LBL_DATE_MODIFIED'),    
                                                   'created_by' => array('width'   => '8', 
                                                                         'label'   => 'LBL_CREATED'),
                                                   'assigned_user_name' => array('width'   => '8', 
                                                                                 'label'   => 'LBL_LIST_ASSIGNED_USER'),
                                                   //BEGIN SUGARCRM flav=pro ONLY
                                                   'team_name' => array('width'   => '15', 
                                                                        'label'   => 'LBL_LIST_TEAM'),
                                                   //END SUGARCRM flav=pro ONLY
                                                                         );


?>
