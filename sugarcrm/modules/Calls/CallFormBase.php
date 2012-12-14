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
 * $Id: CallFormBase.php 56853 2010-06-08 02:36:54Z clee $
 * Description: Call Form Base
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/SugarObjects/forms/FormBase.php');

class CallFormBase extends FormBase {

    protected $repeatDataArray = array();
    
    protected $recurringCreated = array();

function getFormBody($prefix, $mod='', $formname='',$cal_date='',$cal_time=''){
if(!ACLController::checkAccess('Calls', 'edit', true)){
		return '';
	}
global $mod_strings;
$temp_strings = $mod_strings;
if(!empty($mod)){
	global $current_language;
	$mod_strings = return_module_language($current_language, $mod);
}
global $app_strings;
global $app_list_strings;
global $current_user;
global $theme;


$lbl_subject = $mod_strings['LBL_SUBJECT'];
// Unimplemented until jscalendar language files are fixed
// global $current_language;
// global $default_language;
// global $cal_codes;
// Unimplemented until jscalendar language files are fixed
// $cal_lang = (empty($cal_codes[$current_language])) ? $cal_codes[$default_language] : $cal_codes[$current_language];

global $timedate;
$cal_lang = "en";
$cal_dateformat = $timedate->get_cal_date_format();

$lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
$lbl_date = $mod_strings['LBL_DATE'];
$lbl_time = $mod_strings['LBL_TIME'];
$ntc_date_format = $timedate->get_user_date_format();
$ntc_time_format = '('.$timedate->get_user_time_format().')';

	$user_id = $current_user->id;
$default_status = $app_list_strings['call_status_default'];
$default_parent_type= $app_list_strings['record_type_default_key'];
$date = TimeDate::getInstance()->nowDb();
$default_date_start = $timedate->to_display_date($date,false);
$default_time_start = $timedate->to_display_time($date);
$time_ampm = $timedate->AMPMMenu($prefix,$default_time_start);
$lbl_save_button_title = $app_strings['LBL_SAVE_BUTTON_TITLE'];
$lbl_save_button_key = $app_strings['LBL_SAVE_BUTTON_KEY'];
$lbl_save_button_label = $app_strings['LBL_SAVE_BUTTON_LABEL'];
	$form =	<<<EOQ
			<form name="${formname}" onSubmit="return check_form('${formname}') "method="POST" action="index.php">
			<input type="hidden" name="${prefix}module" value="Calls">
			<input type="hidden" name="${prefix}action" value="Save">
				<input type="hidden" name="${prefix}record" value="">
			<input type="hidden"  name="${prefix}direction" value="Outbound">
			<input type="hidden" name="${prefix}status" value="${default_status}">
			<input type="hidden" name="${prefix}parent_type" value="${default_parent_type}">
			<input type="hidden" name="${prefix}assigned_user_id" value='${user_id}'>
			<input type="hidden" name="${prefix}duration_hours" value="1">
			<input type="hidden" name="${prefix}duration_minutes" value="0">
			<input type="hidden" name="${prefix}user_id" value="${user_id}">

		<table cellspacing="1" cellpadding="0" border="0">
<tr>
    <td colspan="2"><input type='radio' name='appointment' value='Call' class='radio' onchange='document.${formname}.module.value="Calls";' style='vertical-align: middle;' checked> <span scope="row">${mod_strings['LNK_NEW_CALL']}</span>
&nbsp;
&nbsp;
<input type='radio' name='appointment' value='Meeting' class='radio' onchange='document.${formname}.module.value="Meetings";'><span scope="row">${mod_strings['LNK_NEW_MEETING']}</span></td>
</tr>
<tr>
    <td colspan="2"><span scope="row">$lbl_subject</span>&nbsp;<span class="required">$lbl_required_symbol</span></td>
</tr>
<tr><td valign=top><input name='${prefix}name' size='30' maxlength='255' type="text"></td>
    <td><input name='${prefix}date_start' id='${formname}jscal_field' maxlength='10' type="hidden" value="${cal_date}"></td>
    <td><input name='${prefix}time_start' type="hidden" maxlength='10' value="{$cal_time}"></td>

			<script type="text/javascript">
//		Calendar.setup ({
//			inputField : "${formname}jscal_field", daFormat : "$cal_dateformat" ifFormat : "$cal_dateformat", showsTime : false, button : "${formname}jscal_trigger", singleClick : true, step : 1, weekNumbers:false
//		});
		</script>



EOQ;



$javascript = new javascript();
$javascript->setFormName($formname);
$javascript->setSugarBean(new Call());
$javascript->addRequiredFields($prefix);
$form .=$javascript->getScript();
$form .= "<td align=\"left\" valign=top><input title='$lbl_save_button_title' accessKey='$lbl_save_button_key' class='button' type='submit' name='button' value=' $lbl_save_button_label ' ></td></tr></table></form>";
$mod_strings = $temp_strings;
return $form;

}
function getFormHeader($prefix, $mod='', $title=''){
	if(!ACLController::checkAccess('Calls', 'edit', true)){
		return '';
	}
	if(!empty($mod)){
	global $current_language;
	$mod_strings = return_module_language($current_language, $mod);
}else global $mod_strings;






if(!empty($title)){
	$the_form = get_left_form_header($title);
}else{
	$the_form = get_left_form_header($mod_strings['LBL_NEW_FORM_TITLE']);
}
$the_form .= <<<EOQ
		<form name="${prefix}CallSave" onSubmit="return check_form('${prefix}CallSave') "method="POST" action="index.php">
			<input type="hidden" name="${prefix}module" value="Calls">
			<input type="hidden" name="${prefix}action" value="Save">

EOQ;
return $the_form;
}
function getFormFooter($prefic, $mod=''){
	if(!ACLController::checkAccess('Calls', 'edit', true)){
		return '';
	}
global $app_strings;
global $app_list_strings;
$lbl_save_button_title = $app_strings['LBL_SAVE_BUTTON_TITLE'];
$lbl_save_button_label = $app_strings['LBL_SAVE_BUTTON_LABEL'];
$the_form = "	<p><input title='$lbl_save_button_title' class='button' type='submit' name='button' value=' $lbl_save_button_label ' ></p></form>";
$the_form .= get_left_form_footer();
$the_form .= get_validate_record_js();
return $the_form;
}

function getForm($prefix, $mod=''){
	if(!ACLController::checkAccess('Calls', 'edit', true)){
		return '';
	}
$the_form = $this->getFormHeader($prefix, $mod);
$the_form .= $this->getFormBody($prefix, $mod, "${prefix}CallSave");
$the_form .= $this->getFormFooter($prefix, $mod);

return $the_form;
}


function handleSave($prefix,$redirect=true,$useRequired=false) {


	require_once('include/formbase.php');

	global $current_user;
	global $timedate;

	//BUG 17418 MFH
	if (isset($_POST[$prefix.'duration_hours'])){
		$_POST[$prefix.'duration_hours'] = trim($_POST[$prefix.'duration_hours']);
	}

	$focus = new Call();

	if($useRequired && !checkRequired($prefix, array_keys($focus->required_fields))) {
		return null;
	}
    if ( !isset($_POST[$prefix.'reminder_checked']) or ($_POST[$prefix.'reminder_checked'] == 0)) {
        $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): No reminder checked, resetting the reminder_time');
        $_POST[$prefix.'reminder_time'] = -1;
    }

	if(!isset($_POST[$prefix.'reminder_time'])) {
        $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Getting the users default reminder time');
		$_POST[$prefix.'reminder_time'] = $current_user->getPreference('reminder_time');
	}

	if(!isset($_POST['email_reminder_checked']) || (isset($_POST['email_reminder_checked']) && $_POST['email_reminder_checked'] == '0')) {
		$_POST['email_reminder_time'] = -1;
	}
	if(!isset($_POST['email_reminder_time'])){
		$_POST['email_reminder_time'] = $current_user->getPreference('email_reminder_time');
		$_POST['email_reminder_checked'] = 1;
	}

	// don't allow to set recurring_source from a form
	unset($_POST['recurring_source']);

	$time_format = $timedate->get_user_time_format();
    $time_separator = ":";
    if(preg_match('/\d+([^\d])\d+([^\d]*)/s', $time_format, $match)) {
       $time_separator = $match[1];
    }

	if(!empty($_POST[$prefix.'time_hour_start']) && empty($_POST[$prefix.'time_start'])) {
		$_POST[$prefix.'time_start'] = $_POST[$prefix.'time_hour_start']. $time_separator .$_POST[$prefix.'time_minute_start'];
	}

	if(isset($_POST[$prefix.'meridiem']) && !empty($_POST[$prefix.'meridiem'])) {
		$_POST[$prefix.'time_start'] = $timedate->merge_time_meridiem($_POST[$prefix.'time_start'],$timedate->get_time_format(), $_POST[$prefix.'meridiem']);
	}

	if(isset($_POST[$prefix.'time_start']) && strlen($_POST[$prefix.'date_start']) == 10) {
	   $_POST[$prefix.'date_start'] = $_POST[$prefix.'date_start'] . ' ' . $_POST[$prefix.'time_start'];
	}

	// retrieve happens here
	$focus = populateFromPost($prefix, $focus);
	if(!$focus->ACLAccess('Save')) {
	   ACLController::displayNoAccess(true);
	   sugar_cleanup(true);
	}

	//add assigned user and current user if this is the first time bean is saved
  	if(empty($focus->id) && !empty($_REQUEST['return_module']) && $_REQUEST['return_module'] =='Calls' && !empty($_REQUEST['return_action']) && $_REQUEST['return_action'] =='DetailView'){
		//if return action is set to detail view and return module to call, then this is from the long form, do not add the assigned user (only the current user)
		//The current user is already added to UI and we want to give the current user the option of opting out of meeting.
  		if($current_user->id != $_POST['assigned_user_id']){
  			$_POST['user_invitees'] .= ','.$_POST['assigned_user_id'].', ';
  			$_POST['user_invitees'] = str_replace(',,', ',', $_POST['user_invitees']);
  		}
  	}else {
	  	//this is not from long form so add assigned and current user automatically as there is no invitee list UI.
	  	//This call could be through an ajax call from subpanels or shortcut bar
	  	$_POST['user_invitees'] .= ','.$_POST['assigned_user_id'].', ';

	  	//add current user if the assigned to user is different than current user.
	  	if($current_user->id != $_POST['assigned_user_id'] && $_REQUEST['module'] != "Calendar"){
	  		$_POST['user_invitees'] .= ','.$current_user->id.', ';
	  	}

	  	//remove any double commas introduced during appending
	    $_POST['user_invitees'] = str_replace(',,', ',', $_POST['user_invitees']);
  	}

    if( (isset($_POST['isSaveFromDetailView']) && $_POST['isSaveFromDetailView'] == 'true') ||
        (isset($_POST['is_ajax_call']) && !empty($_POST['is_ajax_call']) && !empty($focus->id) ||
        (isset($_POST['return_action']) && $_POST['return_action'] == 'SubPanelViewer') && !empty($focus->id))
    ){
        $focus->save(true);
        $return_id = $focus->id;
    }else{

        if($focus->status == 'Held' && $this->isEmptyReturnModuleAndAction() && !$this->isSaveFromDCMenu()){
    		//if we are closing the meeting, and the request does not have a return module AND return action set and it is not a save
            //being triggered by the DCMenu (shortcut bar) then the request is coming from a dashlet or subpanel close icon and there is no
            //need to process user invitees, just save the current values.
    		$focus->save(true);
	    }else{
            $userInvitees = array();
            $contactInvitees = array();
            $leadInvitees = array();
           
            $existingUsers = array();
            $existingContacts = array();
            $existingLeads =  array();
            
            if (!empty($_POST['user_invitees'])) {
               $userInvitees = explode(',', trim($_POST['user_invitees'], ','));
            }
            if (!empty($_POST['existing_invitees'])) {
               $existingUsers =  explode(",", trim($_POST['existing_invitees'], ','));
            }
           
            if (!empty($_POST['contact_invitees'])) {
               $contactInvitees = explode(',', trim($_POST['contact_invitees'], ','));
            }
            if (!empty($_POST['existing_contact_invitees'])) {
                $existingContacts =  explode(",", trim($_POST['existing_contact_invitees'], ','));
            }     
	        if (!empty($_POST['parent_id']) && $_POST['parent_type'] == 'Contacts') {
                $contactInvitees[] = $_POST['parent_id'];
            }               
            if (!empty($_REQUEST['relate_to']) && $_REQUEST['relate_to'] == 'Contacts') {
                if (!empty($_REQUEST['relate_id']) && !in_array($_REQUEST['relate_id'], $contactInvitees)) {
                    $contactInvitees[] = $_REQUEST['relate_id'];
                } 
            }
            
            //BEGIN SUGARCRM flav!=sales ONLY            
            if (!empty($_POST['lead_invitees'])) {
                $leadInvitees = explode(',', trim($_POST['lead_invitees'], ','));
            }            
            if (!empty($_POST['existing_lead_invitees'])) {
                $existingLeads =  explode(",", trim($_POST['existing_lead_invitees'], ','));
            }
	        if (!empty($_POST['parent_id']) && $_POST['parent_type'] == 'Leads') {
                $leadInvitees[] = $_POST['parent_id'];
            }            
            if (!empty($_REQUEST['relate_to']) && $_REQUEST['relate_to'] == 'Leads') {
                if (!empty($_REQUEST['relate_id']) && !in_array($_REQUEST['relate_id'], $leadInvitees)) {
                    $leadInvitees[] = $_REQUEST['relate_id'];
                } 
            }
            //END SUGARCRM flav!=sales ONLY

            // Call the Call module's save function to handle saving other fields besides
            // the users and contacts relationships
            $focus->update_vcal = false;    // Bug #49195 : don't update vcal b/s related users aren't saved yet, create vcal cache below
            
            $focus->users_arr = $userInvitees;
            $focus->contacts_arr = $contactInvitees;
            $focus->leads_arr = $leadInvitees;
            
            $focus->save(true);
            $return_id = $focus->id;
            
            $focus->setUserInvitees($focus->users_arr, $existingUsers);
            $focus->setContactInvitees($focus->contacts_arr, $existingContacts);
            //BEGIN SUGARCRM flav!=sales ONLY
            $focus->setLeadInvitees($focus->leads_arr, $existingLeads);
            //END SUGARCRM flav!=sales ONLY

            // Bug #49195 : update vcal
            vCal::cache_sugar_vcal($current_user);
            
            // CCL - Comment out call to set $current_user as invitee
            // set organizer to auto-accept
            //$focus->set_accept_status($current_user, 'accept');
            
            $this->processRecurring($focus);
	    }
    }
	if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Home'){
		$_REQUEST['return_action'] = 'index';
        handleRedirect('', 'Home');
	}
	else if($redirect) {
		handleRedirect($return_id, 'Calls');
	} else {
		return $focus;
	}

} // end handleSave();

function getWideFormBody ($prefix, $mod='', $formname='', $wide =true){
	if(!ACLController::checkAccess('Calls', 'edit', true)){
		return '';
	}
global $mod_strings;
$temp_strings = $mod_strings;
if(!empty($mod)){
	global $current_language;
	$mod_strings = return_module_language($current_language, $mod);
}
global $app_strings;
global $app_list_strings;
global $current_user;
global $theme;

$lbl_subject = $mod_strings['LBL_SUBJECT'];
// Unimplemented until jscalendar language files are fixed
// global $current_language;
// global $default_language;
// global $cal_codes;
// Unimplemented until jscalendar language files are fixed
// $cal_lang = (empty($cal_codes[$current_language])) ? $cal_codes[$default_language] : $cal_codes[$current_language];
$cal_lang = "en";


$lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
	$lbl_date = $mod_strings['LBL_DATE'];
$lbl_time = $mod_strings['LBL_TIME'];
global $timedate;
$ntc_date_format = '('.$timedate->get_user_date_format(). ')';
$ntc_time_format = '('.$timedate->get_user_time_format(). ')';
$cal_dateformat = $timedate->get_cal_date_format();

	$user_id = $current_user->id;
$default_status = $app_list_strings['call_status_default'];
$default_parent_type= $app_list_strings['record_type_default_key'];
$date = TimeDate::getInstance()->nowDb();
$default_date_start = $timedate->to_display_date($date);
$default_time_start = $timedate->to_display_time($date,true);
$time_ampm = $timedate->AMPMMenu($prefix,$default_time_start);
	$form =	<<<EOQ
			<input type="hidden"  name="${prefix}direction" value="Outbound">
			<input type="hidden" name="${prefix}record" value="">
			<input type="hidden" name="${prefix}status" value="${default_status}">
			<input type="hidden" name="${prefix}parent_type" value="${default_parent_type}">
			<input type="hidden" name="${prefix}assigned_user_id" value='${user_id}'>
			<input type="hidden" name="${prefix}duration_hours" value="1">
			<input type="hidden" name="${prefix}duration_minutes" value="0">
			<input type="hidden" name="${prefix}user_id" value="${user_id}">

		<table cellspacing='0' cellpadding='0' border='0' width="100%">
<tr>
EOQ;

if($wide){
$form .= <<<EOQ
<td scope='row' width="20%"><input type='radio' name='appointment' value='Call' class='radio' checked> ${mod_strings['LNK_NEW_CALL']}</td>
<td scope='row' width="80%">${mod_strings['LBL_DESCRIPTION']}</td>
</tr>

<tr>
<td scope='row'><input type='radio' name='appointment' value='Meeting' class='radio'> ${mod_strings['LNK_NEW_MEETING']}</td>

<td rowspan='8' ><textarea name='Appointmentsdescription' cols='50' rows='5'></textarea></td>
</tr>
EOQ;
}else{
		$form .= <<<EOQ
<td scope='row' width="20%"><input type='radio' name='appointment' value='Call' class='radio' onchange='document.$formname.module.value="Calls";' checked> ${mod_strings['LNK_NEW_CALL']}</td>
</tr>

<tr>
<td scope='row'><input type='radio' name='appointment' value='Meeting' class='radio' onchange='document.$formname.module.value="Meetings";'> ${mod_strings['LNK_NEW_MEETING']}</td>
</tr>
EOQ;
}
$jscalenderImage = SugarThemeRegistry::current()->getImageURL('jscalendar.gif');
$form .=	<<<EOQ


<tr>
<td scope='row'>$lbl_subject&nbsp;<span class="required">$lbl_required_symbol</span></td>
</tr>

<tr>
<td ><input name='${prefix}name' maxlength='255' type="text"></td>
</tr>

<tr>
<td scope='row'>$lbl_date&nbsp;<span class="required">$lbl_required_symbol</span>&nbsp;<span class="dateFormat">$ntc_date_format</span></td>
</tr>
<tr>
<td ><input onblur="parseDate(this, '$cal_dateformat');" name='${prefix}date_start' size="12" id='${prefix}jscal_field' maxlength='10' type="text" value="${default_date_start}"> <!--not_in_theme!--><img src="{$jscalenderImage}" alt="{$app_strings['LBL_ENTER_DATE']}"  id="${prefix}jscal_trigger" align="absmiddle"></td>
</tr>

<tr>
<td scope='row'>$lbl_time&nbsp;<span class="required">$lbl_required_symbol</span>&nbsp;<span class="dateFormat">$ntc_time_format</span></td>
</tr>
<tr>
<td ><input name='${prefix}time_start' size="12" type="text" maxlength='5' value="{$default_time_start}">$time_ampm</td>
</tr>

</table>

		<script type="text/javascript">
		Calendar.setup ({
			inputField : "${prefix}jscal_field", daFormat : "$cal_dateformat", ifFormat : "$cal_dateformat", showsTime : false, button : "${prefix}jscal_trigger", singleClick : true, step : 1, weekNumbers:false
		});
		</script>
EOQ;


$javascript = new javascript();
$javascript->setFormName($formname);
$javascript->setSugarBean(new Call());
$javascript->addRequiredFields($prefix);
$form .=$javascript->getScript();
$mod_strings = $temp_strings;
return $form;

}

    /**
     * Saves recurring records if needed. Flushes existing recurrences if needed.
     */
    protected function processRecurring(Call $focus)
    {
            require_once "modules/Calendar/CalendarUtils.php";            
            if (!empty($_REQUEST['edit_all_recurrences'])) {
                // flush existing recurrence
                CalendarUtils::markRepeatDeleted($focus);
            }            
            if (count($this->repeatDataArray) > 0) {
                // prevent sending invites for recurring activities
                unset($_REQUEST['send_invites'], $_POST['send_invites']);
                $this->recurringCreated = CalendarUtils::saveRecurring($focus, $this->repeatDataArray);
            }
    }

    /**
     * Prepare recurring sequence if needed.
     * @return bool true if recurring records need to be created
     */
    public function prepareRecurring()
    {       
        require_once "modules/Calendar/CalendarUtils.php";
        
        if (empty($_REQUEST['edit_all_recurrences'])) {        
            $repeatFields = array('type', 'interval', 'count', 'until', 'dow', 'parent_id');
            foreach ($repeatFields as $param) {
                unset($_POST['repeat_' . $param]);
            }           
        } else if (!empty($_REQUEST['repeat_type']) && !empty($_REQUEST['date_start'])) {        
            $params = array(
                    'type' => $_REQUEST['repeat_type'],
                    'interval' => $_REQUEST['repeat_interval'],
                    'count' => $_REQUEST['repeat_count'],    
                    'until' => $_REQUEST['repeat_until'],    
                    'dow' => $_REQUEST['repeat_dow'],            
            );                            
            $this->repeatDataArray = CalendarUtils::buildRecurringSequence($_REQUEST['date_start'], $params);
            return true;
        }
        return false;
    }
    
    /**
     * Check if amount of recurring records is exceeding the limit. 
     * @return bool/int Limit if exceeded or fase if not exceeded.
     */
    public function checkRecurringLimitExceeded()
    {
        $limit = SugarConfig::getInstance()->get('calendar.max_repeat_count', 1000);            
        if (count($this->repeatDataArray) > ($limit - 1)) {
            return $limit;
        }
        return false;
    }
    
    /**
     * Returns list of created recurrings records. Id and date start. 
     * @return array
     */
    public function getRecurringCreated()
    {
        return $this->recurringCreated;
    }
}
?>
