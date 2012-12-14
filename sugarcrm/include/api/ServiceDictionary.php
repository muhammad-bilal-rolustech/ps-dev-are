<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/********************************************************************************
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

/**
 * API service dictionary
 * Collects information about what endpoints are available in the system
 */
class ServiceDictionary {
    public function __construct() {
        $this->cacheDir = sugar_cached('include/api/');
    }

    /**
     * Clear all API path caches
     */
    public function clearCache() {
        $thedir = $this->cacheDir;
        if ($current = @opendir($thedir)) {
            while (false !== ($children = readdir($current))) {
                $children = trim($children);
                $fileexists = (is_file($thedir."/". $children)) ? "TRUE" : "FALSE";
                if ($children != "." && $children != "..") {
                    if (is_dir($thedir . "/" . $children)) {
                        $this->clearCache($thedir . "/" . $children, 'php');
                    }
                    elseif (is_file($thedir . "/" . $children) && (substr_count($children, 'php'))) {
                        unlink($thedir . "/" . $children);
                    }
                }
            }
        }
    }

    /**
     * Load a dictionary for a particular api
     * @internal
     * @param string $apiType The api type for the dictionary you want to load ("Rest" or "Soap")
     * @return array The data stored in saveDictionaryToStorage()
     */
    protected function loadDictionaryFromStorage($apiType) {
        $dictFile = $this->cacheDir.'ServiceDictionary.'.$apiType.'.php';
        if ( ! file_exists($dictFile) || inDeveloperMode() ) {
            // No stored service dictionary, I need to build them
            $this->buildAllDictionaries();
        }

        require($dictFile);
        
        return $apiDictionary[$apiType];
    }

    /**
     * Save a dictionary for a particular API to storage
     * @internal
     * @param string $apiType The api type for the dictionary you want to load ("Rest" or "Soap")
     * @param array $storageData The data that the API needs to store for it's dictionary.
     */
    protected function saveDictionaryToStorage($apiType,$storageData) {
        if ( !is_dir($this->cacheDir) ) {
            sugar_mkdir($this->cacheDir,null,true);
        }

        sugar_file_put_contents($this->cacheDir.'ServiceDictionary.'.$apiType.'.php','<'."?php\n\$apiDictionary['".$apiType."'] = ".var_export($storageData,true).";\n");
        
    }

    /**
     * Build all dictionaries for the known service types.
     */
    public function buildAllDictionaries() {
        $apis = $this->loadAllDictionaryClasses();

        foreach ( $apis as $apiType => $api ) {
            $api->preRegisterEndpoints();
        }

        $globPaths = array(
            array('glob'=>'clients/*/api/*.php','custom'=>false, 'platformPart'=>1),
            array('glob'=>'custom/clients/*/api/*.php','custom'=>true, 'platformPart'=>2),
            array('glob'=>'modules/*/clients/*/api/*.php','custom'=>false, 'platformPart'=>3),
            array('glob'=>'custom/modules/*/clients/*/api/*.php','custom'=>true, 'platformPart'=>4),
        );

        foreach ( $globPaths as $path ) {
            $files = glob($path['glob'],GLOB_NOSORT);

            if ( !is_array($files) ) {
                // No matched files, skip to the next glob
                continue;
            }
            foreach ( $files as $file ) {
                // Strip off the directory, then the .php from the end
                $fileClass = substr(basename($file),0,-4);

                $pathParts = explode('/',$file);
                if (empty($pathParts[$path['platformPart']]) ) {
                    $platform = 'base';
                } else {
                    $platform = $pathParts[$path['platformPart']];
                }

                require_once($file);
                if (!(class_exists($fileClass) 
                      && is_subclass_of($fileClass,'SugarApi')) ) {
                    // Either the class doesn't exist, or it's not a subclass of SugarApi, regardless, we move on
                    continue;
                }

                $obj = new $fileClass();
                foreach ( $apis as $apiType => $api ) {
                    $methodName = 'registerApi'.$apiType;
                    
                    if ( method_exists($obj,$methodName) ) {
                        $api->registerEndpoints($obj->$methodName(),$file,$fileClass,$platform,$path['custom']);
                    }
                }
            }
        }

        foreach ( $apis as $apiType => $api ) {
            $this->saveDictionaryToStorage($apiType,$api->getRegisteredEndpoints());
        }
    }

    /**
     * Fetch the list of recognized service types.
     * @internal
     */
    protected function loadAllDictionaryClasses() {
        // Currently hardcoded to just Soap and Rest
        require_once('include/api/ServiceDictionaryRest.php');
        // require_once('include/api/ServiceDictionarySoap.php');
        

        $apis = array();
        $apis['rest'] = new ServiceDictionaryRest();
        // $apis['soap'] = new ServiceDictionarySoap();
        
        return $apis;
    }
}
