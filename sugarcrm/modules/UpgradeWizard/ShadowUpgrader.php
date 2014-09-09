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
require_once 'CliUpgrader.php';

/**
 * Upgrder for the shadow environment
 */
class ShadowUpgrader extends CliUpgrader
{
    protected $options = array(
        // required, short, long
        'pre_template' => array(true, 'f', 'from'),
        'post_template' => array(true, 't', 'to'),
        "source_dir" => array(true, 's', 'source'),
        "log" => array(true, 'l', 'log'),
        "admin" => array(true, 'u', 'user'),
        "backup" => array(false, 'b', 'backup'),
        "script_mask" => array(false, 'm', 'mask'),
        "stage" => array(false, 'S', 'stage'),
        "autoconfirm" => array(false, 'A', 'autoconfirm')
    );

    /**
     * @see CliUpgrader::usage()
     */
    protected function commit()
    {
        // commit doesn't do anything
        return true;
    }

    /**
     * @see CliUpgrader::usage()
     */
    protected static function usage()
    {
		list($version, $build) = static::getVersion();
    	$usage =<<<eoq2
Shadow Upgrader v.$version (build $build)
php ShadowUpgrader.php -f oldTemplate -t newTemplate -s pathToSugarInstance -l logFile -u admin-user

Example:
    php ShadowUpgrader.php -f /sugar/templates/7.0.0 -t /sugar/templates/7.1.0 -s path-to-sugar-instance/ \
    	    -l silentupgrade.log -u admin

Arguments:
    -f/--from oldTemplate                : Pre-upgrade template
    -t/--to newTemplate                  : Target template
    -s/--source pathToSugarInstance      : Sugar instance being upgraded.
    -l/--log logFile                     : Upgarde log file (by default relative to instance dir)
    -u/--user admin-user                 : admin user performing the upgrade
Optional arguments:
    -m/--mask scriptMask                 : Script mask - which types of scripts to run.
                                           Supported types: db, custom, none. Default is db,custom.
    -b/--backup 0/1                      : Create backup of deleted files? 0 means no backup, default is 1.
    -S/--stage stage                     : Run specific stage of the upgrader. 'continue' means start where it stopped last time.
    -A/--autoconfirm                     : Automatic confirm health check results (use with caution !)

eoq2;
    	echo $usage;
    }

    /**
     * @see UpgradeDriver::verifyArguments()
     * @return bool
     */
    protected function verifyArguments()
    {
        if(!function_exists("shadow")) {
            $this->argError("Shadow module should be installed to run this script.");
        }

        if(empty($this->context['source_dir']) || !is_dir($this->context['source_dir'])) {
            $this->argError("Source dir parameter must be a valid directory.");
        }

        if(empty($this->context['pre_template']) || empty($this->context['post_template'])) {
            $this->argError("Templates should be specified");
        }

        if(!is_file("{$this->context['pre_template']}/include/entryPoint.php")) {
            $this->argError("{$this->context['pre_template']} is not a SugarCRM template.");
        }

        if(!is_file("{$this->context['post_template']}/include/entryPoint.php")) {
            $this->argError("{$this->context['post_template']} is not a SugarCRM template.");
        }

        if(!is_file("{$this->context['source_dir']}/config.php")) {
            $this->argError("{$this->context['source_dir']} is not a SugarCRM directory.");
        }

    	return true;
    }

    /**
     * Returns version from the given $path
     * @param $path
     * @return string
     */
    protected function getVersionFromPath($path)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $f = array_pop($parts);
        $v = array_pop($parts);
        return $v.$f;
    }

    /**
     * Fix values in the context
     * @param array $context
     * @return array
     */
    public function fixupContext($context)
    {
        $context = parent::fixupContext($context);
        $context['script'] = __FILE__;
        $context['pre_template'] = realpath($context['pre_template']);
        $context['post_template'] = realpath($context['post_template']);
        $from = $this->getVersionFromPath($context['pre_template']);
        $to = $this->getVersionFromPath($context['post_template']);
        $context['zip'] = "ShadowUpgrade-$from-$to";
        // only use custom and DB scripts
        if(isset($context['script_mask'])) {
            $context['script_mask'] &= UpgradeScript::UPGRADE_CUSTOM|UpgradeScript::UPGRADE_DB;
        } else {
            $context['script_mask'] = UpgradeScript::UPGRADE_CUSTOM|UpgradeScript::UPGRADE_DB;
        }
        $context['new_source_dir'] = $context['post_template'];
        $context['backup'] = 0;
        return $context;
    }

    /**
     * @see CliUpgrader::extractZip()
     * @param string $zip
     * @return bool|false
     */
    protected function extractZip($zip)
    {
        // no zip, nothing to extract
        return true;
    }

    /**
     * @see CliUpgrader::unlink()
     * @param string $file
     * @return bool
     */
    public function unlink($file)
    {
        if($file[0] == '/') {
            return parent::unlink($file);
        }
        // check relative paths against source dir
        if(file_exists($this->context['source_dir']."/".$file)) {
            return @unlink($file);
        }
        return true;
    }

    /**
     * @see CliUpgrader::getManifest()
     * @return array
     */
    protected function getManifest()
    {
        // load target data
        chdir($this->context['post_template']);
        list($to_version, $to_flavor) = $this->loadVersion();
        chdir($this->context['source_dir']);
        // return fake manifest
        return array(
            'description' => 'Shadow Upgrade from {$this->from_version}/{$this->from_flavor} to $to_version/$to_flavor',
            'acceptable_sugar_flavors' => array($this->from_flavor),
            'acceptable_sugar_versions' => array('exact_matches' => array($this->from_version)),
            'type' => 'patch',
            'version' => $to_version,
            'flavor' => $to_flavor,
        );
    }

    /**
     * @see CliUpgrader::verify()
     * @param string $zip
     * @param string $dir
     * @return bool|false
     */
    protected function verify($zip, $dir)
    {
        chdir($this->context['pre_template']);
        return parent::verify($zip, $dir);
    }

    /**
     * @see CliUpgrader::initSugar()
     */
    protected function initSugar()
    {
        if($this->context['stage'] == 'pre' || $this->context['stage'] == 'unpack') {
            $templ_dir = $this->context['pre_template'];
        } else {
            $templ_dir = $this->context['post_template'];
        }
        chdir($templ_dir);
        $this->log("Shadow configuration: $templ_dir -> {$this->context['source_dir']}");
        shadow($templ_dir, $this->context['source_dir'], array("cache", "upload", "config.php"));
        $this->context['source_dir'] = $templ_dir;
        return parent::initSugar();
    }

    /**
     * @see CliUpgrader::healthcheck()
     * @return bool
     */
    public function healthcheck()
    {
        $this->initSugar();
        return parent::healthcheck();
    }

    /**
     * @see UpgradeDriver::getPackageUid()
     * @return string
     */
    protected function getPackageUid()
    {
        return md5($this->context['post_template']);
    }
}

if(empty($argv[0]) || basename($argv[0]) != basename(__FILE__)) return;

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die("This is command-line only script");
}
ShadowUpgrader::start();


