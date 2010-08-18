{if false}
/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: uw_main.tpl 49064 2009-06-26 14:13:22Z jmertic $
{/if}

<script type="text/javascript" language="Javascript" src="modules/UpgradeWizard/upgradeWizard.js"></script>

{$UW_JS}

<div id="title">
{$UW_TITLE}
</div>

<div id="progress" style="display:none;">
{$UW_PROGRESS}
</div>

<div id="message" style="display:none;">
{$UW_MESSAGE}
</div>

<div id="nav">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
<form action="index.php" method="post" name="UpgradeWizardForm" id="form">
	<input type="hidden" name="module" value="UpgradeWizard">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="step" value="{$UW_STEP}">
	<input type="hidden" name="overwrite_files" id="over">
	<input type="hidden" name="schema_change" id="schema">
	<input type="hidden" name="schema_drop"   id="schema_drop">
	<input type="hidden" name="overwrite_files_serial" id="overwrite_files_serial">
	<input type="hidden" name="addTaskReminder" id="addTaskReminder">
	<input type="hidden" name="addEmailReminder" id="addEmailReminder">
    {if !isset($includeContainerCSS) || $includeContainerCSS}
    <link rel='stylesheet' type='text/css' href='include/javascript/yui/assets/container.css' />
        {if $step == 'commit'}
    <link rel='stylesheet' type='text/css' href='include/javascript/yui/build/container/assets/container.css'/>
    <link rel='stylesheet' type='text/css' href='themes/default/css/yui.css'/>
       {/if}
    {/if}
		{if $showBack}
			<input	title		= "{$MOD.LBL_BUTTON_BACK}"
					class		= "button"
					onclick		= "this.form.step.value='{$STEP_BACK}';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_BACK}  ">
		{/if}
		{if $showNext}
			<input	title		= "{$MOD.LBL_BUTTON_NEXT}"
					class		= "button"
					{$disableNextForLicense}
 					onclick	= " handleUploadCheck('{$step}', {$u_allow}); if(!{$u_allow}) return; upgradeP('{$step}');this.form.step.value='{$STEP_NEXT}'; handlePreflight('{$step}'); document.getElementById('form').submit();"
					type		= "button"
					value		= "  {$MOD.LBL_BUTTON_NEXT}  "
					id			= "next_button" >
		{/if}
		{if $showCancel}
			<input	title		= "{$MOD.LBL_BUTTON_CANCEL}"
					class		= "button"
					onclick		= "cancelUpgrade();this.form.step.value='{$STEP_CANCEL}';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_CANCEL}  ">
		{/if}
		{if $showRecheck}
			<input	title		= "{$MOD.LBL_BUTTON_RECHECK}"
					class		= "button"
					onclick		= "this.form.step.value='{$STEP_RECHECK}';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_RECHECK}  ">
		{/if}
		{if $showDone}
			<input	title		= "{$MOD.LBL_BUTTON_DONE}"
					class		= "button"
					onclick		= "deleteCacheAjax();window.location.href='index.php?module=Home&action=About';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_DONE}  ">
		{/if}

</form>
		</td>
	</tr>
</table>
</div>
<br />
<div id="main">
<table width="100%" border="0" cellpadding="0" cellpadding="0" 
    class="{if !isset($includeContainerCSS) || $includeContainerCSS}tabDetailView{else}detail view small{/if}">
{if $frozen}
	<tr>
		<td id=error_messages colspan="2">
			<span class="error"><b>{$frozen}</b></span>
		</td>
	</tr>
{/if}
{if $upload_success}
	<tr>
		<td colspan="2">
			<span class="error"><b>{$upload_success}</b></span>
		</td>
	</tr>
{/if}

	<tr>
		<td width="25%" rowspan="2" {if !isset($includeContainerCSS) || $includeContainerCSS}class="tabDetailViewDL"{else}scope="row"{/if}><slot>
			{$CHECKLIST}
		</slot></td>
		<td width="75%" {if !isset($includeContainerCSS) || $includeContainerCSS}class="tabDetailViewDF"{/if}><slot>
			{$UW_MAIN}&nbsp;
		</slot></td>
	</tr>
{if $step == "upload"}
	<tr>
		<td valign="top" {if !isset($includeContainerCSS) || $includeContainerCSS}class="tabDetailViewDF"{/if}>
			&nbsp;<br />
			{$UW_HISTORY}
		</td>
	</tr>
{/if}
</table>
</div>
<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
<form action="index.php" method="post" name="UpgradeWizardForm" id="form">
	<input type="hidden" name="module" value="UpgradeWizard">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="step" value="{$UW_STEP}">
	<input type="hidden" name="overwrite_files" id="over">
	<input type="hidden" name="schema_change" id="schema">
	<input type="hidden" name="schema_drop"   id="schema_drop">
	<input type="hidden" name="overwrite_files_serial" id="overwrite_files_serial">
	<input type="hidden" name="addTaskReminder" id="addTaskReminder">
	<input type="hidden" name="addEmailReminder" id="addEmailReminder">
    {if !isset($includeContainerCSS) || $includeContainerCSS}
    <link rel='stylesheet' type='text/css' href='include/javascript/yui/assets/container.css' />
        {if $step == 'commit'}
    <link rel='stylesheet' type='text/css' href='include/javascript/yui/build/container/assets/container.css'/>
    <link rel='stylesheet' type='text/css' href='themes/default/css/yui.css'/>
       {/if}
    {/if}
		{if $showBack}
			<input	title		= "{$MOD.LBL_BUTTON_BACK}"
					class		= "button"
					onclick		= "this.form.step.value='{$STEP_BACK}';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_BACK}  ">
		{/if}
		{if $showNext}
			<input	title		= "{$MOD.LBL_BUTTON_NEXT}"
					class		= "button"
					{$disableNextForLicense}
 					onclick	= " handleUploadCheck('{$step}', {$u_allow}); if(!{$u_allow}) return; upgradeP('{$step}');this.form.step.value='{$STEP_NEXT}'; handlePreflight('{$step}'); document.getElementById('form').submit();"
					type		= "button"
					value		= "  {$MOD.LBL_BUTTON_NEXT}  "
					id			= "next_button" >
		{/if}
		{if $showCancel}
			<input	title		= "{$MOD.LBL_BUTTON_CANCEL}"
					class		= "button"
					onclick		= "cancelUpgrade();this.form.step.value='{$STEP_CANCEL}';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_CANCEL}  ">
		{/if}
		{if $showRecheck}
			<input	title		= "{$MOD.LBL_BUTTON_RECHECK}"
					class		= "button"
					onclick		= "this.form.step.value='{$STEP_RECHECK}';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_RECHECK}  ">
		{/if}
		{if $showDone}
			<input	title		= "{$MOD.LBL_BUTTON_DONE}"
					class		= "button"
					onclick		= "deleteCacheAjax();window.location.href='index.php?module=Home&action=About';"
					type		= "submit"
					value		= "  {$MOD.LBL_BUTTON_DONE}  ">
		{/if}

</form>
		</td>
	</tr>
</table>


<script>

UPGRADE_PROGRESS = '{$MOD.LBL_UPGRADE_IN_PROGRESS}';
TIME_ELAPSED = "{$MOD.LBL_UPGRADE_TIME_ELAPSED}";
START_IN_PROGRESS = "{$MOD.LBL_START_UPGRADE_IN_PROGRESS}";
SYSTEM_CHECK_IN_PROGRESS = "{$MOD.LBL_SYSTEM_CHECKS_IN_PROGRESS}";
LICENSE_CHECK_IN_PROGRESS = "{$MOD.LBL_LICENSE_CHECK_IN_PROGRESS}";
PREFLIGHT_CHECK_IN_PROGRESS ="{$MOD.LBL_PREFLIGHT_CHECK_IN_PROGRESS}";
COMMIT_UPGRADE_IN_PROGRESS ="{$MOD.LBL_COMMIT_UPGRADE_IN_PROGRESS}";
UPGRADE_SUMMARY_IN_PROGRESS ="{$MOD.LBL_UPGRADE_SUMMARY_IN_PROGRESS}";
SET_STEP_TO_COMPLETE = "{$MOD.LBL_UW_COMPLETE}";
UPLOADE_UPGRADE_IN_PROGRESS= "{$MOD.LBL_UPLOADE_UPGRADE_IN_PROGRESS}";
UPLOADING_UPGRADE_PACKAGE = "{$MOD.LBL_UPLOADING_UPGRADE_PACKAGE}";
UPGRADE_CANCEL_IN_PROGRESS ="{$MOD.LBL_UPGRADE_CANCEL_IN_PROGRESS}";
{literal}
var msgPanel;
var c=0
var s=0
var t
var currStage
var timeOutWindowMultiplier = 1
var timeOutWindow = 60
function upgradeP(step){
if(step == 'systemCheck'){
	return;
}

if(document.getElementById("upgradeDiv") != null){
	    var args = {    width:"300px",
	                    modal:true,
	                    //xy:[400,300],
	                    fixedcenter: true,
	                    constraintoviewport: false,
	                    underlay:"shadow",
	                    close:false,
	                    draggable:true,
	                    effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:.5}
	                   } ;
	            msg_panel = new YAHOO.widget.Panel('p_msg', args);
	            //If we haven't built our panel using existing markup,
	            //we can set its content via script:

				if(step == 'start'){
                	//currStage = START_IN_PROGRESS;
                	currStage = SYSTEM_CHECK_IN_PROGRESS;
                }
                /* removed window from system check. if you need to add back, remove check at the top
                 * of this function as well
                if(step == 'systemCheck'){
                	currStage = UPLOADE_UPGRADE_IN_PROGRESS;
                	//document.getElementById(step).innerHTML='<i>'+SET_STEP_TO_COMPLETE+'</i>'
                }
                */
                if(step == 'uploadingUpgardePackage'){
                	currStage = UPLOADING_UPGRADE_PACKAGE;
                }
                if(step == 'license_fiveO'){
                	//currStage = LICENSE_CHECK_IN_PROGRESS;
                	currStage = PREFLIGHT_CHECK_IN_PROGRESS;
                	//document.getElementById(step).innerHTML='<i>'+SET_STEP_TO_COMPLETE+'</i>'
                }
                if(step == 'upload'){
                	//currStage = LICENSE_CHECK_IN_PROGRESS;
                	currStage = PREFLIGHT_CHECK_IN_PROGRESS;
                	//document.getElementById(step).innerHTML='<i>'+SET_STEP_TO_COMPLETE+'</i>'
                }
                if(step == 'preflight'){
                	//currStage = PREFLIGHT_CHECK_IN_PROGRESS;
                	currStage = COMMIT_UPGRADE_IN_PROGRESS;
                	//document.getElementById(step).innerHTML='<i>'+SET_STEP_TO_COMPLETE+'</i>'
                }
                if(step == 'commit'){
                	currStage = UPGRADE_SUMMARY_IN_PROGRESS;
                	//document.getElementById(step).innerHTML='<i>'+SET_STEP_TO_COMPLETE+'</i>'
                }
                if(step == 'layouts'){
                	currStage = UPGRADE_SUMMARY_IN_PROGRESS;
                	//document.getElementById(step).innerHTML='<i>'+SET_STEP_TO_COMPLETE+'</i>'
                }
	            msg_panel.setHeader(currStage);
	            msg_panel.setBody(document.getElementById("upgradeDiv").innerHTML);
	            //timedCount(currStage);
	            //msg_panel.setFooter('Time Elapsed '+s);
	            msg_panel.render(document.body);
	            msgPanel = msg_panel;
	    		msgPanel.show;
    }
    return;
}

function cancelUpgrade(){
if(document.getElementById("upgradeDiv") != null){
	    var args = {    width:"300px",
	                    modal:true,
	                    //xy:[400,300],
	                    fixedcenter: true,
	                    constraintoviewport: false,
	                    underlay:"shadow",
	                    close:false,
	                    draggable:true,
	                    effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:.5}
	                   } ;
	            msg_panel = new YAHOO.widget.Panel('p_msg', args);
	            //If we haven't built our panel using existing markup,
	            //we can set its content via script:

                currStage = UPGRADE_CANCEL_IN_PROGRESS;
                //document.getElementById(step).innerHTML='<i>'+SET_STEP_TO_COMPLETE+'</i>'
	            msg_panel.setHeader(currStage);
	            msg_panel.setBody(document.getElementById("upgradeDiv").innerHTML);
	            //timedCount(currStage);
	            //msg_panel.setFooter('Time Elapsed '+s);
	            msg_panel.render(document.body);
	            msgPanel = msg_panel;
	    		msgPanel.show;
    }
    return;
}

function timedCount(currStage)
{
      msg_panel.setFooter(TIME_ELAPSED+'   '+s);
      currStage = currStage+'   '+s;
      msg_panel.setHeader(currStage);
    	c=c+1
		s=c

		timeOutWindowMultiples = timeOutWindowMultiplier*timeOutWindow
		if(c == timeOutWindowMultiples){
		  updateUpgradeStepTime(timeOutWindow)
		  timeOutWindowMultiplier = timeOutWindowMultiplier+1
		}

		if(c<10){
		 	s='0'+c
		}

	  if(c>=60 && c<3600){
			 m=1
			 while(c>=((m+1)*60)){
			    m=m+1
			  }
			 secs= (c-(m*60))
			 if(m < 10){
			     m = '0'+m
			  }
			  if(secs < 10){
			     secs = '0'+secs
			  }
			  s=m+':'+ secs
		 }
		 if(c>=3600){
			  h=1;
			  while(c>=((h+1)*3600)){
			    h=h+1;
			   }
			  r= c-(h*3600)
			  m = 0
			  secs = 0
			  if(r>=60){
				 m=1;
				  while(r>=((m+1)*60)){
				     m=m+1;
				  }
				  secs =  (r-(m*60))
			    }
			    if(h < 10){
			       h = '0'+h
			     }
			     if(m < 10){
			       m = '0'+m
			     }
			     if(secs <10){
				     secs = '0'+ secs
				  }
			  s=h+':'+m+':'+ secs
		   }
		t=setTimeout("timedCount(currStage)",1000)
}
function updateUpgradeStepTime(ts){
  success = function(r) {
    	//making ajax call every three minutes to make sure the browser
    	//remains active
    }
   postData = 'upgradeStepTime=' + ts + '&module=UpgradeWizard&action=upgradeTimeCounter&to_pdf=1&sugar_body_only=1';
   var ajxProgress = YAHOO.util.Connect.asyncRequest('POST','index.php', {success: success, failure: success}, postData);
}
</script>
{/literal}


