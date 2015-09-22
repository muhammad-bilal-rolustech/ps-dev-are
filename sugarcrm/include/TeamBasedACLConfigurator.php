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

class TeamBasedACLConfigurator
{
    const CONFIG_KEY = 'team_based_acl';

    /**
     * @var array
     */
    protected $defaultConfig = array(
        'enabled' => false,
        'disabled_modules' => array(),
    );

    /**
     * @var array List of TBA field constants.
     */
    protected $fieldOptions = array(
        'ACL_READ_SELECTED_TEAMS_WRITE' => 65,
        'ACL_SELECTED_TEAMS_READ_OWNER_WRITE' => 68,
        'ACL_SELECTED_TEAMS_READ_WRITE' => 71,
    );

    /**
     * @var array List of TBA module constants.
     */
    protected $moduleOptions = array(
        'ACL_ALLOW_SELECTED_TEAMS' => 72,
    );

    /**
     * @var string Fields fallback key.
     */
    protected $fieldFallbackOption = 'ACL_OWNER_READ_WRITE';
    /**
     * @var string Modules fallback key.
     */
    protected $moduleFallbackOption = 'ACL_ALLOW_OWNER';

    /**
     * @var array Affected during fallback actions.
     */
    protected $affectedRows = array();

    /**
     * Get TBA field options.
     * @return array
     */
    public function getFieldOptions()
    {
        return $this->fieldOptions;
    }

    /**
     * Get TBA module options.
     * @return array
     */
    public function getModuleOptions()
    {
        return $this->moduleOptions;
    }

    /**
     * Get field fallback option.
     * @return string
     */
    public function getFieldFallbackOption()
    {
        return $this->fieldFallbackOption;
    }

    /**
     * Get module fallback option.
     * @return string
     */
    public function getModuleFallbackOption()
    {
        return $this->moduleFallbackOption;
    }

    /**
     * Check is passed ACL option is handled by TBA.
     * @param mixed $access
     * @return boolean
     */
    public function isValidAccess($access)
    {
        return in_array($access, $this->fieldOptions) || in_array($access, $this->moduleOptions);
    }

    /**
     * Set Team Based ACL for a particular module.
     * @param $module
     * @param boolean $enable
     */
    public function setForModule($module, $enable)
    {
        $enabledGlobally = $this->isEnabledForModule($module);
        if (($enable && $enabledGlobally) || (!$enable && !$enabledGlobally)) {
            return;
        }
        $cfg = new Configurator();
        $actualList = $cfg->config[self::CONFIG_KEY]['disabled_modules'];

        if ($enable) {
            $actualList = array_values(array_diff($actualList, array($module)));
        } else {
            $actualList[] = $module;
        }
        // Configurator doesn't handle lists, to remove an element overriding needed.
        $cfg->config[self::CONFIG_KEY]['disabled_modules'] = false;
        $this->saveConfig($cfg);
        $cfg->config[self::CONFIG_KEY]['disabled_modules'] = $actualList;
        $this->saveConfig($cfg);

        if ($enable) {
            $this->restoreTBA($module);
        } else {
            $this->fallbackTBA($module);
        }
        $this->applyTBA($module);
    }

    /**
     * Set Team Based ACL for a set of modules.
     * @param array $modules List of modules.
     * @param boolean $enable
     */
    public function setForModulesList(array $modules, $enable)
    {
        if (empty($modules)) {
            return;
        }
        $cfg = new Configurator();
        $actualList = $cfg->config[self::CONFIG_KEY]['disabled_modules'];
        $newList = $actualList;

        foreach ($modules as $module) {
            $enabledGlobally = $this->isEnabledForModule($module);
            if (($enable && $enabledGlobally) || (!$enable && !$enabledGlobally)) {
                continue;
            }
            if ($enable) {
                $newList = array_values(array_diff($newList, array($module)));
            } else {
                $newList[] = $module;
            }
        }
        if ($newList == $actualList) {
            return;
        }
        $cfg->config[self::CONFIG_KEY]['disabled_modules'] = false;
        $this->saveConfig($cfg);
        $cfg->config[self::CONFIG_KEY]['disabled_modules'] = $newList;
        $this->saveConfig($cfg);

        if ($enable) {
            $this->restoreTBA();
        } else {
            $this->fallbackTBA();
        }
        $this->applyTBA();
    }

    /**
     * Is Team Based ACL enabled for module, if not set - uses global value.
     * Does not check implementation.
     * @param $module
     * @return bool
     */
    public function isEnabledForModule($module)
    {
        if (!$this->isEnabledGlobally()) {
            return false;
        }
        $config = $this->getConfig();
        return !in_array($module, $config['disabled_modules']);
    }

    /**
     * Set global state of the Team Based ACL.
     * @param boolean $enable
     */
    public function setGlobal($enable)
    {
        $enabledGlobally = $this->isEnabledGlobally();
        if (($enable && $enabledGlobally) || (!$enable && !$enabledGlobally)) {
            return;
        }
        $cfg = new Configurator();
        $cfg->config[self::CONFIG_KEY]['enabled'] = $enable;
        $this->saveConfig($cfg);

        if ($enable) {
            $this->restoreTBA();
        } else {
            $this->fallbackTBA();
        }
        $this->applyTBA();
    }

    /**
     * Global state of the Team Based ACL.
     * @return boolean
     */
    public function isEnabledGlobally()
    {
        $config = $this->getConfig();
        return $config['enabled'];
    }

    /**
     * Update modules vardefs to apply the Team Based visibility.
     * @param string $module Module name.
     */
    protected function applyTBA($module = null)
    {
        if ($module) {
            $bean = BeanFactory::getBean($module);
            VardefManager::clearVardef($bean->module_dir, $bean->object_name);
        } else {
            VardefManager::clearVardef();
        }
    }

    /**
     * Fallback all roles options in case of TBA disabling.
     * Don't pass a module to affect all modules in all roles.
     * @param string $module Module name.
     */
    protected function fallbackTBA($module = null)
    {
        $config = $this->getConfig();
        $aclRole = new ACLRole();
        $aclField = new ACLField();
        $fieldOptions = $this->getFieldOptions();

        $allRoles = $aclRole->getAllRoles();
        foreach ($allRoles as $role) {
            $actions = $aclRole->getRoleActions($role->id);
            $fields = $aclField->getACLFieldsByRole($role->id);

            foreach ($actions as $aclKey => $aclModule) {
                if (($module && $aclKey != $module) ||
                    !in_array($aclKey, $config['disabled_modules'])
                ) {
                    continue;
                }
                $aclType = BeanFactory::getBean($aclKey)->acltype;
                foreach ($aclModule[$aclType] as $action) {
                    if (in_array($action['aclaccess'], $this->getModuleOptions())) {
                        $this->fallbackModule($aclKey, $role->id, $action['id'], $action['aclaccess']);
                    }
                }
            }
            if ($fields) {
                $tbaRecords = array_filter($fields, function ($val) use ($module, $fieldOptions) {
                    if ($module && $val['category'] != $module) {
                        return false;
                    }
                    return in_array($val['aclaccess'], $fieldOptions);
                });
                foreach ($tbaRecords as $fieldToReset) {
                    $this->fallbackField(
                        $fieldToReset['category'],
                        $role->id,
                        $fieldToReset['name'],
                        $fieldToReset['aclaccess']
                    );
                }
            }
        }
        $this->applyFallback();
    }

    /**
     * Restore previously disabled TBA actions if they were not changed after fallback.
     * Don't pass a module to affect all modules in all roles.
     * @param string $module Module name.
     */
    protected function restoreTBA($module = null)
    {
        $config = $this->getConfig();
        $savedActions = $this->getSavedAffectedRows();
        if (($module && !isset($savedActions[$module])) ||
            (!$module && !$savedActions)
        ) {
            return;
        }
        $aclRole = new ACLRole();
        $aclField = new ACLField();
        $actions = $module ? array($module => $savedActions[$module]) : $savedActions;

        foreach ($actions as $moduleName => $moduleActions) {
            if (in_array($moduleName, $config['disabled_modules'])) {
                continue;
            }
            $aclType = BeanFactory::getBean($moduleName)->acltype;
            if (isset($moduleActions[$aclType])) {
                foreach ($moduleActions[$aclType] as $moduleRow) {
                    $accessOverride = $aclRole->retrieve_relationships(
                        'acl_roles_actions',
                        array('role_id' => $moduleRow['role'], 'action_id' => $moduleRow['action']),
                        'access_override'
                    );
                    if (!empty($accessOverride[0]['access_override']) &&
                        $accessOverride[0]['access_override'] == constant($this->getModuleFallbackOption())
                    ) {
                        $aclRole->setAction(
                            $moduleRow['role'],
                            $moduleRow['action'],
                            $moduleRow['access']
                        );
                    }
                }
            }
            if (isset($moduleActions['field'])) {
                foreach ($moduleActions['field'] as $fieldRow) {
                    $roleFields = $aclField->getFields($moduleName, '', $fieldRow['role']);
                    if (!empty($roleFields[$fieldRow['field']]) &&
                        $roleFields[$fieldRow['field']]['aclaccess'] == constant($this->getFieldFallbackOption())
                    ) {
                        $aclField->setAccessControl(
                            $moduleName,
                            $fieldRow['role'],
                            $fieldRow['field'],
                            $fieldRow['access']
                        );
                    }
                }
            }
            unset($savedActions[$moduleName]);
        }
        $admin = BeanFactory::getBean('Administration');
        $admin->saveSetting(self::CONFIG_KEY, 'fallback', json_encode($savedActions), 'base');
        // Calls ACLAction::clearACLCache.
        $aclField->clearACLCache();
    }

    /**
     * Get affected by fallback module and field actions.
     * @return array [ModuleName => ['module' => [[role, action, access]], 'field' => [[role, field, access]]]]|null
     */
    protected function getSavedAffectedRows()
    {
        $admin = BeanFactory::getBean('Administration');
        // Uses json_decode().
        $settings = $admin->getConfigForModule(self::CONFIG_KEY, 'base', true);
        return isset($settings['fallback']) ? $settings['fallback'] : null;
    }

    /**
     * Save field's data in internal property to change its ACL option in future.
     * @param string $module Module name.
     * @param string $role Role id.
     * @param string $field Field name.
     * @param mixed $access Access value.
     */
    protected function fallbackField($module, $role, $field, $access)
    {
        $arrObj = new ArrayObject($this->affectedRows);
        $arrObj[$module]['field'][] = array('role' => $role, 'field' => $field, 'access' => $access);
        $this->affectedRows = $arrObj->getArrayCopy();
    }

    /**
     * Save module's data in internal property.
     * @param string $module Module name.
     * @param string $role Role id.
     * @param string $action Action id.
     * @param mixed $access Access value.
     */
    protected function fallbackModule($module, $role, $action, $access)
    {
        $aclType = BeanFactory::getBean($module)->acltype;
        $arrObj = new ArrayObject($this->affectedRows);
        $arrObj[$module][$aclType][] = array('role' => $role, 'action' => $action, 'access' => $access);
        $this->affectedRows = $arrObj->getArrayCopy();
    }

    /**
     * Fallback TBA options to default and save affected actions in module's settings.
     */
    protected function applyFallback()
    {
        $aclRole = new ACLRole();
        $aclField = new ACLField();

        foreach ($this->affectedRows as $moduleName => $data) {
            $aclType = BeanFactory::getBean($moduleName)->acltype;
            if (isset($data[$aclType])) {
                foreach ($data[$aclType] as $moduleRow) {
                    $aclRole->setAction(
                        $moduleRow['role'],
                        $moduleRow['action'],
                        constant($this->getModuleFallbackOption())
                    );
                }
            }
            if (isset($data['field'])) {
                foreach ($data['field'] as $fieldRow) {
                    $aclField->setAccessControl(
                        $moduleName,
                        $fieldRow['role'],
                        $fieldRow['field'],
                        constant($this->getFieldFallbackOption())
                    );
                }
            }
        }
        $actions = $this->getSavedAffectedRows();
        if ($actions) {
            $this->affectedRows = array_merge($actions, $this->affectedRows);
        }
        $admin = BeanFactory::getBean('Administration');
        $admin->saveSetting(self::CONFIG_KEY, 'fallback', json_encode($this->affectedRows), 'base');
        $this->affectedRows = array();
        // Calls ACLAction::clearACLCache.
        $aclField->clearACLCache();
    }

    /**
     * Return default config.
     * @return array
     */
    public function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    /**
     * Return config.
     * @return array
     */
    public function getConfig()
    {
        return SugarConfig::getInstance()->get(self::CONFIG_KEY, $this->getDefaultConfig());
    }

    /**
     * Check if the module implements TBA.
     * @param string $module Module name.
     * @return bool
     */
    public function isImplementTBA($module)
    {
        $bean = BeanFactory::getBean($module);
        return (bool)$bean->getFieldDefinition('team_set_selected_id');
    }

    /**
     * Save new config and clear cache.
     * @param Configurator $cfg
     */
    protected function saveConfig(\Configurator $cfg)
    {
        $cfg->handleOverride();
        $cfg->clearCache();
        SugarConfig::getInstance()->clearCache();
        // PHP 5.5+. Because of the default value for "opcache.revalidate_freq" is 2 seconds and for modules
        // the config_override.php is being overridden frequently.
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate('config_override.php', true);
        }
    }
}
