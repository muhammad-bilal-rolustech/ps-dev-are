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
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
//FILE SUGARCRM flav=ent ONLY
require_once('soap/SoapError.php');
require_once('soap/SoapHelperFunctions.php');

require_once('include/entryPoint.php');

require_once('include/nusoap/nusoap.php');






$user_name ='';
$user_password = '';
$quick_test = false;

foreach($_POST as $name=>$value){
		$$name = $value;
}
if(!isset($sugar_soap_path)){
	$sugar_soap_path =  'http://localhost/original/soap.php';
}

echo <<<EOQ
<form name='test' method='POST'>
<table width ='800'><tr>
<tr><th colspan='6'>Enter  SugarCRM  User Information - this is the same info entered when logging into sugarcrm</th></tr>
<td >USER NAME:</td><td><input type='text' name='user_name' value='$user_name'></td><td>USER PASSWORD:</td><td><input type='password' name='user_password' value='$user_password'></td>
</tr>
<tr>
<td>CONTACT NAME:</td><td><input type='text' name='contact_name' value='$contact_name'></td>
</tr>
<tr>
<td>URL To SOAP File:</td><td><input type='text' name='sugar_soap_path' value='$sugar_soap_path' size=50></td>
</tr>
<tr><td><input type='submit' value='Submit'></td></tr>
</table>
</form>


EOQ;
if(!empty($user_name)){
$offset = 0;
if(isset($_REQUEST['offset'])){
	$offset = $_REQUEST['offset'] + 20;
	echo $offset;
}
function print_result($result){
global $soapclient;
if(!empty($soapclient->error_str)){
	echo '<b>HERE IS ERRORS:</b><BR>';
	echo $soapclient->error_str;

	echo '<BR><BR><b>HERE IS RESPONSE:</b><BR>';
	echo $soapclient->response;


}

echo '<BR><BR><b>HERE IS RESULT:</b><BR>';
print_r($result);
echo '<br>';
}


require_once('include/nusoap/nusoap.php');  //must also have the nusoap code on the ClientSide.
$soapclient = new nusoapclient($sugar_soap_path);  //define the SOAP Client an

//ignore notices
error_reporting(E_ALL ^ E_NOTICE);

// check for old config format.
if(empty($sugar_config) && isset($dbconfig['db_host_name']))
{
   make_sugar_config($sugar_config);
}

// Administration include


global $HTTP_RAW_POST_DATA;

$administrator = new Administration();
$administrator->retrieveSettings();


echo '<b>Test (ECHO): - test test</b><BR>';
$result = $soapclient->call('test',array('string'=>'hello sugar'));
print_result($result);


echo '<b>LOGIN: -portal_login test</b><BR>';
$result = $soapclient->call('portal_login',array('user_auth'=>array('user_name'=>$user_name,'password'=>md5($user_password), 'version'=>'.01'),'user_name'=>$contact_name, 'application_name'=>'SoapTest'));
print_result($result);
$session = $result['id'];

echo '<b>Get Portal Contact Id: - portal_get_sugar_id test</b><BR>';
$result = $soapclient->call('portal_get_sugar_id',array('session'=>$session));
print_result($result);

echo '<br><br><b>Get cases - portal_get_entry test:</b><BR>';
$timestart = microtime(true);
$result = $soapclient->call('portal_get_entry_list',array('session'=>$session,'module_name'=>'Cases','where'=>'', 'order_by'=>'','offset'=>0,'select_fields'=>array('name', 'date_modified', 'description')));
$diff = microtime(true) - $timestart;
echo "<b>Time for retrieving Cases is $diff </b> <br><br>";
print_result($result);

echo '<br><br><b>Set a case - portal_set_entry test:</b><BR>';
$time = date($GLOBALS['timedate']->get_db_date_time_format()) ;
$date = date($GLOBALS['timedate']->dbDayFormat, time() + rand(0,360000));
$hour = date($GLOBALS['timedate']->dbTimeFormat, time() + rand(0,360000));
$result = $soapclient->call('portal_set_entry',array('session'=>$session,'module_name'=>'Cases', 'name_value_list'=>array(array('name'=>'name' , 'value'=>"$time Case $i"), array('name'=>'description' , 'value'=>'Created through soap'))));
print_result($result);
$case_id = $result['id'];
echo "<br><br><b>Get a case : -test portal_get_entry</b><BR>";
$timestart = microtime(true);
$result = $soapclient->call('portal_get_entry',array('session'=>$session,'module_name'=>'Cases','id'=>$result['id'],'select_fields'=>array('name', 'date_modified','description')));
$diff = microtime(true) - $timestart;
echo "<b>Time for retrieving a Case is $diff </b> <br><br>";
print_result($result);

echo '<br><br><b>Set a note - portal_set_entry test:</b><BR>';
$time = date($GLOBALS['timedate']->get_db_date_time_format()) ;
$date = date($GLOBALS['timedate']->dbDayFormat, time() + rand(0,360000));
$hour = date($GLOBALS['timedate']->dbTimeFormat, time() + rand(0,360000));
$result = $soapclient->call('portal_set_entry',array('session'=>$session,'module_name'=>'Notes', 'name_value_list'=>array(array('name'=>'name' , 'value'=>"$time Note $i"), array('name'=>'description' , 'value'=>'Created through soap'))));
print_result($result);
$note_id = $result['id'];

echo '<br><br><b>Attach a file to the note - portal_relate_note_to_module test:</b><BR>';
$file = base64_encode('this would be the contents of your file');
$result = $soapclient->call('portal_set_note_attachment',array('session'=>$session, 'note_attachment'=>array('id'=>$note_id, 'filename'=>'an_attached_file.txt', 'file'=>$file)));
print_result($result);

echo '<br><br><b>Attach a note - portal_relate_note_to_module test:</b><BR>';
$result = $soapclient->call('portal_relate_note_to_module',array('session'=>$session,'note_id'=>$note_id,'module_name'=>'Cases', 'module_id'=>$case_id));
print_result($result);

echo '<br><br><b>Get Notes Related to a Case- portal_get_related_notes test:</b><BR>';
$result = $soapclient->call('portal_get_related_notes',array('session'=>$session , 'module_name'=>'Cases', 'module_id'=>$case_id, 'select_fields'=>array('name', 'description', 'filename')));
print_result($result);
if(isset($result['entry_list'][0]['id'])){
	$note_id = $result['entry_list'][0]['id'];

	echo '<br><br><b>Get attachment to a note - portal_get_note_attachment test:</b><BR>';
	$result = $soapclient->call('portal_get_note_attachment',array('session'=>$session , 'id'=>$note_id));
	print_result($result);
	print_r($result);
	echo '<br>It Reads:' . base64_decode($result['note_attachment']['file']);
}else{
	echo '<br> Note attaching failed<br>';
}
echo '<br><br><b>logout - portal_logout test:</b><BR>';
$result = $soapclient->call('portal_logout',array('session'=>$session));
print_result($result);

echo '<br><br>Now test entry_lists<br>';
echo '<br><br><b>LOGIN: -portal_login test</b><BR>';
$result = $soapclient->call('portal_login',array('user_auth'=>array('user_name'=>$user_name,'password'=>md5($user_password), 'version'=>'.01'),'user_name'=>$contact_name, 'application_name'=>'SoapTest'));

$session = $result['id'];

echo '<b>Get Portal Contact Id: - portal_get_portal_id test</b><BR>';
$result = $soapclient->call('portal_get_sugar_id',array('session'=>$session));
print_result($result);

echo '<br><br><b>Get cases - portal_get_entry_list test:</b><BR>';
$timestart = microtime(true);
$result = $soapclient->call('portal_get_entry_list',array('session'=>$session,'module_name'=>'Cases','where'=>'', 'order_by'=>'','offset'=>0,'select_fields'=>array('name', 'date_modified', 'description')));
$diff = microtime(true) - $timestart;
echo "<b>Time for retrieving Cases is $diff </b> <br><br>";
print_result($result);

echo '<br><br><b>logout - portal_logout test:</b><BR>';
$result = $soapclient->call('portal_logout',array('session'=>$session));
print_result($result);

}



?>
