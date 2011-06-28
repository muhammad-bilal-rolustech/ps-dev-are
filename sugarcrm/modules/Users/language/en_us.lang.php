<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.	Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.	You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.	See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.	Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: en_us.lang.php 57067 2010-06-23 16:52:55Z kjing $
 * Description:	Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$mod_strings = array (
//BEGIN SUGARCRM flav=pro ONLY
	'LBL_ASSIGN_PRIVATE_TEAM'			=> '(private team on save)',
	'LBL_ASSIGN_TEAM'					=> 'Assign To Team',
	'LBL_DEFAULT_TEAM_TEXT'				=> 'Teams available to appear by default in records are those of which you are a member.',
	'LBL_DEFAULT_TEAM'					=> 'Default Teams',
	'LBL_LIST_DESCRIPTION'				=> 'Description',
	'LBL_MY_TEAMS'						=> 'My Teams',
	'LBL_PRIVATE_TEAM_FOR'				=> 'Private team for',
	'LNK_EDIT_TABS'						=> 'Edit Tabs',
	'NTC_REMOVE_TEAM_MEMBER_CONFIRMATION'	=> 'Are you sure you want to remove this user\\\'s membership?',
	'LBL_TEAMS'							=> 'Teams',
	'LBL_TEAM_UPLINE'					=> 'Member Reports-to',
	'LBL_TEAM_UPLINE_EXPLICIT'			=> 'Member',

//END SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav!=sales ONLY
    'LBL_DELETE_USER_CONFIRM'           => 'When the User record is deleted, the corresponding Employee record will also be deleted. After the user is deleted, any workflow definitions and reports involving the user might need to be updated.<br/><br/>'.
                                                'Deleting a User record cannot be undone.',
	'LBL_DELETE_GROUP_CONFIRM'          => 'Are you sure you want to delete this Group User? Click OK to delete the User record.<br/>After clicking OK, you will be given the ability to reassign records assigned to the Group User to another user.',
	'LBL_DELETE_PORTAL_CONFIRM'         => 'Are you sure you want to delete this Portal API User? Click OK to delete the User record.',
//END SUGARCRM flav!=sales ONLY
//BEGIN SUGARCRM flav=sales ONLY
	'LBL_DELETE_USER_CONFIRM'           => 'When the User record is deleted, the corresponding Employee record will also be deleted. After the user is deleted, any workflow definitions and reports involving the user might need to be updated.<br/><br/>'.
                                                'Click OK to delete the User record.',
//END SUGARCRM flav=sales ONLY
//BEGIN SUGARCRM flav=com ONLY
    'LBL_DELETE_USER_CONFIRM'           => 'When the User record is deleted, the corresponding Employee record will also be deleted.<br/><br/>'.
                                                  'Click OK to delete the User record.',
//END SUGARCRM flav=com ONLY

	'LNK_IMPORT_USERS'                 => 'Import Users',
	'ERR_DELETE_RECORD'					=> 'A record number must be specified to delete the account.',
	'ERR_EMAIL_INCORRECT'				=> 'Provide a valid email address in order to create and send the password.',
	'ERR_EMAIL_NO_OPTS'					=> 'Could not find optimum settings for Inbound Email.',
	'ERR_ENTER_CONFIRMATION_PASSWORD'	=> 'Please enter your password confirmation.',
	'ERR_ENTER_NEW_PASSWORD'			=> 'Please enter your new password.',
	'ERR_ENTER_OLD_PASSWORD'			=> 'Please enter your current password.',
	'ERR_IE_FAILURE1'					=> '[Click here to return]',
	'ERR_IE_FAILURE2'					=> 'There was a problem connecting to the Email Account.  Please check your settings and try again.',
	'ERR_IE_MISSING_REQUIRED'			=> "Inbound Email settings are missing required information.\n  Please check your settings and try again.\n\nIf you are not setting up Inbound Email, please clear all fields in that section.",
	'ERR_INVALID_PASSWORD'				=> 'You must specify a valid username and password.',
	'ERR_NO_LOGIN_MOBILE'				=> 'Your first login to this application must be completed with a non-mobile browser or in normal mode. Please return with a full browser or click on the normal link below. We apologize for any inconvenience.',
	'ERR_LAST_ADMIN_1'					=> 'The user name "',
	'ERR_LAST_ADMIN_2'					=> '" is the last user with administrator access.	At least one user must be an administrator.',
	'ERR_PASSWORD_CHANGE_FAILED_1'		=> 'User password change failed for ',
	'ERR_PASSWORD_CHANGE_FAILED_2'		=> ' failed.	The new password must be set.',
	'ERR_PASSWORD_INCORRECT_OLD_1'		=> 'Incorrect current password for user ',
	'ERR_PASSWORD_INCORRECT_OLD_2'		=> '. Re-enter password information.',
	'ERR_PASSWORD_MISMATCH'				=> 'The passwords do not match.',
	'ERR_PASSWORD_USERNAME_MISSMATCH'   => 'You must specify a valid User Name and Email Address.',
	'ERR_PASSWORD_LINK_EXPIRED'         => 'Your link has expired, please generate a new one',
	'ERR_REENTER_PASSWORDS'				=> 'The New Password and Confirm Password values do not match.',
	'ERR_REPORT_LOOP'					=> 'The system detected a reporting loop. A user cannot report to themselves, nor can any of their managers report to them.',
	'ERR_RULES_NOT_MET'                 => 'The password you entered did not meet the password requirements.  Please try again.',
	'ERR_USER_INFO_NOT_FOUND'			=> 'User Information not found',
	'ERR_USER_NAME_EXISTS_1'			=> 'The user name ',
	'ERR_USER_NAME_EXISTS_2'			=> ' already exists.	Duplicate user names are not allowed.	Change the user name to be unique.',
	'ERR_USER_IS_LOCKED_OUT'			=> 'This user is locked out of the Sugar application and cannot log in using his/her existing password.',

	'LBL_PASSWORD_SENT'                => 'Password Updated',
	'LBL_CANNOT_SEND_PASSWORD'         => 'Cannot send password',
	'ERR_EMAIL_NOT_SENT_ADMIN'			=> 'System is unable to process your request. Please check:',
	'ERR_SMTP_URL_SMTP_PORT'			=> 'SMTP Server URL and Port',
	'ERR_SMTP_USERNAME_SMTP_PASSWORD'	=> 'SMTP Username and  SMTP Password',
	'ERR_RECIPIENT_EMAIL'				=> 'Recipient Email Address',
	'ERR_SERVER_STATUS'					=> 'Your server status',
	'ERR_SERVER_SMTP_EMPTY'				=> 'The system is unable to send an email to the user. Please check the Outgoing Mail Configuration in <a href="index.php?module=EmailMan&action=config">Email Settings</a>.',

	'LBL_ADDRESS_CITY'					=> 'Address City',
	'LBL_ADDRESS_COUNTRY'				=> 'Address Country',
	'LBL_ADDRESS_INFORMATION'			=> 'Address Information',
	'LBL_ADDRESS_POSTALCODE'			=> 'Address Postal Code',
	'LBL_ADDRESS_STATE'					=> 'Address State',
	'LBL_ADDRESS_STREET'				=> 'Address Street',
	'LBL_ADDRESS'						=> 'Address',
	'LBL_ADMIN_USER'					=> 'System Administrator User',

	//BEGIN SUGARCRM flav=com || flav=sales ONLY

	'LBL_ADMIN_DESC'					=> 'User can access the Administration page all records.',
	'LBL_REGULAR_DESC'					=> 'User can access modules and records based on roles.',

	//END SUGARCRM flav=com || flav=sales ONLY


	//BEGIN SUGARCRM flav=pro ONLY
	'LBL_ADMIN_DESC'					=> 'User can access the Administration page all records, regardless of team security.',
	'LBL_REGULAR_DESC'					=> 'User can access modules and records based on team security and roles.',
	//END SUGARCRM flav=pro ONLY
	'LBL_ADMIN'							=> 'System Administrator',
	'LBL_ADVANCED'                     => 'Advanced',
    'LBL_ANY_ADDRESS'                  => 'Any Address:',
	'LBL_ANY_EMAIL'						=> 'Any Email',
	'LBL_ANY_PHONE'						=> 'Any Phone',
	'LBL_BUTTON_CREATE'					=> 'Create',
	'LBL_BUTTON_EDIT'					=> 'Edit',
	'LBL_CALENDAR_OPTIONS'				=> 'Calendar Options',
	'LBL_CHANGE_PASSWORD'               => 'Change Generated Password',
	'LBL_CHANGE_SYSTEM_PASSWORD'		=> 'Please provide a new password.',
	'LBL_CHANGE_PASSWORD_TITLE'         => 'Password',
    'LBL_CHOOSE_A_KEY'					=> 'Choose a key to prevent unauthorized publishing of your calendar',
	'LBL_CHOOSE_WHICH'					=> 'Choose which tabs are displayed',
	'LBL_CITY'							=> 'City',

	'LBL_CLEAR_BUTTON_TITLE'			=> 'Clear',


	'LBL_CONFIRM_PASSWORD'				=> 'Confirm Password',
	'LBL_CONFIRM_REGULAR_USER'			=> 'You have changed the user type from System Administrator User to Regular User.  After saving this change, the user will no longer have system administrator privileges.\n\nClick OK to proceed.\nClick Cancel to return to the record.',
	'LBL_COUNTRY'						=> 'Country',
	'LBL_CURRENCY_TEXT'					=> 'Select the currency that will be displayed by default when you create new records. This is also the currency that will be displayed in the Amount columns in the Opportunities ListView.',
	'LBL_CURRENCY'						=> 'Currency',
	'LBL_CURRENCY_EXAMPLE'				=> 'Currency Display Example',
	'LBL_CURRENCY_SIG_DIGITS'			=> 'Currency Significant Digits',
	'LBL_CURRENCY_SIG_DIGITS_DESC'		=> 'Number of decimal places to show for currency',
	'LBL_NUMBER_GROUPING_SEP'			=> '1000s separator',
	'LBL_NUMBER_GROUPING_SEP_TEXT'		=> 'Character used to separate thousands',
	'LBL_DECIMAL_SEP'					=> 'Decimal Symbol',
	'LBL_DECIMAL_SEP_TEXT'				=> 'Character used to separate decimal portion',
	'LBL_DATE_FORMAT_TEXT'				=> 'Set the display format for date stamps',
	'LBL_DATE_FORMAT'					=> 'Date Format',
	'LBL_DEFAULT_SUBPANEL_TITLE'		=> 'Users',
	'LBL_DEPARTMENT'					=> 'Department',
	'LBL_DESCRIPTION'					=> 'Description',
	'LBL_DISPLAY_TABS'					=> 'Display Tabs',
	'LBL_DOWNLOADS'                    => 'Downloads',
	'LBL_DST_INSTRUCTIONS'				=> '(+DST) indicates the observance of Daylight Savings Time',
	'LBL_EDIT_TABS'						=> 'Edit Tabs',
	'LBL_EDIT'							=> 'Edit',
	'LBL_USER_HASH'						=> 'Password',
	'LBL_AUTHENTICATE_ID'				=> 'Authentication Id',
	'LBL_ACCOUNT_NAME'					=> 'Account Name',
	'LBL_USER_PREFERENCES'				=> 'User Preferences',
	'LBL_EXT_AUTHENTICATE'				=> 'External Authentication',
	'LBL_EMAIL_OTHER'					=> 'Email 2',
	'LBL_EMAIL'							=> 'Email Address',
	'LBL_EMAIL_CHARSET'					=> 'Outbound Character Set',
	'LBL_EMAIL_EDITOR_OPTION'			=> 'Compose format',
	'LBL_EMAIL_GMAIL_DEFAULTS'			=> 'Prefill Gmail&#153; Defaults',
	'LBL_EMAIL_LINK_TYPE'				=> 'Email Client',

    'LBL_EMAIL_LINK_TYPE_HELP'			=> '<b>Sugar Mail Client:</b> Send emails using the email client in the Sugar application.<br><b>External Mail Client:</b> Send email using an email client outside of the Sugar application, such as Microsoft Outlook.',

    'LBL_EMAIL_NOT_SENT'                => 'System is unable to process your request. Please contact the system administrator.',
    'LBL_EMAIL_PROVIDER'               => 'Email Provider',
	'LBL_EMAIL_SHOW_COUNTS'				=> 'Show email counts?',
	'LBL_EMAIL_SIGNATURE_ERROR1'		=> 'This signature requires a name.',
    'LBL_EMAIL_SMTP_SSL'				=> 'Enable SMTP over SSL',
    'LBL_EMAIL_TEMPLATE_MISSING'            => 'No email template is selected for the email containing the password that will be sent to the user.  Please select an email template in the Password Management page.',
    'LBL_EMPLOYEE_STATUS'				=> 'Employee Status',
    'LBL_EMPLOYEE_INFORMATION'         => 'Employee Information',
	'LBL_ERROR'							=> 'Error',
	'LBL_EXPORT_CHARSET'				=> 'Import/Export Character Set',
	'LBL_EXPORT_CHARSET_DESC'			=> 'Choose the character set used in your locale.  This property will be used for data imports, .csv exports and for vCard generation.',
	'LBL_EXPORT_DELIMITER'				=> 'Export Delimiter',
	'LBL_EXPORT_DELIMITER_DESC'			=> 'Specify the character(s) used to delimit exported data.',
	'LBL_FAX_PHONE'						=> 'Fax',
	'LBL_FAX'							=> 'Fax',
	'LBL_FIRST_NAME'					=> 'First Name',
    'LBL_GENERATE_PASSWORD_BUTTON_KEY'  => 'G',
    'LBL_SYSTEM_GENERATED_PASSWORD'     =>'System Generated Password',
    'LBL_GENERATE_PASSWORD_BUTTON_LABEL'   => 'Reset Password',
    'LBL_GENERATE_PASSWORD_BUTTON_TITLE'   => 'Reset Password [Alt+G]',
    'LBL_GENERATE_PASSWORD'             => 'Reset Password',
	'LBL_GROUP_DESC'					=> 'Use for assigning items to a group (example: for Inbound Email).  This type cannot login through the Sugar web interface.',
	'LBL_GROUP_USER_STATUS'				=> 'Group User',
	'LBL_GROUP_USER'					=> 'Group User',
	'LBL_HIDE_TABS'						=> 'Hide Tabs',
	'LBL_HOME_PHONE'					=> 'Home Phone',
	'LBL_INBOUND_TITLE'					=> 'Account Information',
	'LBL_IS_ADMIN'						=> 'Is Administrator',
	'LBL_LANGUAGE'						=> 'Language',
	'LBL_LAST_NAME'						=> 'Last Name',
    'LBL_LAST_NAME_SLASH_NAME'			=> 'Last Name/Name',
    'LBL_LAYOUT_OPTIONS'                => 'Layout Options',
	'LBL_LDAP'							=> 'LDAP',
	'LBL_LDAP_AUTHENTICATION'			=> 'LDAP Authentication',
	'LBL_LIST_ACCEPT_STATUS'			=> 'Accept Status',
	'LBL_LIST_ADMIN'					=> 'Admin',
	'LBL_LIST_DEPARTMENT'				=> 'Department',
	'LBL_LIST_EMAIL'					=> 'Email',
	'LBL_LIST_FORM_TITLE'				=> 'Users',
	'LBL_LIST_GROUP'					=> 'Group',
	'LBL_LIST_LAST_NAME'				=> 'Last Name',
	'LBL_LIST_MEMBERSHIP'				=> 'Membership',
	'LBL_LIST_NAME'						=> 'Name',
	'LBL_LIST_PRIMARY_PHONE'			=> 'Primary Phone',
	'LBL_LIST_PASSWORD'					=> 'Password',
	'LBL_LIST_STATUS'					=> 'Status',
	'LBL_LIST_TITLE'					=> 'Title',
	'LBL_LIST_USER_NAME'				=> 'User Name',
	'LBL_LOCALE_DEFAULT_NAME_FORMAT'	=> 'Name Display Format',
	'LBL_LOCALE_DESC_FIRST'				=> '[First]',
	'LBL_LOCALE_DESC_LAST'				=> '[Last]',
	'LBL_LOCALE_DESC_SALUTATION'		=> '[Salutation]',
	'LBL_LOCALE_DESC_TITLE'				=> '[Title]',
    //BEGIN SUGARCRM flav!=com ONLY
	'LBL_PICTURE_FILE'					=> 'Picture',
    //END SUGARCRM flav!=com ONLY
	'LBL_LOCALE_EXAMPLE_NAME_FORMAT'	=> 'Example',
	'LBL_LOCALE_NAME_FORMAT_DESC'		=> 'Set how names will be displayed.',
	'LBL_LOCALE_NAME_FORMAT_DESC_2'	=> '<i>"s" Salutation<br>"f" First Name<br>"l" Last Name</i>',
    'LBL_SAVED_SEARCH'                  => 'Saved Search & Layout',
	// LOGIN PAGE STRINGS
	'LBL_LOGIN_BUTTON_KEY'				=> 'L',
	'LBL_LOGIN_BUTTON_LABEL'			=> 'Log In',
	'LBL_LOGIN_BUTTON_TITLE'			=> 'Log In [Alt+L]',
	'LBL_LOGIN_WELCOME_TO'				=> 'Welcome to',
	'LBL_LOGIN_OPTIONS'					=> 'Options',
    'LBL_LOGIN_FORGOT_PASSWORD'         => 'Forgot Password?',
    'LBL_LOGIN_SUBMIT'      		    => 'Submit',
    'LBL_LOGIN_ATTEMPTS_OVERRUN'        => 'Too many failed login attempts.',
    'LBL_LOGIN_LOGIN_TIME_ALLOWED'      => 'You can try logging in again in ',
    'LBL_LOGIN_LOGIN_TIME_DAYS'     	=> 'days.',
    'LBL_LOGIN_LOGIN_TIME_HOURS'    	=> 'h.',
    'LBL_LOGIN_LOGIN_TIME_MINUTES'      => 'min.',
   	'LBL_LOGIN_LOGIN_TIME_SECONDS'      => 'sec.',
    'LBL_LOGIN_ADMIN_CALL'              => 'Please contact the system administrator.',
	// END LOGIN PAGE STRINGS
	'LBL_MAIL_FROMADDRESS'				=> 'Reply-to address',
	'LBL_MAIL_FROMNAME'					=> 'Reply-to name',
	'LBL_MAIL_OPTIONS_TITLE'			=> 'Email Settings',
	'LBL_MAIL_SENDTYPE'					=> 'Mail transfer agent',
	'LBL_MAIL_SMTPAUTH_REQ'				=> 'Use SMTP Authentication?',
	'LBL_MAIL_SMTPPORT'					=> 'SMTP Port',
	'LBL_MAILMERGE_TEXT'				=> 'Enable Mail Merge (Mail Merge must also be enabled by the system administrator in Configure Settings)',
	'LBL_MAILMERGE'						=> 'Mail Merge',
	'LBL_MAX_TAB'						=> 'Number of Tabs',
    'LBL_MAX_TAB_DESCRIPTION'           => 'Number of tabs shown at the top of the page before an overflow menu appears.',
    'LBL_MAX_SUBTAB'                    => 'Number of subtabs',
    'LBL_MAX_SUBTAB_DESCRIPTION'        => 'Number of subtabs shown per tab before an overflow menu appears.',
	'LBL_MESSENGER_ID'					=> 'IM Name',
	'LBL_MESSENGER_TYPE'				=> 'IM Type',
	'LBL_MOBILE_PHONE'					=> 'Mobile',
	'LBL_MODIFIED_BY'                  =>'Modified By',
    'LBL_MODIFIED_BY_ID'               =>'Modified By ID',
    'LBL_MODULE_NAME'					=> 'Users',
	'LBL_MODULE_TITLE'					=> 'Users: Home',
    'LBL_NAME'							=> 'Full Name',
    'LBL_SIGNATURE_NAME'                                        =>  'Name',
    'LBL_NAVIGATION_PARADIGM'           => 'Navigation',
    'LBL_NAVIGATION_PARADIGM_DESCRIPTION'   => 'Select to view modules tabs in the navigation bar based on pre-defined groups. If this feature is not selected, all modules will appear within the navigation bar.',
    'LBL_USE_GROUP_TABS'                => 'Grouped Modules',
	'LBL_NEW_FORM_TITLE'				=> 'New User',
	'LBL_NEW_PASSWORD'					=> 'New Password',
	'LBL_NEW_PASSWORD1'					=> 'Password',
	'LBL_NEW_PASSWORD2'					=> 'Confirm Password',
	'LBL_NEW_USER_PASSWORD_1'			=> 'Password was changed successfully.',
	'LBL_NEW_USER_PASSWORD_2'			=> 'An email was sent to the user containing a system-generated password.',
	'LBL_NEW_USER_PASSWORD_3'			=> 'Password was created successfully.',
	'LBL_NEW_USER_BUTTON_KEY'			=> 'N',
	'LBL_NEW_USER_BUTTON_LABEL'			=> 'New User',
	'LBL_NEW_USER_BUTTON_TITLE'			=> 'New User [Alt+N]',
	'LBL_NORMAL_LOGIN'					=> 'Switch to Normal View',
	'LBL_NOTES'							=> 'Notes',
	'LBL_OFFICE_PHONE'					=> 'Office Phone',
	'LBL_OLD_PASSWORD'					=> 'Current Password',
	'LBL_OTHER_EMAIL'					=> 'Other email address',
	'LBL_OTHER_PHONE'					=> 'Other Phone',
	'LBL_OTHER'							=> 'Other',
	'LBL_PASSWORD'						=> 'Password',
    'LBL_PASSWORD_GENERATED'            => 'New password generated',
    'LBL_PASSWORD_EXPIRATION_LOGIN'     => 'Your password has expired. Please provide a new password.',
    'LBL_PASSWORD_EXPIRATION_GENERATED' => 'Your password is system-generated',
    'LBL_PASSWORD_EXPIRATION_TIME'      => 'Your password has expired. Please provide a new password.',

	'LBL_PSW_MODIFIED'                  => 'password last changed',
    'LBL_PHONE'							=> 'Phone',
	'LBL_PICK_TZ_WELCOME'				=> 'Welcome to Sugar.',
	'LBL_PICK_TZ_DESCRIPTION'           => 'Before continuing, please confirm your time zone.  Select the appropriate time zone from the list below, and click Save to continue. The time zone can be changed at any time in your user settings.',
	'LBL_PORTAL_ONLY_DESC'				=> 'Use for the Portal API. This type cannot login through the Sugar web interface.',
	'LBL_PORTAL_ONLY_USER'					=> 'Portal API User',
	'LBL_POSTAL_CODE'					=> 'Postal Code',
	'LBL_PRIMARY_ADDRESS'				=> 'Primary Address',
	'LBL_PROMPT_TIMEZONE_TEXT'			=> 'Select to have new users go through the New User Wizard upon first login.',
	'LBL_PROMPT_TIMEZONE'				=> 'User Wizard Prompt',
	'LBL_PROVIDE_USERNAME_AND_EMAIL' 	=> 'Provide both a User Name and an Email Address.',
	'LBL_PUBLISH_KEY'					=> 'Publish Key',

	'LBL_RECAPTCHA_NEW_CAPTCHA'         => 'Get another CAPTCHA',
	'LBL_RECAPTCHA_SOUND'				=> 'Switch to Sound',
	'LBL_RECAPTCHA_IMAGE'				=> 'Switch to Image',
	'LBL_RECAPTCHA_INSTRUCTION'         => 'Enter the Two Words Below',
	'LBL_RECAPTCHA_INSTRUCTION_OPPOSITE'=> 'Enter the Two Words to the Right',
	'LBL_RECAPTCHA_FILL_FIELD'			=> 'Enter the text that appears in the image.',
	'LBL_RECAPTCHA_INVALID_PRIVATE_KEY'	=> 'Invalid Recaptcha Private Key',
	'LBL_RECAPTCHA_INVALID_REQUEST_COOKIE'=> 'The challenge parameter of the verify Recaptcha script was incorrect.',
	'LBL_RECAPTCHA_UNKNOWN'				=> 'Unknown Recaptcha Error',

	'LBL_RECEIVE_NOTIFICATIONS_TEXT'	=> 'Receive an email notification when a record is assigned to you.',
	'LBL_RECEIVE_NOTIFICATIONS'			=> 'Notify on Assignment',
	'LBL_REGISTER'                      => 'New user? Please register',
	'LBL_REGULAR_USER'                  => 'Regular User',
	'LBL_REMINDER_TEXT'					=> 'Set a default for reminders for calls and meetings.',
	'LBL_REMINDER'						=> 'Reminders',
	'LBL_REMOVED_TABS'					=> 'Admin Remove Tabs',
	'LBL_REPORTS_TO_NAME'				=> 'Reports to',
	'LBL_REPORTS_TO'					=> 'Reports to',
    'LBL_REPORTS_TO_ID'                => 'Reports to ID:',
	'LBL_REQUEST_SUBMIT'				=> 'Your request has been submitted.',
	'LBL_RESET_TO_DEFAULT'				=> 'Reset to Default',
	'LBL_RESET_PREFERENCES'				=> 'Reset User Preferences',
    'LBL_RESET_PREFERENCES_WARNING'     => 'Are you sure you want reset all of your user preferences? Warning: This will also log you out of the application.',
    'LBL_RESET_PREFERENCES_WARNING_USER' => 'Are you sure you want reset all of the preferences for this user?',
    'LBL_RESET_HOMEPAGE'                => 'Reset Homepage',
    'LBL_RESET_DASHBOARD'               => 'Reset Dashboard',
    'LBL_RESET_HOMEPAGE_WARNING'        => 'Are you sure you want reset your Homepage?',
    'LBL_RESET_HOMEPAGE_WARNING_USER'   => 'Are you sure you want reset the Homepage for this user?',
	'LBL_SALUTATION'                    => 'Salutation',
    'LBL_ROLES_SUBPANEL_TITLE'			=> 'Roles',
	'LBL_SEARCH_FORM_TITLE'				=> 'User Search',
	'LBL_SEARCH_URL'					=> 'Search location',
	'LBL_SELECT_CHECKED_BUTTON_LABEL'	=> 'Select Checked Users',
	'LBL_SELECT_CHECKED_BUTTON_TITLE'	=> 'Select Checked Users',
	'LBL_SETTINGS_URL_DESC'				=> 'Use this URL when establishing login settings for the Sugar Plug-in for Microsoft&reg; Outlook&reg; and the Sugar Plug-in for Microsoft&reg; Word&reg;.',
	'LBL_SETTINGS_URL'					=> 'URL',
	'LBL_SIGNATURE'						=> 'Signature',
	'LBL_SIGNATURE_HTML'				=> 'HTML signature',
	'LBL_SIGNATURE_DEFAULT'				=> 'Use signature?',
	'LBL_SIGNATURE_PREPEND'				=> 'Signature above reply?',
	'LBL_SIGNATURES'					=> 'Signatures',
	'LBL_STATE'							=> 'State',
	'LBL_STATUS'						=> 'Status',
    'LBL_SUBPANEL_LINKS'                => 'Subpanel Links',
    'LBL_SUBPANEL_LINKS_DESCRIPTION'    => 'In Detail Views, display a row of Subpanel shortcut links.',
    'LBL_SUBPANEL_TABS'                 => 'Subpanel Tabs',
    'LBL_SUBPANEL_TABS_DESCRIPTION'     => 'In Detail Views, group Subpanels into tabs and display one tab at a time.',
    'LBL_SUGAR_LOGIN'					=> 'Is Sugar User',
    'LBL_SUPPORTED_THEME_ONLY'          => 'Only affects themes that support this option.',
    'LBL_SWAP_LAST_VIEWED_DESCRIPTION'  => 'Display the Last Viewed bar on the side if checked.  Otherwise it goes on top.',
    'LBL_SWAP_SHORTCUT_DESCRIPTION'     => 'Display the Shortcuts bar on top if checked.  Otherwise it goes on the side.',
    'LBL_SWAP_LAST_VIEWED_POSITION'     => 'Last Viewed on side',
    'LBL_SWAP_SHORTCUT_POSITION'        => 'Shortcuts on top',
	'LBL_TAB_TITLE_EMAIL'				=> 'Email Settings',
	'LBL_TAB_TITLE_USER'				=> 'User Settings',
	'LBL_THEME'							=> 'Themes',
	'LBL_THEME_COLOR'					=> 'Color',
	'LBL_THEME_FONT'					=> 'Font',
	'LBL_TIME_FORMAT_TEXT'				=> 'Set the display format for time stamps',
	'LBL_TIME_FORMAT'					=> 'Time Format',
	'LBL_TIMEZONE_DST_TEXT'				=> 'Observe Daylight Savings',
	'LBL_TIMEZONE_DST'					=> 'Daylight Savings',
	'LBL_TIMEZONE_TEXT'					=> 'Set the current time zone',
	'LBL_TIMEZONE'						=> 'Time Zone',
	'LBL_TITLE'							=> 'Title',
	'LBL_USE_REAL_NAMES'				=> 'Show Full Names',
	'LBL_USE_REAL_NAMES_DESC'			=> 'Display users\' full names instead of their User Names in assignment fields.',
	'LBL_USER_INFORMATION'				=> 'User Profile',
	'LBL_USER_LOCALE'					=> 'Locale Settings',
	'LBL_USER_NAME'						=> 'User Name',
	'LBL_USER_SETTINGS'					=> 'User Settings',
	'LBL_USER_TYPE'		   			    => 'User Type',
	'LBL_USER_ACCESS'                  => 'Access',
	'LBL_USER'							=> 'Users',
	'LBL_WORK_PHONE'					=> 'Work Phone',
	'LBL_YOUR_PUBLISH_URL'				=> 'Publish at my location',
	'LBL_YOUR_QUERY_URL'				=> 'Your Query URL',
	'LNK_NEW_USER'						=> 'Create New User',
	'LNK_NEW_PORTAL_USER'				=> 'Create Portal API User',
	'LNK_NEW_GROUP_USER'				=> 'Create Group User',
	'LNK_USER_LIST'						=> 'View Users',
	'LNK_REASSIGN_RECORDS'				=> 'Reassign Records',
    'LBL_PROSPECT_LIST'                 => 'Prospect List',
    'LBL_PROCESSING'                    => 'Processing',
    'LBL_UPDATE_FINISH'                 => 'Update complete',
    'LBL_AFFECTED'                      => 'affected',

    //BEGIN SUGARCRM flav=sales ONLY
    'LBL_USER_NAME_FOR_ROLE'            => 'Users/Roles',
    'LBL_USER_TYPE'                     => 'User Type',
    'LBL_USER_ADMINISTRATOR'            => 'User Administrator',
    'LBL_USER_ADMIN_DESC'               => 'User can manage users in the system.',
    //END SUGARCRM flav=sales ONLY
    //BEGIN SUGARCRM flav!=sales ONLY
    'LBL_USER_NAME_FOR_ROLE'            =>'Users/Teams/Roles',
    //END SUGARCRM flav!=sales ONLY
    'LBL_SESSION_EXPIRED'               => 'You have been logged out because your session has expired.',
//BEGIN SUGARCRM flav=pro||flav=sales ONLY

    'LBL_TOO_MANY_CONCURRENT'           => 'This session has ended because another session has been started under the same username.',
// MASS REASSIGNMENT SCRIPT STRINGS
	'LBL_REASS_SCRIPT_TITLE'			=> 'Reassign Records',
	'LBL_REASS_DESC_PART1'				=> 'Select the modules containing the records to reassign from a specific user to another user. <br/><br/>
                                                            Click Next to view the number of records that will be updated in each selected module.
                                                            Click Cancel to exit the page without reassigning any records.',
        'LBL_REASS_DESC_PART2'=>                    'Select which modules against which to run workflows, send assignment notifications, and do auditing tracking during reassignment. Click Next to continue and reassign records. Click Restart to start over.',
	'LBL_REASS_STEP2_TITLE'				=> 'Team Reassignment',
	'LBL_REASS_STEP2_DESC'				=> 'The teams listed below were available in the from user\'s team, but not in the to user\'s team. All records in the From User\'s team will not be visible in the To User team unless the team values are mapped. ',
	'LBL_REASS_USER_FROM_TEAM'			=> 'From User Team:',
	'LBL_REASS_USER_TO_TEAM'			=> 'To User Team:',
	'LBL_REASS_USER_FROM'				=> 'From User:',
	'LBL_REASS_USER_TO'					=> 'To User:',
	'LBL_REASS_TEAM_TO'					=> 'Set Teams to:',
	'LBL_REASS_TEAMS_GOOD_MSG'			=> 'The To user has access to all of the From user\'s teams. No mapping necessary. Redirecting to the next page in 5 seconds.',
	'LBL_REASS_TEAM_NO_CHANGE'			=> '-- No Change --',
	'LBL_REASS_NOT_PROCESSED' 			=> 'could not be processed:',
	'LBL_REASS_MOD_REASSIGN' 			=> 'Modules to Include in Reassignment:',
	'LBL_REASS_FILTERS'					=> 'Filters',
	'LBL_REASS_NOTES_TITLE'				=> 'Notes:',
	'LBL_REASS_NOTES_THREE'				=> 'Assigning records to yourself will not trigger assignment notifications.',
	//BEGIN SUGARCRM flav=sales ONLY
	'LBL_REASS_NOTES_ONE'				=> 'Including Notifications, and Audit tracking in the reassignment is significantly slower.',
	//END SUGARCRM flav=sales ONLY
	//BEGIN SUGARCRM flav!=sales ONLY
	'LBL_REASS_NOTES_ONE'				=> 'Running workflows will cause the reassignment process to be significantly slower.',
	//END SUGARCRM flav!=sales ONLY
	'LBL_REASS_NOTES_TWO'				=> 'Even though you do not select to do audit tracking, the Date Modified and Modified By field in the records will still be updated accordingly.',
	'LBL_REASS_VERBOSE_OUTPUT'			=> 'Verbose Output',
        'LBL_REASS_VERBOSE_HELP'                     =>  'Select this option to view detailed information about the reassignment tasks that involve workflows.',
	'LBL_REASS_ASSESSING'				=> 'Assessing',
	'LBL_REASS_RECORDS_FROM'			=> 'records from',
	'LBL_REASS_WILL_BE_UPDATED'			=> 'will be updated.',
	//BEGIN SUGARCRM flav=sales ONLY
	'LBL_REASS_WORK_NOTIF_AUDIT' => 'Include Notifications/Audit (significantly slower)',
	//END SUGARCRM flav=sales ONLY
	//BEGIN SUGARCRM flav!=sales ONLY
	'LBL_REASS_WORK_NOTIF_AUDIT' => 'Include Workflow/Notifications/Audit (significantly slower)',
	//END SUGARCRM flav!=sales ONLY
	'LBL_REASS_SUCCESS_ASSIGN'			=> 'Successfully assigned',
	'LBL_REASS_FROM'					=> 'from',
	'LBL_REASS_TO'						=> 'to',
	'LBL_REASS_TEAM_SET_TO'				=> 'and teams were set to',
	'LBL_REASS_FAILED_SAVE'				=> 'Failure to save for record',
	'LBL_REASS_THE_FOLLOWING'			=> 'The following',
	'LBL_REASS_HAVE_BEEN_UPDATED'		=> 'have been updated:',
    'LBL_REASS_CANNOT_PROCESS'		    => 'could not be processed:',
	'LBL_REASS_NONE'					=> 'None',
	'LBL_REASS_UPDATE_COMPLETE'			=> 'Update complete',
	'LBL_REASS_SUCCESSFUL'				=> 'Successful',
	'LBL_REASS_FAILED'					=> 'Failed',
	'LBL_REASS_BUTTON_SUBMIT' 			=> 'Submit',
	'LBL_REASS_BUTTON_CLEAR' 			=> 'Clear',
	'LBL_REASS_BUTTON_CONTINUE'			=> 'Next >',
    'LBL_REASS_BUTTON_REASSIGN'         => 'Reassign',
	'LBL_REASS_BUTTON_GO_BACK' 			=> '< Back',
	'LBL_REASS_BUTTON_RESTART' 			=> 'Restart',
	'LBL_REASS_BUTTON_RETURN' 			=> 'Return',
	// js
	'LBL_REASS_CONFIRM_REASSIGN'		=> 'Would you like to reassign all of this user\'s records?',
	// end js
	'ERR_REASS_SELECT_MODULE'			=> 'Please go back and select at least one module.',
	'ERR_REASS_DIFF_USERS'				=> 'Please select a To User that is different from the From User.',
// END MASS REASSIGNMENT SCRIPT STRINGS

//END SUGARCRM flav=pro||flav=sales ONLY

// INBOUND EMAIL STRINGS
	'LBL_APPLY_OPTIMUMS'				=> 'Apply Optimums',
	'LBL_ASSIGN_TO_USER'				=> 'Assign To User',
	'LBL_BASIC'							=> 'Inbound Setup',
	'LBL_CERT_DESC'						=> 'Force validation of the mail server\'s Security Certificate - do not use if self-signing.',
	'LBL_CERT'							=> 'Validate Certificate',
	'LBL_FIND_OPTIMUM_KEY'				=> 'f',
	'LBL_FIND_OPTIMUM_MSG'				=> '<br>Finding optimum connection variables.',
	'LBL_FIND_OPTIMUM_TITLE'			=> 'Find Optimum Configuration',
	'LBL_FORCE'							=> 'Force Negative',
	'LBL_FORCE_DESC'					=> 'Some IMAP/POP3 servers require special switches. Check to force a negative switch when connecting (i.e., /notls)',
	'LBL_FOUND_OPTIMUM_MSG'				=> '<br>Found optimum settings.	Press the button below to apply them to your Mailbox.',
	'LBL_EMAIL_INBOUND_TITLE'			=> 'Inbound Email Settings',
	'LBL_EMAIL_OUTBOUND_TITLE'			=> 'Outbound Email Settings',
	'LBL_LOGIN'							=> 'User Name',
	'LBL_MAILBOX_DEFAULT'				=> 'INBOX',
	'LBL_MAILBOX_SSL_DESC'				=> 'Use SSL when connecting. If this does not work, check that your PHP installation included "--with-imap-ssl" in the configuration.',
	'LBL_MAILBOX'						=> 'Monitored Folder',
	'LBL_MAILBOX_TYPE'					=> 'Possible Actions',
	'LBL_MARK_READ_NO'					=> 'Email marked deleted after import',
	'LBL_MARK_READ_YES'					=> 'Email left on server after import',
	'LBL_MARK_READ_DESC'				=> 'Import and mark messages read on mail server; do not delete.',
	'LBL_MARK_READ'						=> 'Leave messages on server',
	'LBL_ONLY_SINCE_NO'					=> 'No. Check against all emails on mail server.',
	'LBL_ONLY_SINCE_YES'				=> 'Yes.',
	'LBL_ONLY_SINCE_DESC'				=> 'PHP cannot discern New from Unread messages when using POP3.	Check this flag to scan for messages since the last time the mail account was polled.	This will significantly improve performance if your mail server cannot support IMAP.',
	'LBL_ONLY_SINCE'					=> 'Import only since last check',
	'LBL_PORT'							=> 'Mail server port',
	'LBL_SERVER_OPTIONS'				=> 'Advanced Setup',
	'LBL_SERVER_TYPE'					=> 'Mail server protocol',
	'LBL_SERVER_URL'					=> 'Mail server address',
	'LBL_SSL'							=> 'Use SSL',
	'LBL_SSL_DESC'						=> 'Use Secure Socket Layer when connecting to your mail server.',
	'LBL_TEST_BUTTON_KEY'				=> 't',
	'LBL_TEST_BUTTON_TITLE'				=> 'Test [Alt+T]',
	'LBL_TEST_SETTINGS'					=> 'Test Settings',
	'LBL_TEST_SUCCESSFUL'				=> 'Connection completed successfully.',
	'LBL_TLS_DESC'						=> 'Use Transport Layer Security when connecting to the mail server - only use this if your mail server supports this protocol.',
	'LBL_TLS'							=> 'Use TLS',
	'LBL_TOGGLE_ADV'					=> 'Show Advanced',
    'LBL_OWN_OPPS'                      => 'No Opportunities',
	'LBL_EXTERNAL_AUTH_ONLY'			=> 'Authenticate this user only through',
	'LBL_ONLY'							=> 'Only',
    'LBL_OWN_OPPS_DESC'                 => 'Select if user will not be assigned opportunities. Use this setting for users who are managers that are not involved in sales activities. The setting is used for the forecasting module.',
// END INBOUND EMAIL STRINGS
	'LBL_LDAP_ERROR' => 'LDAP Error: Please contact an Admin',
	'LBL_LDAP_EXTENSION_ERROR' => 'LDAP Error: Extensions not loaded',

// PROJECT RESOURCES STRINGS
	'LBL_USER_HOLIDAY_SUBPANEL_TITLE' => 'User Holidays',
	'LBL_RESOURCE_NAME' => 'Name',
	'LBL_RESOURCE_TYPE' => 'Type',

    //BEGIN SUGARCRM flav=dce ONLY
    'LBL_USER_DCEINST_FORM_TITLE' => 'User-Instance:',
    'LBL_DCEINST_NAME' => 'Instance Name',
    'LBL_LIST_USER_ROLE' => 'Role',
    'LBL_USER_ROLE' => 'Role',
    'LBL_DCEINSTANCES' => 'Instances',
    //END SUGARCRM flav=dce ONLY
	'LBL_PDF_SETTINGS'  =>  'PDF Settings',
	'LBL_PDF_PAGE_FORMAT'  =>  'Page Format',
	'LBL_PDF_PAGE_FORMAT_TEXT'  =>  'The format used for pages',
	'LBL_PDF_PAGE_ORIENTATION'  =>  'Page Orientation',
	'LBL_PDF_PAGE_ORIENTATION_TEXT'  =>  '',
	'LBL_PDF_PAGE_ORIENTATION_P'  =>  'Portrait',
	'LBL_PDF_PAGE_ORIENTATION_L'  =>  'Landscape',
	'LBL_PDF_MARGIN_HEADER'  =>  'Header Margin',
	'LBL_PDF_MARGIN_HEADER_TEXT'  =>  '',
	'LBL_PDF_MARGIN_FOOTER'  =>  'Footer Margin',
	'LBL_PDF_MARGIN_FOOTER_TEXT'  =>  '',
	'LBL_PDF_MARGIN_TOP'  =>  'Top Margin',
	'LBL_PDF_MARGIN_TOP_TEXT'  =>  '',
	'LBL_PDF_MARGIN_BOTTOM'  =>  'Bottom Margin',
	'LBL_PDF_MARGIN_BOTTOM_TEXT'  =>  '',
	'LBL_PDF_MARGIN_LEFT'  =>  'Left Margin',
	'LBL_PDF_MARGIN_LEFT_TEXT'  =>  '',
	'LBL_PDF_MARGIN_RIGHT'  =>  'Right Margin',
	'LBL_PDF_MARGIN_RIGHT_TEXT'  =>  '',
	'LBL_PDF_FONT_NAME_MAIN'  =>  'Font for Header and Body',
	'LBL_PDF_FONT_NAME_MAIN_TEXT'  =>  'The selected font will be applied to the text in the header and the body of the PDF Document',
	'LBL_PDF_FONT_SIZE_MAIN'  =>  'Main Font Size',
	'LBL_PDF_FONT_SIZE_MAIN_TEXT'  =>  '',
	'LBL_PDF_FONT_NAME_DATA'  =>  'Font for Footer',
	'LBL_PDF_FONT_NAME_DATA_TEXT'  =>  'The selected font will be applied to the text in the footer of the PDF Document',
	'LBL_PDF_FONT_SIZE_DATA'  =>  'Data Font Size',
	'LBL_PDF_FONT_SIZE_DATA_TEXT'  =>  '',
	'LBL_LAST_ADMIN_NOTICE' => 'You have selected yourself. You cannot change the User Type or Status of yourself.',
	'LBL_MAIL_SMTPUSER'	=> 'Username',
	'LBL_MAIL_SMTPPASS'	=> 'Password',
	'LBL_MAIL_SMTPSERVER' => 'SMTP Mail Server',
	'LBL_SMTP_SERVER_HELP' => 'This SMTP Mail Server can be used for outgoing mail. Provide a username and password for your email account in order to use the mail server.',
    'LBL_MISSING_DEFAULT_OUTBOUND_SMTP_SETTINGS' => 'The administator has not yet configured the default outbound account.  Unable to send test email.',
    'LBL_MAIL_SMTPAUTH_REQ'				=> 'Use SMTP Authentication?',
	'LBL_MAIL_SMTPPASS'					=> 'SMTP Password:',
	'LBL_MAIL_SMTPPORT'					=> 'SMTP Port:',
	'LBL_MAIL_SMTPSERVER'				=> 'SMTP Server:',
	'LBL_MAIL_SMTPUSER'					=> 'SMTP Username:',
	'LBL_MAIL_SMTPTYPE'                => 'SMTP Server Type:',
	'LBL_MAIL_SMTP_SETTINGS'           => 'SMTP Server Specification',
	'LBL_CHOOSE_EMAIL_PROVIDER'        => 'Choose your Email provider:',
	'LBL_YAHOOMAIL_SMTPPASS'					=> 'Yahoo! Mail Password:',
	'LBL_YAHOOMAIL_SMTPUSER'					=> 'Yahoo! Mail ID:',
	'LBL_GMAIL_SMTPPASS'					=> 'Gmail Password:',
	'LBL_GMAIL_SMTPUSER'					=> 'Gmail Email Address:',
	'LBL_EXCHANGE_SMTPPASS'					=> 'Exchange Password:',
	'LBL_EXCHANGE_SMTPUSER'					=> 'Exchange Username:',
	'LBL_EXCHANGE_SMTPPORT'					=> 'Exchange Server Port:',
	'LBL_EXCHANGE_SMTPSERVER'				=> 'Exchange Server:',
        'LBL_OK'   =>'OK',
        'LBL_CANCEL'    => 'Cancel',
        'LBL_DELETE_USER' => 'Delete User',
	// Wizard
	'LBL_WIZARD_TITLE' => 'User Wizard',
    'LBL_WIZARD_WELCOME_TAB' => 'Welcome',
    'LBL_WIZARD_WELCOME_TITLE' => 'Welcome to Sugar!',
    'LBL_WIZARD_WELCOME' => 'Click <b>Next</b> to configure a few basic settings for using Sugar.',
    'LBL_WIZARD_WELCOME_NOSMTP' => 'Click <b>Next</b> to configure a few basic settings for using Sugar.',
    'LBL_WIZARD_NEXT_BUTTON' => 'Next >',
    'LBL_WIZARD_BACK_BUTTON' => '< Back',
    'LBL_WIZARD_SKIP_BUTTON' => 'Skip',
    'LBL_WIZARD_FINISH_BUTTON' => 'Finish',
    'LBL_WIZARD_FINISH_TAB' => 'Finish',
    'LBL_WIZARD_FINISH_TITLE' => 'You are ready to use Sugar!',
    'LBL_WIZARD_FINISH' => 'Click <b>Finish</b> below to save your settings and to begin using Sugar. For more information on using Sugar:<br /><br />
<table cellpadding=0 cellspacing=0>
<tr><td><img src=include/images/university.png style="margin-right: 5px;"></td><td><a href="http://www.sugarcrm.com/university" target="_blank"><b>Sugar University</b></a><br>End-user and System Administrator Training and Resources</td></tr>
<tr><td colspan=2><hr style="margin: 5px 0px;"></td></tr>
<tr><td><img src=include/images/docs.png style="margin-right: 5px;"></td><td><a href="http://docs.sugarcrm.com/" target="_blank"><b>Documentation</b></a><br>Product Guides and Release Notes</td></tr>
<tr><td colspan=2><hr style="margin: 5px 0px;"></td></tr>
<tr><td><img src=include/images/kb.png style="margin-right: 5px;"></td><td><a href="http://kb.sugarcrm.com/" target="_blank"><b>Knowledge Base</b></a><br>Tips from SugarCRM Support for performing common tasks and processes in Sugar</td></tr>
<tr><td colspan=2><hr style="margin: 5px 0px;"></td></tr>
<tr><td><img src=include/images/forums.png style="margin-right: 5px;"></td><td><a href="http://www.sugarcrm.com/forums" target="_blank"><b>Forums</b></a><br>Forums dedicated to the Sugar Community to discuss topics of interest with each other and with SugarCRM Developers</td></tr>
</table>',
    'LBL_WIZARD_PERSONALINFO' => 'Your Information',
    'LBL_WIZARD_LOCALE' => 'Your Locale',
    'LBL_WIZARD_SMTP' => 'Your Email Account',
    'LBL_WIZARD_PERSONALINFO_DESC' => 'Provide information about yourself. The information you provide about yourself will be visible to other Sugar users.<br />Fields marked with <span class="required">*</span> are required.',
    'LBL_WIZARD_LOCALE_DESC' => 'Specify your time zone and how you would like dates, currencies and names to appear in Sugar.',
    'LBL_WIZARD_SMTP_DESC' => 'Provide your email account username and password for the default outbound email server.',
	'LBL_EAPM_SUBPANEL_TITLE' => 'External Accounts',
    'LBL_OAUTH_TOKENS' => 'OAuth Tokens',
    'LBL_OAUTH_TOKENS_SUBPANEL_TITLE' => "OAuth Access Tokens",

    //For export labels
    'LBL_MODIFIED_USER_ID' => 'Modified User ID',
    'LBL_PHONE_HOME' => 'Phone Home',
    'LBL_PHONE_MOBILE' => 'Phone Mobile',
    'LBL_PHONE_WORK' => 'Phone Work',
    'LBL_PHONE_OTHER' => 'Phone Other',
    'LBL_PHONE_FAX' => 'Phone Fax',
    'LBL_PORTAL_ONLY' => 'Portal Only',
    'LBL_IS_GROUP' => 'Is Group',
    'LBL_EXPORT_CREATED_BY' => 'Created By ID',
); // END STRINGS DEFS

?>
