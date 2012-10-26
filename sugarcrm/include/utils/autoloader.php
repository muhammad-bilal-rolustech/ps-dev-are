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
require_once 'include/utils/file_utils.php';
/**
 * File and class loader
 * @api
 */
class SugarAutoLoader
{
    const CACHE_FILE = "file_map.php";

    /**
     * Direct class mapping
     * @var array name => path
     */
	public static $map = array(
		'XTemplate'=>'XTemplate/xtpl.php',
		'Javascript'=>'include/javascript/javascript.php',
        'CustomSugarView' => 'custom/include/MVC/View/SugarView.php',
	);

	/**
	 * Classes not to be loaded
	 * @var array name => boolean
	 */
	public static $noAutoLoad = array(
		'Tracker'=>true,
	);

    /**
     * @var array
     */
    public static $moduleMap = array();

	/**
	 * Class prefixes
	 * Classes are loaded by prefix:
	 * SugarAclFoo.php => data/acl/SugarACLFoo.php
	 * @var array prefix => directory
	 */
	public static $prefixMap = array(
	    'SugarACL' => "data/acl/",
	    'SugarWidget' => "include/generic/SugarWidgets/",
	    'Zend_' => '',
	    'SugarJob' => 'include/SugarQueue/jobs/',
	);

	/**
	 * Class loading directories
	 * Classes in these dirs are loaded by class name:
	 * Foo -> $dir/Foo.php
	 * @var array paths
	 */
	public static $dirMap = array(
	    "data/visibility/",
	    "include/SugarSearchEngine/",
	    "include/",
	);

	/**
	 * Directories to exclude form mapping
	 * @var array
	 */
	public static $exclude = array(
        "cache/",
        "custom/history/",
        ".idea/",
        "custom/blowfish/",
        "custom/Extension/",
        "custom/backup/",
	    "custom/modulebuilder/",
        "tests/",
        "examples/",
        'docs/',
        'log4php/',
        'upload/',
	    'portal/',
	    'include/HTMLPurifier/',
	    'include/phpmailer/',
	    'include/reCaptcha/',
	    'include/ytree/',
	    'include/pclzip/',
	    'include/nusoap/',
	);
	/**
	 * Extensions to include in mapping
	 * @var string
	 */
    public static $exts = array("php", "tpl", "html", "js", "override", 'gif', 'png', 'jpg', 'tif', 'bmp', 'css', 'xml', 'hbt');
    /**
     * File map
     * @var array
     */
    public static $filemap = array();
    /**
     * Copy of extension map
     * @var array
     */
    public static $extensions = array();

    /**
     * Initialize the loader
     */
	static public function init()
	{
	    if(!empty($GLOBALS['sugar_config']['autoloader']['exts']) && is_array($GLOBALS['sugar_config']['autoloader']['exts'])) {
	        self::$exts += $GLOBALS['sugar_config']['autoloader']['exts'];
	    }
		if(!empty($GLOBALS['sugar_config']['autoloader']['exclude']) && is_array($GLOBALS['sugar_config']['autoloader']['exclude'])) {
	        self::$exclude += $GLOBALS['sugar_config']['autoloader']['exclude'];
	    }
	    self::loadFileMap();
	    spl_autoload_register(array('SugarAutoLoader', 'autoload'));
	    self::loadExts();
	}

	/**
	 * Load a class
	 * @param string $class Class name
	 * @return boolean Success?
	 */
    public static function autoload($class)
	{
		$uclass = ucfirst($class);
		if(!empty(self::$noAutoLoad[$class])){
			return false;
		}

		// try known classes
		if(!empty(self::$map[$uclass])){
		    if(self::fileExists(self::$map[$uclass])) {
			    require_once(self::$map[$uclass]);
			    return true;
		    } else {
		        return false;
		    }
		}

		if(empty(self::$moduleMap)){
			if(isset($GLOBALS['beanFiles'])){
				self::$moduleMap = $GLOBALS['beanFiles'];
			}else{
				include('include/modules.php');
				self::$moduleMap = $beanFiles;
			}
		}

		// Try known modules
		if(!empty(self::$moduleMap[$class])){
			require_once(self::$moduleMap[$class]);
			return true;
		}

	    // Split on _, capitalize elements and make a path
	    // foo_bar -> Foo/Bar.
	    $class_file = join('/', array_map('ucfirst', explode('_', $class)));

		// Try known prefixes
		foreach(self::$prefixMap as $prefix => $dir) {
		    if(strncasecmp($prefix, $class, strlen($prefix)) === 0) {
		        if(self::requireWithCustom("{$dir}$class_file.php")) {
		            return true;
		        } else {
		            break;
		        }
		    }
		}

		// Try known dirs
		foreach(self::$dirMap as $dir) {
		    // include/Class.php
		    if(self::requireWithCustom("{$dir}$class_file.php")) {
		        return true;
		    }
		    // include/Class/Class.php
		    // Note here we don't use $class_file since using path twice would not make sense:
		    // Foo/Bar/Foo/Bar.php vs. Foo_Bar/Foo_Bar.php
			if(self::requireWithCustom("{$dir}$class/$class.php")) {
		        return true;
		    }
		    // try include/Foo_Bar.php as a last resort
			if(self::requireWithCustom("{$dir}$class.php")) {
		        return true;
		    }
		}

		// Special cases
        if(self::getFilenameForViewClass($class)) {
            return true;
        }
        if(self::getFilenameForSugarWidget($class)) {
            return true;
        }

  		return false;
	}

	/**
	 * Add directory for loading classes
	 * Directory should include trailing /
	 * @param string $dir
	 */
	public static function addDirectory($dir)
	{
	    self::$dirMap[] = $dir;
	}

	/**
	 * Add directory for loading classes by prefix
	 * Directory should include trailing /
	 * @param string $prefix
	 * @param string $dir
	 */
	public static function addPrefixDirectory($prefix, $dir)
	{
	    self::$prefixMap[$prefix] = $dir;
	}

	protected static function getFilenameForViewClass($class)
    {
        $module = false;
        if (!empty($_REQUEST['module']) && substr($class, 0, strlen($_REQUEST['module'])) === $_REQUEST['module'])
        {
            //This is a module view
            $module = $_REQUEST['module'];
            $class = substr($class, strlen($module));
        }

        if (substr($class, 0, 4) == "View")
        {
            $view = strtolower(substr($class, 4));
            if ($module)
            {
                return self::requireWithCustom("modules/$module/views/view.$view.php");
            } else {
                return self::requireWithCustom("include/MVC/View/views/view.$view.php");
            }
        }
        return false;
    }

    /**
     * getFilenameForSugarWidget
     *
     * This method attempts to autoload classes starting with name "SugarWidget".  It first checks for the file
     * in custom/include/generic/SugarWidgets directory and if not found defaults to include/generic/SugarWidgets.
     * This method is used so that we can easily customize and extend these SugarWidget classes.
     *
     * @static
     * @param $class String name of the class to load
     * @return String file of the SugarWidget class; false if none found
     */
    protected static function getFilenameForSugarWidget($class)
    {
        //Only bother to check if the class name starts with SugarWidget
        if(strpos($class, 'SugarWidgetField') !== false) {
            //We need to lowercase the portion after SugarWidgetField
            $name = substr($class, 16);
            if(empty($name)) {
                return false;
            }
            $class = 'SugarWidgetField' . strtolower($name);
            return self::requireWithCustom("include/generic/SugarWidgets/{$class}.php");
        }
        return false;
    }

    /**
     * Load file if exists
     * @param string $file
     * @return boolean True if file was loaded
     */
    public static function load($file)
    {
        if(self::fileExists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }

    /**
     * Load file either from custom, if exists, or from core
     * @param string $file filename
     * @param bool $both Do we want both?
     * @return was any file loaded?
     */
    public static function requireWithCustom($file, $both = false)
    {
        if(self::fileExists("custom/$file")) {
            if($both) {
                // when loading both, core file goes first so custom can override it
                // however we check for custom first and if $both not set load only it
                if(self::fileExists($file)) {
                    require_once($file);
                }
            }
            require_once "custom/$file";
            return true;
        } else {
            if(self::fileExists($file)) {
                require_once($file);
                return true;
            }
        }
        return false;
    }

    /**
     * Get list of existing files and their customizations.
     * @param ... $files
     * @return array Existing files and customizations. Customizations go after files.
     */
    public static function existing()
    {
        $files = func_get_args();
        $out = array();
        foreach($files as $file) {
            if(empty($file)) continue;
            if(is_array($file)) {
                $out += call_user_func_array(array("SugarAutoLoader", "existing"), $file);
                continue;
            }
            if(self::fileExists($file)) {
                $out[] = $file;
            }
        }
        return $out;
    }

    /**
     * Get list of existing files and their customizations.
     * @param ... $files
     * @return array Existing files and customizations. Customizations go after files.
     */
    public static function existingCustom()
    {
        $files = func_get_args();
        $out = array();
        foreach($files as $file) {
            if(empty($file)) continue;
            if(is_array($file)) {
                $out += call_user_func_array(array("SugarAutoLoader", "existingCustom"), $file);
            }
            if(self::fileExists($file)) {
                $out[] = $file;
            }
            if(substr($file, 0, 7) != 'custom/' && self::fileExists("custom/$file")) {
                $out[] = "custom/$file";
            }
        }
        return $out;
    }

    /**
     * Get customized file or core file.
     * Returns only the last existing variant, custom if exists
     * @param ... $files
     * @return string|null Last existing file out of given arguments
     */
    public static function existingCustomOne()
    {
        $files = func_get_args();
        $out = call_user_func_array(array("SugarAutoLoader", "existingCustom"), $files);
        if(empty($out)) {
            return null;
        } else {
            return array_pop($out);
        }
    }

    /**
     * Lookup filename in a list of paths. Paths are checked with and without custom/
     * @param array $paths
     * @param string $file
     * @return string|bool Filename found or false
     */
    public static function lookupFile($paths, $file)
    {
        foreach($paths as $path) {
            $fullname = "$path/$file";
            if(self::fileExists("custom/$fullname")) {
                return "custom/$fullname";
            }
            if(self::fileExists($fullname)) {
                return $fullname;
            }
        }
        return false;
    }

    /**
     * Load all classes in self::$map
     */
	public static function loadAll()
	{
		foreach(self::$map as $class=>$file){
			require_once($file);
		}
		if(isset($GLOBALS['beanFiles'])){
			$files = $GLOBALS['beanFiles'];
		}else{
			include('include/modules.php');
			$files = $beanList;
		}
		foreach(self::$map as $class=>$file){
			require_once($file);
		}
	}

	/**
	 * Load viewdefs file using the following logic:
	 * 1. Check custom/module/metadata/$varname.php
	 * 2. If not there, check metafiles.php
	 * 3. If still not found, use module/metadata/$varname.php
	 * @param string $module
	 * @param string $varname Name of the vardef file (listviewdef, etc.) - no .php
	 * @param string $defname metafiles definition name, if different from $varname
	 * @return string|null Suitable metadata file or null
	 */
	public static function loadWithMetafiles($module, $varname, $defname = null)
	{
	    if(empty($defname)) {
	        $defname = $varname;
	    }
	    $vardef = self::existingCustomOne("modules/{$module}/metadata/{$varname}.php");
	    if(!empty($vardef) && substr($vardef, 0, 7) == "custom/") {
	        // custom goes first
	        return $vardef;
	    }
	    // otherwise check metadata
	    global $metafiles;
	    if(!isset($metafiles[$module])) {
	        $meta = self::existingCustomOne('modules/'.$module.'/metadata/metafiles.php');
    	    if($meta) {
    	    	require $meta;
    	    }
	    }
	    if(!empty($metafiles[$module][$defname])) {
	        $defs = self::existing($metafiles[$module][$defname], $vardef);
	    } else {
	        $defs = self::existing($vardef);
	    }
	    if(!$defs) {
	        return null;
	    } else {
	        return $defs[0];
	    }
	}

	/**
	 * Load popupdefs metadata file
	 * @param string $module
	 * @param string $metadata metadata name override
	 * @return array popup defs data or NULL
	 */
	public static function loadPopupMeta($module, $metadata = null)
	{
	    $defs = null;
	    if($metadata == 'undefined' || strpos($metadata, "..") !== false) {
	        $metadata = null;
	    }
	    if(!empty($metadata)) {
	    	$defs = SugarAutoLoader::loadWithMetafiles($module, $metadata);
	    }

	    if(!$defs) {
	    	$defs = SugarAutoLoader::loadWithMetafiles($module, 'popupdefs');
	    }
        if($defs) {
            require $defs;
            return $popupMeta;
        }
        return array();
	}

	/**
	 * Get metadata file for an extension
	 * see extensions.php for the list
	 * @param string $extname Extension name
	 * @param string $module Module to apply to
	 * @return boolean|string File to load, false if none
	 */
	public static function loadExtension($extname, $module = "application")
	{
	    if(empty(self::$extensions[$extname])) return false;
	    $ext = self::$extensions[$extname];
	    if(empty($ext['file']) || empty($ext['extdir'])) {
	        // custom rebuilds, can't handle
	        return false;
	    }
	    if(isset($ext["module"])) {
	        $module = $ext["module"];
	    }
	    if($module == "application") {
	        $file = "custom/application/Ext/{$ext["extdir"]}/{$ext["file"]}";
	    } else {
	        $file = "custom/modules/{$module}/Ext/{$ext["extdir"]}/{$ext["file"]}";
	    }
	    if(self::fileExists($file)) {
	        return $file;
	    }
        return false;
	}

    /**
     * Check if file exists in the cache
     * @param string $filename
     * @return boolean
     */
    public static function fileExists($filename)
    {
        if(DIRECTORY_SEPARATOR != '/') {
            $filename = str_replace(DIRECTORY_SEPARATOR, "/", $filename);
        }
        $parts = explode('/', $filename);
        $data = self::$filemap;
        foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
            if(!isset($data[$part])) {
                return false;
            }
            $data = $data[$part];
        }
        if($data || $data == array()) {
            return true;
        }
        return false;
    }

    /**
     * Get all files in directory from cache
     * @param string $dir
     * @param bool $get_dirs Get directories and not files
     * @param string $extension Get only files with given extension
     * @return array List of files
     */
    public static function getDirFiles($dir, $get_dirs = false, $extension = null)
    {
        if(DIRECTORY_SEPARATOR != '/') {
            $filename = str_replace(DIRECTORY_SEPARATOR, "/", $filename);
        }
        // remove leading . if present
        $extension = ltrim($extension, ".");
        $dir = rtrim($dir, "/");
        $parts = explode('/', $dir);
        $data = self::$filemap;
        foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
            if(!isset($data[$part])) {
        		return array();
        	}
        	$data = $data[$part];
        }
        $result = array();
        if(!is_array($data)) {
            return $result;
        }
        foreach($data as $file => $data) {
            // check extension if given
            if(!empty($extension) && pathinfo($file, PATHINFO_EXTENSION) != $extension) continue;
            // get dirs or files depending on $get_dirs
            if(is_array($data) == $get_dirs) {
                $result[] = "$dir/$file";
            }
        }
        return $result;
    }

    /**
     * Get list of files in this dir and custom duplicate of it
     * @param string $dir
     * @param bool $get_dirs Get directories and not files
     * @return array
     */
    public static function getFilesCustom($dir, $get_dirs = false, $extension = null)
    {
        return array_merge(self::getDirFiles($dir, $get_dirs, $extension), self::getDirFiles("custom/$dir", $get_dirs, $extension));
    }


    /**
     * Build file cache
     */
	public static function buildCache()
	{
        $data = self::scanDir("");
        write_array_to_file("existing_files", $data, sugar_cached(self::CACHE_FILE));
        self::$filemap = $data;
	}

	/**
	 * Load cached file map
	 */
	public static function loadFileMap()
	{
	    $existing_files = null;
	    @include sugar_cached(self::CACHE_FILE);
	    if(empty($existing_files)) {
	        // oops, something happened to cache
	        // try to rebuild
	        self::buildCache();
	        @include sugar_cached(self::CACHE_FILE);
	    }
        self::$filemap = $existing_files;
	}

	/**
	 * Load extensions map
	 */
	protected static function loadExts()
	{
	    include "ModuleInstall/extensions.php";
	    self::$extensions = $extensions;
	}

	/**
	 * Add filename to list of existing files
	 * @param string $filename
	 * @param bool $save should we save it to file?
	 * @param bool $dir should it be empty directory?
	 */
	public static function addToMap($filename, $save = true, $dir = false)
	{
	    if(self::existing($filename))
	        return;
        foreach(self::$exclude as $exclude_pattern) {
            if(substr($filename, 0, strlen($exclude_pattern)) == $exclude_pattern) {
                return;
            }
        }

	    if(DIRECTORY_SEPARATOR != '/') {
            $filename = str_replace(DIRECTORY_SEPARATOR, "/", $filename);
        }
        $parts = explode('/', $filename);
	    $filename = array_pop($parts);
	    $data =& self::$filemap;
	    foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
	        if(!isset($data[$part])) {
                $data[$part] = array();
	        }
	        $data =& $data[$part];
	    }
	    if(!is_array($data)) {
	        $data = array();
	    }
	    $data[$filename] = $dir?array():1;
	    if($save) {
	        write_array_to_file("existing_files", self::$filemap, sugar_cached(self::CACHE_FILE));
	    }
	}

	/**
	 * Delete file from the map
	 * Mainly for use in tests
	 * @param string $filename
	 * @param bool $save should we save it to file?
	 */
	public static function delFromMap($filename, $save = true)
	{
	    if(DIRECTORY_SEPARATOR != '/') {
            $filename = str_replace(DIRECTORY_SEPARATOR, "/", $filename);
        }
	    $parts = explode('/', $filename);
	    $filename = array_pop($parts);
	    $data =& self::$filemap;
	    foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
	        if(!isset($data[$part])) {
	    	  return;
	    	}
	    	$data =& $data[$part];
	    }
	    unset($data[$filename]);
	    if($save) {
	        write_array_to_file("existing_files", self::$filemap, sugar_cached(self::CACHE_FILE));
	    }
	}

	/**
	 * Scan directory and build the list of files it contains
	 * @param string $path
	 * @return array Files data
	 */
	public static function scanDir($path)
	{
	    $data = array();
	    if(in_array($path, self::$exclude)) {
	    	return array();
	    }
	    $iter = new DirectoryIterator("./".$path);
	    foreach($iter as $item) {
	    	if($item->isDot()) continue;
	    	$filename = $item->getFilename();
	    	if($item->isDir()) {
	    		$data[$filename] = self::scanDir($path.$filename."/");
	    	} else {
	    		if(!in_array(pathinfo($filename, PATHINFO_EXTENSION), self::$exts)) continue;
	    		$data[$filename] = 1;
	    	}
	    }
	    return $data;
	}

	/**
	 * Get custom class name if that exists or original one if not
	 * @param string $classname
	 * @return string Classname
	 */
	public static function customClass($classname)
	{
	    $customClass = 'Custom'.$classname;
	    if(class_exists($customClass)) {
	        return $customClass;
	    }
	    return $classname;
	}

	/**
	 * Unlink and delete from map
	 * To use mainly for tests
	 * @param string $filename
	 * @param bool $save Save map to file?
	 * @return bool Success?
	 */
	public static function unlink($filename, $save = false)
	{
	    self::delFromMap($filename, $save);
	    unlink($filename);
	}

	/**
	 * Create empty file and add to map
	 * To use mainly for tests
	 * @param string $filename
	 * @param bool $save Save map to file?
	 * @return bool Success?
	 */
	public static function touch($filename, $save = false)
	{
	    if(sugar_touch($filename)) {
	        self::addToMap($filename, $save);
	        return true;
	    }
	    return false;
	}

	/**
	 * Put data to file and add to map
	 * To use mainly for tests
	 * @param string $filename
	 * @param bool $save Save map to file?
	 * @return bool Success?
	 */
	public static function put($filename, $data, $save = false)
	{
	    if(file_put_contents($filename, $data) !== false) {
	        self::addToMap($filename, $save);
	        return true;
	    }
	    return false;
	}

	/**
	 * Ensure the directory exists
	 * @param string $dir
	 * @return boolean
	 */
	public static function ensureDir($dir)
	{
	    if(self::fileExists($dir)) {
	        return true;
	    }
	    if(sugar_mkdir($dir, null, true)) {
	        self::addToMap($dir, true, true);
	        return true;
	    }
	    return false;
	}

	/**
	 * Save the file map to disk
	 */
	public static function saveMap()
	{
	    write_array_to_file("existing_files", self::$filemap, sugar_cached(self::CACHE_FILE));
	}
}
