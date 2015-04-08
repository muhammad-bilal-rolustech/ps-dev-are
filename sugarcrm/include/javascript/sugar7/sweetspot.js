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
(function(app) {
    app.events.on('app:init', function() {

        /**
         * Gets system actions.
         *
         * These action items should have a `callback` string that maps to a
         * system action on
         * {@link View.Layouts.Base.SweetspotLayout#_systemActions}.
         *
         * @return {Array} Formatted items.
         */
        var getSystemActions = function() {
            var actions = [
                {
                    callback: 'toggleHelp',
                    action: 'help',
                    name: app.lang.get('LBL_HELP'),
                    icon: 'fa-exclamation-circle'
                }
            ];
            return actions;
        };

        /**
         * Verifies if the user has access to the action
         *
         * @param {string} module The module corresponding to the action.
         * @param {string} action The action
         * @returns {Object|boolean} The action object if the user has access,
         *  `false` otherwise.
         */
        var hasAccessToAction = function(module, action) {
            if (module && action.acl_action) {
                if (!app.acl.hasAccess(action.acl_action, module)) {
                    return false;
                }
                return action;
            }

            if (action.acl_action === 'admin' && action['label'] === 'LBL_ADMIN') {
                //Edge case for admin link. We only show the Admin link when
                //user has the "Admin & Developer" or "Developer" (so developer
                //in either case; see SP-1827)
                if (!app.acl.hasAccessToAny('developer')) {
                    return false;
                }
                return action;
            }

            return action;
        };

        /**
         * Gets all the mega menu actions.
         *
         * @return {Array} Formatted items.
         */
        var getModuleLinks = function() {
            var actions = [];
            var moduleList = app.metadata.getModuleNames({filter: 'display_tab'});
            if (app.metadata.getModule('Administration')) {
                moduleList.push('Administration');
                moduleList = _.uniq(moduleList);
            }
            _.each(moduleList, function(module) {
                var menuMeta = app.metadata.getModule(module).menu;
                var headerMeta = menuMeta && menuMeta.header && menuMeta.header.meta || [];
                var sweetspotMeta = menuMeta && menuMeta.sweetspot && menuMeta.sweetspot.meta || [];
                _.each(headerMeta.concat(sweetspotMeta), function(action) {
                    if (hasAccessToAction(action.acl_module || module, action) === false) {
                        return;
                    }

                    var name;
                    var jsFunc = 'push';
                    var weight;
                    if (action.route === '#' + module) {
                        jsFunc = 'unshift';
                        name = app.lang.getModuleName(module, {plural: true});
                        weight = 10;
                    }
                    else if (action.route === '#' + module + '/create') {
                        weight = 20;
                        name = app.lang.get(action.label, module)
                    } else {
                        weight = 30;
                        name = app.lang.get(action.label, module)
                    }
                    actions[jsFunc]({
                        module: module,
                        label: module.substr(0, 2),
                        name: name,
                        route: action.route,
                        icon: action.icon,
                        weight: weight
                    })
                });
            });
            var profileActions = app.metadata.getView(null, 'profileactions');
            _.each(profileActions, function(action) {
                if (hasAccessToAction(action.acl_module, action) === false) {
                    return;
                }

                actions.push({
                    name: app.lang.get(action.label),
                    route: action.route,
                    icon: action.icon
                });
            });
            return actions;
        };

        /**
         * Gets all the sweetspot actions.
         *
         * @returns {Object} The list of actions.
         */
        app.metadata.getSweetspotActions = function() {
            var collection = {};
            var actions = getModuleLinks().concat(getSystemActions());
            _.each(actions, function(action) {
                collection[action.route || action.callback] = action;
            });
            return collection;
        };

    });
})(SUGAR.App);
