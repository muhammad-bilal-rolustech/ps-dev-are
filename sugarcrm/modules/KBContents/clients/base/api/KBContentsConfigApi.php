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

require_once('clients/base/api/ConfigModuleApi.php');

class KBContentsConfigApi extends ConfigModuleApi
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function registerApiRest()
    {
        $api = parent::registerApiRest();

        $api['kbcontentsConfigCreate'] = array(
            'reqType' => 'POST',
            'path' => array('KBContents', 'config'),
            'pathVars' => array('module', ''),
            'method' => 'configSave',
            'shortHelp' => 'Creates the config entries for the KBContents module.',
            'longHelp' => 'modules/KBContents/clients/base/api/help/kb_config_put_help.html',
        );
        $api['kbcontentsConfigUpdate'] = array(
            'reqType' => 'PUT',
            'path' => array('KBContents', 'config'),
            'pathVars' => array('module', ''),
            'method' => 'configSave',
            'shortHelp' => 'Updates the config entries for the KBContents module',
            'longHelp' => 'modules/KBContents/clients/base/api/help/kb_config_put_help.html',
        );

        return $api;
    }

    /**
     * {@inheritdoc}
     * Overridden method to save KBContents module settings.
     *
     * @param ServiceBase $api
     * @param array $params
     * @param string $module
     */
    protected function save(ServiceBase $api, $params, $module)
    {
        $admin = BeanFactory::getBean('Administration');

        $deletedLanguages = array();
        if (isset($params['deleted_languages'])) {
            $deletedLanguages = $params['deleted_languages'];
            unset($params['deleted_languages']);
        }

        $config = $admin->getConfigForModule($module);

        foreach ($params as $name => $value) {

            $configSaved = $admin->saveSetting(
                $module,
                $name,
                is_array($value) ? $this->_encodeJSON($value) : $value,
                $api->platform
            );

            if ((1 == $configSaved && 'languages' == $name) && (isset($config['languages']))) {
                $initialLanguageList = $this->_getLanguagesAbbreviations($config['languages']);

                foreach ($value as $key => $language) {
                    unset($language['primary']);
                    $languageKey = key($language);

                    if (in_array($languageKey, $deletedLanguages)) {
                        // Case when we removed and after add the same language back.
                        unset($deletedLanguages[array_search($languageKey, $deletedLanguages)]);
                        continue;
                    }

                    if (!in_array($languageKey, $initialLanguageList)) {
                        if (isset($config['languages'][$key])) {
                            $_tmp = $config['languages'][$key];
                            unset($_tmp['primary']);
                            $configLanguageKey = key($_tmp);
                            if (!in_array($configLanguageKey, $deletedLanguages)) {
                                // $configLanguageKey - initial key
                                // $languageKey - updated key
                                $this->updateDocuments(
                                    array('language' => $languageKey),
                                    array($configLanguageKey)
                                );
                            }
                        }
                    }
                }
                // Process documents for deleted languages
                if (!empty($deletedLanguages)) {
                    $this->updateDocuments(
                        array('deleted' => 1),
                        $deletedLanguages
                    );
                }
            }
        }
    }

    /**
     * Update documents.
     *
     * @param Array $values Pairs {key=>value} for update.
     * @param Array $lang Languages which should be updated.
     */
    protected function updateDocuments($values, $lang)
    {
        $db = DBManagerFactory::getInstance();
        $bean = BeanFactory::getBean('KBContents');

        if (!empty($lang) && !empty($values)) {
            $inString = implode(',', array_map(array($db, 'quoted'), $lang));
            $setParams = array();
            foreach ($values as $key => $value) {
                $setParams[] = $key . ' = ' . $db->quoted($value);
            }
            $db->query('UPDATE ' . $bean->table_name . ' SET ' . implode(',', $setParams) . ' WHERE language IN (' . $inString . ')');
        }
    }

    /**
     * Return list of abbreviations from languages.
     *
     * @param array $list Language list.
     * @return array Of language abbreviations.
     */
    private function _getLanguagesAbbreviations($list)
    {
        $result = array();
        foreach ($list as $item) {
            unset($item['primary']);
            if (2 == strlen(key($item))) {
                $result[] = key($item);
            }
        }
        return $result;
    }

    /**
     * IMPORTANT: this function will be deprecated and should be deleted when minimum version of PHP become 5.4
     *
     * Encode JSON.
     *
     * @param $value
     * @return string
     */
    private function _encodeJSON($value) {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            // For PHP <= 5.4.0 to emulate JSON_UNESCAPED_UNICODE behavior.
            array_walk_recursive($value, function (&$item, $key) {
                if (is_string($item)) $item = mb_encode_numericentity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
            });
            return mb_decode_numericentity(json_encode($value), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
        }
    }
}