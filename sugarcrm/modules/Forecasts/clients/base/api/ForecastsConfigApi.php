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

require_once('include/api/ConfigModuleApi.php');

class ForecastsConfigApi extends ConfigModuleApi {


    /**
     * Save function for the config settings for a given module.
     * @param $api
     * @param $args 'module' is required, 'platform' is optional and defaults to 'base'
     */
    public function configSave($api, $args) {
        $admin = BeanFactory::getBean('Administration');

        //acl check, only allow if they are module admin
        if(!parent::hasAccess("Forecasts")) {
            throw new SugarApiExceptionNotAuthorized("Current User not authorized to change Forecasts configuration settings");
        }

        $platform = (isset($args['platform']) && !empty($args['platform']))?$args['platform']:'base';

        //track what settings have changed to determine if timeperiods need to be rebuilt
        $prior_forecasts_settings = $admin->getConfigForModule('Forecasts', $platform);

        //save new settings from the admin wizard
        $new_settings = parent::configSave($api, $args);
        $current_forecasts_settings = $admin->getConfigForModule('Forecasts', $platform);

        $timePeriod = BeanFactory::getBean('TimePeriods');
        if(!$timePeriod->isSettingIdentical($prior_forecasts_settings, $current_forecasts_settings))
        {
            $timePeriod->rebuildForecastingTimePeriods($prior_forecasts_settings, $current_forecasts_settings);
            $timePeriod->deleteTimePeriods($prior_forecasts_settings, $current_forecasts_settings);
        }
        return $new_settings;
    }

}
