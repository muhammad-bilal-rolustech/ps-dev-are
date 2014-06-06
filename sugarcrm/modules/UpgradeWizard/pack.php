<?php
/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2014 SugarCRM Inc. All rights reserved.
 */
if(empty($argv[0]) || basename($argv[0]) != basename(__FILE__)) return;

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die("This is command-line only script");
}

if(empty($argv[1])) {
	die("Use pack.php name.zip");
}

$name = $argv[1];

chdir(dirname(__FILE__)."/../..");
$files=array(
	"UpgradeWizard.php",
	"modules/UpgradeWizard/UpgradeDriver.php",
	"modules/UpgradeWizard/WebUpgrader.php",
	"modules/UpgradeWizard/upgrade_screen.php",
	"modules/UpgradeWizard/upgrader_version.json",
	"include/javascript/jquery/jquery-min.js",
	"sidecar/lib/jquery/jquery.iframe.transport.js",
);

$manifest = array(
	'acceptable_sugar_versions' =>
	array (
        'regex_matches' => array('6\.[5-7]\.*','7\.[012]\.*')
	),
	'author' => 'SugarCRM, Inc.',
	'description' => 'SugarCRM Upgrader 2.0',
	'icon' => '',
	'is_uninstallable' => 'true',
	'name' => 'SugarCRM Upgrader 2.0',
	'published_date' => date("Y-m-d H:i:s"),
	'type' => 'module',
);
if(file_exists("modules/UpgradeWizard/upgrader_version.json")) {
	$v = json_decode(file_get_contents('modules/UpgradeWizard/upgrader_version.json'), true);
	if(!empty($v['upgrader_version'])) {
		$manifest['version'] = $v['upgrader_version'];
	}
}
$installdefs = array("id" => "upgrader".time(), "copy" => array());
$zip = new ZipArchive();
$zip->open($name, ZipArchive::CREATE);

foreach($files as $file) {
	$zip->addFile($file);
	$installdefs['copy'][] = array("from" => "<basepath>/$file", "to" => $file);
}

$installdefs['copy'][] = array("from" => "<basepath>/upgrader2.php", "to" => "custom/Extension/modules/Administration/Ext/Administration/upgrader2.php");

$zip->addFromString("upgrader2.php", "<?php\n\$admin_group_header[2][3]['Administration']['upgrade_wizard']= array('Upgrade','LBL_UPGRADE_WIZARD_TITLE','LBL_UPGRADE_WIZARD','./UpgradeWizard.php');");


$cont = sprintf("<?php\n\$manifest = %s;\n\$installdefs = %s;\n", var_export($manifest, true), var_export($installdefs, true));
$zip->addFromString("manifest.php", $cont);

$zip->close();
exit(0);
