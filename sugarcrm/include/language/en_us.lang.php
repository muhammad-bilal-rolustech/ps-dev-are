<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: en_us.lang.php 57929 2010-08-25 21:52:39Z kjing $
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

//the left value is the key stored in the db and the right value is ie display value
//to translate, only modify the right value in each key/value pair
$app_list_strings = array (
//e.g. auf Deutsch 'Contacts'=>'Contakten',
  'language_pack_name' => 'US English',
  'moduleList' =>
  array (
    'Home' => 'Home',
    'Contacts' => 'Contacts',
    'Accounts' => 'Accounts',
    'Opportunities' => 'Opportunities',
    'Cases' => 'Cases',
    'Notes' => 'Notes',
    'Calls' => 'Calls',
    'Emails' => 'Emails',
    'Meetings' => 'Meetings',
    'Tasks' => 'Tasks',
    'Calendar' => 'Calendar',
  //BEGIN SUGARCRM flav!=sales ONLY
    'Leads' => 'Leads',
  //END SUGARCRM flav!=sales ONLY
    'Currencies' => 'Currencies',
    //BEGIN SUGARCRM flav=pro ONLY
    'Contracts'=>'Contracts',
    'Quotes' => 'Quotes',
    'Products' => 'Products',
    'ProductCategories' => 'Product Categories',
    'ProductTypes' => 'Product Types',
    'ProductTemplates' => 'Product Catalog',
  //END SUGARCRM flav=pro ONLY

  //BEGIN SUGARCRM flav=pro || flav=sales ONLY
    'Reports' => 'Reports',
    'Reports_1'=>'Reports',
  //END SUGARCRM flav=pro || flav=sales ONLY
  //BEGIN SUGARCRM flav=pro ONLY
    'Forecasts' => 'Forecasts',
    'ForecastSchedule'=>'Forecast Schedule',
    'ForecastWorksheets' => 'Forecast Worksheets',
    'ForecastManagerWorksheets' => 'Forecast Manager Worksheets',
    'MergeRecords'=>'Merge Records',
    'Quotas' => 'Quotas',
    'Teams' => 'Teams',
    'TeamNotices' => 'Team Notices',
    'Manufacturers' => 'Manufacturers',
    //END SUGARCRM flav=pro ONLY
    'Activities' => 'Activities',
  //BEGIN SUGARCRM flav!=sales ONLY
    'Bugs' => 'Bug Tracker',
    'Feeds' => 'RSS',
    'iFrames'=>'My Sites',
    'TimePeriods'=>'Time Periods',
    'TaxRates'=>'Tax Rates',
    'ContractTypes' => 'Contract Types',
    'Schedulers'=>'Schedulers',
    'Project'=>'Projects',
    'ProjectTask'=>'Project Tasks',
    'Campaigns'=>'Campaigns',
    'CampaignLog'=>'Campaign Log',
    'Documents'=>'Documents',
    'DocumentRevisions'=>'Document Revisions',
    //END SUGARCRM flav!=sales ONLY
    'Connectors'=>'Connectors',
    'Roles'=>'Roles',
    //BEGIN SUGARCRM flav=following ONLY
    'SugarFollowing'=>'SugarFollowing',
    //END SUGARCRM flav=following ONLY
    'Notifications'=>'Notifications',
    'Sync'=>'Sync',
//BEGIN SUGARCRM flav=ent ONLY
    'ReportMaker' => 'Advanced Reports',
    'DataSets' => 'Data Formats',
    'CustomQueries' => 'Custom Queries',
//END SUGARCRM flav=ent ONLY
//BEGIN SUGARCRM flav=pro ONLY
    'WorkFlow' => 'Workflow Definitions',
    'EAPM' => 'External Accounts',
    'Worksheet' => 'Worksheet',
//END SUGARCRM flav=pro ONLY
    'Users' => 'Users',
    'Employees' => 'Employees',
    'Administration' => 'Administration',
    'ACLRoles' => 'Roles',
    'InboundEmail' => 'Inbound Email',
    'Releases' => 'Releases',
    'Prospects' => 'Targets',
    'Queues' => 'Queues',
    'EmailMarketing' => 'Email Marketing',
    'EmailTemplates' => 'Email Templates',
    'SNIP' => "Email Archiving",
//BEGIN SUGARCRM flav!=sales ONLY
    'ProspectLists' => 'Target Lists',
//END SUGARCRM flav!=sales ONLY
    'SavedSearch' => 'Saved Searches',
    'UpgradeWizard' => 'Upgrade Wizard',
    'Trackers' => 'Trackers',
    'TrackerPerfs' => 'Tracker Performance',
    'TrackerSessions' => 'Tracker Sessions',
    'TrackerQueries' => 'Tracker Queries',
    'FAQ' => 'FAQ',
    'Newsletters' => 'Newsletters',
    'SugarFeed'=>'Sugar Feed',
    'KBDocuments' => 'Knowledge Base',
  	'SugarFavorites'=>'Favorites',
//BEGIN SUGARCRM flav=pro ONLY
	'PdfManager' => 'PDF Manager',
//END SUGARCRM flav=pro ONLY

    'OAuthKeys' => 'OAuth Consumer Keys',
    'OAuthTokens' => 'OAuth Tokens',
  ),

  'moduleListSingular' =>
  array (
    'Home' => 'Home',
  //BEGIN SUGARCRM flav!=sales ONLY
    'Dashboard' => 'Dashboard',
  //END SUGARCRM flav!=sales ONLY
    'Contacts' => 'Contact',
    'Accounts' => 'Account',
    'Opportunities' => 'Opportunity',
    'Cases' => 'Case',
    'Notes' => 'Note',
    'Calls' => 'Call',
    'Emails' => 'Email',
    'Meetings' => 'Meeting',
    'Tasks' => 'Task',
    'Calendar' => 'Calendar',
  //BEGIN SUGARCRM flav!=sales ONLY
    'Leads' => 'Lead',
  //END SUGARCRM flav!=sales ONLY
    //BEGIN SUGARCRM flav=pro ONLY
    'Contracts'=>'Contract',
    'Quotes' => 'Quote',
    'Products' => 'Product',
    'Reports' => 'Report',
    'Forecasts' => 'Forecast',
    'ForecastWorksheets' => 'Forecast Worksheet',
    'ForecastManagerWorksheets' => 'Forecast Manager Worksheet',
    'ForecastSchedule'=>'Forecast Schedule',
    'Quotas' => 'Quota',
    'Teams' => 'Team',
    //END SUGARCRM flav=pro ONLY
    'Activities' => 'Activity',
  //BEGIN SUGARCRM flav!=sales ONLY
    'Bugs' => 'Bug',
    'KBDocuments' => 'KBDocument',
    'Feeds' => 'RSS',
    'iFrames'=>'My Sites',
    'TimePeriods'=>'Time Period',
    'Project'=>'Project',
    'ProjectTask'=>'Project Task',
    'Prospects' => 'Target',
    'Campaigns'=>'Campaign',
    'Documents'=>'Document',
    //END SUGARCRM flav!=sales ONLY
    'SugarFollowing'=>'SugarFollowing',
    'Sync'=>'Sync',
//BEGIN SUGARCRM flav=pro ONLY
	'PdfManager' => 'PDF Manager',
//END SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav=ent ONLY
    'ReportMaker' => ' SweetReport',
//END SUGARCRM flav=ent ONLY
//BEGIN SUGARCRM flav=pro ONLY
    'WorkFlow' => 'Workflow',
    'EAPM' => 'External Account',
//END SUGARCRM flav=pro ONLY
    'Users' => 'User',
  'SugarFavorites'=>'SugarFavorites'

        ),

  'checkbox_dom'=> array(
    ''=>'',
    '1'=>'Yes',
    '2'=>'No',
  ),

  //e.g. en franï¿½ais 'Analyst'=>'Analyste',
  'account_type_dom' =>
  array (
    '' => '',
    'Analyst' => 'Analyst',
    'Competitor' => 'Competitor',
    'Customer' => 'Customer',
    'Integrator' => 'Integrator',
    'Investor' => 'Investor',
    'Partner' => 'Partner',
    'Press' => 'Press',
    'Prospect' => 'Prospect',
    'Reseller' => 'Reseller',
    'Other' => 'Other',
  ),
  //e.g. en espaï¿½ol 'Apparel'=>'Ropa',
  'industry_dom' =>
  array (
    '' => '',
    'Apparel' => 'Apparel',
    'Banking' => 'Banking',
    'Biotechnology' => 'Biotechnology',
    'Chemicals' => 'Chemicals',
    'Communications' => 'Communications',
    'Construction' => 'Construction',
    'Consulting' => 'Consulting',
    'Education' => 'Education',
    'Electronics' => 'Electronics',
    'Energy' => 'Energy',
    'Engineering' => 'Engineering',
    'Entertainment' => 'Entertainment',
    'Environmental' => 'Environmental',
    'Finance' => 'Finance',
    'Government' => 'Government',
    'Healthcare' => 'Healthcare',
    'Hospitality' => 'Hospitality',
    'Insurance' => 'Insurance',
    'Machinery' => 'Machinery',
    'Manufacturing' => 'Manufacturing',
    'Media' => 'Media',
    'Not For Profit' => 'Not For Profit',
    'Recreation' => 'Recreation',
    'Retail' => 'Retail',
    'Shipping' => 'Shipping',
    'Technology' => 'Technology',
    'Telecommunications' => 'Telecommunications',
    'Transportation' => 'Transportation',
    'Utilities' => 'Utilities',
    'Other' => 'Other',
  ),
  'lead_source_default_key' => 'Self Generated',
  'lead_source_dom' =>
  array (
    '' => '',
    'Cold Call' => 'Cold Call',
    'Existing Customer' => 'Existing Customer',
    'Self Generated' => 'Self Generated',
    'Employee' => 'Employee',
    'Partner' => 'Partner',
    'Public Relations' => 'Public Relations',
    'Direct Mail' => 'Direct Mail',
    'Conference' => 'Conference',
    'Trade Show' => 'Trade Show',
    'Web Site' => 'Web Site',
    'Word of mouth' => 'Word of mouth',
    'Email' => 'Email',
    'Campaign'=>'Campaign',
    //BEGIN SUGARCRM flav=ent ONLY
    'Support Portal User Registration' => 'Support Portal User Registration',
    //END SUGARCRM flav=ent ONLY
    'Other' => 'Other',
  ),
  'opportunity_type_dom' =>
  array (
    '' => '',
    'Existing Business' => 'Existing Business',
    'New Business' => 'New Business',
  ),
  'roi_type_dom' =>
    array (
    'Revenue' => 'Revenue',
    'Investment'=>'Investment',
    'Expected_Revenue'=>'Expected Revenue',
    'Budget'=>'Budget',

  ),
  //Note:  do not translate opportunity_relationship_type_default_key
//       it is the key for the default opportunity_relationship_type_dom value
  'opportunity_relationship_type_default_key' => 'Primary Decision Maker',
  'opportunity_relationship_type_dom' =>
  array (
    '' => '',
    'Primary Decision Maker' => 'Primary Decision Maker',
    'Business Decision Maker' => 'Business Decision Maker',
    'Business Evaluator' => 'Business Evaluator',
    'Technical Decision Maker' => 'Technical Decision Maker',
    'Technical Evaluator' => 'Technical Evaluator',
    'Executive Sponsor' => 'Executive Sponsor',
    'Influencer' => 'Influencer',
    'Other' => 'Other',
  ),
  //BEGIN SUGARCRM flav!=sales ONLY
  //Note:  do not translate case_relationship_type_default_key
//       it is the key for the default case_relationship_type_dom value
  'case_relationship_type_default_key' => 'Primary Contact',
  'case_relationship_type_dom' =>
  array (
    '' => '',
    'Primary Contact' => 'Primary Contact',
    'Alternate Contact' => 'Alternate Contact',
  ),
  //END SUGARCRM flav!=sales ONLY
  'payment_terms' =>
  array (
    '' => '',
    'Net 15' => 'Net 15',
    'Net 30' => 'Net 30',
  ),
  'sales_stage_default_key' => 'Prospecting',
  'fts_type' => array (
      '' => '',
      'Elastic' => 'elasticsearch'
  ),
  'sales_stage_dom' =>
  array (
    'Prospecting' => 'Prospecting',
    'Qualification' => 'Qualification',
    'Needs Analysis' => 'Needs Analysis',
    'Value Proposition' => 'Value Proposition',
    'Id. Decision Makers' => 'Id. Decision Makers',
    'Perception Analysis' => 'Perception Analysis',
    'Proposal/Price Quote' => 'Proposal/Price Quote',
    'Negotiation/Review' => 'Negotiation/Review',
    'Closed Won' => 'Closed Won',
    'Closed Lost' => 'Closed Lost',
  ),

  'commit_stage_binary_dom' => array (
    'include' => 'Include',
    'pipeline' => 'Pipeline',
  ),

  'commit_stage_dom' =>
  array (
    'include' => 'Include',
    'exclude' => 'Exclude',
    'upside'  => 'Upside',
  ),

  //The n-option for commit_stage dropdowns
  'commit_stage_expanded_dom' =>
  array (
    'include' => 'Include',
    'exclude' => 'Exclude',
    'stretch' => 'Stretch',
    'risk' => 'Risk',
  ),

  'in_total_group_stages' => array (
    'Draft' => 'Draft',
    'Negotiation' => 'Negotiation',
    'Delivered' => 'Delivered',
    'On Hold' => 'On Hold',
    'Confirmed' => 'Confirmed',
    'Closed Accepted' => 'Closed Accepted',
    'Closed Lost' => 'Closed Lost',
    'Closed Dead' => 'Closed Dead',
  ),
  'sales_probability_dom' => // keys must be the same as sales_stage_dom
  array (
    'Prospecting' => '10',
    'Qualification' => '20',
    'Needs Analysis' => '25',
    'Value Proposition' => '30',
    'Id. Decision Makers' => '40',
    'Perception Analysis' => '50',
    'Proposal/Price Quote' => '65',
    'Negotiation/Review' => '80',
    'Closed Won' => '100',
    'Closed Lost' => '0',
  ),
  'activity_dom' =>
  array (
    'Call' => 'Call',
    'Meeting' => 'Meeting',
    'Task' => 'Task',
    'Email' => 'Email',
    'Note' => 'Note',
  ),
  'salutation_dom' =>
      array (
        '' => '',
        'Mr.' => 'Mr.',
        'Ms.' => 'Ms.',
        'Mrs.' => 'Mrs.',
        'Dr.' => 'Dr.',
        'Prof.' => 'Prof.',
      ),
  //time is in seconds; the greater the time the longer it takes;
  'reminder_max_time' => 90000,
  'reminder_time_options' => array( 60=> '1 minute prior',
                                  300=> '5 minutes prior',
                                  600=> '10 minutes prior',
                                  900=> '15 minutes prior',
                                  1800=> '30 minutes prior',
                                  3600=> '1 hour prior',
                                  7200 => '2 hours prior',
                                  10800 => '3 hours prior',
                                  18000 => '5 hours prior',
                                  86400 => '1 day prior',
                                 ),

  'task_priority_default' => 'Medium',
  'task_priority_dom' =>
  array (
    'High' => 'High',
    'Medium' => 'Medium',
    'Low' => 'Low',
  ),
  'task_status_default' => 'Not Started',
  'task_status_dom' =>
  array (
    'Not Started' => 'Not Started',
    'In Progress' => 'In Progress',
    'Completed' => 'Completed',
    'Pending Input' => 'Pending Input',
    'Deferred' => 'Deferred',
  ),
  'meeting_status_default' => 'Planned',
  'meeting_status_dom' =>
  array (
    'Planned' => 'Planned',
    'Held' => 'Held',
    'Not Held' => 'Not Held',
  ),
  'extapi_meeting_password' =>
  array (
      'WebEx' => 'WebEx',
  ),
  'meeting_type_dom' =>
   array (
      'Other' => 'Other',
      'Sugar' => 'SugarCRM',
   ),
  'call_status_default' => 'Planned',
  'call_status_dom' =>
  array (
    'Planned' => 'Planned',
    'Held' => 'Held',
    'Not Held' => 'Not Held',
  ),
  'call_direction_default' => 'Outbound',
  'call_direction_dom' =>
  array (
    'Inbound' => 'Inbound',
    'Outbound' => 'Outbound',
  ),
  //BEGIN SUGARCRM flav!=sales ONLY
  'lead_status_dom' =>
  array (
    '' => '',
    'New' => 'New',
    'Assigned' => 'Assigned',
    'In Process' => 'In Process',
    'Converted' => 'Converted',
    'Recycled' => 'Recycled',
    'Dead' => 'Dead',
  ),
  //END SUGARCRM flav!=sales ONLY
   'gender_list' =>
  array (
    'male' => 'Male',
    'female' => 'Female',
  ),
  //BEGIN SUGARCRM flav!=sales ONLY
  //Note:  do not translate case_status_default_key
//       it is the key for the default case_status_dom value
  'case_status_default_key' => 'New',
  'case_status_dom' =>
  array (
    'New' => 'New',
    'Assigned' => 'Assigned',
    'Closed' => 'Closed',
    'Pending Input' => 'Pending Input',
    'Rejected' => 'Rejected',
    'Duplicate' => 'Duplicate',
  ),
  'case_priority_default_key' => 'P2',
  'case_priority_dom' =>
  array (
    'P1' => 'High',
    'P2' => 'Medium',
    'P3' => 'Low',
  ),
  //END SUGARCRM flav!=sales ONLY
  'user_type_dom' =>
  array (
    'RegularUser' => 'Regular User',
  //BEGIN SUGARCRM flav=sales ONLY
    'UserAdministrator' => 'User Administrator',
  //END SUGARCRM flav=sales ONLY
    'Administrator' => 'Administrator',
  ),
  'user_status_dom' =>
  array (
    'Active' => 'Active',
    'Inactive' => 'Inactive',
  ),
  'employee_status_dom' =>
  array (
    'Active' => 'Active',
    'Terminated' => 'Terminated',
    'Leave of Absence' => 'Leave of Absence',
  ),
  'messenger_type_dom' =>
  array (
    '' => '',
    'MSN' => 'MSN',
    'Yahoo!' => 'Yahoo!',
    'AOL' => 'AOL',
  ),
//BEGIN SUGARCRM flav!=sales ONLY
    'project_task_priority_options' => array (
        'High' => 'High',
        'Medium' => 'Medium',
        'Low' => 'Low',
    ),
    'project_task_priority_default' => 'Medium',

    'project_task_status_options' => array (
        'Not Started' => 'Not Started',
        'In Progress' => 'In Progress',
        'Completed' => 'Completed',
        'Pending Input' => 'Pending Input',
        'Deferred' => 'Deferred',
    ),
    'project_task_utilization_options' => array (
        '0' => 'none',
        '25' => '25',
        '50' => '50',
        '75' => '75',
        '100' => '100',
    ),

    'project_status_dom' => array (
        'Draft' => 'Draft',
        'In Review' => 'In Review',
        'Published' => 'Published',
    ),
    'project_status_default' => 'Draft',

    'project_duration_units_dom' => array (
        'Days' => 'Days',
        'Hours' => 'Hours',
    ),

    'project_priority_options' => array (
        'High' => 'High',
        'Medium' => 'Medium',
        'Low' => 'Low',
    ),
    'project_priority_default' => 'Medium',
//END SUGARCRM flav!=sales ONLY
  //Note:  do not translate record_type_default_key
//       it is the key for the default record_type_module value
  'record_type_default_key' => 'Accounts',
  'record_type_display' =>
  array (
    '' => '',
    'Accounts' => 'Account',
    'Opportunities' => 'Opportunity',
  //BEGIN SUGARCRM flav!=sales ONLY
    'Cases' => 'Case',
    'Leads' => 'Lead',
  //END SUGARCRM flav!=sales ONLY
    'Contacts' => 'Contacts', // cn (11/22/2005) added to support Emails
    //BEGIN SUGARCRM flav=pro ONLY
    'ProductTemplates' => 'Product',
    'Quotes' => 'Quote',

    //END SUGARCRM flav=pro ONLY

    //BEGIN SUGARCRM flav!=sales ONLY

    'Bugs' => 'Bug',
    'Project' => 'Project',

    'Prospects' => 'Target',
    'ProjectTask' => 'Project Task',
    //END SUGARCRM flav!=sales ONLY

    //BEGIN SUGARCRM flav=int ONLY

    'Project2' => 'Project2',

    //END SUGARCRM flav=int ONLY

    'Tasks' => 'Task',

  ),

  'record_type_display_notes' =>
  array (
    'Accounts' => 'Account',
    'Contacts' => 'Contact',
    'Opportunities' => 'Opportunity',
    'Tasks' => 'Task',
    //BEGIN SUGARCRM flav=pro ONLY
    'ProductTemplates' => 'Product Catalog',
    'Quotes' => 'Quote',
    'Products' => 'Product',
    'Contracts' => 'Contract',
    //END SUGARCRM flav=pro ONLY
    'Emails' => 'Email',

//BEGIN SUGARCRM flav!=sales ONLY
    'Bugs' => 'Bug',
    'Project' => 'Project',
    'ProjectTask' => 'Project Task',
    'Prospects' => 'Target',
    'Cases' => 'Case',
    'Leads' => 'Lead',
//END SUGARCRM flav!=sales ONLY

    'Meetings' => 'Meeting',
    'Calls' => 'Call',
  ),

  'parent_type_display' =>
  array (
    'Accounts' => 'Account',
    'Contacts' => 'Contact',
    'Tasks' => 'Task',
    'Opportunities' => 'Opportunity',


    //BEGIN SUGARCRM flav=pro ONLY

    'Products' => 'Product',

    'Quotes' => 'Quote',
    //END SUGARCRM flav=pro ONLY

    //BEGIN SUGARCRM flav!=sales ONLY
    'Bugs' => 'Bug Tracker',
    'Cases' => 'Case',
    'Leads' => 'Lead',

    'Project' => 'Project',
    'ProjectTask' => 'Project Task',

    'Prospects' => 'Target',
    //END SUGARCRM flav!=sales ONLY

  ),

  //BEGIN SUGARCRM flav=pro ONLY
  'product_status_default_key' => 'Ship',
  'product_status_quote_key' => 'Quotes',
  'product_status_dom' =>
  array (
    'Quotes' => 'Quoted',
    'Orders' => 'Ordered',
    'Ship' => 'Shipped',
  ),


  'pricing_formula_default_key' => 'Fixed',
  'pricing_formula_dom' =>
  array (
    'Fixed' => 'Fixed Price',
    'ProfitMargin' => 'Profit Margin',
    'PercentageMarkup' => 'Markup over Cost',
    'PercentageDiscount' => 'Discount from List',
    'IsList' => 'Same as List',
  ),
  'product_template_status_dom' =>
  array (
    'Available' => 'In Stock',
    'Unavailable' => 'Out Of Stock',
  ),
  'tax_class_dom' =>
  array (
    'Taxable' => 'Taxable',
    'Non-Taxable' => 'Non-Taxable',
  ),
  'support_term_dom' =>
  array (
    '+6 months' => 'Six months',
    '+1 year' => 'One year',
    '+2 years' => 'Two years',
  ),

  'quote_type_dom' =>
  array (
    'Quotes' => 'Quote',
    'Orders' => 'Order',
  ),
  'default_quote_stage_key' => 'Draft',
  'quote_stage_dom' =>
  array (
    'Draft' => 'Draft',
    'Negotiation' => 'Negotiation',
    'Delivered' => 'Delivered',
    'On Hold' => 'On Hold',
    'Confirmed' => 'Confirmed',
    'Closed Accepted' => 'Closed Accepted',
    'Closed Lost' => 'Closed Lost',
    'Closed Dead' => 'Closed Dead',
  ),
  'default_order_stage_key' => 'Pending',
  'order_stage_dom' =>
  array (
    'Pending' => 'Pending',
    'Confirmed' => 'Confirmed',
    'On Hold' => 'On Hold',
    'Shipped' => 'Shipped',
    'Cancelled' => 'Cancelled',
  ),

//Note:  do not translate quote_relationship_type_default_key
//       it is the key for the default quote_relationship_type_dom value
  'quote_relationship_type_default_key' => 'Primary Decision Maker',
  'quote_relationship_type_dom' =>
  array (
    '' => '',
    'Primary Decision Maker' => 'Primary Decision Maker',
    'Business Decision Maker' => 'Business Decision Maker',
    'Business Evaluator' => 'Business Evaluator',
    'Technical Decision Maker' => 'Technical Decision Maker',
    'Technical Evaluator' => 'Technical Evaluator',
    'Executive Sponsor' => 'Executive Sponsor',
    'Influencer' => 'Influencer',
    'Other' => 'Other',
  ),
  'layouts_dom' =>
  array (
    'Standard' => 'Quote',
    'Invoice' => 'Invoice',
  ),
  //END SUGARCRM flav=PRO ONLY
  //BEGIN SUGARCRM flav!=sales ONLY
  'issue_priority_default_key' => 'Medium',
  'issue_priority_dom' =>
  array (
    'Urgent' => 'Urgent',
    'High' => 'High',
    'Medium' => 'Medium',
    'Low' => 'Low',
  ),
  'issue_resolution_default_key' => '',
  'issue_resolution_dom' =>
  array (
    '' => '',
    'Accepted' => 'Accepted',
    'Duplicate' => 'Duplicate',
    'Closed' => 'Closed',
    'Out of Date' => 'Out of Date',
    'Invalid' => 'Invalid',
  ),

  'issue_status_default_key' => 'New',
  'issue_status_dom' =>
  array (
    'New' => 'New',
    'Assigned' => 'Assigned',
    'Closed' => 'Closed',
    'Pending' => 'Pending',
    'Rejected' => 'Rejected',
  ),

  'bug_priority_default_key' => 'Medium',
  'bug_priority_dom' =>
  array (
    'Urgent' => 'Urgent',
    'High' => 'High',
    'Medium' => 'Medium',
    'Low' => 'Low',
  ),
   'bug_resolution_default_key' => '',
  'bug_resolution_dom' =>
  array (
    '' => '',
    'Accepted' => 'Accepted',
    'Duplicate' => 'Duplicate',
    'Fixed' => 'Fixed',
    'Out of Date' => 'Out of Date',
    'Invalid' => 'Invalid',
    'Later' => 'Later',
  ),
  'bug_status_default_key' => 'New',
  'bug_status_dom' =>
  array (
    'New' => 'New',
    'Assigned' => 'Assigned',
    'Closed' => 'Closed',
    'Pending' => 'Pending',
    'Rejected' => 'Rejected',
  ),
   'bug_type_default_key' => 'Bug',
  'bug_type_dom' =>
  array (
    'Defect' => 'Defect',
    'Feature' => 'Feature',
  ),
 'case_type_dom' =>
  array (
    'Administration' => 'Administration',
    'Product' => 'Product',
    'User' => 'User',
  ),

  'source_default_key' => '',
  'source_dom' =>
  array (
    '' => '',
    'Internal' => 'Internal',
    'Forum' => 'Forum',
    'Web' => 'Web',
    'InboundEmail' => 'Email'
  ),

  'product_category_default_key' => '',
  'product_category_dom' =>
  array (
    '' => '',
    'Accounts' => 'Accounts',
    'Activities' => 'Activities',
    'Bug Tracker' => 'Bug Tracker',
    'Calendar' => 'Calendar',
    'Calls' => 'Calls',
    'Campaigns' => 'Campaigns',
    'Cases' => 'Cases',
    'Contacts' => 'Contacts',
    'Currencies' => 'Currencies',
  'Dashboard' => 'Dashboard',
  'Documents' => 'Documents',
    'Emails' => 'Emails',
    'Feeds' => 'Feeds',
    'Forecasts' => 'Forecasts',
    'Help' => 'Help',
    'Home' => 'Home',
  'Leads' => 'Leads',
  'Meetings' => 'Meetings',
    'Notes' => 'Notes',
    'Opportunities' => 'Opportunities',
    'Outlook Plugin' => 'Outlook Plugin',
    //BEGIN SUGARCRM flav=pro ONLY
    'Product Catalog' => 'Product Catalog',
    'Products' => 'Products',
    //END SUGARCRM flav=pro ONLY
    'Projects' => 'Projects',
    'Quotes' => 'Quotes',
    'Releases' => 'Releases',
    'RSS' => 'RSS',
    'Studio' => 'Studio',
    'Upgrade' => 'Upgrade',
    'Users' => 'Users',
  ),
  /*Added entries 'Queued' and 'Sending' for 4.0 release..*/
  'campaign_status_dom' =>
  array (
        '' => '',
        'Planning' => 'Planning',
        'Active' => 'Active',
        'Inactive' => 'Inactive',
        'Complete' => 'Complete',
        'In Queue' => 'In Queue',
        'Sending'=> 'Sending',
  ),
  'campaign_type_dom' =>
  array (
        '' => '',
        'Telesales' => 'Telesales',
        'Mail' => 'Mail',
        'Email' => 'Email',
        'Print' => 'Print',
        'Web' => 'Web',
        'Radio' => 'Radio',
        'Television' => 'Television',
        'NewsLetter' => 'Newsletter',
        ),

  'newsletter_frequency_dom' =>
  array (
        '' => '',
        'Weekly' => 'Weekly',
        'Monthly' => 'Monthly',
        'Quarterly' => 'Quarterly',
        'Annually' => 'Annually',
        ),
  //END SUGARCRM flav!=sales ONLY

  'notifymail_sendtype' =>
  array (
    'SMTP' => 'SMTP',
  ),
      'dom_cal_month_long'=>array(
                '0'=>"",
                '1'=>"January",
                '2'=>"February",
                '3'=>"March",
                '4'=>"April",
                '5'=>"May",
                '6'=>"June",
                '7'=>"July",
                '8'=>"August",
                '9'=>"September",
                '10'=>"October",
                '11'=>"November",
                '12'=>"December",
                ),
        'dom_cal_month_short'=>array(
                '0'=>"",
                '1'=>"Jan",
                '2'=>"Feb",
                '3'=>"Mar",
                '4'=>"Apr",
                '5'=>"May",
                '6'=>"Jun",
                '7'=>"Jul",
                '8'=>"Aug",
                '9'=>"Sep",
                '10'=>"Oct",
                '11'=>"Nov",
                '12'=>"Dec",
                ),
        'dom_cal_day_long'=>array(
                '0'=>"",
                '1'=>"Sunday",
                '2'=>"Monday",
                '3'=>"Tuesday",
                '4'=>"Wednesday",
                '5'=>"Thursday",
                '6'=>"Friday",
                '7'=>"Saturday",
                ),
        'dom_cal_day_short'=>array(
                '0'=>"",
                '1'=>"Sun",
                '2'=>"Mon",
                '3'=>"Tue",
                '4'=>"Wed",
                '5'=>"Thu",
                '6'=>"Fri",
                '7'=>"Sat",
        ),
    'dom_meridiem_lowercase'=>array(
                'am'=>"am",
                'pm'=>"pm"
        ),
    'dom_meridiem_uppercase'=>array(
                 'AM'=>'AM',
                 'PM'=>'PM'
        ),

    'dom_report_types'=>array(
                'tabular'=>'Rows and Columns',
                'summary'=>'Summation',
                'detailed_summary'=>'Summation with details',
                'Matrix' => 'Matrix',
        ),


    'dom_email_types'=> array(
        'out'       => 'Sent',
        'archived'  => 'Archived',
        'draft'     => 'Draft',
        'inbound'   => 'Inbound',
        'campaign'  => 'Campaign'
    ),
    'dom_email_status' => array (
        'archived'  => 'Archived',
        'closed'    => 'Closed',
        'draft'     => 'In Draft',
        'read'      => 'Read',
        'replied'   => 'Replied',
        'sent'      => 'Sent',
        'send_error'=> 'Send Error',
        'unread'    => 'Unread',
    ),
    'dom_email_archived_status' => array (
        'archived'  => 'Archived',
    ),

    'dom_email_server_type' => array(   ''          => '--None--',
                                        'imap'      => 'IMAP',
    ),
    'dom_mailbox_type'      => array(/*''           => '--None Specified--',*/
                                     'pick'     => '--None--',
                                     'createcase'  => 'Create Case',
                                     'bounce'   => 'Bounce Handling',
    ),
    'dom_email_distribution'=> array(''             => '--None--',
                                     'direct'       => 'Direct Assign',
                                     'roundRobin'   => 'Round-Robin',
                                     'leastBusy'    => 'Least-Busy',
    ),
    'dom_email_distribution_for_auto_create'=> array('roundRobin'   => 'Round-Robin',
                                                     'leastBusy'    => 'Least-Busy',
    ),
    'dom_email_errors'      => array(1 => 'Only select one user when Direct Assigning items.',
                                     2 => 'You must assign Only Checked Items when Direct Assigning items.',
    ),
    'dom_email_bool'        => array('bool_true' => 'Yes',
                                     'bool_false' => 'No',
    ),
    'dom_int_bool'          => array(1 => 'Yes',
                                     0 => 'No',
    ),
    'dom_switch_bool'       => array ('on' => 'Yes',
                                        'off' => 'No',
                                        '' => 'No', ),

    'dom_email_link_type'   => array(   'sugar'     => 'Sugar Email Client',
                                        'mailto'    => 'External Email Client'),


    'dom_email_editor_option'=> array(  ''          => 'Default Email Format',
                                        'html'      => 'HTML Email',
                                        'plain'     => 'Plain Text Email'),

    'schedulers_times_dom'  => array(   'not run'       => 'Past Run Time, Not Executed',
                                        'ready'         => 'Ready',
                                        'in progress'   => 'In Progress',
                                        'failed'        => 'Failed',
                                        'completed'     => 'Completed',
                                        'no curl'       => 'Not Run: No cURL available',
    ),

    'scheduler_status_dom' =>
        array (
        'Active' => 'Active',
        'Inactive' => 'Inactive',
        ),

    'scheduler_period_dom' =>
        array (
        'min' => 'Minutes',
        'hour' => 'Hours',
        ),
    //BEGIN SUGARCRM flav!=sales ONLY
    'forecast_schedule_status_dom' =>
    array (
    'Active' => 'Active',
    'Inactive' => 'Inactive',
  ),
    'forecast_type_dom' =>
    array (
    'Direct' => 'Direct',
    'Rollup' => 'Rollup',
  ),
    'document_category_dom' =>
    array (
    '' => '',
    'Marketing' => 'Marketing',
    'Knowledege Base' => 'Knowledge Base',
    'Sales' => 'Sales',
  ),

    'document_subcategory_dom' =>
    array (
    '' => '',
    'Marketing Collateral' => 'Marketing Collateral',
    'Product Brochures' => 'Product Brochures',
    'FAQ' => 'FAQ',
  ),

    'document_status_dom' =>
    array (
    'Active' => 'Active',
    'Draft' => 'Draft',
    'FAQ' => 'FAQ',
    'Expired' => 'Expired',
    'Under Review' => 'Under Review',
    'Pending' => 'Pending',
  ),
  'document_template_type_dom' =>
  array(
    ''=>'',
    'mailmerge'=>'Mail Merge',
    'eula'=>'EULA',
    'nda'=>'NDA',
    'license'=>'License Agreement',
  ),
    //END SUGARCRM flav!=sales ONLY
    'dom_meeting_accept_options' =>
    array (
    'accept' => 'Accept',
    'decline' => 'Decline',
    'tentative' => 'Tentative',
  ),
    'dom_meeting_accept_status' =>
    array (
    'accept' => 'Accepted',
    'decline' => 'Declined',
    'tentative' => 'Tentative',
    'none'      => 'None',
  ),
  //BEGIN SUGARCRM flav=ent ONLY
        'dataset_output_default_dom' =>
    array (
    'table' => 'Table',
  ),
    'report_maker_status_dom' =>
    array (
    'Single Module' => 'Single Module',
    'Multi Module' => 'Multi Module',
  ),
  'report_align_dom' =>
    array (
    'left' => 'Left',
    'center' => 'Center',
    'right' => 'Right',
  ),

    'width_type_dom' =>
    array (
    '%' => 'Percent (%)',
    'px' => 'Pixels (px)',
  ),
    'report_color_dom' =>
    array (
    '' => 'Default',
    'black' => 'Black',
    'green' => 'Green',
    'blue' => 'Blue',
    'red' => 'Red',
    'white' => 'White',
    'DarkGreen' => 'Dark Green',
    'LightGray' => 'Light Gray',
    'DodgerBlue' => 'Dodger Blue',
    'LightBlue' => 'Light Blue',
  ),
    'font_size_dom' =>
    array (
    '-5' => 'Smaller (-5)',
    '-4' => 'Smaller (-4)',
    '-3' => 'Smaller (-3)',
    '-2' => 'Smaller (-2)',
    '-1' => 'Smaller (-1)',
    'Default' => 'Default',
    '1' => 'Larger (+1)',
    '2' => 'Larger (+2)',
    '3' => 'Larger (+3)',
    '4' => 'Larger (+4)',
    '5' => 'Larger (+5)',
  ),
        'query_type_dom' =>
    array (
    'Main Query' => 'Main Query',
//  'Sub Query' => 'Sub Query',
//  'Reverse Sub Query' => 'Reverse Sub Query',
  ),
        'query_column_type_dom' =>
    array (
    'Display' => 'Display',
//  'Group By' => 'Group By',
    'Calculation' => 'Calculation',
  ),
        'query_display_type_dom' =>
    array (
    'Default' => 'Default',
    'Custom' => 'Custom',
    'Hidden' => 'Hidden',
  ),

            'query_groupby_qualifier_dom' =>
    array (
    'Day' => 'Day',
    'Week' => 'Week',
    'Month' => 'Month',
    'Quarter' => 'Quarter',
    'Year' => 'Year',
  ),

            'query_groupby_qualifier_qty_dom' =>
    array (
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7',
    '8' => '8',
    '9' => '9',
    '10' => '10',
    '12' => '12',
    '18' => '18',
    '24' => '24',
  ),

            'query_groupby_qualifier_start_dom' =>
    array (
    '0' => 'Now',
    '-1' => '-1',
    '-2' => '-2',
    '-3' => '-3',
    '-4' => '-4',
    '-5' => '-5',
    '-6' => '-6',
    '-7' => '-7',
    '-8' => '-8',
    '-9' => '-9',
    '-10' => '-10',
    '12' => '-12',
    '-18' => '-18',
    '-24' => '-24',
  ),
            'query_groupby_calc_type_dom' =>
    array (
    'SUM' => 'SUM',
    'AVG' => 'AVG',
    'COUNT' => 'Count',
    'STDDEV' => 'Standard Deviation',
    'VARIANCE' => 'Variance',
//  'Custom' => 'Custom',
  ),
            'query_groupby_type_dom' =>
    array (
    'Field' => 'Standard Field',
    'Time' => 'Time Interval',
//  'Custom' => 'Custom Group By',
  ),

  'query_groupby_axis_dom' =>
    array (
    'Rows' => 'Rows (Y-Axis)',
    'Columns' => 'Columns (X-Axis)',
  ),

  'query_calc_calc_type_dom' =>
    array (
    'SUM' => 'Sum (Total)',
    'AVG' => 'Average',
    'MAX' => 'Maximum',
    'MIN' => 'Minimum',
    'COUNT' => 'Count',
    'STDDEV' => 'Standard Deviation',
    'VARIANCE' => 'Variance',
  ),
  'query_calc_type_dom' =>
    array (
    'Standard' => 'Standard',
//  'Sub-Query' => 'Sub Query',
    'Math' => 'Math Calc',
  ),

  'query_calc_oper_dom' =>
      array (
    '+' => '(+) Plus',
    '-' => '(-) Minus',
    '*' => '(X) Multiplied By',
    '/' => '(/) Divided By',
  ),

    'query_calc_leftright_type_dom' =>
      array (
    'Field' => 'Field',
    'Value' => 'Value',
    'Group' => 'Group',
  ),


  'dataset_layout_type_dom' =>
      array (
    'Column' => 'Column',
    //'Row' => 'Row',
  ),

  'custom_layout_dom' =>
         array (
    'Disabled' => 'Disabled',
    'Enabled' => 'Enabled',
  ),
    'dataset_att_display_type_dom' =>
      array (
     'Normal' => 'Normal',
    'Scalar' => 'Scalar',
  ),
  'dataset_att_format_type_dom' =>
        array (
    'Text' => 'Text',
    'Accounting' => 'Accounting',
    'Date' => 'Date',
    'Datetime' => 'Datetime',
  ),
    'dataset_att_format_type_scalar_dom' =>
        array (
    'Year' => 'Year',
    'Quarter' => 'Quarter',
    'Month' => 'Month',
    'Week' => 'Week',
    'Day' => 'Day',
  ),
  'dataset_style_dom' =>
        array (
    'normal' => 'Normal',
    'bold' => 'Bold',
    'italic' => 'Italic',
  ),
//END SUGARCRM flav=ent ONLY
//BEGIN SUGARCRM flav=pro ONLY
  'query_calc_oper_dom' =>
      array (
    '+' => '(+) Plus',
    '-' => '(-) Minus',
    '*' => '(X) Multiplied By',
    '/' => '(/) Divided By',
  ),
  'wflow_type_dom' =>
        array (
    'Normal' => 'When record saved',
    'Time' => 'After time elapses',
  ),
  'mselect_type_dom' =>
        array (
    'Equals' => 'Is',
    'in' => 'Is One of',
  ),
  'mselect_multi_type_dom' =>
        array (
    'in' => 'Is One of',
    'not_in' => 'Is Not One of',
  ),
   'cselect_type_dom' =>
        array (
    'Equals' => 'Equals',
    'Does not Equal' => 'Does Not Equal',
  ),
   'dselect_type_dom' =>
        array (
    'Equals' => 'Equals',
    'Less Than' => 'Less Than',
    'More Than' => 'More Than',
    'Does not Equal' => 'Does not Equal',
  ),
   'bselect_type_dom' =>
        array (
    'bool_true' => 'Yes',
    'bool_false' => 'No',
  ),
    'bopselect_type_dom' =>
        array (
    'Equals' => 'Equals',
  ),
    'tselect_type_dom' =>
        array (
    '0'     =>  '0 hours',
    '14440' => '4 hours',
    '28800' => '8 hours',
    '43200' => '12 hours',
    '86400' => '1 day',
    '172800' => '2 days',
    '259200' => '3 days',
    '345600' => '4 days',
    '432000' => '5 days',
    '604800' => '1 week',
    '1209600' => '2 weeks',
    '1814400' => '3 weeks',
    '2592000' => '30 days',
    '5184000' => '60 days',
    '7776000' => '90 days',
    '10368000' => '120 days',
    '12960000' => '150 days',
    '15552000' => '180 days',
  ),
      'dtselect_type_dom' =>
        array (
    'More Than' => 'was more than',
    'Less Than' => 'is less than',
  ),
        'wflow_alert_type_dom' =>
        array (
    'Email' => 'Email',
    'Invite' => 'Invite',
  ),
        'wflow_source_type_dom' =>
        array (
    'Normal Message' => 'Normal Message',
    'Custom Template' => 'Custom Template',
    'System Default' => 'System Default',
  ),
          'wflow_user_type_dom' =>
        array (
    'current_user' => 'Current Users',
    'rel_user' => 'Related Users',
    'rel_user_custom' => 'Related Custom User',
    'specific_team' => 'Specific Team',
    'specific_role' => 'Specific Role',
    'specific_user' => 'Specific User',
  ),
          'wflow_array_type_dom' =>
        array (
    'future' => 'New Value',
    'past' => 'Old Value',
  ),
          'wflow_relate_type_dom' =>
        array (
    'Self' => 'User',
    'Manager' => "User's Manager",
  ),
    'wflow_address_type_dom' =>
        array (
    'to' => 'To:',
    'cc' => 'CC:',
    'bcc' => 'BCC:',
  ),
     'wflow_address_type_invite_dom' =>
        array (
    'to' => 'To:',
    'cc' => 'CC:',
    'bcc' => 'BCC:',
    'invite_only' => '(Invite Only)',
  ),
     'wflow_address_type_to_only_dom' =>
        array (
    'to' => 'To:',
  ),
    'wflow_action_type_dom' =>
        array (
    'update' => 'Update Record',
    'update_rel' => 'Update Related Record',
    'new' => 'New Record',
  ),
  'wflow_action_datetime_type_dom' =>
        array (
    'Triggered Date' => 'Triggered Date',
    'Existing Value' => 'Existing Value',
  ),
  'wflow_set_type_dom' =>
        array (
    'Basic' => 'Basic Options',
    'Advanced' => 'Advanced Options',
  ),
  'wflow_adv_user_type_dom' =>
        array (
    'assigned_user_id' => 'User assigned to triggered record',
    'modified_user_id' => 'User who last modified triggered record',
    'created_by' => 'User who created triggered record',
    'current_user' => 'Logged-in User',
  ),
  'wflow_adv_team_type_dom' =>
        array (
    'team_id' => 'Current Team of triggered Record',
    'current_team' => 'Team of Logged-in User',
  ),
  'wflow_adv_enum_type_dom' =>
        array (
    'retreat' => 'Move dropdown backwards by ',
    'advance' => 'Move dropdown forwards by ',
  ),
  'wflow_record_type_dom' =>
   array (
    'All' => 'New and Updated Records',
    'New' => 'New Records Only',
    'Update' => 'Updated Records Only',
  ),
  'wflow_rel_type_dom' =>
        array (
    'all' => 'All related',
    //'first' => 'The first related',
    'filter' => 'Filter related',
        ),
  'wflow_relfilter_type_dom' =>
        array (
    'all' => 'all related',
    'any' => 'any related',
        ),
        'wflow_fire_order_dom' => array('alerts_actions'=>'Alerts then Actions',
                                    'actions_alerts'=>'Actions then Alerts'),



//END SUGARCRM flav=pro ONLY
    'duration_intervals' => array('0'=>'00',
                                    '15'=>'15',
                                    '30'=>'30',
                                    '45'=>'45'),

    'repeat_type_dom' => array(
    	'' => 'None',
    	'Daily'	=> 'Daily',
	'Weekly' => 'Weekly',
	'Monthly' => 'Monthly',
	'Yearly' => 'Yearly',
    ),

    'repeat_intervals' => array(
        '' => '',
        'Daily' => 'day(s)',
        'Weekly' => 'week(s)',
        'Monthly' => 'month(s)',
        'Yearly' => 'year(s)',
    ),

    'duration_dom' => array(
    	'' => 'None',
    	'900' => '15 minutes',
	'1800' => '30 minutes',
	'2700' => '45 minutes',
	'3600' => '1 hour',
	'5400' => '1.5 hours',
	'7200' => '2 hours',
	'10800' => '3 hours',
	'21600' => '6 hours',
	'86400' => '1 day',
	'172800' => '2 days',
	'259200' => '3 days',
	'604800' => '1 week',
    ),

// deferred
/*// QUEUES MODULE DOMs
'queue_type_dom' => array(
    'Users' => 'Users',
//BEGIN SUGARCRM flav=pro ONLY
    'Teams' => 'Teams',
//END SUGARCRM flav=pro ONLY
    'Mailbox' => 'Mailbox',
),
*/
//BEGIN SUGARCRM flav!=sales ONLY
//prospect list type dom
  'prospect_list_type_dom' =>
  array (
    'default' => 'Default',
    'seed' => 'Seed',
    'exempt_domain' => 'Suppression List - By Domain',
    'exempt_address' => 'Suppression List - By Email Address',
    'exempt' => 'Suppression List - By Id',
    'test' => 'Test',
  ),

  'email_settings_num_dom' =>
  array(
        '10'    => '10',
        '20'    => '20',
        '50'    => '50'
    ),
  'email_marketing_status_dom' =>
  array (
    '' => '',
    'active'=>'Active',
    'inactive'=>'Inactive'
  ),

  'campainglog_activity_type_dom' =>
  array (
    ''=>'',
    'targeted' => 'Message Sent/Attempted',
    'send error'=>'Bounced Messages,Other',
    'invalid email'=>'Bounced Messages,Invalid Email',
    'link'=>'Click-thru Link',
    'viewed'=>'Viewed Message',
    'removed'=>'Opted Out',
    'lead'=>'Leads Created',
    'contact'=>'Contacts Created',
    'blocked'=>'Suppressed by address or domain',
  ),

  'campainglog_target_type_dom' =>
  array (
    'Contacts' => 'Contacts',
    'Users'=>'Users',
    'Prospects'=>'Targets',
    'Leads'=>'Leads',
    'Accounts'=>'Accounts',
  ),
//END SUGARCRM flav!=sales ONLY
  'merge_operators_dom' => array (
    'like'=>'Contains',
    'exact'=>'Exactly',
    'start'=>'Starts With',
  ),

  'custom_fields_importable_dom' => array (
    'true'=>'Yes',
    'false'=>'No',
    'required'=>'Required',
  ),

    'Elastic_boost_options' => array (
        '0' =>'Disabled',
        '1'=>'Low Boost',
        '2'=>'Medium Boost',
        '3'=>'High Boost',
    ),

  'custom_fields_merge_dup_dom'=> array (
        0=>'Disabled',
        1=>'Enabled',
//BEGIN SUGARCRM flav=pro ONLY
        2=>'In Filter',
        3=>'Default Selected Filter',
        4=>'Filter Only',
//END SUGARCRM flav=pro ONLY
  ),

  'navigation_paradigms' => array(
        'm'=>'Modules',
        'gm'=>'Grouped Modules',
  ),

//BEGIN SUGARCRM flav=pro ONLY

    // Contracts module enums

    'contract_status_dom' => array (
        'notstarted' => 'Not Started',
        'inprogress' => 'In Progress',
        'signed' => 'Signed',
    ),

    'contract_payment_frequency_dom' => array (
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'halfyearly' => 'Half yearly',
        'yearly' => 'Yearly',
    ),

    'contract_expiration_notice_dom' => array (
        '1' => '1 Day',
        '3' => '3 Days',
        '5' => '5 Days',
        '7' => '1 Week',
        '14' => '2 Weeks',
        '21' => '3 Weeks',
        '31' => '1 Month',
    ),

     'oc_status_dom' =>
     array (
     '' => '',
     'Active' => 'Active',
    'Inactive' => 'Inactive',
    ),


//END SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav!=sales ONLY

    'projects_priority_options' => array (
        'high'      => 'High',
        'medium'    => 'Medium',
        'low'       => 'Low',
    ),

    'projects_status_options' => array (
        'notstarted'    => 'Not Started',
        'inprogress'    => 'In Progress',
        'completed'     => 'Completed',
    ),
//END SUGARCRM flav!=sales ONLY
    // strings to pass to Flash charts
    'chart_strings' => array (
        'expandlegend'      => 'Expand Legend',
        'collapselegend'    => 'Collapse Legend',
        'clickfordrilldown' => 'Click for Drilldown',
        'drilldownoptions'  => 'Drill Down Options',
        'detailview'        => 'More Details...',
        'piechart'          => 'Pie Chart',
        'groupchart'        => 'Group Chart',
        'stackedchart'      => 'Stacked Chart',
        'barchart'      => 'Bar Chart',
        'horizontalbarchart'   => 'Horizontal Bar Chart',
        'linechart'         => 'Line Chart',
        'noData'            => 'Data not available',
        'print'       => 'Print',
        'pieWedgeName'      => 'sections',
    ),
    //BEGIN SUGARCRM flav=pro ONLY
    'pipeline_chart_dom' => array (
        'fun'   => 'Funnel',
        'hbar'  => 'Horizontal Bar',
    ),
    //END SUGARCRM flav=pro ONLY
    'release_status_dom' =>
    array (
        'Active' => 'Active',
        'Inactive' => 'Inactive',
    ),
    'email_settings_for_ssl' =>
    array (
        '0' => '',
        '1' => 'SSL',
        '2' => 'TLS',
    ),
    'import_enclosure_options' =>
    array (
        '\'' => 'Single Quote (\')',
        '"' => 'Double Quote (")',
        '' => 'None',
        'other' => 'Other:',
    ),
    'import_delimeter_options' =>
    array (
        ',' => ',',
        ';' => ';',
        '\t' => '\t',
        '.' => '.',
        ':' => ':',
        '|' => '|',
        'other' => 'Other:',
    ),
    'link_target_dom' =>
    array (
        '_blank' => 'New Window',
        '_self' => 'Same Window',
    ),
    'dashlet_auto_refresh_options' =>
    array (
        '-1'  => 'Do not auto-refresh',
        '30'  => 'Every 30 seconds',
        '60'  => 'Every 1 minute',
        '180'   => 'Every 3 minutes',
        '300'   => 'Every 5 minutes',
        '600'   => 'Every 10 minutes',
    ),
  'dashlet_auto_refresh_options_admin' =>
    array (
        '-1'  => 'Never',
        '30'  => 'Every 30 seconds',
        '60'  => 'Every 1 minute',
        '180'   => 'Every 3 minutes',
        '300'   => 'Every 5 minutes',
        '600'   => 'Every 10 minutes',
    ),
  'date_range_search_dom' =>
  array(
    '=' => 'Equals',
    'not_equal' => 'Not On',
    'greater_than' => 'After',
    'less_than' => 'Before',
    'last_7_days' => 'Last 7 Days',
    'next_7_days' => 'Next 7 Days',
    'last_30_days' => 'Last 30 Days',
    'next_30_days' => 'Next 30 Days',
    'last_month' => 'Last Month',
    'this_month' => 'This Month',
    'next_month' => 'Next Month',
    'last_year' => 'Last Year',
    'this_year' => 'This Year',
    'next_year' => 'Next Year',
    'between' => 'Is Between',
  ),
  'numeric_range_search_dom' =>
  array(
    '=' => 'Equals',
    'not_equal' => 'Does Not Equal',
    'greater_than' => 'Greater Than',
    'greater_than_equals' => 'Greater Than Or Equal To',
    'less_than' => 'Less Than',
    'less_than_equals' => 'Less Than Or Equal To',
    'between' => 'Is Between',
  ),
  'lead_conv_activity_opt' =>
  array(
        'copy' => 'Copy',
        'move' => 'Move',
        'donothing' => 'Do Nothing'
  ),
    'forecasts_chart_options_group' => array(
        'forecast' => 'Included In Forecast',
        'sales_stage' => 'Sales Stage',
        'probability' => 'Probability'
    ),
    'forecasts_chart_options_dataset' => array(
        'likely' => 'Likely',
        'best' => 'Best',
        'worst' => 'Worst'
    ),
);

$app_strings = array (
  'LBL_TOUR_NEXT' => 'Next',
  'LBL_TOUR_SKIP' => 'Skip',
  'LBL_TOUR_BACK' => 'Back',
  'LBL_TOUR_CLOSE' => 'Close',
  'LBL_TOUR_TAKE_TOUR' => 'Take the tour',
  'LBL_MY_AREA_LINKS' => 'My area links: ' /*for 508 compliance fix*/,
  'LBL_GETTINGAIR' => 'Getting Air' /*for 508 compliance fix*/,
  'LBL_WELCOMEBAR' => 'Welcome' /*for 508 compliance fix*/,
  'LBL_ADVANCEDSEARCH' => 'Advanced Search' /*for 508 compliance fix*/,
  'LBL_MOREDETAIL' => 'More Detail' /*for 508 compliance fix*/,
  'LBL_EDIT_INLINE' => 'Edit Inline' /*for 508 compliance fix*/,
  'LBL_VIEW_INLINE' => 'View' /*for 508 compliance fix*/,
  'LBL_BASIC_SEARCH' => 'Search' /*for 508 compliance fix*/,
  'LBL_PROJECT_MINUS' => 'Remove' /*for 508 compliance fix*/,
  'LBL_PROJECT_PLUS' => 'Add' /*for 508 compliance fix*/,
  'LBL_Blank' => ' ' /*for 508 compliance fix*/,
  'LBL_ICON_COLUMN_1' => 'Column' /*for 508 compliance fix*/,
  'LBL_ICON_COLUMN_2' => '2 Columns' /*for 508 compliance fix*/,
  'LBL_ICON_COLUMN_3' => '3 Columns' /*for 508 compliance fix*/,
  'LBL_ADVANCED_SEARCH' => 'Advanced Search' /*for 508 compliance fix*/,
  'LBL_ID_FF_ADD' => 'Add' /*for 508 compliance fix*/,
  'LBL_HIDE_SHOW' => 'Hide/Show' /*for 508 compliance fix*/,
  'LBL_DELETE_INLINE' => 'Delete' /*for 508 compliance fix*/,
  'LBL_PLUS_INLINE' => 'Add' /*for 508 compliance fix*/,
  'LBL_ID_FF_CLEAR' => 'Clear' /*for 508 compliance fix*/,
  'LBL_ID_FF_VCARD' => 'vCard' /*for 508 compliance fix*/,
  'LBL_ID_FF_REMOVE' => 'Remove' /*for 508 compliance fix*/,
  'LBL_ADD' => 'Add' /*for 508 compliance fix*/,
  'LBL_COMPANY_LOGO' => 'Company logo' /*for 508 compliance fix*/,
  'LBL_JS_CALENDAR' => 'Calendar' /*for 508 compliance fix*/,
    'LBL_ADVANCED' => 'Advanced',
    'LBL_BASIC' => 'Basic',
    'LBL_MODULE_FILTER' => 'Filter By',
    'LBL_CONNECTORS_POPUPS'=>'Connectors Popups',
    'LBL_CLOSEINLINE'=>'Close',
    'LBL_MOREDETAIL'=>'More Detail',
    'LBL_EDITINLINE'=>'Edit',
    'LBL_VIEWINLINE'=>'View',
    'LBL_INFOINLINE'=>'Info',
    'LBL_POWERED_BY_SUGARCRM' => "Powered by SugarCRM",
    'LBL_PRINT' => "Print",
    'LBL_HELP' => "Help",
    'LBL_ID_FF_SELECT' => "Select",
    'DEFAULT'                              => 'Basic',
    'LBL_SORT'                              => 'Sort',
    'LBL_OUTBOUND_EMAIL_ADD_SERVER'         => 'Add Server...',
    'LBL_EMAIL_SMTP_SSL_OR_TLS'         => 'Enable SMTP over SSL or TLS?',
    'LBL_NO_ACTION'                         => 'There is no action by that name.',
    'LBL_NO_DATA'                           => 'No Data',
    'LBL_ROUTING_ADD_RULE'                  => 'Add Rule',
    'LBL_ROUTING_ALL'                       => 'At Least',
    'LBL_ROUTING_ANY'                       => 'Any',
    'LBL_ROUTING_BREAK'                     => '-',
    'LBL_ROUTING_BUTTON_CANCEL'             => 'Cancel',
    'LBL_ROUTING_BUTTON_SAVE'               => 'Save Rule',

    'LBL_ROUTING_ACTIONS_COPY_MAIL'         => 'Copy Mail',
    'LBL_ROUTING_ACTIONS_DELETE_BEAN'       => 'Delete Sugar Object',
    'LBL_ROUTING_ACTIONS_DELETE_FILE'       => 'Delete File',
    'LBL_ROUTING_ACTIONS_DELETE_MAIL'       => 'Delete Email',
    'LBL_ROUTING_ACTIONS_FORWARD'           => 'Forward Email',
    'LBL_ROUTING_ACTIONS_MARK_FLAGGED'      => 'Flag Email',
    'LBL_ROUTING_ACTIONS_MARK_READ'         => 'Mark Read',
    'LBL_ROUTING_ACTIONS_MARK_UNREAD'       => 'Mark Unread',
    'LBL_ROUTING_ACTIONS_MOVE_MAIL'         => 'Move Email',
    'LBL_ROUTING_ACTIONS_PEFORM'            => 'Perform the following actions',
    'LBL_ROUTING_ACTIONS_REPLY'             => 'Reply to Email',

    'LBL_ROUTING_CHECK_RULE'                => "An error was detected:\n",
    'LBL_ROUTING_CHECK_RULE_DESC'           => 'Please verify all fields that are marked.',
    'LBL_ROUTING_CONFIRM_DELETE'            => "Are you sure you want to delete this rule?\nThis cannot be undone.",

    'LBL_ROUTING_FLAGGED'                   => 'flag set',
    'LBL_ROUTING_FORM_DESC'                 => 'Saved Rules are immediately active.',
    'LBL_ROUTING_FW'                        => 'FW: ',
    'LBL_ROUTING_LIST_TITLE'                => 'Rules',
    'LBL_ROUTING_MATCH'                     => 'If',
    'LBL_ROUTING_MATCH_2'                   => 'of the following conditions are met:',
    'LBL_NOTIFICATIONS'                     => 'Notifications',
    'LBL_ROUTING_MATCH_CC_ADDR'             => 'CC',
    'LBL_ROUTING_MATCH_DESCRIPTION'         => 'Body Content',
    'LBL_ROUTING_MATCH_FROM_ADDR'           => 'From',
    'LBL_ROUTING_MATCH_NAME'                => 'Subject',
    'LBL_ROUTING_MATCH_PRIORITY_HIGH'       => 'High Priority',
    'LBL_ROUTING_MATCH_PRIORITY_NORMAL'     => 'Normal Priority',
    'LBL_ROUTING_MATCH_PRIORITY_LOW'        => 'Low Priority',
    'LBL_ROUTING_MATCH_TO_ADDR'             => 'To',
    'LBL_ROUTING_MATCH_TYPE_MATCH'          => 'Contains',
    'LBL_ROUTING_MATCH_TYPE_NOT_MATCH'      => 'Does not contain',

    'LBL_ROUTING_NAME'                      => 'Rule Name',
    'LBL_ROUTING_NEW_NAME'                  => 'New Rule',
    'LBL_ROUTING_ONE_MOMENT'                => 'One moment please...',
    'LBL_ROUTING_ORIGINAL_MESSAGE_FOLLOWS'  => 'Original message follows.',
    'LBL_ROUTING_RE'                        => 'RE: ',
    'LBL_ROUTING_SAVING_RULE'               => 'Saving Rule',
    'LBL_ROUTING_SUB_DESC'                  => 'Checked rules are active. Click name to edit.',
    'LBL_ROUTING_TO'                        => 'to',
    'LBL_ROUTING_TO_ADDRESS'                => 'to address',
    'LBL_ROUTING_WITH_TEMPLATE'             => 'with template',
  'NTC_OVERWRITE_ADDRESS_PHONE_CONFIRM' => 'This record currently contains values in the Office Phone and Address fields. To overwrite these values with the following Office Phone and Address of the Account that you selected, click "OK". To keep the current values, click "Cancel".',
  'LBL_DROP_HERE' => '[Drop Here]',
    'LBL_EMAIL_ACCOUNTS_EDIT'               => 'Edit',
    'LBL_EMAIL_ACCOUNTS_GMAIL_DEFAULTS'     => 'Prefill Gmail&#153; Defaults',
    'LBL_EMAIL_ACCOUNTS_NAME'               => 'Name',
    'LBL_EMAIL_ACCOUNTS_OUTBOUND'           => 'Outgoing Mail Server Properties',
    'LBL_EMAIL_ACCOUNTS_SENDTYPE'           => 'Mail transfer agent',
    'LBL_EMAIL_ACCOUNTS_SMTPAUTH_REQ'       => 'Use SMTP Authentication?',
    'LBL_EMAIL_ACCOUNTS_SMTPPASS'           => 'SMTP Password',
    'LBL_EMAIL_ACCOUNTS_SMTPPORT'           => 'SMTP Port',
    'LBL_EMAIL_ACCOUNTS_SMTPSERVER'         => 'SMTP Server',
    'LBL_EMAIL_ACCOUNTS_SMTPSSL'            => 'Use SSL when connecting',
    'LBL_EMAIL_ACCOUNTS_SMTPUSER'           => 'SMTP Username',
    'LBL_EMAIL_ACCOUNTS_SMTPDEFAULT'        => 'Default',
    'LBL_EMAIL_WARNING_MISSING_USER_CREDS'  => 'Warning: Missing username and password for outgoing mail account.',
    'LBL_EMAIL_ACCOUNTS_SMTPUSER_REQD'      => 'SMTP Username is required',
    'LBL_EMAIL_ACCOUNTS_SMTPPASS_REQD'      => 'SMTP Password is required',
    'LBL_EMAIL_ACCOUNTS_TITLE'              => 'Mail Account Management',
    'LBL_EMAIL_POP3_REMOVE_MESSAGE'     => 'Mail Server Protocol of type POP3 will not be supported in the next release. Only IMAP will be supported.',
  'LBL_EMAIL_ACCOUNTS_SUBTITLE'           => 'Set up Mail Accounts to view incoming emails from your email accounts.',
  'LBL_EMAIL_ACCOUNTS_OUTBOUND_SUBTITLE'  => 'Provide SMTP mail server information to use for outgoing email in Mail Accounts.',
    'LBL_EMAIL_ADD'                         => 'Add Address',

    'LBL_EMAIL_ADDRESS_BOOK_ADD'            => 'Done',
    'LBL_EMAIL_ADDRESS_BOOK_CLEAR'          => 'Clear',
    'LBL_EMAIL_ADDRESS_BOOK_ADD_TO'         => 'To:',
    'LBL_EMAIL_ADDRESS_BOOK_ADD_CC'         => 'Cc:',
    'LBL_EMAIL_ADDRESS_BOOK_ADD_BCC'        => 'Bcc:',
    'LBL_EMAIL_ADDRESS_BOOK_ADRRESS_TYPE'   => 'To/Cc/Bcc',
    'LBL_EMAIL_ADDRESS_BOOK_ADD_LIST'       => 'New List',
    'LBL_EMAIL_ADDRESS_BOOK_EMAIL_ADDR'     => 'Email Address',
    'LBL_EMAIL_ADDRESS_BOOK_ERR_NOT_CONTACT'=> 'Only Contact editting is supported at this time.',
    'LBL_EMAIL_ADDRESS_BOOK_FILTER'         => 'Filter',
    'LBL_EMAIL_ADDRESS_BOOK_FIRST_NAME'     => 'First Name/Account Name',
    'LBL_EMAIL_ADDRESS_BOOK_LAST_NAME'      => 'Last Name',
    'LBL_EMAIL_ADDRESS_BOOK_MY_CONTACTS'    => 'My Contacts',
    'LBL_EMAIL_ADDRESS_BOOK_MY_LISTS'       => 'My Mailing Lists',
    'LBL_EMAIL_ADDRESS_BOOK_NAME'           => 'Name',
    'LBL_EMAIL_ADDRESS_BOOK_NOT_FOUND'      => 'No Addresses Found',
    'LBL_EMAIL_ADDRESS_BOOK_SAVE_AND_ADD'   => 'Save & Add to Address Book',
    'LBL_EMAIL_ADDRESS_BOOK_SEARCH'         => 'Search',
    'LBL_EMAIL_ADDRESS_BOOK_SELECT_TITLE'   => 'Select Email Recipients',
    'LBL_EMAIL_ADDRESS_BOOK_TITLE'          => 'Address Book',
    'LBL_EMAIL_REPORTS_TITLE'               => 'Reports',
    'LBL_EMAIL_ADDRESS_BOOK_TITLE_ICON'     => SugarThemeRegistry::current()->getImage('icon_email_addressbook', "", null, null, ".gif", 'Address Book').' Address Book',
    'LBL_EMAIL_ADDRESS_BOOK_TITLE_ICON_SHORT'     => SugarThemeRegistry::current()->getImage('icon_email_addressbook', 'align=absmiddle border=0', 14, 14, ".gif", ''),
    'LBL_EMAIL_REMOVE_SMTP_WARNING'         => 'Warning! The outbound account you are trying to delete is associated to an existing inbound account.  Are you sure you want to continue?',
    'LBL_EMAIL_ADDRESSES'                   => 'Email',
    'LBL_EMAIL_ADDRESS_PRIMARY'             => 'Email Address',
    'LBL_EMAIL_ADDRESSES_TITLE'             => 'Email Addresses',
    'LBL_EMAIL_ARCHIVE_TO_SUGAR'            => 'Import to Sugar',
    'LBL_EMAIL_ASSIGNMENT'                  => 'Assignment',
    'LBL_EMAIL_ATTACH_FILE_TO_EMAIL'        => 'Attach',
    'LBL_EMAIL_ATTACHMENT'                  => 'Attach',
    'LBL_EMAIL_ATTACHMENTS'                 => 'From Local System',
    'LBL_EMAIL_ATTACHMENTS2'                => 'From Sugar Documents',
    'LBL_EMAIL_ATTACHMENTS3'                => 'Template Attachments',
    'LBL_EMAIL_ATTACHMENTS_FILE'            => 'File',
    'LBL_EMAIL_ATTACHMENTS_DOCUMENT'        => 'Document',
    'LBL_EMAIL_ATTACHMENTS_EMBEDED'         => 'Embeded',
    'LBL_EMAIL_BCC'                         => 'BCC',
    'LBL_EMAIL_CANCEL'                      => 'Cancel',
    'LBL_EMAIL_CC'                          => 'CC',
    'LBL_EMAIL_CHARSET'                     => 'Character Set',
    'LBL_EMAIL_CHECK'                       => 'Check Mail',
    'LBL_EMAIL_CHECKING_NEW'                => 'Checking for New Email',
    'LBL_EMAIL_CHECKING_DESC'               => 'One moment please... <br><br>If this is the first check for the mail account, it may take some time.',
    'LBL_EMAIL_CLOSE'                       => 'Close',
    'LBL_EMAIL_COFFEE_BREAK'                => 'Checking for New Email. <br><br>Large mail accounts may take a considerable amount of time.',
    'LBL_EMAIL_COMMON'                      => 'Common',

    'LBL_EMAIL_COMPOSE'                     => 'Email',
    'LBL_EMAIL_COMPOSE_ERR_NO_RECIPIENTS'   => 'Please enter recipient(s) for this email.',
    'LBL_EMAIL_COMPOSE_LINK_TO'             => 'Associate with',
    'LBL_EMAIL_COMPOSE_NO_BODY'             => 'The body of this email is empty.  Send anyway?',
    'LBL_EMAIL_COMPOSE_NO_SUBJECT'          => 'This email has no subject.  Send anyway?',
    'LBL_EMAIL_COMPOSE_NO_SUBJECT_LITERAL'  => '(no subject)',
    'LBL_EMAIL_COMPOSE_READ'                => 'Read & Compose Email',
    'LBL_EMAIL_COMPOSE_SEND_FROM'           => 'Send From Mail Account',
    'LBL_EMAIL_COMPOSE_OPTIONS'             => 'Options',
    'LBL_EMAIL_COMPOSE_INVALID_ADDRESS'     => 'Please enter valid email address for To, CC and BCC fields',

    'LBL_EMAIL_CONFIRM_CLOSE'               => 'Discard this email?',
    'LBL_EMAIL_CONFIRM_DELETE'              => 'Remove these entries from your Address Book?',
    'LBL_EMAIL_CONFIRM_DELETE_SIGNATURE'    => 'Are you sure you want to delete this signature?',

    'LBL_EMAIL_CREATE_NEW'                  => '--Create On Save--',
    'LBL_EMAIL_MULT_GROUP_FOLDER_ACCOUNTS'  => 'Multiple',
    'LBL_EMAIL_MULT_GROUP_FOLDER_ACCOUNTS_EMPTY' => 'Empty',
    'LBL_EMAIL_DATE_SENT_BY_SENDER'         => 'Date Sent by Sender',
  'LBL_EMAIL_DATE_RECEIVED'               => 'Date Received',
    'LBL_EMAIL_ASSIGNED_TO_USER'            =>'Assigned to User',
    'LBL_EMAIL_DATE_TODAY'                  => 'Today',
    'LBL_EMAIL_DATE_YESTERDAY'              => 'Yesterday',
    'LBL_EMAIL_DD_TEXT'                     => 'email(s) selected.',
    'LBL_EMAIL_DEFAULTS'                    => 'Defaults',
    'LBL_EMAIL_DELETE'                      => 'Delete',
    'LBL_EMAIL_DELETE_CONFIRM'              => 'Delete selected messages?',
    'LBL_EMAIL_DELETE_SUCCESS'              => 'Email deleted successfully.',
    'LBL_EMAIL_DELETING_MESSAGE'            => 'Deleting Message',
    'LBL_EMAIL_DETAILS'                     => 'Details',
    'LBL_EMAIL_DISPLAY_MSG'                 => 'Displaying email(s) {0} - {1} of {2}',
    'LBL_EMAIL_ADDR_DISPLAY_MSG'            => 'Displaying email address(es) {0} - {1} of {2}',

    'LBL_EMAIL_EDIT_CONTACT'                => 'Edit Contact',
    'LBL_EMAIL_EDIT_CONTACT_WARN'           => 'Only the Primary address will be used when working with Contacts.',
    'LBL_EMAIL_EDIT_MAILING_LIST'           => 'Edit Mailing List',

    'LBL_EMAIL_EMPTYING_TRASH'              => 'Emptying Trash',
    'LBL_EMAIL_DELETING_OUTBOUND'           => 'Deleteting outbound server',
    'LBL_EMAIL_CLEARING_CACHE_FILES'        => 'CLearing cache files',
    'LBL_EMAIL_EMPTY_MSG'                   => 'No emails to display.',
    'LBL_EMAIL_EMPTY_ADDR_MSG'              => 'No email addresses to display.',

    'LBL_EMAIL_ERROR_ADD_GROUP_FOLDER'      => 'Folder name be unique and not empty. Please try again.',
    'LBL_EMAIL_ERROR_DELETE_GROUP_FOLDER'   => 'Cannot delete a folder. Either the folder or its children has emails or a mail box associated to it.',
    'LBL_EMAIL_ERROR_CANNOT_FIND_NODE'      => 'Cannot determine the intended folder from context.  Try again.',
    'LBL_EMAIL_ERROR_CHECK_IE_SETTINGS'     => 'Please check your settings.',
    'LBL_EMAIL_ERROR_CONTACT_NAME'          => 'Please make sure you enter a last name.',
    'LBL_EMAIL_ERROR_DESC'                  => 'Errors were detected: ',
    'LBL_EMAIL_DELETE_ERROR_DESC'           => 'You do not have access to this area. Contact your site administrator to obtain access.',
    'LBL_EMAIL_ERROR_DUPE_FOLDER_NAME'      => 'Sugar Folder names must be unique.',
    'LBL_EMAIL_ERROR_EMPTY'                 => 'Please enter some search criteria.',
    'LBL_EMAIL_ERROR_GENERAL_TITLE'         => 'An error has occured',
    'LBL_EMAIL_ERROR_LIST_NAME'             => 'An email list with that name already exists',
    'LBL_EMAIL_ERROR_MESSAGE_DELETED'       => 'Message Removed from Server',
    'LBL_EMAIL_ERROR_IMAP_MESSAGE_DELETED'  => 'Either message Removed from Server or moved to a different folder',
    'LBL_EMAIL_ERROR_MAILSERVERCONNECTION'  => 'Connection to the mail server failed. Please contact your Administrator',
    'LBL_EMAIL_ERROR_MOVE'                  => 'Moving email between servers and/or mail accounts is not supported at this time.',
    'LBL_EMAIL_ERROR_MOVE_TITLE'            => 'Move Error',
    'LBL_EMAIL_ERROR_NAME'                  => 'A name is required.',
    'LBL_EMAIL_ERROR_FROM_ADDRESS'          => 'From Address is required.  Please enter a valid email address.',
    'LBL_EMAIL_ERROR_NO_FILE'               => 'Please provide a file.',
    'LBL_EMAIL_ERROR_NO_IMAP_FOLDER_RENAME' => 'IMAP folder renaming is not supported at this time.',
    'LBL_EMAIL_ERROR_SERVER'                => 'A mail server address is required.',
    'LBL_EMAIL_ERROR_SAVE_ACCOUNT'          => 'The mail account may not have been saved.',
    'LBL_EMAIL_ERROR_TIMEOUT'               => 'An error has occured while communicating with the mail server.',
    'LBL_EMAIL_ERROR_USER'                  => 'A login name is required.',
    'LBL_EMAIL_ERROR_PASSWORD'              => 'A password is required.',
    'LBL_EMAIL_ERROR_PORT'                  => 'A mail server port is required.',
    'LBL_EMAIL_ERROR_PROTOCOL'              => 'A server protocol is required.',
    'LBL_EMAIL_ERROR_MONITORED_FOLDER'      => 'Monitored Folder is required.',
    'LBL_EMAIL_ERROR_TRASH_FOLDER'          => 'Trash Folder is required.',
    'LBL_EMAIL_ERROR_VIEW_RAW_SOURCE'       => 'This information is not available',
    'LBL_EMAIL_ERROR_NO_OUTBOUND'           => 'No outgoing mail server specified.',
    'LBL_EMAIL_FOLDERS'                     => SugarThemeRegistry::current()->getImage('icon_email_folder', 'align=absmiddle border=0', null, null, ".gif", '').'Folders',
    'LBL_EMAIL_FOLDERS_SHORT'               => SugarThemeRegistry::current()->getImage('icon_email_folder', 'align=absmiddle border=0', null, null, ".gif", ''),
    'LBL_EMAIL_FOLDERS_ACTIONS'             => 'Move To',
    'LBL_EMAIL_FOLDERS_ADD'                 => 'Add',
    'LBL_EMAIL_FOLDERS_ADD_DIALOG_TITLE'    => 'Add New Folder',
    'LBL_EMAIL_FOLDERS_RENAME_DIALOG_TITLE' => 'Rename Folder',
    'LBL_EMAIL_FOLDERS_ADD_NEW_FOLDER'      => 'Save',
    'LBL_EMAIL_FOLDERS_ADD_THIS_TO'         => 'Add this folder to',
    'LBL_EMAIL_FOLDERS_CHANGE_HOME'         => 'This folder cannot be changed',
    'LBL_EMAIL_FOLDERS_DELETE_CONFIRM'      => 'Are you sure you would like to delete this folder?\nThis process cannot be reversed.\nFolder deletions will cascade to all contained folders.',
    'LBL_EMAIL_FOLDERS_NEW_FOLDER'          => 'New Folder Name',
    'LBL_EMAIL_FOLDERS_NO_VALID_NODE'       => 'Please select a folder before performing this action.',
    'LBL_EMAIL_FOLDERS_TITLE'               => 'Folder Management',
    'LBL_EMAIL_FOLDERS_USING_GROUP_USER'    => 'Using Group',
    //BEGIN SUGARCRM flav=pro ONLY
    'LBL_EMAIL_FOLDERS_USING_TEAM'          => 'Assign to Team',
    //END SUGARCRM flav=pro ONLY

    'LBL_EMAIL_FORWARD'                     => 'Forward',
    'LBL_EMAIL_DELIMITER'                   => '::;::',
    'LBL_EMAIL_DOWNLOAD_STATUS'             => 'Downloaded [[count]] of [[total]] emails',
    'LBL_EMAIL_FOUND'                       => 'Found',
    'LBL_EMAIL_FROM'                        => 'From',
    'LBL_EMAIL_GROUP'                       => 'group',
    'LBL_EMAIL_UPPER_CASE_GROUP'            => 'Group',
    'LBL_EMAIL_HOME_FOLDER'                 => 'Home',
    'LBL_EMAIL_HTML_RTF'                    => 'Send HTML',
    'LBL_EMAIL_IE_DELETE'                   => 'Deleting Mail Account',
    'LBL_EMAIL_IE_DELETE_SIGNATURE'         => 'Deleting signature',
    'LBL_EMAIL_IE_DELETE_CONFIRM'           => 'Are you sure you would like to delete this mail account?',
    'LBL_EMAIL_IE_DELETE_SUCCESSFUL'        => 'Deletion successful.',
    'LBL_EMAIL_IE_SAVE'                     => 'Saving Mail Account Information',
    'LBL_EMAIL_IMPORTING_EMAIL'             => 'Importing Email',
    'LBL_EMAIL_IMPORT_EMAIL'                => 'Import to Sugar',
    'LBL_EMAIL_IMPORT_SETTINGS'                => 'Import Settings',
    'LBL_EMAIL_INVALID'                     => 'Invalid',
    //BEGIN SUGARCRM flav=pro ONLY
    'LBL_EMAIL_LIST_RENAME_TITLE'           => 'Rename Mailing List',
    'LBL_EMAIL_LIST_RENAME_DESC '           => 'Enter a new name for this mailing list',
    'LBL_EMAIL_CONFIRM_DELETE_LIST'         => 'Remove these entries from your mailing lists?',
    //END SUGARCRM flav=pro ONLY
    'LBL_EMAIL_LOADING'                     => 'Loading...',
    'LBL_EMAIL_MARK'                        => 'Mark',
    'LBL_EMAIL_MARK_FLAGGED'                => 'As Flagged',
    'LBL_EMAIL_MARK_READ'                   => 'As Read',
    'LBL_EMAIL_MARK_UNFLAGGED'              => 'As Unflagged',
    'LBL_EMAIL_MARK_UNREAD'                 => 'As Unread',
    'LBL_EMAIL_ASSIGN_TO'                   => 'Assign To',

    'LBL_EMAIL_MENU_ADD_FOLDER'             => 'Create Folder',
    'LBL_EMAIL_MENU_COMPOSE'                => 'Compose to',
    'LBL_EMAIL_MENU_DELETE_FOLDER'          => 'Delete Folder',
    'LBL_EMAIL_MENU_EDIT'                   => 'Edit',
    'LBL_EMAIL_MENU_EMPTY_TRASH'            => 'Empty Trash',
    'LBL_EMAIL_MENU_SYNCHRONIZE'            => 'Synchronize',
    'LBL_EMAIL_MENU_CLEAR_CACHE'            => 'Clear cache files',
    'LBL_EMAIL_MENU_REMOVE'                 => 'Remove',
    'LBL_EMAIL_MENU_RENAME'                 => 'Rename',
    'LBL_EMAIL_MENU_RENAME_FOLDER'          => 'Rename Folder',
    'LBL_EMAIL_MENU_RENAMING_FOLDER'        => 'Renaming Folder',
    'LBL_EMAIL_MENU_MAKE_SELECTION'         => 'Please make a selection before trying this operation.',

    'LBL_EMAIL_MENU_HELP_ADD_FOLDER'        => 'Create a Folder (remote or in Sugar)',
    'LBL_EMAIL_MENU_HELP_ARCHIVE'           => 'Archive these email(s) to SugarCRM',
    'LBL_EMAIL_MENU_HELP_COMPOSE_TO_LIST'   => 'Email selected Mailing Lists',
    'LBL_EMAIL_MENU_HELP_CONTACT_COMPOSE'   => 'Email this Contact',
    'LBL_EMAIL_MENU_HELP_CONTACT_REMOVE'    => 'Remove a Contact',
    'LBL_EMAIL_MENU_HELP_DELETE'            => 'Delete these email(s)',
    'LBL_EMAIL_MENU_HELP_DELETE_FOLDER'     => 'Delete a Folder (remote or in Sugar)',
    'LBL_EMAIL_MENU_HELP_EDIT_CONTACT'      => 'Edit a Contact',
    'LBL_EMAIL_MENU_HELP_EDIT_LIST'         => 'Edit a Mailing List',
    'LBL_EMAIL_MENU_HELP_EMPTY_TRASH'       => 'Empties all Trash folders for your mail accounts',
    'LBL_EMAIL_MENU_HELP_MARK_FLAGGED'      => 'Mark these email(s) flagged',
    'LBL_EMAIL_MENU_HELP_MARK_READ'         => 'Mark these email(s) read',
    'LBL_EMAIL_MENU_HELP_MARK_UNFLAGGED'    => 'Mark these email(s) unflagged',
    'LBL_EMAIL_MENU_HELP_MARK_UNREAD'       => 'Mark these email(s) unread',
    'LBL_EMAIL_MENU_HELP_REMOVE_LIST'       => 'Removes Mailing Lists',
    'LBL_EMAIL_MENU_HELP_RENAME_FOLDER'     => 'Rename a Folder (remote or in Sugar)',
    'LBL_EMAIL_MENU_HELP_REPLY'             => 'Reply to these email(s)',
    'LBL_EMAIL_MENU_HELP_REPLY_ALL'         => 'Reply to all recipients for these email(s)',

    'LBL_EMAIL_MESSAGES'                    => 'messages',

    'LBL_EMAIL_ML_NAME'                     => 'List Name',
    'LBL_EMAIL_ML_ADDRESSES_1'              => 'Selected List Addresses',
    'LBL_EMAIL_ML_ADDRESSES_2'              => 'Available List Addresses',

    'LBL_EMAIL_MULTISELECT'                 => '<b>Ctrl-Click</b> to select multiples<br />(Mac users use <b>CMD-Click</b>)',

    'LBL_EMAIL_NO'                          => 'No',
    'LBL_EMAIL_NOT_SENT'                    => 'System is unable to process your request. Please contact the system administrator.',

    'LBL_EMAIL_OK'                          => 'OK',
    'LBL_EMAIL_ONE_MOMENT'                  => 'One moment please...',
    'LBL_EMAIL_OPEN_ALL'                    => 'Open Multiple Messages',
    'LBL_EMAIL_OPTIONS'                     => 'Options',
    'LBL_EMAIL_QUICK_COMPOSE'       => 'Quick Compose',
    'LBL_EMAIL_OPT_OUT'                     => 'Opted Out',
    'LBL_EMAIL_OPT_OUT_AND_INVALID'         => 'Opted Out and Invalid',
    'LBL_EMAIL_PAGE_AFTER'                  => 'of {0}',
    'LBL_EMAIL_PAGE_BEFORE'                 => 'Page',
    'LBL_EMAIL_PERFORMING_TASK'             => 'Performing Task',
    'LBL_EMAIL_PRIMARY'                     => 'Primary',
    'LBL_EMAIL_PRINT'                       => 'Print',

    'LBL_EMAIL_QC_BUGS'                     => 'Bug',
    'LBL_EMAIL_QC_CASES'                    => 'Case',
    'LBL_EMAIL_QC_LEADS'                    => 'Lead',
    'LBL_EMAIL_QC_CONTACTS'                 => 'Contact',
    'LBL_EMAIL_QC_TASKS'                    => 'Task',
    'LBL_EMAIL_QC_OPPORTUNITIES'            => 'Opportunity',
    'LBL_EMAIL_QUICK_CREATE'                => 'Quick Create',

    'LBL_EMAIL_REBUILDING_FOLDERS'          => 'Rebuilding Folders',
    'LBL_EMAIL_RELATE_TO'                   => 'Relate',
    'LBL_EMAIL_VIEW_RELATIONSHIPS'          => 'View Relationships',
    'LBL_EMAIL_RECORD'                => 'Email Record',
    'LBL_EMAIL_REMOVE'                      => 'Remove',
    'LBL_EMAIL_REPLY'                       => 'Reply',
    'LBL_EMAIL_REPLY_ALL'                   => 'Reply All',
    'LBL_EMAIL_REPLY_TO'                    => 'Reply-to',
    'LBL_EMAIL_RETRIEVING_LIST'             => 'Retrieving Email List',
    'LBL_EMAIL_RETRIEVING_MESSAGE'          => 'Retrieving Message',
    'LBL_EMAIL_RETRIEVING_RECORD'           => 'Retrieving Email Record',
    'LBL_EMAIL_SELECT_ONE_RECORD'           => 'Please select only one email record',
    'LBL_EMAIL_RETURN_TO_VIEW'              => 'Return to Previous Module?',
    'LBL_EMAIL_REVERT'                      => 'Revert',
    'LBL_EMAIL_RELATE_EMAIL'                => 'Relate Email',

    'LBL_EMAIL_RULES_TITLE'                 => 'Rule Management',

    'LBL_EMAIL_SAVE'                        => 'Save',
    'LBL_EMAIL_SAVE_AND_REPLY'              => 'Save & Reply',
    'LBL_EMAIL_SAVE_DRAFT'                  => 'Save Draft',

    'LBL_EMAIL_SEARCHING'                   => 'Conducting Search',
    'LBL_EMAIL_SEARCH'                      => SugarThemeRegistry::current()->getImage('Search', 'align=absmiddle border=0', null, null, ".gif", ''),
    'LBL_EMAIL_SEARCH_SHORT'                => SugarThemeRegistry::current()->getImage('Search', 'align=absmiddle border=0', null, null, ".gif", ''),
    'LBL_EMAIL_SEARCH_ADVANCED'             => 'Advanced Search',
    'LBL_EMAIL_SEARCH_DATE_FROM'            => 'Date From',
    'LBL_EMAIL_SEARCH_DATE_UNTIL'           => 'Date Until',
    'LBL_EMAIL_SEARCH_FULL_TEXT'            => 'Body Text',
    'LBL_EMAIL_SEARCH_NO_RESULTS'           => 'No results match your search criteria.',
    'LBL_EMAIL_SEARCH_RESULTS_TITLE'        => 'Search Results',
    'LBL_EMAIL_SEARCH_TITLE'                => 'Simple Search',
    'LBL_EMAIL_SEARCH__FROM_ACCOUNTS'       => 'Search email account',

    'LBL_EMAIL_SELECT'                      => 'Select',

    'LBL_EMAIL_SEND'                        => 'Send',
    'LBL_EMAIL_SENDING_EMAIL'               => 'Sending Email',

    'LBL_EMAIL_SETTINGS'                    => 'Settings',
    'LBL_EMAIL_SETTINGS_2_ROWS'             => '2 Rows',
    'LBL_EMAIL_SETTINGS_3_COLS'             => '3 Columns',
    'LBL_EMAIL_SETTINGS_LAYOUT'             => 'Layout Style',
    'LBL_EMAIL_SETTINGS_ACCOUNTS'           => 'Mail Accounts',
    'LBL_EMAIL_SETTINGS_ADD_ACCOUNT'        => 'Clear Form',
    'LBL_EMAIL_SETTINGS_AUTO_IMPORT'        => 'Import Email Upon View',
    'LBL_EMAIL_SETTINGS_CHECK_INTERVAL'     => 'Check for New Mail',
    'LBL_EMAIL_SETTINGS_COMPOSE_INLINE'     => 'Use Preview Pane',
    'LBL_EMAIL_SETTINGS_COMPOSE_POPUP'      => 'Use Popup Window',
    'LBL_EMAIL_SETTINGS_DISPLAY_NUM'        => 'Number emails per page',
    'LBL_EMAIL_SETTINGS_EDIT_ACCOUNT'       => 'Edit Mail Account',
    'LBL_EMAIL_SETTINGS_FOLDERS'            => 'Folders',
    'LBL_EMAIL_SETTINGS_FROM_ADDR'          => 'From Address',
    'LBL_EMAIL_SETTINGS_FROM_TO_EMAIL_ADDR' => 'Email Address For Test Notification:',
    'LBL_EMAIL_SETTINGS_TO_EMAIL_ADDR'      => 'To Email Address',
    'LBL_EMAIL_SETTINGS_FROM_NAME'          => 'From Name',
    'LBL_EMAIL_SETTINGS_REPLY_TO_ADDR'      =>'Reply to Address',
    'LBL_EMAIL_SETTINGS_FULL_SCREEN'        => 'Full Screen',
    'LBL_EMAIL_SETTINGS_FULL_SYNC'          => 'Synchronize All Mail Accounts',
    'LBL_EMAIL_TEST_NOTIFICATION_SENT'      => 'An email was sent to the specified email address using the provided outgoing mail settings. Please check to see if the email was received to verify the settings are correct.',
    'LBL_EMAIL_SETTINGS_FULL_SYNC_DESC'     => 'Performing this action will synchronize mail accounts and their contents.',
    'LBL_EMAIL_SETTINGS_FULL_SYNC_WARN'     => 'Perform a full synchronization?\nLarge mail accounts may take a few minutes.',
    'LBL_EMAIL_SUBSCRIPTION_FOLDER_HELP'    => 'Click the Shift key or the Ctrl key to select multiple folders.',
    'LBL_EMAIL_SETTINGS_GENERAL'            => 'General',
    'LBL_EMAIL_SETTINGS_GROUP_FOLDERS'      => 'Available Group Folders',
    'LBL_EMAIL_SETTINGS_GROUP_FOLDERS_CREATE'   => 'Create Group Folders',
    'LBL_EMAIL_SETTINGS_GROUP_FOLDERS_Save' => 'Saving Group Folders',
    'LBL_EMAIL_SETTINGS_RETRIEVING_GROUP'   => 'Retrieving Group Folder',

    'LBL_EMAIL_SETTINGS_GROUP_FOLDERS_EDIT' => 'Edit Group Folder',

    'LBL_EMAIL_SETTINGS_NAME'               => 'Mail Account Name',
    'LBL_EMAIL_SETTINGS_REQUIRE_REFRESH'    => 'Select the number of emails per page in the Inbox. This setting might require a page refresh in order to take effect.',
    'LBL_EMAIL_SETTINGS_RETRIEVING_ACCOUNT' => 'Retrieving Mail Account',
    'LBL_EMAIL_SETTINGS_RULES'              => 'Rules',
    'LBL_EMAIL_SETTINGS_SAVED'              => 'The settings have been saved.\n\nYou must reload the page for the new settings to take effect.',
    'LBL_EMAIL_SETTINGS_SEND_EMAIL_AS'      => 'Send Plain Text Emails Only',
    'LBL_EMAIL_SETTINGS_SHOW_IN_FOLDERS'    => 'Active',
    'LBL_EMAIL_SETTINGS_SHOW_NUM_IN_LIST'   => 'Emails per Page',
    'LBL_EMAIL_SETTINGS_TAB_POS'            => 'Place Tabs at Bottom',
    'LBL_EMAIL_SETTINGS_TITLE_LAYOUT'       => 'Visual Settings',
    'LBL_EMAIL_SETTINGS_TITLE_PREFERENCES'  => 'Preferences',
    'LBL_EMAIL_SETTINGS_TOGGLE_ADV'         => 'Show Advanced',
    'LBL_EMAIL_SETTINGS_USER_FOLDERS'       => 'Available User Folders',
    'LBL_EMAIL_ERROR_PREPEND'               => 'Error:',
  'LBL_EMAIL_INVALID_PERSONAL_OUTBOUND' => 'The outbound mail server selected for the mail account you are using is invalid.  Check the settings or select a different mail server for the mail account.',
  'LBL_EMAIL_INVALID_SYSTEM_OUTBOUND' => 'An outgoing mail server is not configured to send emails. Please configure an outgoing mail server or select an outgoing mail server for the mail account that you are using in Settings >> Mail Account.',
    'LBL_EMAIL_SHOW_READ'                   => 'Show All',
    'LBL_EMAIL_SHOW_UNREAD_ONLY'            => 'Show Unread Only',
    'LBL_EMAIL_SIGNATURES'                  => 'Signatures',
    'LBL_EMAIL_SIGNATURE_CREATE'            => 'Create Signature',
    'LBL_EMAIL_SIGNATURE_NAME'              => 'Signature Name',
    'LBL_EMAIL_SIGNATURE_TEXT'              => 'Signature Body',
  'LBL_SMTPTYPE_GMAIL'                    => 'Gmail',
  'LBL_SMTPTYPE_YAHOO'                    => 'Yahoo! Mail',
  'LBL_SMTPTYPE_EXCHANGE'                 => 'Microsoft Exchange',
    'LBL_SMTPTYPE_OTHER'                  => 'Other',
    'LBL_EMAIL_SPACER_MAIL_SERVER'          => '[ Remote Folders ]',
    'LBL_EMAIL_SPACER_LOCAL_FOLDER'         => '[ Sugar Folders ]',
    'LBL_EMAIL_SUBJECT'                     => 'Subject',
    'LBL_EMAIL_TO'                        => 'To',
    'LBL_EMAIL_SUCCESS'                     => 'Success',
    'LBL_EMAIL_SUGAR_FOLDER'                => 'SugarFolder',
    //BEGIN SUGARCRM flav=pro ONLY
    'LBL_EMAIL_TEAMS'                       => 'Assign to Teams',
    //END SUGARCRM flav=pro ONLY
    'LBL_EMAIL_TEMPLATE_EDIT_PLAIN_TEXT'    => 'Email template body is empty',
    'LBL_EMAIL_TEMPLATES'                   => 'Templates',
    'LBL_EMAIL_TEXT_FIRST'                  => 'First Page',
    'LBL_EMAIL_TEXT_PREV'                   => 'Previous Page',
    'LBL_EMAIL_TEXT_NEXT'                   => 'Next Page',
    'LBL_EMAIL_TEXT_LAST'                   => 'Last Page',
    'LBL_EMAIL_TEXT_REFRESH'                => 'Refresh',
    'LBL_EMAIL_TO'                          => 'To',
    'LBL_EMAIL_TOGGLE_LIST'                 => 'Toggle List',
    'LBL_EMAIL_VIEW'                        => 'View',
    'LBL_EMAIL_VIEWS'                       => 'Views',
    'LBL_EMAIL_VIEW_HEADERS'                => 'Display Headers',
    'LBL_EMAIL_VIEW_PRINTABLE'              => 'Printable Version',
    'LBL_EMAIL_VIEW_RAW'                    => 'Display Raw Email',
    'LBL_EMAIL_VIEW_UNSUPPORTED'            => 'This feature is unsupported when used with POP3.',
    'LBL_DEFAULT_LINK_TEXT'                 => 'Default link text.',
    'LBL_EMAIL_YES'                         => 'Yes',
    'LBL_EMAIL_TEST_OUTBOUND_SETTINGS'      => 'Send Test Email',
    'LBL_EMAIL_TEST_OUTBOUND_SETTINGS_SENT' => 'Test Email Sent',
    'LBL_EMAIL_CHECK_INTERVAL_DOM'          => array(
        '-1' => "Manually",
        '5' => 'Every 5 minutes',
        '15' => 'Every 15 minutes',
        '30' => 'Every 30 minutes',
        '60' => 'Every hour'
    ),


    'LBL_EMAIL_MESSAGE_NO'                  => 'Message No',
    'LBL_EMAIL_IMPORT_SUCCESS'              => 'Import Passed',
    'LBL_EMAIL_IMPORT_FAIL'                 => 'Import Failed because either the message is already imported or deleted from server',

    'LBL_LINK_NONE'=> 'None',
    'LBL_LINK_ALL'=> 'All',
    'LBL_LINK_RECORDS'=> 'Records',
    'LBL_LINK_SELECT'=> 'Select',
    'LBL_LINK_ACTIONS'=> 'Actions',
    'LBL_LINK_MORE'=> 'More',
    'LBL_CLOSE_ACTIVITY_HEADER' => "Confirm",
    'LBL_CLOSE_ACTIVITY_CONFIRM' => "Do you want to close this #module#?",
    'LBL_CLOSE_ACTIVITY_REMEMBER' => "Do not display this message in the future: &nbsp;",
    'LBL_INVALID_FILE_EXTENSION' => 'Invalid File Extension',
    //BEGIN SUGARCRM flav=pro ONLY
    'LBL_LIST_TEAM' => 'Team',
    'LBL_TEAM' => 'Team:',
    'LBL_TEAMS' =>'Teams',
    'LBL_TEAM_ID'=>'Team ID:',
    'LBL_TEAM_SET_ID' => 'Team Set ID',
    'LBL_EXPORT_TEAM_SET_ID' => 'Teams',
    'LBL_TEAM_SET'=>'Team Set',
    'LBL_SEARCH_UNAVAILABLE' => 'Search unavailable, please try again later.',
    'ERR_NO_PRIMARY_TEAM_SPECIFIED' => 'No Primary Team specified',
    'LBL_REMOVE_PRIMARY_TEAM_ERROR' => 'Error attempting to remove primary team id [{0}] for [{1}] module with id [{2}]',
    //END SUGARCRM flav=pro ONLY

    //BEGIN SUGARCRM flav=ent ONLY
    'LBL_QUERY_VALID'=>'Valid',
    'LBL_QUERY_ERROR'=>'Error!',
    'LBL_QUERY_CHILD'=>'Valid Sub-Query',
    'LBL_CLOSE_BUTTON_KEY' => 'C',
    'LBL_CLOSE_BUTTON_LABEL' => 'Close',
    'ERROR_EXAMINE_MSG' => '  Please examine the error message below:',
    'NO_QUERY_SELECTED' => 'The data format you have selected does not contain a query.  Please select a custom query for this data format.',
    //END SUGARCRM flav=ent ONLY

    'ERR_AJAX_LOAD'     => 'An error has occured:',
    'ERR_AJAX_LOAD_FAILURE'     => 'There was an error processing your request, please try again at a later time.',
    'ERR_AJAX_LOAD_FOOTER' => 'If this error persists, please have your administrator disable Ajax for this module',
    'ERR_CREATING_FIELDS' => 'Error filling in additional detail fields: ',
    'ERR_CREATING_TABLE' => 'Error creating table: ',
    'ERR_DECIMAL_SEP_EQ_THOUSANDS_SEP'  => "The decimal separator cannot use the same character as the thousands separator.\\n\\n  Please change the values.",
    'ERR_DELETE_RECORD' => 'A record number must be specified to delete the contact.',
    'ERR_EXPORT_DISABLED' => 'Exports Disabled.',
    'ERR_EXPORT_TYPE' => 'Error exporting ',
    'ERR_INVALID_AMOUNT' => 'Please enter a valid amount.',
    'ERR_INVALID_DATE_FORMAT' => 'The date format must be: ',
    'ERR_INVALID_DATE' => 'Please enter a valid date.',
    'ERR_INVALID_DAY' => 'Please enter a valid day.',
    'ERR_INVALID_EMAIL_ADDRESS' => 'not a valid email address.',
    'ERR_INVALID_FILE_REFERENCE' => 'Invalid File Reference',
    'ERR_INVALID_HOUR' => 'Please enter a valid hour.',
    'ERR_INVALID_MONTH' => 'Please enter a valid month.',
    'ERR_INVALID_TIME' => 'Please enter a valid time.',
    'ERR_INVALID_YEAR' => 'Please enter a valid 4 digit year.',
    'ERR_NEED_ACTIVE_SESSION' => 'An active session is required to export content.',
    'ERR_NO_HEADER_ID' => 'This feature is unavailable in this theme.',
    'ERR_NOT_ADMIN' => "Unauthorized access to administration.",
    'ERR_MISSING_REQUIRED_FIELDS' => 'Missing required field:',
    'ERR_INVALID_REQUIRED_FIELDS' => 'Invalid required field:',
    'ERR_INVALID_VALUE' => 'Invalid Value:',
    'ERR_NO_SUCH_FILE' =>'File does not exist on system',
    'ERR_NO_SINGLE_QUOTE' => 'Cannot use the single quotation mark for ',
    'ERR_NOTHING_SELECTED' =>'Please make a selection before proceeding.',
    'ERR_OPPORTUNITY_NAME_DUPE' => 'An opportunity with the name %s already exists.  Please enter another name below.',
    'ERR_OPPORTUNITY_NAME_MISSING' => 'An opportunity name was not entered.  Please enter an opportunity name below.',
    'ERR_POTENTIAL_SEGFAULT' => 'A potential Apache segmentation fault was detected.  Please notify your system administrator to confirm this problem and have her/him report it to SugarCRM.',
    'ERR_SELF_REPORTING' => 'User cannot report to him or herself.',
    'ERR_SINGLE_QUOTE'  => 'Using the single quote is not supported for this field.  Please change the value.',
    'ERR_SQS_NO_MATCH_FIELD' => 'No match for field: ',
    'ERR_SQS_NO_MATCH' =>'No Match',
    'ERR_ADDRESS_KEY_NOT_SPECIFIED' => 'Please specify \'key\' index in displayParams attribute for the Meta-Data definition',
    'ERR_EXISTING_PORTAL_USERNAME'=>'Error: The Portal Name is already assigned to another contact.',
    'ERR_COMPATIBLE_PRECISION_VALUE' => 'Field value is not compatible with precision value',
    'ERR_EXTERNAL_API_SAVE_FAIL' => 'An error occurred when trying to save to the external account.',
    'ERR_EXTERNAL_API_UPLOAD_FAIL' => 'An error occurred while uploading.  Please ensure the file you are uploading is not empty.',
    'ERR_NO_DB' => 'Could not connect to the database. Please refer to sugarcrm.log for details.',
    'ERR_DB_FAIL' => 'Database failure. Please refer to sugarcrm.log for details.',
    'ERR_EXTERNAL_API_403' => 'Permission Denied. File type is not supported.',
    'ERR_DB_VERSION' => 'Sugar CRM {0} Files May Only Be Used With A Sugar CRM {1} Database.',

    'EXCEPTION_CREATE_MODULE_NOT_AUTHORIZED' => 'You are not authorized to create {moduleName}. Contact your administrator if you need access.',

    // Default SugarApiException error messages
    'EXCEPTION_UNKNOWN_EXCEPTION'       => 'Your request failed due to an unknown exception.',
    'EXCEPTION_FATAL_ERROR'             => 'Your request failed to complete.  A fatal error occurred.  Check logs for more details.',
    'EXCEPTION_NEED_LOGIN'              => 'You need to be logged in to perform this action.',
    'EXCEPTION_NOT_AUTHORIZED'          => 'You are not authorized to perform this action. Contact your administrator if you need access.',
    'EXCEPTION_PORTAL_NOT_CONFIGURED'   => 'Portal is not configured properly.  Contact your Portal Administrator for assistance.',
    'EXCEPTION_NO_METHOD'               => 'Your request was not supported. Could not find the HTTP method of your request for this path.',
    'EXCEPTION_NOT_FOUND'               => 'Your requested resource was not found.  Could not find a handler for the path specified in the request.',
    'EXCEPTION_MISSING_PARAMTER'        => 'A required parameter in your request was missing.',
    'EXCEPTION_INVALID_PARAMETER'       => 'A parameter in your request was invalid.',
    'EXCEPTION_REQUEST_FAILURE'         => 'Your request failed to complete.',
    'EXCEPTION_REQUEST_TOO_LARGE'       => 'Your request is too large to process.',


    //BEGIN SUGARCRM flav=pro ONLY
    //Lotus Live specific error messages
    'ERR_EXTERNAL_API_LOTUS_LIVE_CONFLICT' => 'A file with the same name already exists in the system.',

    //Forecast specific error messages
    'ERR_TIMEPERIOD_UNDEFINED_FOR_DATE' => 'Error Timeperiod undefined for date {0}',
    'LBL_CURRENT_TIMEPERIOD' => 'Current Time Period',
    'LBL_PREVIOUS_TIMEPERIOD' => 'Previous Time Period',
    'LBL_NEXT_TIMEPERIOD' => 'Next Time Period',
    'LBL_PREVIOUS_CURRENT_NEXT_TIMEPERIODS' => 'Previous,Current,Next',

    //END SUGARCRM flav=pro ONLY

    'LBL_ACCOUNT'=>'Account',
    'LBL_OLD_ACCOUNT_LINK'=>'Old Account',
    'LBL_ACCOUNTS'=>'Accounts',
    'LBL_ACTIVITIES_SUBPANEL_TITLE'=>'Activities',
    'LBL_ACCUMULATED_HISTORY_BUTTON_KEY' => 'H',
    'LBL_ACCUMULATED_HISTORY_BUTTON_LABEL' => 'View Summary',
    'LBL_ACCUMULATED_HISTORY_BUTTON_TITLE' => 'View Summary',
    'LBL_ADD_BUTTON_KEY' => 'A',
    'LBL_ADD_BUTTON_TITLE' => 'Add',
    'LBL_ADD_BUTTON' => 'Add',
    'LBL_ADD_DOCUMENT' => 'Add Document',
    'LBL_REPLACE_BUTTON' => 'Replace',
    'LBL_ADD_TO_PROSPECT_LIST_BUTTON_KEY' => 'L',
    'LBL_ADD_TO_PROSPECT_LIST_BUTTON_LABEL' => 'Add To Target List',
    'LBL_ADD_TO_PROSPECT_LIST_BUTTON_TITLE' => 'Add To Target List',
    'LBL_ADDITIONAL_DETAILS_CLOSE_TITLE' => 'Click to Close',
    'LBL_ADDITIONAL_DETAILS_CLOSE' => 'Close',
    'LBL_ADDITIONAL_DETAILS' => 'Additional Details',
    'LBL_ADMIN' => 'Admin',
//BEGIN SUGARCRM flav=sales ONLY
    'LBL_USER_ADMIN' => 'User Admin',
//END SUGARCRM flav=sales ONLY
    'LBL_ALT_HOT_KEY' => '',
    'LBL_ARCHIVE' => 'Archive',
    'LBL_ASSIGNED_TO_USER'=>'Assigned to User',
    'LBL_ASSIGNED_TO' => 'Assigned to:',
    'LBL_BACK' => 'Back',
    'LBL_BILL_TO_ACCOUNT'=>'Bill to Account',
    'LBL_BILL_TO_CONTACT'=>'Bill to Contact',
    'LBL_BILLING_ADDRESS'=>'Billing Address',
    'LBL_QUICK_CREATE_TITLE' => 'Quick Create',
    'LBL_BROWSER_TITLE' => 'SugarCRM - Commercial Open Source CRM',
    'LBL_BUGS'=>'Bugs',
    'LBL_BY' => 'by',
    'LBL_CALLS'=>'Calls',
    'LBL_CALL'=>'Call',
//BEGIN SUGARCRM flav!=sales ONLY
    'LBL_CAMPAIGNS_SEND_QUEUED' => 'Send Queued Campaign Emails',
//END SUGARCRM flav!=sales ONLY
    'LBL_SUBMIT_BUTTON_LABEL' => 'Submit',
    'LBL_CASE'=>'Case',
    'LBL_CASES'=>'Cases',
    'LBL_CHANGE_BUTTON_KEY' => 'G',
    'LBL_CHANGE_PASSWORD' => 'Change password',
    'LBL_CHANGE_BUTTON_LABEL' => 'Change',
    'LBL_CHANGE_BUTTON_TITLE' => 'Change',
    'LBL_CHARSET' => 'UTF-8',
    'LBL_CHECKALL' => 'Check All',
    'LBL_CITY' => 'City',
    'LBL_CLEAR_BUTTON_KEY' => 'C',
    'LBL_CLEAR_BUTTON_LABEL' => 'Clear',
    'LBL_CLEAR_BUTTON_TITLE' => 'Clear',
    'LBL_CLEARALL' => 'Clear All',
    'LBL_CLOSE_BUTTON_TITLE' =>'Close',
    'LBL_CLOSE_BUTTON_KEY'=>'Q',
    'LBL_CLOSE_WINDOW'=>'Close Window',
    'LBL_CLOSEALL_BUTTON_KEY' => 'Q',
    'LBL_CLOSEALL_BUTTON_LABEL' => 'Close All',
    'LBL_CLOSEALL_BUTTON_TITLE' => 'Close All',
    'LBL_CLOSE_AND_CREATE_BUTTON_LABEL' => 'Close and Create New',
    'LBL_CLOSE_AND_CREATE_BUTTON_TITLE' => 'Close and Create New',
    'LBL_CLOSE_AND_CREATE_BUTTON_KEY' => 'C',
    'LBL_OPEN_ITEMS' => 'Open Items:',
    'LBL_COMPOSE_EMAIL_BUTTON_KEY' => 'L',
    'LBL_COMPOSE_EMAIL_BUTTON_LABEL' => 'Compose Email',
    'LBL_COMPOSE_EMAIL_BUTTON_TITLE' => 'Compose Email',
    'LBL_SEARCH_DROPDOWN_YES'=>'Yes',
    'LBL_SEARCH_DROPDOWN_NO'=>'No',
    'LBL_CONTACT_LIST' => 'Contact List',
    'LBL_CONTACT'=>'Contact',
    'LBL_CONTACTS'=>'Contacts',
    'LBL_CONTRACTS'=>'Contracts',
    'LBL_COUNTRY' => 'Country:',
    'LBL_CREATE_BUTTON_LABEL' => 'Create',
    'LBL_CREATED_BY_USER'=>'Created by User',
    'LBL_CREATED_USER'=>'Created by User',
    'LBL_CREATED_ID' => 'Created By Id',
    'LBL_CREATED' => 'Created by',
    'LBL_CURRENT_USER_FILTER' => 'My Items:',
    'LBL_CURRENCY'=>'Currency:',
    'LBL_DOCUMENTS'=>'Documents',
    'LBL_DATE_ENTERED' => 'Date Created:',
    'LBL_DATE_MODIFIED' => 'Date Modified:',
    'LBL_EDIT_BUTTON' => 'Edit',
    'LBL_DUPLICATE_BUTTON' => 'Duplicate',
    'LBL_DELETE_BUTTON' => 'Delete',
    'LBL_DELETE' => 'Delete',
    'LBL_DELETED'=>'Deleted',
    'LBL_DIRECT_REPORTS'=>'Direct Reports',
    'LBL_DONE_BUTTON_KEY' => 'X',
    'LBL_DONE_BUTTON_LABEL' => 'Done',
    'LBL_DONE_BUTTON_TITLE' => 'Done',
    'LBL_DST_NEEDS_FIXIN' => 'The application requires a Daylight Saving Time fix to be applied.  Please go to the <a href="index.php?module=Administration&action=DstFix">Repair</a> link in the Admin console and apply the Daylight Saving Time fix.',
    'LBL_EDIT_AS_NEW_BUTTON_LABEL' => 'Edit As New',
    'LBL_EDIT_AS_NEW_BUTTON_TITLE' => 'Edit As New',
    'LBL_FAVORITES' => 'Favorites',
    'LBL_FILTER_MENU_BY' => 'Filter Menu By',
    'LBL_VCARD' => 'vCard',
    'LBL_EMPTY_VCARD' => 'Please select a vCard file',
    'LBL_IMAGE' => 'Image',
    'LBL_IMPORT_VCARD' => 'Import vCard:',
    'LBL_IMPORT_VCARD_BUTTON_KEY' => 'I',
    'LBL_IMPORT_VCARD_BUTTON_LABEL' => 'Import vCard',
    'LBL_IMPORT_VCARD_BUTTON_TITLE' => 'Import vCard',
    'LBL_VIEW_BUTTON_KEY' => 'V',
    'LBL_VIEW_BUTTON_LABEL' => 'View',
    'LBL_VIEW_BUTTON_TITLE' => 'View',
    'LBL_VIEW_BUTTON' => 'View',
    'LBL_EMAIL_PDF_BUTTON_KEY' => 'M',
    'LBL_EMAIL_PDF_BUTTON_LABEL' => 'Email as PDF',
    'LBL_EMAIL_PDF_BUTTON_TITLE' => 'Email as PDF',
    'LBL_EMAILS'=>'Emails',
    'LBL_EMPLOYEES' => 'Employees',
    'LBL_ENTER_DATE' => 'Enter Date',
    'LBL_EXPORT_ALL' => 'Export All',
    'LBL_EXPORT' => 'Export',
    'LBL_FAVORITES_FILTER' => 'My Favorites:',
    'LBL_GO_BUTTON_LABEL' => 'Go',
    'LBL_GS_HELP' => 'The fields in this module used in this search appear above.  The highlighted text matches your search criteria.',
    'LBL_HIDE'=>'Hide',
    'LBL_ID'=>'ID',
    'LBL_IMPORT' => 'Import',
    'LBL_IMPORT_STARTED' => 'Import Started: ',
    'LBL_MISSING_CUSTOM_DELIMITER' => 'Must specify a custom delimiter.',
    'LBL_LAST_VIEWED' => 'Recently Viewed',
    'LBL_SHOW_LESS' => 'Show Less',
    'LBL_SHOW_MORE' => 'Show More',
    'LBL_TODAYS_ACTIVITIES' => 'Today\'s Activities',
  //BEGIN SUGARCRM flav!=sales ONLY
    'LBL_LEADS'=>'Leads',
  //END SUGARCRM flav!=sales ONLY
    'LBL_LESS' => 'less',
//BEGIN SUGARCRM flav!=sales ONLY
    'LBL_CAMPAIGN' => 'Campaign:',
    'LBL_CAMPAIGNS' => 'Campaigns',
    'LBL_CAMPAIGNLOG' => 'CampaignLog',
    'LBL_CAMPAIGN_CONTACT'=>'Campaigns',
    'LBL_CAMPAIGN_ID'=>'campaign_id',
//END SUGARCRM flav!=sales ONLY
    'LBL_SITEMAP'=>'Sitemap',
    'LBL_THEME'=>'Theme:',
    'LBL_THEME_PICKER'=>'Page Style',
    'LBL_THEME_PICKER_IE6COMPAT_CHECK' => 'Warning: Internet Explorer 6 is not supported for the selected theme. Click OK to select it anyways or Cancel to select a different theme.',
    'LBL_FOUND_IN_RELEASE'=>'Found In Release',
    'LBL_FIXED_IN_RELEASE'=>'Fixed In Release',
    'LBL_LIST_ACCOUNT_NAME' => 'Account Name',
    'LBL_LIST_ASSIGNED_USER' => 'User',
    'LBL_LIST_CONTACT_NAME' => 'Contact Name',
    'LBL_LIST_CONTACT_ROLE' => 'Contact Role',
    'LBL_LIST_DATE_ENTERED'=>'Date Created',
    'LBL_LIST_EMAIL' => 'Email',
    'LBL_LIST_NAME' => 'Name',
    'LBL_LIST_OF' => 'of',
    'LBL_LIST_PHONE' => 'Phone',
    'LBL_LIST_RELATED_TO' => 'Related to',
    'LBL_LIST_USER_NAME' => 'User Name',
    'LBL_LISTVIEW_MASS_UPDATE_CONFIRM' => 'Are you sure you want to update the entire list?',
    'LBL_LISTVIEW_NO_SELECTED' => 'Please select at least 1 record to proceed.',
    'LBL_LISTVIEW_TWO_REQUIRED' => 'Please select at least 2 records to proceed.',
    'LBL_LISTVIEW_LESS_THAN_TEN_SELECT' => 'Please select less than 10 records to proceed.',
    'LBL_LISTVIEW_ALL' => 'All',
    'LBL_LISTVIEW_NONE' => 'Deselect All',
    'LBL_LISTVIEW_OPTION_CURRENT' => 'Select This Page',
    'LBL_LISTVIEW_OPTION_ENTIRE' => 'Select All',
    'LBL_LISTVIEW_OPTION_SELECTED' => 'Selected Records',
    'LBL_LISTVIEW_SELECTED_OBJECTS' => 'Selected: ',

    'LBL_LOCALE_NAME_EXAMPLE_FIRST' => 'David',
    'LBL_LOCALE_NAME_EXAMPLE_LAST' => 'Livingstone',
    'LBL_LOCALE_NAME_EXAMPLE_SALUTATION' => 'Dr.',
    'LBL_LOCALE_NAME_EXAMPLE_TITLE' => 'Code Monkey Extraordinaire',
    'LBL_LOGIN_TO_ACCESS' => 'Please sign in to access this area.',
    'LBL_LOGOUT' => 'Log Out',
    'LBL_PROFILE' => 'Profile',
    'LBL_MAILMERGE_KEY' => 'M',
    'LBL_MAILMERGE' => 'Mail Merge',
    'LBL_MASS_UPDATE' => 'Mass Update',
    'LBL_NO_MASS_UPDATE_FIELDS_AVAILABLE' => 'There are no fields available for the Mass Update operation',
    'LBL_OPT_OUT_FLAG_PRIMARY' => 'Opt out Primary Email',
    'LBL_MEETINGS'=>'Meetings',
    'LBL_MEETING'=>'Meeting',
    'LBL_MEETING_GO_BACK'=>'Go back to the meeting',
    'LBL_MEMBERS'=>'Members',
    'LBL_MEMBER_OF'=>'Member Of',
    'LBL_MODIFIED_BY_USER'=>'Modified by User',
    'LBL_MODIFIED_USER'=>'Modified by User',
    'LBL_MODIFIED' => 'Modified by',
    'LBL_MODIFIED_NAME'=>'Modified By',
    'LBL_MODIFIED_ID'=>'Modified By Id',
    'LBL_MORE' => 'More',
    'LBL_MY_ACCOUNT' => 'My Settings',
    'LBL_NAME' => 'Name',
    'LBL_NEW_BUTTON_KEY' => 'N',
    'LBL_NEW_BUTTON_LABEL' => 'Create',
    'LBL_NEW_BUTTON_TITLE' => 'Create',
    'LBL_NEXT_BUTTON_LABEL' => 'Next',
    'LBL_NONE' => '--None--',
    'LBL_NOTES'=>'Notes',
    'LBL_OPENALL_BUTTON_KEY' => 'O',
    'LBL_OPENALL_BUTTON_LABEL' => 'Open All',
    'LBL_OPENALL_BUTTON_TITLE' => 'Open All',
    'LBL_OPENTO_BUTTON_KEY' => 'T',
    'LBL_OPENTO_BUTTON_LABEL' => 'Open To: ',
    'LBL_OPENTO_BUTTON_TITLE' => 'Open To:',
    'LBL_OPPORTUNITIES'=>'Opportunities',
    'LBL_OPPORTUNITY_NAME' => 'Opportunity Name',
    'LBL_OPPORTUNITY'=>'Opportunity',
    'LBL_OR' => 'OR',
    'LBL_LOWER_OR' => 'or',
    'LBL_PANEL_ASSIGNMENT' => 'Other',
    'LBL_PANEL_ADVANCED' => 'More Information',
    'LBL_PARENT_TYPE' => 'Parent Type',
    'LBL_PERCENTAGE_SYMBOL' => '%',
    'LBL_PHASE' => 'Range',
    //BEGIN SUGARCRM flav!=com ONLY
    'LBL_PICTURE_FILE' => 'Picture',
    //END SUGARCRM flav!=com ONLY
    'LBL_POSTAL_CODE' => 'Postal Code:',
    'LBL_PRIMARY_ADDRESS_CITY' => 'Primary Address City:',
    'LBL_PRIMARY_ADDRESS_COUNTRY' => 'Primary Address Country:',
    'LBL_PRIMARY_ADDRESS_POSTALCODE' => 'Primary Address Postal Code:',
    'LBL_PRIMARY_ADDRESS_STATE' => 'Primary Address State:',
    'LBL_PRIMARY_ADDRESS_STREET_2' => 'Primary Address Street 2:',
    'LBL_PRIMARY_ADDRESS_STREET_3' => 'Primary Address Street 3:',
    'LBL_PRIMARY_ADDRESS_STREET' => 'Primary Address Street:',
    'LBL_PRIMARY_ADDRESS' => 'Primary Address:',

	'LBL_BILLING_STREET'=> 'Street:',
	'LBL_SHIPPING_STREET'=> 'Street:',

    //BEGIN SUGARCRM flav=pro || flav!=sales ONLY
    'LBL_PRODUCT_BUNDLES'=>'Product Bundles',
    'LBL_PRODUCT_BUNDLES'=>'Product Bundles',
    'LBL_PRODUCTS'=>'Products',
    'LBL_PROJECT_TASKS'=>'Project Tasks',
    'LBL_PROJECTS'=>'Projects',
    'LBL_PROJECTS'=>'Projects',
    'LBL_QUOTE_TO_OPPORTUNITY_KEY' => 'O',
    'LBL_QUOTE_TO_OPPORTUNITY_LABEL' => 'Create Opportunity from Quote',
    'LBL_QUOTE_TO_OPPORTUNITY_TITLE' => 'Create Opportunity from Quote',
    'LBL_QUOTES_SHIP_TO'=>'Quotes Ship to',
    'LBL_QUOTES'=>'Quotes',
    //END SUGARCRM flav=pro || flav!=sales ONLY

    'LBL_RELATED' => 'Related',
    'LBL_RELATED_INFORMATION' => 'Related Information',
    'LBL_RELATED_RECORDS' => 'Related Records',
    'LBL_REMOVE' => 'Remove',
    'LBL_REPORTS_TO' => 'Reports To',
    'LBL_REQUIRED_SYMBOL' => '*',
    'LBL_REQUIRED_TITLE' => 'Indicates required field',
    'LBL_EMAIL_DONE_BUTTON_LABEL' => 'Done',
    'LBL_SAVE_AS_BUTTON_KEY' => 'A',
    'LBL_SAVE_AS_BUTTON_LABEL' => 'Save As',
    'LBL_SAVE_AS_BUTTON_TITLE' => 'Save As',
    'LBL_FULL_FORM_BUTTON_KEY' => 'L',
    'LBL_FULL_FORM_BUTTON_LABEL' => 'Full Form',
    'LBL_FULL_FORM_BUTTON_TITLE' => 'Full Form',
    'LBL_SAVE_NEW_BUTTON_KEY' => 'V',
    'LBL_SAVE_NEW_BUTTON_LABEL' => 'Save & Create New',
    'LBL_SAVE_NEW_BUTTON_TITLE' => 'Save & Create New',
    'LBL_SAVE_OBJECT' => 'Save {0}',
    'LBL_SEARCH_BUTTON_KEY' => 'Q',
    'LBL_SEARCH_BUTTON_LABEL' => 'Search',
    'LBL_SEARCH_BUTTON_TITLE' => 'Search',
    'LBL_SEARCH' => 'Search',
    'LBL_SEARCH_TIPS' => "Press the search button or click enter to get an exact match for them.",
    'LBL_SEARCH_TIPS_2' => "Press the search button or click enter to get an exact match for",
    'LBL_SEARCH_MORE' => 'more',
    'LBL_SEE_ALL' => 'See All',
    'LBL_UPLOAD_IMAGE_FILE_INVALID' => 'Invalid file format, only image file can be uploaded.',
    'LBL_SELECT_BUTTON_KEY' => 'T',
    'LBL_SELECT_BUTTON_LABEL' => 'Select',
    'LBL_SELECT_BUTTON_TITLE' => 'Select',
    'LBL_SELECT_TEAMS_KEY' => 'Z',
    'LBL_SELECT_TEAMS_LABEL' => 'Add Team(s)',
    'LBL_SELECT_TEAMS_TITLE' => 'Add Teams(s)',
    'LBL_BROWSE_DOCUMENTS_BUTTON_KEY' => 'B',
    'LBL_BROWSE_DOCUMENTS_BUTTON_LABEL' => 'Browse Documents',
    'LBL_BROWSE_DOCUMENTS_BUTTON_TITLE' => 'Browse Documents',
    'LBL_SELECT_CONTACT_BUTTON_KEY' => 'T',
    'LBL_SELECT_CONTACT_BUTTON_LABEL' => 'Select Contact',
    'LBL_SELECT_CONTACT_BUTTON_TITLE' => 'Select Contact',
    'LBL_GRID_SELECTED_FILE' => 'selected file',
    'LBL_GRID_SELECTED_FILES' => 'selected files',
    'LBL_SELECT_REPORTS_BUTTON_LABEL' => 'Select from Reports',
    'LBL_SELECT_REPORTS_BUTTON_TITLE' => 'Select Reports',
    'LBL_SELECT_USER_BUTTON_KEY' => 'U',
    'LBL_SELECT_USER_BUTTON_LABEL' => 'Select User',
    'LBL_SELECT_USER_BUTTON_TITLE' => 'Select User',
    // Clear buttons take up too many keys, lets default the relate and collection ones to be empty
    'LBL_ACCESSKEY_CLEAR_RELATE_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_RELATE_TITLE' => 'Clear Selection',
    'LBL_ACCESSKEY_CLEAR_RELATE_LABEL' => 'Clear Selection',
    'LBL_ACCESSKEY_CLEAR_COLLECTION_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_COLLECTION_TITLE' => 'Clear Selection',
    'LBL_ACCESSKEY_CLEAR_COLLECTION_LABEL' => 'Clear Selection',
    'LBL_ACCESSKEY_SELECT_FILE_KEY' => 'F',
    'LBL_ACCESSKEY_SELECT_FILE_TITLE' => 'Select File',
    'LBL_ACCESSKEY_SELECT_FILE_LABEL' => 'Select File',
    'LBL_ACCESSKEY_CLEAR_FILE_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_FILE_TITLE' => 'Clear File',
    'LBL_ACCESSKEY_CLEAR_FILE_LABEL' => 'Clear File',


    'LBL_ACCESSKEY_SELECT_USERS_KEY' => 'U',
    'LBL_ACCESSKEY_SELECT_USERS_TITLE' => 'Select User',
    'LBL_ACCESSKEY_SELECT_USERS_LABEL' => 'Select User',
    'LBL_ACCESSKEY_CLEAR_USERS_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_USERS_TITLE' => 'Clear User',
    'LBL_ACCESSKEY_CLEAR_USERS_LABEL' => 'Clear User',
    'LBL_ACCESSKEY_SELECT_ACCOUNTS_KEY' => 'A',
    'LBL_ACCESSKEY_SELECT_ACCOUNTS_TITLE' => 'Select Account',
    'LBL_ACCESSKEY_SELECT_ACCOUNTS_LABEL' => 'Select Account',
    'LBL_ACCESSKEY_CLEAR_ACCOUNTS_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_ACCOUNTS_TITLE' => 'Clear Account',
    'LBL_ACCESSKEY_CLEAR_ACCOUNTS_LABEL' => 'Clear Account',
    'LBL_ACCESSKEY_SELECT_CAMPAIGNS_KEY' => 'M',
    'LBL_ACCESSKEY_SELECT_CAMPAIGNS_TITLE' => 'Select Campaign',
    'LBL_ACCESSKEY_SELECT_CAMPAIGNS_LABEL' => 'Select Campaign',
    'LBL_ACCESSKEY_CLEAR_CAMPAIGNS_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_CAMPAIGNS_TITLE' => 'Clear Campaign',
    'LBL_ACCESSKEY_CLEAR_CAMPAIGNS_LABEL' => 'Clear Campaign',
    'LBL_ACCESSKEY_SELECT_CONTACTS_KEY' => 'C',
    'LBL_ACCESSKEY_SELECT_CONTACTS_TITLE' => 'Select Contact',
    'LBL_ACCESSKEY_SELECT_CONTACTS_LABEL' => 'Select Contact',
    'LBL_ACCESSKEY_CLEAR_CONTACTS_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_CONTACTS_TITLE' => 'Clear Contact',
    'LBL_ACCESSKEY_CLEAR_CONTACTS_LABEL' => 'Clear Contact',
    'LBL_ACCESSKEY_SELECT_TEAMSET_KEY' => 'Z',
    'LBL_ACCESSKEY_SELECT_TEAMSET_TITLE' => 'Select Team',
    'LBL_ACCESSKEY_SELECT_TEAMSET_LABEL' => 'Select Team',
    'LBL_ACCESSKEY_CLEAR_TEAMS_KEY' => ' ',
    'LBL_ACCESSKEY_CLEAR_TEAMS_TITLE' => 'Clear Team',
    'LBL_ACCESSKEY_CLEAR_TEAMS_LABEL' => 'Clear Team',
    'LBL_SERVER_RESPONSE_RESOURCES' => 'Resources used to construct this page (queries, files)',
    'LBL_SERVER_RESPONSE_TIME_SECONDS' => 'seconds.',
    'LBL_SERVER_RESPONSE_TIME' => 'Server response time:',
    'LBL_SERVER_MEMORY_BYTES' => 'bytes',
    'LBL_SERVER_MEMORY_USAGE' => 'Server Memory Usage: {0} ({1})',
    'LBL_SERVER_MEMORY_LOG_MESSAGE' => 'Usage: - module: {0} - action: {1}',
    'LBL_SERVER_PEAK_MEMORY_USAGE' => 'Server Peak Memory Usage: {0} ({1})',
    'LBL_SHIP_TO_ACCOUNT'=>'Ship to Account',
    'LBL_SHIP_TO_CONTACT'=>'Ship to Contact',
    'LBL_SHIPPING_ADDRESS'=>'Shipping Address',
    'LBL_SHORTCUTS' => 'Shortcuts',
    'LBL_SHOW'=>'Show',
    'LBL_SQS_INDICATOR' => '',
    'LBL_STATE' => 'State:',
    'LBL_STATUS_UPDATED'=>'Your Status for this event has been updated!',
    'LBL_STATUS'=>'Status:',
    'LBL_STREET'=>'Street',
    'LBL_SUBJECT' => 'Subject',

    'LBL_INBOUNDEMAIL_ID' => 'Inbound Email ID',

    /* The following version of LBL_SUGAR_COPYRIGHT is intended for Sugar Open Source only. */

    'LBL_SUGAR_COPYRIGHT' => '&copy; 2004-2012 SugarCRM Inc. The Program is provided AS IS, without warranty.  Licensed under <a href="LICENSE.txt" target="_blank" class="copyRightLink">AGPLv3</a>.<br />SugarCRM is a trademark of SugarCRM, Inc. All other company and product names may be trademarks of the respective companies with which they are associated.',



    // The following version of LBL_SUGAR_COPYRIGHT is for Professional and Enterprise editions.

    'LBL_SUGAR_COPYRIGHT_SUB' => '&copy; 2004-2012 <a href="http://www.sugarcrm.com" target="_blank" class="copyRightLink">SugarCRM Inc.</a> All Rights Reserved.<br />SugarCRM is a trademark of SugarCRM, Inc. All other company and product names may be trademarks of the respective companies with which they are associated.',


    // LOGIN PAGE STRINGS
    'LBL_LOGIN_BUTTON_KEY' => 'L',
    'LBL_LOGIN_BUTTON_LABEL' => 'Log In',
    'LBL_LOGIN_BUTTON_TITLE' => 'Log In',
    'LBL_LOGIN_WELCOME_TO' => 'Welcome to',
    'LBL_LOGIN_OPTIONS' => 'Options',
    'LBL_LOGIN_FORGOT_PASSWORD' => 'Forgot Password?',
    'LBL_LOGIN_SUBMIT' => 'Submit',
    'LBL_LOGIN_ATTEMPTS_OVERRUN' => 'Too many failed login attempts.',
    'LBL_LOGIN_LOGIN_TIME_ALLOWED' => 'You can try logging in again in ',
    'LBL_LOGIN_LOGIN_TIME_DAYS' => 'days.',
    'LBL_LOGIN_LOGIN_TIME_HOURS' => 'h.',
    'LBL_LOGIN_LOGIN_TIME_MINUTES' => 'min.',
    'LBL_LOGIN_LOGIN_TIME_SECONDS' => 'sec.',
    'LBL_LOGIN_ADMIN_CALL' => 'Please contact the system administrator.',
    'LBL_LOGIN_USERNAME' => 'Username',
    'LBL_LOGIN_PASSWORD' => 'Password',
    // END LOGIN PAGE STRINGS

    'LBL_SYNC' => 'Sync',
    'LBL_SYNC' => 'Sync',
    'LBL_TABGROUP_ALL' => 'All',
    'LBL_TABGROUP_ACTIVITIES' => 'Activities',
    'LBL_TABGROUP_COLLABORATION' => 'Collaboration',
    'LBL_TABGROUP_HOME' => 'Dashboard',
    'LBL_TABGROUP_MARKETING' => 'Marketing',
    'LBL_TABGROUP_MY_PORTALS' => 'My Sites',
    'LBL_TABGROUP_OTHER' => 'Other',
    'LBL_TABGROUP_REPORTS' => 'Reports',
    'LBL_TABGROUP_SALES' => 'Sales',
    'LBL_TABGROUP_SUPPORT' => 'Support',
    'LBL_TABGROUP_TOOLS' => 'Tools',
    'LBL_TASKS'=>'Tasks',
    'LBL_TEAMS_LINK'=>'Teams',
    'LBL_THEME_COLOR'=>'Color',
    'LBL_THEME_FONT'=>'Font',
    'LBL_THOUSANDS_SYMBOL' => 'K',
    'LBL_TRACK_EMAIL_BUTTON_KEY' => 'K',
    'LBL_TRACK_EMAIL_BUTTON_LABEL' => 'Archive Email',
    'LBL_TRACK_EMAIL_BUTTON_TITLE' => 'Archive Email',
    'LBL_UNAUTH_ADMIN' => 'Unauthorized access to administration',
    'LBL_UNDELETE_BUTTON_LABEL' => 'Undelete',
    'LBL_UNDELETE_BUTTON_TITLE' => 'Undelete',
    'LBL_UNDELETE_BUTTON' => 'Undelete',
    'LBL_UNDELETE' => 'Undelete',
    'LBL_UNSYNC' => 'Unsync',
    'LBL_UPDATE' => 'Update',
    'LBL_USER_LIST' => 'User List',
    'LBL_USERS_SYNC'=>'Users Sync',
    'LBL_USERS'=>'Users',
    'LBL_VERIFY_EMAIL_ADDRESS'=>'Checking for existing email entry...',
    'LBL_VERIFY_PORTAL_NAME'=>'Checking for existing portal name...',
    'LBL_VIEW_IMAGE' => 'view',
    'LBL_VIEW_PDF_BUTTON_KEY' => 'P',
    'LBL_VIEW_PDF_BUTTON_LABEL' => 'Print as PDF',
    'LBL_VIEW_PDF_BUTTON_TITLE' => 'Print as PDF',


    'LNK_ABOUT' => 'About',
    'LNK_ADVANCED_SEARCH' => 'Advanced Search',
    'LNK_BASIC_SEARCH' => 'Basic Search',
    'LNK_SEARCH_NO_RESULTS' => 'No results were found.',
    'LNK_SEARCH_FTS_VIEW_ALL' => 'View all results',
    'LNK_SEARCH_NONFTS_VIEW_ALL' => 'Show All',
    'LNK_CLOSE' => 'close',
    'LBL_MODIFY_CURRENT_SEARCH'=> 'Modify current search',
    'LNK_SAVED_VIEWS' => 'Layout Options',
    'LNK_DELETE_ALL' => 'del all',
    'LNK_DELETE' => 'delete',
    'LNK_EDIT' => 'edit',
    'LNK_GET_LATEST'=>'Get latest',
    'LNK_GET_LATEST_TOOLTIP'=>'Replace with latest version',
    'LNK_HELP' => 'Help',
    'LNK_CREATE' => 'Create',
    'LNK_CREATE_WHEN_EMPTY' => 'Create a record now.',
    'LNK_LIST_END' => 'End',
    'LNK_LIST_NEXT' => 'Next',
    'LNK_LIST_PREVIOUS' => 'Previous',
    'LNK_LIST_RETURN' => 'Return to List',
    'LNK_LIST_START' => 'Start',
    'LNK_LOAD_SIGNED'=>'Sign',
    'LNK_LOAD_SIGNED_TOOLTIP'=>'Replace with signed document',
    'LNK_PRINT' => 'Print',
    'LNK_BACKTOTOP' => 'Back to top',
    'LNK_REMOVE' => 'remove',
    'LNK_RESUME' => 'Resume',
    'LNK_VIEW_CHANGE_LOG' => 'View Change Log',


    'NTC_CLICK_BACK' => 'Please click the browser back button and fix the error.',
    'NTC_DATE_FORMAT' => '(yyyy-mm-dd)',
    'NTC_DATE_TIME_FORMAT' => '(yyyy-mm-dd 24:00)',
    'NTC_DELETE_CONFIRMATION_MULTIPLE' => 'Are you sure you want to delete selected record(s)?',
    'NTC_TEMPLATE_IS_USED' => 'The template is used in at least one email marketing record. Are you sure you want to delete it?',
    'NTC_TEMPLATES_IS_USED' => "The following templates are used in email marketing records. Are you sure you want to delete them?\n",
    'NTC_DELETE_CONFIRMATION' => 'Are you sure you want to delete this record?',
    'NTC_DELETE_CONFIRMATION_NUM' => 'Are you sure you want to delete the ',
    'NTC_UPDATE_CONFIRMATION_NUM' => 'Are you sure you want to update the ',
    'NTC_DELETE_SELECTED_RECORDS' =>' selected record(s)?',
    'NTC_LOGIN_MESSAGE' => 'Please enter your user name and password.',
    'NTC_NO_ITEMS_DISPLAY' => 'none',
    'NTC_REMOVE_CONFIRMATION' => 'Are you sure you want to remove this relationship? Only the relationship will be removed. The record will not be deleted.',
    'NTC_REQUIRED' => 'Indicates required field',
    'NTC_SUPPORT_SUGARCRM' => 'Support the SugarCRM open source project with a donation through PayPal - it\'s fast, free and secure!',
    'NTC_TIME_FORMAT' => '(24:00)',
    'NTC_WELCOME' => 'Welcome',
    'NTC_YEAR_FORMAT' => '(yyyy)',
    'LOGIN_LOGO_ERROR'=> 'Please replace the SugarCRM logos.',
    'ERROR_FULLY_EXPIRED'=> "Your company's license for SugarCRM has expired for more than 7 days and needs to be brought up to date. Only admins may login.",
    'ERROR_LICENSE_EXPIRED'=> "Your company's license for SugarCRM needs to be updated. Only admins may login",
    'ERROR_LICENSE_VALIDATION'=> "Your company's license for SugarCRM needs to be validated. Only admins may login",
    'WARN_BROWSER_VERSION_WARNING' => '<p><b>Warning:</b>The browser or browser version you are using is not supported.</p><p>The following browser versions are recommended</p><ul><li>Internet Explorer 9</li><li>Mozilla Firefox 13, 14 </li><li>Safari 5.1</li><li>Google Chrome</li></ul>',
    'WARN_LICENSE_SEATS'=>  "Warning: The number of active users is already the maximum number of licenses allowed.",
    'WARN_LICENSE_SEATS_MAXED'=>  "Warning: The number of active users exceeds the maximum number of licenses allowed.",
    'WARN_ONLY_ADMINS'=> "Only admins may log in.",
    'WARN_UNSAVED_CHANGES'=> "You are about to leave this record without saving any changes you may have made to the record. Are you sure you want to navigate away from this record?",
    'ERROR_NO_RECORD' => 'Error retrieving record.  This record may be deleted or you may not be authorized to view it.',
    'ERROR_TYPE_NOT_VALID' => 'Error. This type is not valid.',
    'ERROR_MAX_FIELD_LENGTH' => 'Error. The max length of this field is {{this}}.',
    'ERROR_MIN_FIELD_LENGTH' => 'Error. The min length of this field is {{this}}.',
    'ERROR_EMAIL' => 'Error. Invalid Email Address: {{#each this}}{{this}} {{/each}}',
    'ERROR_FIELD_REQUIRED' => 'Error. This field is required.',
    'ERROR_MINVALUE' => 'Error. This minimum value of this field is {{this}}.',
    'ERROR_MAXVALUE' => 'Error. This maximum value of this field is {{this}}.',
    'ERROR_NO_BEAN' => 'Failed to get bean.',
    'LBL_DUP_MERGE'=>'Find Duplicates',
    'LBL_MANAGE_SUBSCRIPTIONS'=>'Manage Subscriptions',
    'LBL_MANAGE_SUBSCRIPTIONS_FOR'=>'Manage Subscriptions for ',
    'LBL_SUBSCRIBE'=>'Subscribe',
    'LBL_UNSUBSCRIBE'=>'Unsubscribe',
    // Ajax status strings
    'LBL_LOADING' => 'Loading ...',
    'LBL_SEARCHING' => 'Searching...',
    'LBL_SAVING_LAYOUT' => 'Saving Layout ...',
    'LBL_SAVED_LAYOUT' => 'Layout has been saved.',
    'LBL_SAVED' => 'Saved',
    'LBL_SAVING' => 'Saving',
    'LBL_FAILED' => 'Failed!',
    'LBL_DISPLAY_COLUMNS' => 'Display Columns',
    'LBL_HIDE_COLUMNS' => 'Hide Columns',
    'LBL_SEARCH_CRITERIA' => 'Search Criteria',
    'LBL_SAVED_VIEWS' => 'Saved Views',
    'LBL_PROCESSING_REQUEST'=>'Processing..',
    'LBL_REQUEST_PROCESSED'=>'Done',
    'LBL_AJAX_FAILURE' => 'Ajax failure',
    //BEGIN SUGARCRM flav=pro ONLY
    'LBL_OC_STATUS' => 'Offline Client Status',
    'LBL_OC_STATUS_TEXT' => 'Indicates whether or not the current user is able to use an Offline Client.',
    'LBL_OC_DEFAULT_STATUS' => 'Inactive',
    //END SUGARCRM flav=pro ONLY
    'LBL_MERGE_DUPLICATES'  => 'Merge',
    'LBL_SAVED_SEARCH_SHORTCUT' => 'Saved Searches',
    'LBL_SEARCH_POPULATE_ONLY'=> 'Perform a search using the search form above',
    'LBL_DETAILVIEW'=>'Detail View',
    'LBL_LISTVIEW'=>'List View',
    'LBL_EDITVIEW'=>'Edit View',
    'LBL_SEARCHFORM'=>'Search Form',
    'LBL_SAVED_SEARCH_ERROR' => 'Please provide a name for this view.',
    'LBL_DISPLAY_LOG' => 'Display Log',
    'ERROR_JS_ALERT_SYSTEM_CLASS' => 'System',
    'ERROR_JS_ALERT_TIMEOUT_TITLE' => 'Session Timeout',
    'ERROR_JS_ALERT_TIMEOUT_MSG_1' => 'Your session is about to timeout in 2 minutes. Please save your work.',
    'ERROR_JS_ALERT_TIMEOUT_MSG_2' =>'Your session has timed out.',
    'MSG_JS_ALERT_MTG_REMINDER_AGENDA' => "\nAgenda: ",
    'MSG_JS_ALERT_MTG_REMINDER_MEETING' => 'Meeting',
    'MSG_JS_ALERT_MTG_REMINDER_CALL' => 'Call',
    'MSG_JS_ALERT_MTG_REMINDER_TIME' => 'Time: ',
    'MSG_JS_ALERT_MTG_REMINDER_LOC' => 'Location: ',
    'MSG_JS_ALERT_MTG_REMINDER_DESC' => 'Description: ',
    'MSG_JS_ALERT_MTG_REMINDER_CALL_MSG' => "\nClick OK to view this call or click Cancel to dismiss this message.",
  	'MSG_JS_ALERT_MTG_REMINDER_MEETING_MSG' => "\nClick OK to view this meeting or click Cancel to dismiss this message.",
 	'MSG_LIST_VIEW_NO_RESULTS_BASIC' => "No results found.",
	'MSG_LIST_VIEW_NO_RESULTS' => "No results found for <item1>",
 	'MSG_LIST_VIEW_NO_RESULTS_SUBMSG' => "Create <item1> as a new <item2>",
	'MSG_EMPTY_LIST_VIEW_NO_RESULTS' => "You currently have no records saved. <item2> or <item3> one now.",
	'MSG_EMPTY_LIST_VIEW_NO_RESULTS_SUBMSG' =>	"<item4> to learn more about the <item1> module. In order to access more information, use the user menu drop down located on the main navigation bar to access Help.",

    'LBL_CLICK_HERE' => "Click here",
    // contextMenu strings
    'LBL_ADD_TO_FAVORITES' => 'Add to My Favorites',
    'LBL_MARK_AS_FAVORITES' => 'Mark as Favorite',
    'LBL_CREATE_CONTACT' => 'Create Contact',
    'LBL_CREATE_CASE' => 'Create Case',
    'LBL_CREATE_NOTE' => 'Create Note',
    'LBL_CREATE_OPPORTUNITY' => 'Create Opportunity',
    'LBL_SCHEDULE_CALL' => 'Log Call',
    'LBL_SCHEDULE_MEETING' => 'Schedule Meeting',
    'LBL_CREATE_TASK' => 'Create Task',
    'LBL_REMOVE_FROM_FAVORITES' => 'Remove From My Favorites',
    //web to lead
    'LBL_GENERATE_WEB_TO_LEAD_FORM' => 'Generate Form',
    'LBL_SAVE_WEB_TO_LEAD_FORM' =>'Save Web To Lead Form',

    'LBL_PLEASE_SELECT' => 'Please Select',
    'LBL_REDIRECT_URL'=>'Redirect URL',
//BEGIN SUGARCRM flav!=sales ONLY
    'LBL_RELATED_CAMPAIGN' =>'Related campaign',
//END SUGARCRM flav!=sales ONLY
    'LBL_ADD_ALL_LEAD_FIELDS' => 'Add All Fields',
    'LBL_REMOVE_ALL_LEAD_FIELDS' => 'Remove All Fields',
    'LBL_ONLY_IMAGE_ATTACHMENT' => 'Only image type attachment can be embedded',
    'LBL_REMOVE' => 'Remove',
    'LBL_TRAINING' => 'Support',
    'ERR_DATABASE_CONN_DROPPED'=>'Error executing a query. Possibly, your database dropped the connection. Please refresh this page, you may need to restart you web server.',
    'ERR_DATABSE_RELATIONSHIP_QUERY'=>'Error setting {0} relationship: {1}',
    'ERR_MSSQL_DB_CONTEXT' =>'Changed database context to',
  'ERR_MSSQL_WARNING' =>'Warning:',

    //Meta-Data framework
    'ERR_MISSING_VARDEF_NAME' => 'Warning: field [[field]] does not have a mapped entry in [moduleDir] vardefs.php file',
    'ERR_CANNOT_CREATE_METADATA_FILE' => 'Error: File [[file]] is missing.  Unable to create because no corresponding HTML file was found.',
  'ERR_CANNOT_FIND_MODULE' => 'Error: Module [module] does not exist.',
  'LBL_ALT_ADDRESS' => 'Other Address:',
    'ERR_SMARTY_UNEQUAL_RELATED_FIELD_PARAMETERS' => 'Error: There are an unequal number of arguments for the \'key\' and \'copy\' elements in the displayParams array.',
    'ERR_SMARTY_MISSING_DISPLAY_PARAMS' => 'Missing index in displayParams Array for: ',

    /* MySugar Framework (for Home and Dashboard) */
    'LBL_DASHLET_CONFIGURE_GENERAL' => 'General',
    'LBL_DASHLET_CONFIGURE_FILTERS' => 'Filters',
    'LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY' => 'Only My Items',
    'LBL_DASHLET_CONFIGURE_TITLE' => 'Title',
    'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS' => 'Display Rows',

    // MySugar status strings
    'LBL_CREATING_NEW_PAGE' => 'Creating New Page ...',
    'LBL_NEW_PAGE_FEEDBACK' => 'You have created a new page. You may add new content with the Add Sugar Dashlets menu option.',
    'LBL_DELETE_PAGE_CONFIRM' => 'Are you sure you want to delete this page?',
    'LBL_SAVING_PAGE_TITLE' => 'Saving Page Title ...',
    'LBL_RETRIEVING_PAGE' => 'Retrieving Page ...',
    'LBL_MAX_DASHLETS_REACHED' => 'You have reached the maximum number of Sugar Dashlets your adminstrator has set. Please remove a Sugar Dashlet to add more.',
    'LBL_ADDING_DASHLET' => 'Adding Sugar Dashlet ...',
    'LBL_ADDED_DASHLET' => 'Sugar Dashlet Added',
    'LBL_REMOVE_DASHLET_CONFIRM' => 'Are you sure you want to remove the Sugar Dashlet?',
    'LBL_REMOVING_DASHLET' => 'Removing Sugar Dashlet ...',
    'LBL_REMOVED_DASHLET' => 'Sugar Dashlet Removed',

    // MySugar Menu Options
    'LBL_ADD_PAGE' => 'Add Page',
    'LBL_DELETE_PAGE' => 'Delete Page',
    'LBL_CHANGE_LAYOUT' => 'Change Layout',
    'LBL_RENAME_PAGE' => 'Rename Page',

    'LBL_LOADING_PAGE' => 'Loading page, please wait...',

    'LBL_RELOAD_PAGE' => 'Please <a href="javascript: window.location.reload()">reload the window</a> to use this Sugar Dashlet.',
    'LBL_ADD_DASHLETS' => 'Add Dashlets',
    'LBL_CLOSE_DASHLETS' => 'Close',
    'LBL_OPTIONS' => 'Options',
    'LBL_NUMBER_OF_COLUMNS' => 'Select the number of columns',
    'LBL_1_COLUMN' => '1 Column',
    'LBL_2_COLUMN' => '2 Column',
    'LBL_3_COLUMN' => '3 Column',
    'LBL_PAGE_NAME' => 'Page Name',

    'LBL_SEARCH_RESULTS' => 'Search Results',
    'LBL_SEARCH_MODULES' => 'Modules',
    'LBL_SEARCH_CHARTS' => 'Charts',
    'LBL_SEARCH_REPORT_CHARTS' => 'Report Charts',
    'LBL_SEARCH_TOOLS' => 'Tools',
    'LBL_SEARCH_HELP_TITLE' => 'Search Tips',
    'LBL_SEARCH_HELP_CLOSE_TOOLTIP' => 'Close',
    'LBL_SEARCH_RESULTS_FOUND' => 'Search Results Found',
    'LBL_SEARCH_RESULTS_TIME' => 'ms.',
    'ERR_BLANK_PAGE_NAME' => 'Please enter a page name.',
    /* End MySugar Framework strings */

    'LBL_NO_IMAGE' => 'No Image',

    'LBL_MODULE' => 'Module',

    //adding a label for address copy from left
    'LBL_COPY_ADDRESS_FROM_LEFT' => 'Copy address from left:',
    'LBL_SAVE_AND_CONTINUE' => 'Save and Continue',

    'LBL_SEARCH_HELP_TEXT' => '<p><br /><strong>Multiselect controls</strong></p><ul><li>Click on the values to select an attribute.</li><li>Ctrl-click&nbsp;to&nbsp;select multiple. Mac users use CMD-click.</li><li>To select all values between two attributes,&nbsp; click first value&nbsp;and then shift-click last value.</li></ul><p><strong>Advanced Search & Layout Options</strong><br><br>Using the <b>Saved Search & Layout</b> option, you can save a set of search parameters and/or a custom List View layout in order to quickly obtain the desired search results in the future. You can save an unlimited number of custom searches and layouts. All saved searches appear by name in the Saved Searches list, with the last loaded saved search appearing at the top of the list.<br><br>To customize the List View layout, use the Hide Columns and Display Columns boxes to select which fields to display in the search results. For example, you can view or hide details such as the record name, and assigned user, and assigned team in the search results. To add a column to List View, select the field from the Hide Columns list and use the left arrow to move it to the Display Columns list. To remove a column from List View, select it from the Display Columns list and use the right arrow to move it to the Hide Columns list.<br><br>If you save layout settings, you will be able to load them at any time to view the search results in the custom layout.<br><br>To save and update a search and/or layout:<ol><li>Enter a name for the search results in the <b>Save this search as</b> field and click <b>Save</b>.The name now displays in the Saved Searches list adjacent to the <b>Clear</b> button.</li><li>To view a saved search, select it from the Saved Searches list. The search results are displayed in the List View.</li><li>To update the properties of a saved search, select the saved search from the list, enter the new search criteria and/or layout options in the Advanced Search area, and click <b>Update</b> next to <b>Modify Current Search</b>.</li><li>To delete a saved search, select it in the Saved Searches list, click <b>Delete</b> next to <b>Modify Current Search</b>, and then click <b>OK</b> to confirm the deletion.</li></ol><p><strong>Tips</strong><br><br>By using the % as a wildcard operator you can make your search more broad.  For example instead of just searching for results that equal "Apples" you could change your search to "Apples%" which would match all results that start with the word Apples but could contain other characters as well.</p>' ,

    //resource management
    'ERR_QUERY_LIMIT' => 'Error: Query limit of $limit reached for $module module.',
    'ERROR_NOTIFY_OVERRIDE' => 'Error: ResourceObserver->notify() needs to be overridden.',

    //tracker labels
    'ERR_MONITOR_FILE_MISSING' => 'Error: Unable to create monitor because metadata file is empty or file does not exist.',
    'ERR_MONITOR_NOT_CONFIGURED' => 'Error: There is no monitor configured for requested name',
    'ERR_UNDEFINED_METRIC' => 'Error: Unable to set value for undefined metric',
    'ERR_STORE_FILE_MISSING' => 'Error: Unable to find Store implementation file',

    'LBL_MONITOR_ID' => 'Monitor Id',
    //BEGIN SUGARCRM flav=pro ONLY
    'LBL_TEAM_ID' => 'Team id',
    //END SUGARCRM flav=pro ONLY
    //BEGIN SUGARCRM flav=pro || flav=sales ONLY
    'LBL_SELECT_MODULE' => 'Select a module:',
    //END SUGARCRM flav=pro || flav=sales ONLY
    'LBL_USER_ID' => 'User Id',
    'LBL_MODULE_NAME' => 'Module Name',
    'LBL_ITEM_ID' => 'Item Id',
    'LBL_ITEM_SUMMARY' => 'Item Summary',
    'LBL_ACTION' => 'Action',
    'LBL_SESSION_ID' => 'Session Id',
    'LBL_BREADCRUMBSTACK_CREATED' => 'BreadCrumbStack created for user id {0}',
    'LBL_VISIBLE' => 'Record Visible',
    'LBL_DATE_LAST_ACTION' => 'Date of Last Action',

    //BEGIN SUGARCRM flav=pro ONLY
    //Tracker Queries
    'LBL_SQL_TEXT' => 'SQL Text',
    'LBL_QUERY_HASH' => 'SQL Hash',
    'LBL_SEC_TOTAL' => 'Total Seconds',
    'LBL_SEC_AVG' => 'Average Seconds',
    'LBL_RUN_COUNT' => 'Query Count',

    //Tracker Sessions
    'LBL_CLIENT_IP' => 'Client IP Address',
    'LBL_DATE_START' => 'Date Start',
    'LBL_ACTIVE' => 'Session Active',
    'LBL_ROUNDTRIPS' => 'Action Count',
  'LBL_SECONDS' => 'Seconds Active',

  //Tracker Performance
    'LBL_DB_ROUND_TRIPS' => 'Database Roundtrips',
    'LBL_FILES_OPENED' => 'Files Accessed',
    'LBL_MEMORY_USAGE' => 'Memory Usage (bytes)',

    //Twitter Connector
    'LBL_TWITTER_DATA_LOADING' => 'Loading Twitter Connector...',
    'LBL_TWITTER_DATA_EMPTY' => 'No data availlable from Twitter',
    //END SUGARCRM flav=pro ONLY

    //BEGIN SUGARCRM flav!=com ONLY
    'LBL_PLUGIN_OUTLOOK_NAME' => 'Sugar Plug-in for Outlook',
    'LBL_PLUGIN_OUTLOOK_DESC' => 'Integrate Sugar e-mail and calendar information with Microsoft Outlook.',
    'LBL_PLUGIN_WORD_NAME' => 'Sugar Plug-in for Word',
    'LBL_PLUGIN_WORD_DESC' => 'Automatically populate information from SugarCRM into form letters, direct mail and other Microsoft documents.',
    'LBL_PLUGIN_EXCEL_NAME' => 'Sugar Plug-in for Excel',
    'LBL_PLUGIN_EXCEL_DESC' => 'Integrate Sugar with spreadsheets for better analysis of key metrics.',
    'LBL_PLUGINS_TITLE' => 'Sugar Plug-ins for Microsoft Office<sup>TM</sup>',
    'LBL_PLUGINS_DESC' => 'Read about the plug-in features within the <a href="https://www.sugarcrm.com/crm/support/documentation/SugarPlugIns">Plug-ins Documentation</a>.',
    //END SUGARCRM flav!=com ONLY

     //BEGIN SUGARCRM flav=ent ONLY
    'LBL_PLUGINS_LOTUS_TITLE' => 'Sugar Plug-ins for Lotus Notes<sup>TM</sup>',
    'LBL_PLUGIN_LOTUS_NAME' => 'Sugar Plug-in For Lotus Notes',
    'LBL_PLUGIN_LOTUS_DESC' => 'Integrate Sugar with Lotus Notes.',
     //END SUGARCRM flav=ent ONLY


    //jc:#12287 - For javascript validation messages
    'MSG_IS_NOT_BEFORE' => 'is not before',
  'MSG_IS_MORE_THAN' => 'is more than',
  'MSG_IS_LESS_THAN' => 'is less than',
  'MSG_SHOULD_BE' => 'should be',
  'MSG_OR_GREATER' => 'or greater',

    'LBL_PORTAL_WELCOME_TITLE' => 'Welcome to Sugar Portal 5.1.0',
    'LBL_PORTAL_WELCOME_INFO' => 'Sugar Portal is a framework which provides real-time view of cases, bugs & newsletters etc to customers. This is an external facing interface to Sugar that can be deployed within any website.  Stay tuned for more customer self service features like Project Management and Forums in our future releases.',
    'LBL_LIST' => 'List',
    'LBL_CREATE_CASE' => 'Create Case',
    'LBL_CREATE_BUG' => 'Create Bug',
    'LBL_NO_RECORDS_FOUND' => '- 0 Records Found -',

    'DATA_TYPE_DUE' => 'Due:',
    'DATA_TYPE_START' => 'Start:',
    'DATA_TYPE_SENT' => 'Sent:',
    'DATA_TYPE_MODIFIED' => 'Modified:',


    //jchi at 608/06/2008 10913am china time for the bug 12253.
    'LBL_REPORT_NEWREPORT_COLUMNS_TAB_COUNT' => 'Count',
    //jchi #19433
    'LBL_OBJECT_IMAGE' => 'object image',
    //jchi #12300
    'LBL_MASSUPDATE_DATE' => 'Select Date',

    'LBL_VALIDATE_RANGE' => 'is not within the valid range',

    //jchi #  20776
    'LBL_DROPDOWN_LIST_ALL' => 'All',

    'LBL_OPERATOR_IN_TEXT' => 'is one of the following:',
    'LBL_OPERATOR_NOT_IN_TEXT' => 'is not one of the following:',


  //Connector
    'ERR_CONNECTOR_FILL_BEANS_SIZE_MISMATCH' => 'Error: The Array count of the bean parameter does not match the Array count of the results.',
  'ERR_MISSING_MAPPING_ENTRY_FORM_MODULE' => 'Error: Missing mapping entry for module.',
  'ERROR_UNABLE_TO_RETRIEVE_DATA' => 'Error: Unable to retrieve data for {0} Connector.  The service may currently be inaccessible or the configuration settings may be invalid.  Connector error message: ({1}).',
  'LBL_MERGE_CONNECTORS' => 'Get Data',
  'LBL_MERGE_CONNECTORS_BUTTON_KEY' => '[D]',
  'LBL_REMOVE_MODULE_ENTRY' => 'Are you sure you want to disable connector integration for this module?',

    // fastcgi checks
    'LBL_FASTCGI_LOGGING'      => 'For optimal experience using IIS/FastCGI sapi, set fastcgi.logging to 0 in your php.ini file.',

    //cma
    'LBL_MASSUPDATE_DELETE_GLOBAL_TEAM'=> 'The Global team cannot be deleted.',
    'LBL_MASSUPDATE_DELETE_USER_EXISTS'=>'This private team [{0}] cannot be deleted until the user [{1}] is deleted.',

    //martin #25548
    'LBL_NO_FLASH_PLAYER' => 'You either have Abobe Flash turned off or are using an older version of the Adobe Flash Player. To get the latest version of the Flash Player, <a href="http://www.adobe.com/go/getflashplayer/">click here</a>.',
  //Collection Field
  'LBL_COLLECTION_NAME' => 'Name',
  'LBL_COLLECTION_PRIMARY' => 'Primary',
  'ERROR_MISSING_COLLECTION_SELECTION' => 'Empty required field',
    'LBL_COLLECTION_EXACT' => 'Exact',

    // fastcgi checks
    'LBL_FASTCGI_LOGGING'      => 'For optimal experience using IIS/FastCGI sapi, set fastcgi.logging to 0 in your php.ini file.',
    //MB -Fixed Bug #32812 -Max
    'LBL_ASSIGNED_TO_NAME' => 'Assigned to',
    'LBL_DESCRIPTION' => 'Description',

  'LBL_NONE' => '-none-',
  'LBL_YESTERDAY'=> 'yesterday',
  'LBL_TODAY'=>'today',
  'LBL_TOMORROW'=>'tomorrow',
  'LBL_NEXT_WEEK'=> 'next week',
  'LBL_NEXT_MONDAY'=>'next monday',
  'LBL_NEXT_FRIDAY'=>'next friday',
  'LBL_TWO_WEEKS'=> 'two weeks',
  'LBL_NEXT_MONTH'=> 'next month',
  'LBL_FIRST_DAY_OF_NEXT_MONTH'=> 'first day of next month',
  'LBL_THREE_MONTHS'=> 'three months',
  'LBL_SIXMONTHS'=> 'six months',
  'LBL_NEXT_YEAR'=> 'next year',
    'LBL_FILTERED' => 'Filtered',

    //Datetimecombo fields
    'LBL_HOURS' => 'Hours',
    'LBL_MINUTES' => 'Minutes',
    'LBL_MERIDIEM' => 'Meridiem',
    'LBL_DATE' => 'Date',
    'LBL_DASHLET_CONFIGURE_AUTOREFRESH' => 'Auto-Refresh',

    'LBL_DURATION_DAY' => 'day',
    'LBL_DURATION_HOUR' => 'hour',
    'LBL_DURATION_MINUTE' => 'minute',
    'LBL_DURATION_DAYS' => 'days',
    'LBL_DURATION_HOURS' => 'hours',
    'LBL_DURATION_MINUTES' => 'minutes',

    //Calendar widget labels
    'LBL_CHOOSE_MONTH' => 'Choose Month',
    'LBL_ENTER_YEAR' => 'Enter Year',
    'LBL_ENTER_VALID_YEAR' => 'Please enter a valid year',

    //SugarFieldPhone labels
    'LBL_INVALID_USA_PHONE_FORMAT' => 'Please enter a numeric U.S. phone number, including area code.',

    //File write error label
    'ERR_FILE_WRITE' => 'Error: Could not write file {0}.  Please check system and web server permissions.',
  'ERR_FILE_NOT_FOUND' => 'Error: Could not load file {0}.  Please check system and web server permissions.',

    'LBL_AND' => 'And',
    'LBL_BEFORE' => 'Before',

    // File fields
    'LBL_UPLOAD_FROM_COMPUTER' => 'Upload From Your Computer',
    'LBL_SEARCH_EXTERNAL_API' => 'File on External Source',
    'LBL_EXTERNAL_SECURITY_LEVEL' => 'Security',
    'LBL_SHARE_PRIVATE' => 'Private',
    'LBL_SHARE_COMPANY' => 'Company',
    'LBL_SHARE_LINKABLE' => 'Linkable',
    'LBL_SHARE_PUBLIC' => 'Public',


    // Web Services REST RSS
    'LBL_RSS_FEED' => 'RSS Feed',
    'LBL_RSS_RECORDS_FOUND' => 'record(s) found',
    'ERR_RSS_INVALID_INPUT' => 'RSS is not a valid input_type',
    'ERR_RSS_INVALID_RESPONSE' => 'RSS is not a valid response_type for this method',

    //External API Error Messages
    'ERR_GOOGLE_API_415' => 'Google Docs does not support the file format you provided.',

    'LBL_EMPTY' => 'Empty',
    'LBL_IS_EMPTY' => 'Is empty',
    'LBL_IS_NOT_EMPTY' => 'Is not empty',
    //IMPORT SAMPLE TEXT
    'LBL_IMPORT_SAMPLE_FILE_TEXT' => '
"This is a sample import file which provides an example of the expected contents of a file that is ready for import."
"The file is a comma-delimited .csv file, using double-quotes as the field qualifier."

"The header row is the top-most row in the file and contains the field labels as you would see them in the application."
"These labels are used for mapping the data in the file to the fields in the application."

"Notes: The database names could also be used in the header row. This is useful when you are using phpMyAdmin or another database tool to provide an exported list of data to import."
"The column order is not critical as the import process matches the data to the appropriate fields based on the header row."


"To use this file as a template, do the following:"
"1. Remove the sample rows of data"
"2. Remove the help text that you are reading right now"
"3. Input your own data into the appropriate rows and columns"
"4. Save the file to a known location on your system"
"5. Click on the Import option from the Actions menu in the application and choose the file to upload"
   ',
    //define labels to be used for overriding local values during import/export
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Assigned To',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Assigned User',
    'LBL_EXPORT_REPORTS_TO_ID' => 'Reports To ID',
    'LBL_EXPORT_FULL_NAME' => 'Full Name',
    'LBL_EXPORT_TEAM_ID' => 'Team ID',
    'LBL_EXPORT_TEAM_NAME' => 'Teams',
    'LBL_EXPORT_TEAM_SET_ID' => 'Team Set ID',

    'LBL_QUICKEDIT_NODEFS_NAVIGATION'=> 'Navigating... ',

    'LBL_PENDING_NOTIFICATIONS' => 'Notifications',
    'LBL_ALT_ADD_TEAM_ROW' => 'Add new team row',
    'LBL_ALT_REMOVE_TEAM_ROW' => 'Remove team',
    'LBL_ALT_SPOT_SEARCH' => 'Spot Search',
    'LBL_ALT_SORT_DESC' => 'Sorted Descending',
    'LBL_ALT_SORT_ASC' => 'Sorted Ascending',
    'LBL_ALT_SORT' => 'Sort',
    'LBL_ALT_SHOW_OPTIONS' => 'Show Options',
    'LBL_ALT_HIDE_OPTIONS' => 'Hide Options',
    'LBL_ALT_MOVE_COLUMN_LEFT' => 'Move selected entry to the list on the left',
    'LBL_ALT_MOVE_COLUMN_RIGHT' => 'Move selected entry to the list on the right',
    'LBL_ALT_MOVE_COLUMN_UP' =>'Move selected entry up in the displayed list order',
    'LBL_ALT_MOVE_COLUMN_DOWN' => 'Move selected entry down in the displayed list order',
    'LBL_ALT_INFO' => 'Information',
	'MSG_DUPLICATE' => 'The {0} record you are about to create might be a duplicate of an {0} record that already exists. {1} records containing similar names are listed below.<br>Click Create {1} to continue creating this new {0}, or select an existing {0} listed below.',
    'MSG_SHOW_DUPLICATES' => 'The {0} record you are about to create might be a duplicate of a {0} record that already exists. {1} records containing similar names are listed below.  Click Save to continue creating this new {0}, or click Cancel to return to the module without creating the {0}.',
    'LBL_EMAIL_TITLE' => 'email address',
    'LBL_EMAIL_OPT_TITLE' => 'opted out email address',
    'LBL_EMAIL_INV_TITLE' => 'invalid email address',
    'LBL_EMAIL_PRIM_TITLE' => 'primary email address',
    'LBL_SELECT_ALL_TITLE' => 'Select all',
    'LBL_SELECT_THIS_ROW_TITLE' => 'Select this row',
    'LBL_TEAM_SELECTED_TITLE' => 'Team Selected ',
    'LBL_TEAM_SELECT_AS_PRIM_TITLE' => 'Select to make this team primary',

    //for upload errors
    'UPLOAD_ERROR_TEXT'          => 'ERROR: There was an error during upload. Error code: {0} - {1}',
    'UPLOAD_ERROR_TEXT_SIZEINFO' => 'ERROR: There was an error during upload. Error code: {0} - {1}. The upload_maxsize is {2} ',
    'UPLOAD_ERROR_HOME_TEXT'     => 'ERROR: There was an error during your upload, please contact an administrator for help.',
    'UPLOAD_MAXIMUM_EXCEEDED'    => 'Size of Upload ({0} bytes) Exceeded Allowed Maximum: {1} bytes',


    //508 used Access Keys
    'LBL_EDIT_BUTTON_KEY' => 'i',
    'LBL_EDIT_BUTTON_LABEL' => 'Edit',
    'LBL_EDIT_BUTTON_TITLE' => 'Edit',
    'LBL_DUPLICATE_BUTTON_KEY' => 'u',
    'LBL_DUPLICATE_BUTTON_LABEL' => 'Duplicate',
    'LBL_DUPLICATE_BUTTON_TITLE' => 'Duplicate',
    'LBL_DELETE_BUTTON_KEY' => 'd',
    'LBL_DELETE_BUTTON_LABEL' => 'Delete',
    'LBL_DELETE_BUTTON_TITLE' => 'Delete',
    'LBL_SAVE_BUTTON_KEY' => 'a',
    'LBL_SAVE_BUTTON_LABEL' => 'Save',
    'LBL_SAVE_BUTTON_TITLE' => 'Save',
    'LBL_CANCEL_BUTTON_KEY' => 'l',
    'LBL_CANCEL_BUTTON_LABEL' => 'Cancel',
    'LBL_CANCEL_BUTTON_TITLE' => 'Cancel',
    'LBL_FIRST_INPUT_EDIT_VIEW_KEY' => '7',
    'LBL_ADV_SEARCH_LNK_KEY' => '8',
    'LBL_FIRST_INPUT_SEARCH_KEY' => '9',
    'LBL_GLOBAL_SEARCH_LNK_KEY' => '0',
    'LBL_KEYBOARD_SHORTCUTS_HELP_TITLE' => 'Keyboard Shortcuts',
    'LBL_KEYBOARD_SHORTCUTS_HELP' => '<p><strong>Form Functionality - Alt+</strong><br/> I = ed<b>I</b>t (detailview)<br/> U = d<b>U</b>plicate (detailview)<br/> D = <b>D</b>elete (detailview)<br/> A = s<b>A</b>ve (editview)<br/> L = cance<b>L</b> (editview) <br/><br/></p><p><strong>Search and Navigation  - Alt+</strong><br/> 7 = first input on Edit form<br/> 8 = Advanced Search link<br/> 9 = First Search Form input<br/> 0 = Unified search input<br></p>' ,

    'ERR_CONNECTOR_NOT_ARRAY' => 'connector array in {0} been defined incorrectly or is empty and could not be used.',
    //BEGIN SUGARCRM flav=pro ONLY
    'LBL_PDF_VIEW' => 'Download PDF',
    'LBL_PDF_EMAIL' => 'Email PDF',
    //END SUGARCRM flav=pro ONLY

    'ERR_SUHOSIN' => 'Upload stream is blocked by Suhosin, please add &quot;upload&quot; to suhosin.executor.include.whitelist (See sugarcrm.log for more information)',

    //for sidecar
    'LBL_TIME_AGO_NOW' => 'right now',
    'LBL_TIME_AGO_SECONDS' => 'less than a minute ago',
    'LBL_TIME_AGO_MINUTE' => 'about 1 minute ago',
    'LBL_TIME_AGO_MINUTES' => '{{this}} minutes ago',
    'LBL_TIME_AGO_HOUR' => 'about a hour ago',
    'LBL_TIME_AGO_HOURS' => '{{this}} hours ago',
    'LBL_TIME_AGO_DAY' => 'yesterday',
    'LBL_TIME_AGO_DAYS' => '{{this}} days ago',
    'LBL_TIME_AGO_YEAR' => 'over a year ago',
    'LBL_TIME_RELATIVE' => 'Posted {{relativetime}} on {{date}} at {{time}}',
    'LBL_LAST_TOUCHED' => 'Last touched {{relativetime}} on {{date}} at {{time}}',
    'LBL_LISTVIEW_NO_RECORDS' => 'No records were found at this time.',
    'LBL_DETAILVIEW_NO_RECORDS' => 'This record could not be rendered at this time.',
    'LBL_REFINE_LIST' => 'Refine list',
    'LBL_SEARCH_BY' => 'Search by',
    'LBL_PREVIEW' => 'Preview',
    'LBL_STREAM_NO_RECORDS' => 'This record has no notes at this time. Please add a note by clicking on the add note link.',

    //SugarApiExceptionNotAuthorized language string
    'SUGAR_API_EXCEPTION_NOT_AUTHORIZED' => 'Not allowed to edit field {0} in module: {1}',

    'LBL_LOGIN_BUTTON_LABEL' => 'Log In',
    'LBL_TOUR' => 'Activity View Tour',
    'LNK_TOUR' => 'Tour',
    'LBL_TOP' => 'Top',
    'LBL_LANGUAGE' => 'Language',
    'LBL_PREFERRED_LANGUAGE' => 'Preferred Language:',
    'LBL_LOADING_LANGUAGE' => 'Loading language pack',
    'LBL_UPLOADING' => 'Uploading',

    //for portal
    'LBL_PORTAL_SEARCH' => 'Search by Name, Number',
    'LBL_SIGNUP_BUTTON_LABEL' => 'Sign Up',
    'LBL_PORTAL_SIGNUP_PROCESS' => 'Registering',
    'LBL_PORTAL_SIGNUP_TITLE' => 'Thank you for signing up!',
    'LBL_PORTAL_SIGNUP' => 'A customer service representative will contact you shortly to configure your account.',
    'LBL_PORTAL_SIGNUP_FIRST_NAME' => 'First name',
    'LBL_PORTAL_SIGNUP_LAST_NAME' => 'Last name',
    'LBL_PORTAL_SIGNUP_EMAIL' => 'Email',
    'LBL_PORTAL_SIGNUP_PHONE' => '(###) ###-#### (optional)',
    'LBL_PORTAL_SIGNUP_COUNTRY' => 'Country',
    'LBL_PORTAL_SIGNUP_STATE' => 'State',
    'LBL_PORTAL_SIGNUP_COMPANY' => 'Company',
    'LBL_PORTAL_SIGNUP_JOBTITLE' => 'Job title (optional)',
    'LNK_PORTAL_LOGIN_FORGOTPASSWORD' => 'Forgot password?',
    'LBL_PORTAL_LOGIN_FORGOTPASSWORD_TITLE' => 'Forgot Your Password?',
    'LBL_PORTAL_LOGIN_FORGOTPASSWORD' => 'You need to contact your Sugar Admin to reset your password.',
    'LBL_PORTAL_LOGIN_USERNAME' => 'Username',
    'LBL_PORTAL_LOGIN_PASSWORD' => 'Password',
    'LBL_PORTAL_LOADING' => 'Loading',
    'LBL_PORTAL_SAVING' => 'Saving',
    'LBL_PORTAL_PAGE_NOT_AVAIL' => 'Page Not Available',
    'LBL_PORTAL_NOT_ENABLED_MSG' => "We're Sorry, but this feature is not available at this time.",

    //for portal system tour
    'LBL_PORTAL_TOUR_WELCOME_TITLE' => 'Welcome to Portal',
    'LBL_PORTAL_TOUR_WATCH_VIDEO' => 'Watch: What\'s new in Portal',
    'LBL_PORTAL_TOUR_FEATURES_1' => 'Feature 1',
    'LBL_PORTAL_TOUR_FEATURES_2' => 'Feature 2',
    'LBL_PORTAL_TOUR_FEATURES_3' => 'Feature 3',
    'LBL_PORTAL_TOUR_FEATURES_4' => 'Feature 4',
    'LBL_PORTAL_TOUR_MORE_INFO_1' => 'And much more!',
    'LBL_PORTAL_TOUR_MORE_INFO_2' => 'For a full list visit the What\'s New in Portal',
    'LBL_PORTAL_TOUR_TAKE_TOUR_LNK' => 'Take the tour',
    'LBL_PORTAL_TOUR_SKIP_LNK' => 'Skip',
    'LBL_PORTAL_TOUR_NEXT_LNK' => 'Next',
    'LBL_PORTAL_TOUR_BACK_LNK' => 'Back',
    'LBL_PORTAL_TOUR_DONE_LNK' => 'Done',
    'LBL_PORTAL_TOUR_CASES_TITLE' => 'Cases',
    'LBL_PORTAL_TOUR_CASES_BODY' => 'All your cases go here',
    'LBL_PORTAL_TOUR_SEARCH_TITLE' => 'Search',
    'LBL_PORTAL_TOUR_SEARCH_BODY' => 'Search keywords or case numbers',
    'LBL_PORTAL_TOUR_QUICK_TITLE' => 'Quick Create',
    'LBL_PORTAL_TOUR_QUICK_BODY' => 'You can perform all your quick actions here',
    'LBL_PORTAL_TOUR_DONE_TITLE' => 'You\'re Done!',
    'LBL_PORTAL_TOUR_DONE_BODY' => 'You can always retake the tour or read documentation for more information.',

    //for portal errors
    'LBL_PORTAL_INVALID_CREDS' => 'The username/password combination provided is incorrect, please try again.',
    'LBL_PORTAL_INVALID_CREDS_TITLE' => 'Invalid Credentials',
    'LBL_PORTAL_INVALID_GRANT' => 'Your token is invalid or has expired. Please login again.',
    'LBL_PORTAL_INVALID_GRANT_TITLE' => 'Token Expired',
    'LBL_PORTAL_AUTH_FAILED' => 'Client authentication failed.',
    'LBL_PORTAL_AUTH_FAILED_TITLE' => 'Invalid Client',
    'LBL_PORTAL_INVALID_REQUEST' => 'The request made is invalid or malformed. Please contact technical support.',
    'LBL_PORTAL_INVALID_REQUEST_TITLE' => 'Invalid Request',
    'LBL_PORTAL_REQUEST_TIMEOUT' => 'The request timed out.',
    'LBL_PORTAL_REQUEST_TIMEOUT_TITLE' => 'Request timeout',
    'LBL_PORTAL_UNAUTHORIZED' =>'We\'re sorry, but it appears you are unauthorized to access this resource.',
    'LBL_PORTAL_UNAUTHORIZED_TITLE' =>'HTTP Error: 401 Unauthorized',
    'LBL_PORTAL_RESOURCE_UNAVAILABLE' => 'Resource not available.',
    'LBL_PORTAL_RESOURCE_UNAVAILABLE_TITLE' => 'HTTP Error: 403 Forbidden',
    'LBL_PORTAL_METHOD_NOT_ALLOWED' => 'HTTP method not allowed for this resource. Please contact technical support.',
    'LBL_PORTAL_METHOD_NOT_ALLOWED_TITLE' => 'HTTP Error: 405 Method Not Allowed',
    'LBL_PORTAL_PRECONDITION_MISSING' => 'Request failure, or, missing/invalid parameter. Please contact technical support',
    'LBL_PORTAL_PRECONDITION_MISSING_TITLE' => 'HTTP Error: 412',
    'LBL_PORTAL_MIN_MODULES' => 'At minimum, you need to have the Cases, Bugs or Knowledge Base module enabled to use this application.',
    'LBL_PORTAL_ERROR' => 'Error',
    'LBL_PORTAL_ROUTE_ERROR'=>'Issue loading module. Please try again later or contact support.',
    'LBL_PORTAL_OFFLINE' =>'Sorry the application is not available at this time. Please contact the site administrator.',
    'LBL_CONTACT_EDIT_PASSWORD_LNK_TEXT' => 'Change Password',
    'LBL_PORTAL_PASSWORDS_MUST_MATCH' => 'The passwords must match.',
    'LBL_PORTAL_PASSWORD_UPDATE_FAILED' => 'Update password failed. Please try again or contact technical support.',
    'LBL_PORTAL_PASSWORD_VERIFICATION_FAILED' => 'Password entered does not match that in our system.',
    'LBL_PORTAL_PASSWORD_SUCCESS_CHANGED' => 'Your password has been successfully updated.',

    'LBL_PREFERRED_LANGUAGE' => 'Language Preference:',

    //sidecar errors
    'ERR_HTTP_DEFAULT_TYPE' => 'Unknown',
    'ERR_HTTP_DEFAULT_TITLE' => 'Unknown Error',
    'ERR_HTTP_DEFAULT_TEXT' => 'Unknown error.',
    'ERR_HTTP_404_TYPE' => '404',
    'ERR_HTTP_404_TITLE' => 'HTTP: 404 Not Found',
    'ERR_HTTP_404_TEXT' => 'We\'re sorry but the resource you asked for cannot be found.',
    'ERR_HTTP_500_TYPE' => '500',
    'ERR_HTTP_500_TITLE' => 'HTTP: 500 Internal Server Error',
    'ERR_HTTP_500_TEXT' => 'There was an error on the server. Please contact technical support.',
    'ERR_RENDER_FAILED_TITLE' => 'View Render Failed',
    'ERR_RENDER_FAILED_MSG' => 'Failed to render a view',
    'ERR_RENDER_FIELD_FAILED_TITLE' => 'Field Render Failed',
    'ERR_RENDER_FIELD_FAILED_MSG' => 'Unable to render the {0} field.',
    'ERR_NO_VIEW_ACCESS_TITLE' => 'Access Denied',
    'ERR_NO_VIEW_ACCESS_MSG' => 'Contact your Support Administrator to get access to this view for {0} module.',
    'ERR_LAYOUT_RENDER_TITLE' => 'Layout render failed',
    'ERR_LAYOUT_RENDER_MSG' => 'Oops! We are not able to render anything. Please try again later or contact support.',
    'ERR_INTERNAL_ERR_MSG' => 'Internal error',
    'ERR_GENERIC_TITLE' => 'Error',
    'ERR_CONTACT_TECH_SUPPORT' => 'Please contact technical support.',

    'LBL_SYNCED_RECURRING_MSG' => 'You cannot edit this record because it was synced from an external client.',
    'LBL_EXISTING' => 'Existing',
);


$app_list_strings['moduleList']['Library'] = 'Library';
$app_list_strings['library_type'] = array('Books'=>'Book', 'Music'=>'Music', 'DVD'=>'DVD', 'Magazines'=>'Magazines');
$app_list_strings['moduleList']['EmailAddresses'] = 'Email Address';
//BEGIN SUGARCRM flav!=sales ONLY
$app_list_strings['project_priority_default'] = 'Medium';
$app_list_strings['project_priority_options'] = array (
    'High' => 'High',
    'Medium' => 'Medium',
    'Low' => 'Low',
);


$app_list_strings['kbdocument_status_dom'] = array (
    'Draft' => 'Draft',
    'Expired' => 'Expired',
    'In Review' => 'In Review',
    'Published' => 'Published',
  );

   $app_list_strings['kbadmin_actions_dom'] =
    array (
    ''          => '--Admin Actions--',
    'Create New Tag' => 'Create New Tag',
    'Delete Tag'=>'Delete Tag',
    'Rename Tag'=>'Rename Tag',
    'Move Selected Articles'=>'Move Selected Articles',
    'Apply Tags On Articles'=>'Apply Tags To Articles',
    'Delete Selected Articles'=>'Delete Selected Articles',
  );


  $app_list_strings['kbdocument_attachment_option_dom'] =
    array(
        ''=>'',
        'some' => 'Has Attachments',
        'none' => 'Has None',
        'mime' => 'Specify Mime Type',
        'name' => 'Specify Name',
    );

  $app_list_strings['moduleList']['KBDocuments'] = 'Knowledge Base';
  $app_strings['LBL_CREATE_KB_DOCUMENT'] = 'Create Article';
  $app_list_strings['kbdocument_viewing_frequency_dom'] =
  array(
    ''=>'',
    'Top_5'  => 'Top 5',
    'Top_10' => 'Top 10',
    'Top_20' => 'Top 20',
    'Bot_5'  => 'Bottom 5',
    'Bot_10' => 'Bottom 10',
    'Bot_20' => 'Bottom 20',
  );

   $app_list_strings['kbdocument_canned_search'] =
    array(
        'all'=>'All',
        'added' => 'Added Last 30 days',
        'pending' => 'Pending my Approval',
        'updated' =>'Updated Last 30 days',
        'faqs' => 'FAQs',
    );
    $app_list_strings['kbdocument_date_filter_options'] =
        array(
    '' => '',
    'on' => 'On',
    'before' => 'Before',
    'after' => 'After',
    'between_dates' => 'Is Between',
    'last_7_days' => 'Last 7 Days',
    'next_7_days' => 'Next 7 Days',
    'last_month' => 'Last Month',
    'this_month' => 'This Month',
    'next_month' => 'Next Month',
    'last_30_days' => 'Last 30 Days',
    'next_30_days' => 'Next 30 Days',
    'last_year' => 'Last Year',
    'this_year' => 'This Year',
    'next_year' => 'Next Year',
    'isnull' => 'Is Null',
        );
    //END SUGARCRM flav!=sales ONLY

    $app_list_strings['countries_dom'] = array(
        '' => '',
        'ABU DHABI' => 'ABU DHABI',
        'ADEN' => 'ADEN',
        'AFGHANISTAN' => 'AFGHANISTAN',
        'ALBANIA' => 'ALBANIA',
        'ALGERIA' => 'ALGERIA',
        'AMERICAN SAMOA' => 'AMERICAN SAMOA',
        'ANDORRA' => 'ANDORRA',
        'ANGOLA' => 'ANGOLA',
        'ANTARCTICA' => 'ANTARCTICA',
        'ANTIGUA' => 'ANTIGUA',
        'ARGENTINA' => 'ARGENTINA',
        'ARMENIA' => 'ARMENIA',
        'ARUBA' => 'ARUBA',
        'AUSTRALIA' => 'AUSTRALIA',
        'AUSTRIA' => 'AUSTRIA',
        'AZERBAIJAN' => 'AZERBAIJAN',
        'BAHAMAS' => 'BAHAMAS',
        'BAHRAIN' => 'BAHRAIN',
        'BANGLADESH' => 'BANGLADESH',
        'BARBADOS' => 'BARBADOS',
        'BELARUS' => 'BELARUS',
        'BELGIUM' => 'BELGIUM',
        'BELIZE' => 'BELIZE',
        'BENIN' => 'BENIN',
        'BERMUDA' => 'BERMUDA',
        'BHUTAN' => 'BHUTAN',
        'BOLIVIA' => 'BOLIVIA',
        'BOSNIA' => 'BOSNIA',
        'BOTSWANA' => 'BOTSWANA',
        'BOUVET ISLAND' => 'BOUVET ISLAND',
        'BRAZIL' => 'BRAZIL',
        'BRITISH ANTARCTICA TERRITORY' => 'BRITISH ANTARCTICA TERRITORY',
        'BRITISH INDIAN OCEAN TERRITORY' => 'BRITISH INDIAN OCEAN TERRITORY',
        'BRITISH VIRGIN ISLANDS' => 'BRITISH VIRGIN ISLANDS',
        'BRITISH WEST INDIES' => 'BRITISH WEST INDIES',
        'BRUNEI' => 'BRUNEI',
        'BULGARIA' => 'BULGARIA',
        'BURKINA FASO' => 'BURKINA FASO',
        'BURUNDI' => 'BURUNDI',
        'CAMBODIA' => 'CAMBODIA',
        'CAMEROON' => 'CAMEROON',
        'CANADA' => 'CANADA',
        'CANAL ZONE' => 'CANAL ZONE',
        'CANARY ISLAND' => 'CANARY ISLAND',
        'CAPE VERDI ISLANDS' => 'CAPE VERDI ISLANDS',
        'CAYMAN ISLANDS' => 'CAYMAN ISLANDS',
        'CEVLON' => 'CEVLON',
        'CHAD' => 'CHAD',
        'CHANNEL ISLAND UK' => 'CHANNEL ISLAND UK',
        'CHILE' => 'CHILE',
        'CHINA' => 'CHINA',
        'CHRISTMAS ISLAND' => 'CHRISTMAS ISLAND',
        'COCOS (KEELING) ISLAND' => 'COCOS (KEELING) ISLAND',
        'COLOMBIA' => 'COLOMBIA',
        'COMORO ISLANDS' => 'COMORO ISLANDS',
        'CONGO' => 'CONGO',
        'CONGO KINSHASA' => 'CONGO KINSHASA',
        'COOK ISLANDS' => 'COOK ISLANDS',
        'COSTA RICA' => 'COSTA RICA',
        'CROATIA' => 'CROATIA',
        'CUBA' => 'CUBA',
        'CURACAO' => 'CURACAO',
        'CYPRUS' => 'CYPRUS',
        'CZECH REPUBLIC' => 'CZECH REPUBLIC',
        'DAHOMEY' => 'DAHOMEY',
        'DENMARK' => 'DENMARK',
        'DJIBOUTI' => 'DJIBOUTI',
        'DOMINICA' => 'DOMINICA',
        'DOMINICAN REPUBLIC' => 'DOMINICAN REPUBLIC',
        'DUBAI' => 'DUBAI',
        'ECUADOR' => 'ECUADOR',
        'EGYPT' => 'EGYPT',
        'EL SALVADOR' => 'EL SALVADOR',
        'EQUATORIAL GUINEA' => 'EQUATORIAL GUINEA',
        'ESTONIA' => 'ESTONIA',
        'ETHIOPIA' => 'ETHIOPIA',
        'FAEROE ISLANDS' => 'FAEROE ISLANDS',
        'FALKLAND ISLANDS' => 'FALKLAND ISLANDS',
        'FIJI' => 'FIJI',
        'FINLAND' => 'FINLAND',
        'FRANCE' => 'FRANCE',
        'FRENCH GUIANA' => 'FRENCH GUIANA',
        'FRENCH POLYNESIA' => 'FRENCH POLYNESIA',
        'GABON' => 'GABON',
        'GAMBIA' => 'GAMBIA',
        'GEORGIA' => 'GEORGIA',
        'GERMANY' => 'GERMANY',
        'GHANA' => 'GHANA',
        'GIBRALTAR' => 'GIBRALTAR',
        'GREECE' => 'GREECE',
        'GREENLAND' => 'GREENLAND',
        'GUADELOUPE' => 'GUADELOUPE',
        'GUAM' => 'GUAM',
        'GUATEMALA' => 'GUATEMALA',
        'GUINEA' => 'GUINEA',
        'GUYANA' => 'GUYANA',
        'HAITI' => 'HAITI',
        'HONDURAS' => 'HONDURAS',
        'HONG KONG' => 'HONG KONG',
        'HUNGARY' => 'HUNGARY',
        'ICELAND' => 'ICELAND',
        'IFNI' => 'IFNI',
        'INDIA' => 'INDIA',
        'INDONESIA' => 'INDONESIA',
        'IRAN' => 'IRAN',
        'IRAQ' => 'IRAQ',
        'IRELAND' => 'IRELAND',
        'ISRAEL' => 'ISRAEL',
        'ITALY' => 'ITALY',
        'IVORY COAST' => 'IVORY COAST',
        'JAMAICA' => 'JAMAICA',
        'JAPAN' => 'JAPAN',
        'JORDAN' => 'JORDAN',
        'KAZAKHSTAN' => 'KAZAKHSTAN',
        'KENYA' => 'KENYA',
        'KOREA' => 'KOREA',
        'KOREA, SOUTH' => 'KOREA, SOUTH',
        'KUWAIT' => 'KUWAIT',
        'KYRGYZSTAN' => 'KYRGYZSTAN',
        'LAOS' => 'LAOS',
        'LATVIA' => 'LATVIA',
        'LEBANON' => 'LEBANON',
        'LEEWARD ISLANDS' => 'LEEWARD ISLANDS',
        'LESOTHO' => 'LESOTHO',
        'LIBYA' => 'LIBYA',
        'LIECHTENSTEIN' => 'LIECHTENSTEIN',
        'LITHUANIA' => 'LITHUANIA',
        'LUXEMBOURG' => 'LUXEMBOURG',
        'MACAO' => 'MACAO',
        'MACEDONIA' => 'MACEDONIA',
        'MADAGASCAR' => 'MADAGASCAR',
        'MALAWI' => 'MALAWI',
        'MALAYSIA' => 'MALAYSIA',
        'MALDIVES' => 'MALDIVES',
        'MALI' => 'MALI',
        'MALTA' => 'MALTA',
        'MARTINIQUE' => 'MARTINIQUE',
        'MAURITANIA' => 'MAURITANIA',
        'MAURITIUS' => 'MAURITIUS',
        'MELANESIA' => 'MELANESIA',
        'MEXICO' => 'MEXICO',
        'MOLDOVIA' => 'MOLDOVIA',
        'MONACO' => 'MONACO',
        'MONGOLIA' => 'MONGOLIA',
        'MOROCCO' => 'MOROCCO',
        'MOZAMBIQUE' => 'MOZAMBIQUE',
        'MYANAMAR' => 'MYANAMAR',
        'NAMIBIA' => 'NAMIBIA',
        'NEPAL' => 'NEPAL',
        'NETHERLANDS' => 'NETHERLANDS',
        'NETHERLANDS ANTILLES' => 'NETHERLANDS ANTILLES',
        'NETHERLANDS ANTILLES NEUTRAL ZONE' => 'NETHERLANDS ANTILLES NEUTRAL ZONE',
        'NEW CALADONIA' => 'NEW CALADONIA',
        'NEW HEBRIDES' => 'NEW HEBRIDES',
        'NEW ZEALAND' => 'NEW ZEALAND',
        'NICARAGUA' => 'NICARAGUA',
        'NIGER' => 'NIGER',
        'NIGERIA' => 'NIGERIA',
        'NORFOLK ISLAND' => 'NORFOLK ISLAND',
        'NORWAY' => 'NORWAY',
        'OMAN' => 'OMAN',
        'OTHER' => 'OTHER',
        'PACIFIC ISLAND' => 'PACIFIC ISLAND',
        'PAKISTAN' => 'PAKISTAN',
        'PANAMA' => 'PANAMA',
        'PAPUA NEW GUINEA' => 'PAPUA NEW GUINEA',
        'PARAGUAY' => 'PARAGUAY',
        'PERU' => 'PERU',
        'PHILIPPINES' => 'PHILIPPINES',
        'POLAND' => 'POLAND',
        'PORTUGAL' => 'PORTUGAL',
        'PORTUGUESE TIMOR' => 'PORTUGUESE TIMOR',
        'PUERTO RICO' => 'PUERTO RICO',
        'QATAR' => 'QATAR',
        'REPUBLIC OF BELARUS' => 'REPUBLIC OF BELARUS',
        'REPUBLIC OF SOUTH AFRICA' => 'REPUBLIC OF SOUTH AFRICA',
        'REUNION' => 'REUNION',
        'ROMANIA' => 'ROMANIA',
        'RUSSIA' => 'RUSSIA',
        'RWANDA' => 'RWANDA',
        'RYUKYU ISLANDS' => 'RYUKYU ISLANDS',
        'SABAH' => 'SABAH',
        'SAN MARINO' => 'SAN MARINO',
        'SAUDI ARABIA' => 'SAUDI ARABIA',
        'SENEGAL' => 'SENEGAL',
        'SERBIA' => 'SERBIA',
        'SEYCHELLES' => 'SEYCHELLES',
        'SIERRA LEONE' => 'SIERRA LEONE',
        'SINGAPORE' => 'SINGAPORE',
        'SLOVAKIA' => 'SLOVAKIA',
        'SLOVENIA' => 'SLOVENIA',
        'SOMALILIAND' => 'SOMALILIAND',
        'SOUTH AFRICA' => 'SOUTH AFRICA',
        'SOUTH YEMEN' => 'SOUTH YEMEN',
        'SPAIN' => 'SPAIN',
        'SPANISH SAHARA' => 'SPANISH SAHARA',
        'SRI LANKA' => 'SRI LANKA',
        'ST. KITTS AND NEVIS' => 'ST. KITTS AND NEVIS',
        'ST. LUCIA' => 'ST. LUCIA',
        'SUDAN' => 'SUDAN',
        'SURINAM' => 'SURINAM',
        'SW AFRICA' => 'SW AFRICA',
        'SWAZILAND' => 'SWAZILAND',
        'SWEDEN' => 'SWEDEN',
        'SWITZERLAND' => 'SWITZERLAND',
        'SYRIA' => 'SYRIA',
        'TAIWAN' => 'TAIWAN',
        'TAJIKISTAN' => 'TAJIKISTAN',
        'TANZANIA' => 'TANZANIA',
        'THAILAND' => 'THAILAND',
        'TONGA' => 'TONGA',
        'TRINIDAD' => 'TRINIDAD',
        'TUNISIA' => 'TUNISIA',
        'TURKEY' => 'TURKEY',
        'UGANDA' => 'UGANDA',
        'UKRAINE' => 'UKRAINE',
        'UNITED ARAB EMIRATES' => 'UNITED ARAB EMIRATES',
        'UNITED KINGDOM' => 'UNITED KINGDOM',
        'UPPER VOLTA' => 'UPPER VOLTA',
        'URUGUAY' => 'URUGUAY',
        'US PACIFIC ISLAND' => 'US PACIFIC ISLAND',
        'US VIRGIN ISLANDS' => 'US VIRGIN ISLANDS',
        'USA' => 'USA',
        'UZBEKISTAN' => 'UZBEKISTAN',
        'VANUATU' => 'VANUATU',
        'VATICAN CITY' => 'VATICAN CITY',
        'VENEZUELA' => 'VENEZUELA',
        'VIETNAM' => 'VIETNAM',
        'WAKE ISLAND' => 'WAKE ISLAND',
        'WEST INDIES' => 'WEST INDIES',
        'WESTERN SAHARA' => 'WESTERN SAHARA',
        'YEMEN' => 'YEMEN',
        'ZAIRE' => 'ZAIRE',
        'ZAMBIA' => 'ZAMBIA',
        'ZIMBABWE' => 'ZIMBABWE',
    );

$app_list_strings['state_dom'] = array(
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District Of Columbia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia ',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming'
);

  $app_list_strings['charset_dom'] = array(
    'BIG-5'     => 'BIG-5 (Taiwan and Hong Kong)',
    /*'CP866'     => 'CP866', // ms-dos Cyrillic */
    /*'CP949'     => 'CP949 (Microsoft Korean)', */
    'CP1251'    => 'CP1251 (MS Cyrillic)',
    'CP1252'    => 'CP1252 (MS Western European & US)',
    'EUC-CN'    => 'EUC-CN (Simplified Chinese GB2312)',
    'EUC-JP'    => 'EUC-JP (Unix Japanese)',
    'EUC-KR'    => 'EUC-KR (Korean)',
    'EUC-TW'    => 'EUC-TW (Taiwanese)',
    'ISO-2022-JP' => 'ISO-2022-JP (Japanese)',
    'ISO-2022-KR' => 'ISO-2022-KR (Korean)',
    'ISO-8859-1'  => 'ISO-8859-1 (Western European and US)',
    'ISO-8859-2'  => 'ISO-8859-2 (Central and Eastern European)',
    'ISO-8859-3'  => 'ISO-8859-3 (Latin 3)',
    'ISO-8859-4'  => 'ISO-8859-4 (Latin 4)',
    'ISO-8859-5'  => 'ISO-8859-5 (Cyrillic)',
    'ISO-8859-6'  => 'ISO-8859-6 (Arabic)',
    'ISO-8859-7'  => 'ISO-8859-7 (Greek)',
    'ISO-8859-8'  => 'ISO-8859-8 (Hebrew)',
    'ISO-8859-9'  => 'ISO-8859-9 (Latin 5)',
    'ISO-8859-10' => 'ISO-8859-10 (Latin 6)',
    'ISO-8859-13' => 'ISO-8859-13 (Latin 7)',
    'ISO-8859-14' => 'ISO-8859-14 (Latin 8)',
    'ISO-8859-15' => 'ISO-8859-15 (Latin 9)',
    'KOI8-R'    => 'KOI8-R (Cyrillic Russian)',
    'KOI8-U'    => 'KOI8-U (Cyrillic Ukranian)',
    'SJIS'      => 'SJIS (MS Japanese)',
    'UTF-8'     => 'UTF-8',
  );

  $app_list_strings['timezone_dom'] = array(

      'Africa/Algiers' => 'Africa/Algiers',
  'Africa/Luanda' => 'Africa/Luanda',
  'Africa/Porto-Novo' => 'Africa/Porto-Novo',
  'Africa/Gaborone' => 'Africa/Gaborone',
  'Africa/Ouagadougou' => 'Africa/Ouagadougou',
  'Africa/Bujumbura' => 'Africa/Bujumbura',
  'Africa/Douala' => 'Africa/Douala',
  'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde',
  'Africa/Bangui' => 'Africa/Bangui',
  'Africa/Ndjamena' => 'Africa/Ndjamena',
  'Indian/Comoro' => 'Indian/Comoro',
  'Africa/Kinshasa' => 'Africa/Kinshasa',
  'Africa/Lubumbashi' => 'Africa/Lubumbashi',
  'Africa/Brazzaville' => 'Africa/Brazzaville',
  'Africa/Abidjan' => 'Africa/Abidjan',
  'Africa/Djibouti' => 'Africa/Djibouti',
  'Africa/Cairo' => 'Africa/Cairo',
  'Africa/Malabo' => 'Africa/Malabo',
  'Africa/Asmera' => 'Africa/Asmera',
  'Africa/Addis_Ababa' => 'Africa/Addis_Ababa',
  'Africa/Libreville' => 'Africa/Libreville',
  'Africa/Banjul' => 'Africa/Banjul',
  'Africa/Accra' => 'Africa/Accra',
  'Africa/Conakry' => 'Africa/Conakry',
  'Africa/Bissau' => 'Africa/Bissau',
  'Africa/Nairobi' => 'Africa/Nairobi',
  'Africa/Maseru' => 'Africa/Maseru',
  'Africa/Monrovia' => 'Africa/Monrovia',
  'Africa/Tripoli' => 'Africa/Tripoli',
  'Indian/Antananarivo' => 'Indian/Antananarivo',
  'Africa/Blantyre' => 'Africa/Blantyre',
  'Africa/Bamako' => 'Africa/Bamako',
  'Africa/Nouakchott' => 'Africa/Nouakchott',
  'Indian/Mauritius' => 'Indian/Mauritius',
  'Indian/Mayotte' => 'Indian/Mayotte',
  'Africa/Casablanca' => 'Africa/Casablanca',
  'Africa/El_Aaiun' => 'Africa/El_Aaiun',
  'Africa/Maputo' => 'Africa/Maputo',
  'Africa/Windhoek' => 'Africa/Windhoek',
  'Africa/Niamey' => 'Africa/Niamey',
  'Africa/Lagos' => 'Africa/Lagos',
  'Indian/Reunion' => 'Indian/Reunion',
  'Africa/Kigali' => 'Africa/Kigali',
  'Atlantic/St_Helena' => 'Atlantic/St_Helena',
  'Africa/Sao_Tome' => 'Africa/Sao_Tome',
  'Africa/Dakar' => 'Africa/Dakar',
  'Indian/Mahe' => 'Indian/Mahe',
  'Africa/Freetown' => 'Africa/Freetown',
  'Africa/Mogadishu' => 'Africa/Mogadishu',
  'Africa/Johannesburg' => 'Africa/Johannesburg',
  'Africa/Khartoum' => 'Africa/Khartoum',
  'Africa/Mbabane' => 'Africa/Mbabane',
  'Africa/Dar_es_Salaam' => 'Africa/Dar_es_Salaam',
  'Africa/Lome' => 'Africa/Lome',
  'Africa/Tunis' => 'Africa/Tunis',
  'Africa/Kampala' => 'Africa/Kampala',
  'Africa/Lusaka' => 'Africa/Lusaka',
  'Africa/Harare' => 'Africa/Harare',
  'Antarctica/Casey' => 'Antarctica/Casey',
  'Antarctica/Davis' => 'Antarctica/Davis',
  'Antarctica/Mawson' => 'Antarctica/Mawson',
  'Indian/Kerguelen' => 'Indian/Kerguelen',
  'Antarctica/DumontDUrville' => 'Antarctica/DumontDUrville',
  'Antarctica/Syowa' => 'Antarctica/Syowa',
  'Antarctica/Vostok' => 'Antarctica/Vostok',
  'Antarctica/Rothera' => 'Antarctica/Rothera',
  'Antarctica/Palmer' => 'Antarctica/Palmer',
  'Antarctica/McMurdo' => 'Antarctica/McMurdo',
  'Asia/Kabul' => 'Asia/Kabul',
  'Asia/Yerevan' => 'Asia/Yerevan',
  'Asia/Baku' => 'Asia/Baku',
  'Asia/Bahrain' => 'Asia/Bahrain',
  'Asia/Dhaka' => 'Asia/Dhaka',
  'Asia/Thimphu' => 'Asia/Thimphu',
  'Indian/Chagos' => 'Indian/Chagos',
  'Asia/Brunei' => 'Asia/Brunei',
  'Asia/Rangoon' => 'Asia/Rangoon',
  'Asia/Phnom_Penh' => 'Asia/Phnom_Penh',
  'Asia/Beijing' => 'Asia/Beijing',
  'Asia/Harbin' => 'Asia/Harbin',
  'Asia/Shanghai' => 'Asia/Shanghai',
  'Asia/Chongqing' => 'Asia/Chongqing',
  'Asia/Urumqi' => 'Asia/Urumqi',
  'Asia/Kashgar' => 'Asia/Kashgar',
  'Asia/Hong_Kong' => 'Asia/Hong_Kong',
  'Asia/Taipei' => 'Asia/Taipei',
  'Asia/Macau' => 'Asia/Macau',
  'Asia/Nicosia' => 'Asia/Nicosia',
  'Asia/Tbilisi' => 'Asia/Tbilisi',
  'Asia/Dili' => 'Asia/Dili',
  'Asia/Calcutta' => 'Asia/Calcutta',
  'Asia/Jakarta' => 'Asia/Jakarta',
  'Asia/Pontianak' => 'Asia/Pontianak',
  'Asia/Makassar' => 'Asia/Makassar',
  'Asia/Jayapura' => 'Asia/Jayapura',
  'Asia/Tehran' => 'Asia/Tehran',
  'Asia/Baghdad' => 'Asia/Baghdad',
  'Asia/Jerusalem' => 'Asia/Jerusalem',
  'Asia/Tokyo' => 'Asia/Tokyo',
  'Asia/Amman' => 'Asia/Amman',
  'Asia/Almaty' => 'Asia/Almaty',
  'Asia/Qyzylorda' => 'Asia/Qyzylorda',
  'Asia/Aqtobe' => 'Asia/Aqtobe',
  'Asia/Aqtau' => 'Asia/Aqtau',
  'Asia/Oral' => 'Asia/Oral',
  'Asia/Bishkek' => 'Asia/Bishkek',
  'Asia/Seoul' => 'Asia/Seoul',
  'Asia/Pyongyang' => 'Asia/Pyongyang',
  'Asia/Kuwait' => 'Asia/Kuwait',
  'Asia/Vientiane' => 'Asia/Vientiane',
  'Asia/Beirut' => 'Asia/Beirut',
  'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur',
  'Asia/Kuching' => 'Asia/Kuching',
  'Indian/Maldives' => 'Indian/Maldives',
  'Asia/Hovd' => 'Asia/Hovd',
  'Asia/Ulaanbaatar' => 'Asia/Ulaanbaatar',
  'Asia/Choibalsan' => 'Asia/Choibalsan',
  'Asia/Katmandu' => 'Asia/Katmandu',
  'Asia/Muscat' => 'Asia/Muscat',
  'Asia/Karachi' => 'Asia/Karachi',
  'Asia/Gaza' => 'Asia/Gaza',
  'Asia/Manila' => 'Asia/Manila',
  'Asia/Qatar' => 'Asia/Qatar',
  'Asia/Riyadh' => 'Asia/Riyadh',
  'Asia/Singapore' => 'Asia/Singapore',
  'Asia/Colombo' => 'Asia/Colombo',
  'Asia/Damascus' => 'Asia/Damascus',
  'Asia/Dushanbe' => 'Asia/Dushanbe',
  'Asia/Bangkok' => 'Asia/Bangkok',
  'Asia/Ashgabat' => 'Asia/Ashgabat',
  'Asia/Dubai' => 'Asia/Dubai',
  'Asia/Samarkand' => 'Asia/Samarkand',
  'Asia/Tashkent' => 'Asia/Tashkent',
  'Asia/Saigon' => 'Asia/Saigon',
  'Asia/Aden' => 'Asia/Aden',
  'Australia/Darwin' => 'Australia/Darwin',
  'Australia/Perth' => 'Australia/Perth',
  'Australia/Brisbane' => 'Australia/Brisbane',
  'Australia/Lindeman' => 'Australia/Lindeman',
  'Australia/Adelaide' => 'Australia/Adelaide',
  'Australia/Hobart' => 'Australia/Hobart',
  'Australia/Currie' => 'Australia/Currie',
  'Australia/Melbourne' => 'Australia/Melbourne',
  'Australia/Sydney' => 'Australia/Sydney',
  'Australia/Broken_Hill' => 'Australia/Broken_Hill',
  'Indian/Christmas' => 'Indian/Christmas',
  'Pacific/Rarotonga' => 'Pacific/Rarotonga',
  'Indian/Cocos' => 'Indian/Cocos',
  'Pacific/Fiji' => 'Pacific/Fiji',
  'Pacific/Gambier' => 'Pacific/Gambier',
  'Pacific/Marquesas' => 'Pacific/Marquesas',
  'Pacific/Tahiti' => 'Pacific/Tahiti',
  'Pacific/Guam' => 'Pacific/Guam',
  'Pacific/Tarawa' => 'Pacific/Tarawa',
  'Pacific/Enderbury' => 'Pacific/Enderbury',
  'Pacific/Kiritimati' => 'Pacific/Kiritimati',
  'Pacific/Saipan' => 'Pacific/Saipan',
  'Pacific/Majuro' => 'Pacific/Majuro',
  'Pacific/Kwajalein' => 'Pacific/Kwajalein',
  'Pacific/Truk' => 'Pacific/Truk',
  'Pacific/Ponape' => 'Pacific/Ponape',
  'Pacific/Kosrae' => 'Pacific/Kosrae',
  'Pacific/Nauru' => 'Pacific/Nauru',
  'Pacific/Noumea' => 'Pacific/Noumea',
  'Pacific/Auckland' => 'Pacific/Auckland',
  'Pacific/Chatham' => 'Pacific/Chatham',
  'Pacific/Niue' => 'Pacific/Niue',
  'Pacific/Norfolk' => 'Pacific/Norfolk',
  'Pacific/Palau' => 'Pacific/Palau',
  'Pacific/Port_Moresby' => 'Pacific/Port_Moresby',
  'Pacific/Pitcairn' => 'Pacific/Pitcairn',
  'Pacific/Pago_Pago' => 'Pacific/Pago_Pago',
  'Pacific/Apia' => 'Pacific/Apia',
  'Pacific/Guadalcanal' => 'Pacific/Guadalcanal',
  'Pacific/Fakaofo' => 'Pacific/Fakaofo',
  'Pacific/Tongatapu' => 'Pacific/Tongatapu',
  'Pacific/Funafuti' => 'Pacific/Funafuti',
  'Pacific/Johnston' => 'Pacific/Johnston',
  'Pacific/Midway' => 'Pacific/Midway',
  'Pacific/Wake' => 'Pacific/Wake',
  'Pacific/Efate' => 'Pacific/Efate',
  'Pacific/Wallis' => 'Pacific/Wallis',
  'Europe/London' => 'Europe/London',
  'Europe/Dublin' => 'Europe/Dublin',
  'WET' => 'WET',
  'CET' => 'CET',
  'MET' => 'MET',
  'EET' => 'EET',
  'Europe/Tirane' => 'Europe/Tirane',
  'Europe/Andorra' => 'Europe/Andorra',
  'Europe/Vienna' => 'Europe/Vienna',
  'Europe/Minsk' => 'Europe/Minsk',
  'Europe/Brussels' => 'Europe/Brussels',
  'Europe/Sofia' => 'Europe/Sofia',
  'Europe/Prague' => 'Europe/Prague',
  'Europe/Copenhagen' => 'Europe/Copenhagen',
  'Atlantic/Faeroe' => 'Atlantic/Faeroe',
  'America/Danmarkshavn' => 'America/Danmarkshavn',
  'America/Scoresbysund' => 'America/Scoresbysund',
  'America/Godthab' => 'America/Godthab',
  'America/Thule' => 'America/Thule',
  'Europe/Tallinn' => 'Europe/Tallinn',
  'Europe/Helsinki' => 'Europe/Helsinki',
  'Europe/Paris' => 'Europe/Paris',
  'Europe/Berlin' => 'Europe/Berlin',
  'Europe/Gibraltar' => 'Europe/Gibraltar',
  'Europe/Athens' => 'Europe/Athens',
  'Europe/Budapest' => 'Europe/Budapest',
  'Atlantic/Reykjavik' => 'Atlantic/Reykjavik',
  'Europe/Rome' => 'Europe/Rome',
  'Europe/Riga' => 'Europe/Riga',
  'Europe/Vaduz' => 'Europe/Vaduz',
  'Europe/Vilnius' => 'Europe/Vilnius',
  'Europe/Luxembourg' => 'Europe/Luxembourg',
  'Europe/Malta' => 'Europe/Malta',
  'Europe/Chisinau' => 'Europe/Chisinau',
  'Europe/Monaco' => 'Europe/Monaco',
  'Europe/Amsterdam' => 'Europe/Amsterdam',
  'Europe/Oslo' => 'Europe/Oslo',
  'Europe/Warsaw' => 'Europe/Warsaw',
  'Europe/Lisbon' => 'Europe/Lisbon',
  'Atlantic/Azores' => 'Atlantic/Azores',
  'Atlantic/Madeira' => 'Atlantic/Madeira',
  'Europe/Bucharest' => 'Europe/Bucharest',
  'Europe/Kaliningrad' => 'Europe/Kaliningrad',
  'Europe/Moscow' => 'Europe/Moscow',
  'Europe/Samara' => 'Europe/Samara',
  'Asia/Yekaterinburg' => 'Asia/Yekaterinburg',
  'Asia/Omsk' => 'Asia/Omsk',
  'Asia/Novosibirsk' => 'Asia/Novosibirsk',
  'Asia/Krasnoyarsk' => 'Asia/Krasnoyarsk',
  'Asia/Irkutsk' => 'Asia/Irkutsk',
  'Asia/Yakutsk' => 'Asia/Yakutsk',
  'Asia/Vladivostok' => 'Asia/Vladivostok',
  'Asia/Sakhalin' => 'Asia/Sakhalin',
  'Asia/Magadan' => 'Asia/Magadan',
  'Asia/Kamchatka' => 'Asia/Kamchatka',
  'Asia/Anadyr' => 'Asia/Anadyr',
  'Europe/Belgrade' => 'Europe/Belgrade' ,
  'Europe/Madrid' =>'Europe/Madrid' ,
  'Africa/Ceuta' => 'Africa/Ceuta',
  'Atlantic/Canary' => 'Atlantic/Canary',
  'Europe/Stockholm' => 'Europe/Stockholm',
  'Europe/Zurich' => 'Europe/Zurich' ,
  'Europe/Istanbul' => 'Europe/Istanbul',
  'Europe/Kiev' => 'Europe/Kiev',
  'Europe/Uzhgorod' => 'Europe/Uzhgorod',
  'Europe/Zaporozhye' => 'Europe/Zaporozhye',
  'Europe/Simferopol' => 'Europe/Simferopol',
  'America/New_York' => 'America/New_York',
  'America/Chicago' =>'America/Chicago' ,
  'America/North_Dakota/Center' => 'America/North_Dakota/Center',
  'America/Denver' => 'America/Denver',
  'America/Los_Angeles' => 'America/Los_Angeles',
  'America/Juneau' => 'America/Juneau',
  'America/Yakutat' => 'America/Yakutat',
  'America/Anchorage' => 'America/Anchorage',
  'America/Nome' =>'America/Nome' ,
  'America/Adak' => 'America/Adak',
  'Pacific/Honolulu' => 'Pacific/Honolulu',
  'America/Phoenix' => 'America/Phoenix',
  'America/Boise' => 'America/Boise',
  'America/Indiana/Indianapolis' => 'America/Indiana/Indianapolis',
  'America/Indiana/Marengo' => 'America/Indiana/Marengo',
  'America/Indiana/Knox' =>  'America/Indiana/Knox',
  'America/Indiana/Vevay' => 'America/Indiana/Vevay',
  'America/Kentucky/Louisville' =>'America/Kentucky/Louisville'  ,
  'America/Kentucky/Monticello' =>  'America/Kentucky/Monticello' ,
  'America/Detroit' => 'America/Detroit',
  'America/Menominee' => 'America/Menominee',
  'America/St_Johns' => 'America/St_Johns',
  'America/Goose_Bay' => 'America/Goose_Bay' ,
  'America/Halifax' => 'America/Halifax',
  'America/Glace_Bay' =>'America/Glace_Bay' ,
  'America/Montreal' => 'America/Montreal',
  'America/Toronto' => 'America/Toronto',
  'America/Thunder_Bay' => 'America/Thunder_Bay' ,
  'America/Nipigon' => 'America/Nipigon',
  'America/Rainy_River' => 'America/Rainy_River',
  'America/Winnipeg' => 'America/Winnipeg',
  'America/Regina' => 'America/Regina',
  'America/Swift_Current' => 'America/Swift_Current',
  'America/Edmonton' =>  'America/Edmonton',
  'America/Vancouver' => 'America/Vancouver',
  'America/Dawson_Creek' => 'America/Dawson_Creek',
  'America/Pangnirtung' => 'America/Pangnirtung'  ,
  'America/Iqaluit' => 'America/Iqaluit' ,
  'America/Coral_Harbour' => 'America/Coral_Harbour' ,
  'America/Rankin_Inlet' => 'America/Rankin_Inlet',
  'America/Cambridge_Bay' => 'America/Cambridge_Bay',
  'America/Yellowknife' => 'America/Yellowknife',
  'America/Inuvik' =>'America/Inuvik' ,
  'America/Whitehorse' => 'America/Whitehorse' ,
  'America/Dawson' => 'America/Dawson',
  'America/Cancun' => 'America/Cancun',
  'America/Merida' => 'America/Merida',
  'America/Monterrey' => 'America/Monterrey',
  'America/Mexico_City' => 'America/Mexico_City',
  'America/Chihuahua' => 'America/Chihuahua',
  'America/Hermosillo' => 'America/Hermosillo',
  'America/Mazatlan' => 'America/Mazatlan',
  'America/Tijuana' => 'America/Tijuana',
  'America/Anguilla' => 'America/Anguilla',
  'America/Antigua' => 'America/Antigua',
  'America/Nassau' =>'America/Nassau' ,
  'America/Barbados' => 'America/Barbados',
  'America/Belize' => 'America/Belize',
  'Atlantic/Bermuda' => 'Atlantic/Bermuda',
  'America/Cayman' => 'America/Cayman',
  'America/Costa_Rica' => 'America/Costa_Rica',
  'America/Havana' => 'America/Havana',
  'America/Dominica' => 'America/Dominica',
  'America/Santo_Domingo' => 'America/Santo_Domingo',
  'America/El_Salvador' => 'America/El_Salvador',
  'America/Grenada' => 'America/Grenada',
  'America/Guadeloupe' => 'America/Guadeloupe',
  'America/Guatemala' => 'America/Guatemala',
  'America/Port-au-Prince' => 'America/Port-au-Prince',
  'America/Tegucigalpa' => 'America/Tegucigalpa',
  'America/Jamaica' => 'America/Jamaica',
  'America/Martinique' => 'America/Martinique',
  'America/Montserrat' => 'America/Montserrat',
  'America/Managua' => 'America/Managua',
  'America/Panama' => 'America/Panama',
  'America/Puerto_Rico' =>'America/Puerto_Rico' ,
  'America/St_Kitts' => 'America/St_Kitts',
  'America/St_Lucia' => 'America/St_Lucia',
  'America/Miquelon' => 'America/Miquelon',
  'America/St_Vincent' => 'America/St_Vincent',
  'America/Grand_Turk' => 'America/Grand_Turk',
  'America/Tortola' => 'America/Tortola',
  'America/St_Thomas' => 'America/St_Thomas',
  'America/Argentina/Buenos_Aires' => 'America/Argentina/Buenos_Aires',
  'America/Argentina/Cordoba' => 'America/Argentina/Cordoba',
  'America/Argentina/Tucuman' => 'America/Argentina/Tucuman',
  'America/Argentina/La_Rioja' => 'America/Argentina/La_Rioja',
  'America/Argentina/San_Juan' => 'America/Argentina/San_Juan',
  'America/Argentina/Jujuy' => 'America/Argentina/Jujuy',
  'America/Argentina/Catamarca' => 'America/Argentina/Catamarca',
  'America/Argentina/Mendoza' => 'America/Argentina/Mendoza',
  'America/Argentina/Rio_Gallegos' => 'America/Argentina/Rio_Gallegos',
  'America/Argentina/Ushuaia' =>  'America/Argentina/Ushuaia',
  'America/Aruba' => 'America/Aruba',
  'America/La_Paz' => 'America/La_Paz',
  'America/Noronha' => 'America/Noronha',
  'America/Belem' => 'America/Belem',
  'America/Fortaleza' => 'America/Fortaleza',
  'America/Recife' => 'America/Recife',
  'America/Araguaina' => 'America/Araguaina',
  'America/Maceio' => 'America/Maceio',
  'America/Bahia' => 'America/Bahia',
  'America/Sao_Paulo' => 'America/Sao_Paulo',
  'America/Campo_Grande' => 'America/Campo_Grande',
  'America/Cuiaba' => 'America/Cuiaba',
  'America/Porto_Velho' => 'America/Porto_Velho',
  'America/Boa_Vista' => 'America/Boa_Vista',
  'America/Manaus' => 'America/Manaus',
  'America/Eirunepe' => 'America/Eirunepe',
  'America/Rio_Branco' => 'America/Rio_Branco',
  'America/Santiago' => 'America/Santiago',
  'Pacific/Easter' => 'Pacific/Easter' ,
  'America/Bogota' => 'America/Bogota',
  'America/Curacao' => 'America/Curacao',
  'America/Guayaquil' => 'America/Guayaquil',
  'Pacific/Galapagos' => 'Pacific/Galapagos' ,
  'Atlantic/Stanley' => 'Atlantic/Stanley',
  'America/Cayenne' => 'America/Cayenne',
  'America/Guyana' => 'America/Guyana',
  'America/Asuncion' => 'America/Asuncion',
  'America/Lima' => 'America/Lima',
  'Atlantic/South_Georgia' => 'Atlantic/South_Georgia',
  'America/Paramaribo' => 'America/Paramaribo',
  'America/Port_of_Spain' => 'America/Port_of_Spain',
  'America/Montevideo' => 'America/Montevideo',
  'America/Caracas' => 'America/Caracas',
  );

  $app_list_strings['moduleList']['Sugar_Favorites'] = 'Favorites';
  $app_list_strings['eapm_list']= array(
    'Sugar'=>'Sugar',
    'WebEx'=>'WebEx',
    'GoToMeeting'=>'GoToMeeting',
    'LotusLive'=>'LotusLive',
    'Google' => 'Google',
    'Box' => 'Box.net',
    'Facebook'=>'Facebook',
    'Twitter'=>'Twitter',
  );
  $app_list_strings['eapm_list_import']= array(
  	'Google' => 'Google Contacts',
  );
$app_list_strings['eapm_list_documents']= array(
  	'Google' => 'Google Docs',
  );
	$app_list_strings['token_status'] = array(
        1 => 'Request',
        2 => 'Access',
        3 => 'Invalid',
    );
$app_list_strings['oauth_type_dom'] = array(
    'oauth1' => 'OAuth 1.0',
    'oauth2' => 'OAuth 2.0',
);
$app_list_strings['oauth_client_type_dom'] = array(
    'user' => 'Sugar User',
    'mobile' => 'Mobile Client',
    'plugin' => 'Plug-in',
    'support_portal' => 'Support Portal',
    'other' => 'other',
);

$app_list_strings ['emailTemplates_type_list'] = array (
    '' => '' ,
    'campaign' => 'Campaign' ,
    'email' => 'Email',
    //BEGIN SUGARCRM flav=pro ONLY
    'workflow' => 'Workflow',
    //END SUGARCRM flav=pro ONLY
  );

$app_list_strings ['emailTemplates_type_list_campaigns'] = array (
    '' => '' ,
    'campaign' => 'Campaign' ,
  );

$app_list_strings ['emailTemplates_type_list_no_workflow'] = array (
    '' => '' ,
    'campaign' => 'Campaign' ,
    'email' => 'Email',
  );
$app_strings ['documentation'] = array (
    'LBL_DOCS' => 'Documentation',
    'ULT' => '02_Sugar_Ultimate',
	'ENT' => '02_Sugar_Enterprise',
	'CORP' => '03_Sugar_Corporate',
	'PRO' => '04_Sugar_Professional',
	'COM' => '05_Sugar_Community_Edition'
);

$app_list_strings['forecasts_config_category_options_dom'] = array(
    'show_binary' => 'Two Categories',
    'show_buckets' => 'Three Categories',
    'show_n_buckets' => 'N Categories',
);
$app_list_strings['forecasts_timeperiod_types_dom'] = array(
    'fiscal' => 'Fiscal Year',
    'chronological' => 'Date Based Year',
);
$app_list_strings['forecasts_timeperiod_options_dom'] = array(
    'yearly' => 'Yearly',
    'quarterly' => 'Quarterly',
);
$app_list_strings['forecasts_timeperiod_leaf_quarterly_options_dom'] = array(
    'first' => 'First',
    'middle' => 'Middle',
    'last' => 'Last'
);

//BEGIN SUGARCRM flav=free ONLY
$app_list_strings['sales_stage_dom'] = array(
    'Prospecting'=>'New',
    'Qualification'=>'Engaged',
    'Closed Won'=>'Won',
    'Closed Lost'=>'Lost',
);
//END SUGARCRM flav=free ONLY

    //BEGIN SUGARCRM flav=pro ONLY
	$app_list_strings ['pdfmanager_yes_no_list'] = array (
		'yes' => 'Yes' ,
		'no' => 'No',
	);
    //END SUGARCRM flav=pro ONLY
?>
