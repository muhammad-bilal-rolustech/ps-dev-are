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
/**
 * @class View.Views.Base.SpotlightSearchbarView
 * @alias SUGAR.App.view.views.BaseSpotlightSearchbarView
 * @extends View.View
 */
({
    className: 'spotlight-searchbar',
    events: {
        'keyup input': 'throttledSearch',
        'click [data-action=configure]': 'initConfig'
    },

    /**
     * @inheritDoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        app.events.on('app:sync:complete', this.initLibrary, this);
        this.lastTerm = '';
    },

    initConfig: function(evt) {
        this.layout.toggle();
        this.layout.trigger('spotlight:config');
    },

    /**
     * Initializes the libraries.
     *
     * - Adds all the mega menu actions to {@link #internalLibrary}.
     */
    initLibrary: function() {
        /**
         * Static library.
         *
         * Contains mega menu actions and system commands.
         *
         * @type {Array}
         */
        this.internalLibrary = [];

        /**
         * Temporary library.
         *
         * Contains records from the search API, but these are only kept for
         * 5 minutes (for better user experience).
         *
         * @type {Object}
         */
        this.temporaryLibrary = {};

        this.addToInternalLibrary(this.getModuleLinks());
        this.addToInternalLibrary(this.getSystemActions());

        // Parse the library to remove duplicate
        this.internalLibrary = _.chain(this.internalLibrary)
            .map(function(item) {
                return JSON.stringify(item);
            })
            .uniq()
            .map(function(item) {
                return JSON.parse(item)
            })
            .value();
    },

    /**
     * Adds some items to the {@link #internalLibrary}.
     *
     * @param {Array} items Items to add.
     */
    addToInternalLibrary: function(items) {
        this.internalLibrary = this.internalLibrary.concat(items);
    },

    /**
     * Adds some records to the {@link #temporaryLibrary}
     *
     * @param {Array} items Records to add.
     */
    addToTemporaryLibrary: function(items) {
        _.each(items, function(item) {
            this.temporaryLibrary[item.id] = item;
        }, this);
    },

    /**
     * Gets the records from the {@link #temporaryLibrary}.
     *
     * Records that are here for more than 5 minutes are removed.
     *
     * @return {Array} The list of records.
     */
    getTemporaryLibrary: function() {
        var now = new Date().getTime();
        var tooOld = now - 300000;
        var updatedLibrary = {};
        var recordList = [];
        _.each(this.temporaryLibrary, function(item) {
            if (item.timestamp > tooOld) {
                updatedLibrary[item.id] = item;
                recordList.push(item);
            }
        });
        this.temporaryLibrary = updatedLibrary;
        return recordList;
    },

    /**
     * Gets the library to perform the search.
     *
     * Concats {@link #internalLibrary} and {@link #temporaryLibrary}.
     *
     * @returns {Array} The list of items to perform the search.
     */
    getLibrary: function() {
        return this.internalLibrary.concat(this.getTemporaryLibrary());
    },

    /**
     * Gets all the mega menu actions.
     *
     * @return {Array} Formatted items.
     */
    getModuleLinks: function() {
        var actions = [];
        var moduleList = app.metadata.getModuleNames({filter: 'display_tab'});
        _.each(moduleList, function(module) {
            var menuMeta = app.metadata.getModule(module).menu;
            var headerMeta = menuMeta && menuMeta.header && menuMeta.header.meta;
            _.each(headerMeta, function(action) {
                var name;
                var jsFunc = 'push';
                var weight;;
                if (action.route === '#'+module) {
                    jsFunc = 'unshift';
                    name = app.lang.getModuleName(module, {plural: true});
                    weight = 10;
                }
                else if (action.route === '#'+module+'/create') {
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
            actions.push({
                name: app.lang.get(action.label),
                route: action.route,
                icon: action.icon
            })
        });
        return actions;
    },

    /**
     * Gets system actions.
     *
     * These action items should have a `callback` string that maps to a
     * system action on
     * {@link View.Layouts.Base.SpotlightLayout#_systemActions}.
     *
     * @return {Array} Formatted items.
     */
    getSystemActions: function() {
        var actions = [
            {
                callback: 'toggleHelp',
                action: 'help',
                name: app.lang.get('LBL_HELP'),
                icon: 'fa-exclamation-circle'
            }
        ];
        return actions;
    },

    /**
     * Triggers the search and send results.
     *
     * @param {boolean} later `true` if triggered from the search API callback.
     */
    applyQuickSearch: function(later) {
        var term = this.$('input').val();
        if (!later && term === this.lastTerm) {
            return;
        }
        var results = [];
        if (!later && !_.isEmpty(term)) {
            this.fireSearchRequest(term);
        }
        if (!_.isEmpty(term)) {
            results = this.doSearch(term);
        }
        this.sendResults(results);
        this.lastTerm = term;
    },

    /**
     * Performs the actual search in the library.
     *
     * @param {string} term The term to search
     * @return {Array} Hopefully a list of results.
     */
    doSearch: function(term) {
        var options = {
            keys: ['module', 'name'],
            threshold: '0.1'
        };
        this.fuse = new Fuse(this.getLibrary(), options);
        var results = this.fuse.search(term);
        results = results.slice(0, 6);
        results = _.sortBy(results, 'weight');
        return results;
    },

    /**
     * Triggers `spotlight:results` with the results of the search.
     *
     * @param {Array} Hopefully a list of results.
     */
    sendResults: function(results) {
        this.layout.trigger('spotlight:results', results);
    },

    /**
     * Calls {@link #applyQuickSearch} with a debounce of 200ms.
     */
    throttledSearch: _.debounce(function(event) {
        this.applyQuickSearch();
    }, 200),

    /**
     * Makes a request to the search API to find records.
     *
     * On success it calls {@link #addToTemporaryLibrary} to add the records
     * to the temporary library and calls {@link #applyQuickSearch} to re-apply
     * the search.
     *
     * @param {string} term The search term.
     */
    fireSearchRequest: function(term) {
        // FIXME this should not perform an empty search. WTF UnifiedSearchApi!
        term = '';
        var self = this;
        var params = {
            q: term,
            fields: 'name, id',
            max_num: 50
        };
        app.api.search(params, {
            success: function(data) {
                var now = new Date().getTime();
                var formattedRecords = [];
                _.each(data.records, function(record) {
                    if (!record.id) {
                        return; // Elastic Search may return records without id and record names.
                    }
                    var formattedRecord = {
                        id: record.id,
                        name: record.name,
                        module: record._module,
                        label: record._module.substr(0, 2),
                        route: '#' + app.router.buildRoute(record._module, record.id),
                        timestamp: now,
                        weight: 40
                    };

                    if ((record._search.highlighted)) { // full text search
                        _.each(record._search.highlighted, function(val, key) {
                            var safeString = self._escapeSearchResults(val.text);
                            if (key !== 'name') { // found in a related field
                                formattedRecord.field_name = app.lang.get(val.label, val.module);
                                formattedRecord.field_value = safeString;
                            } else { // if it is a name that is found, we need to replace the name with the highlighted text
                                formattedRecord.name = safeString;
                            }
                        });
                    }
                    formattedRecords.push(formattedRecord);
                });
                self.addToTemporaryLibrary(formattedRecords);
                self.applyQuickSearch(true);
            },
            error: function(error) {
                app.logger.error("Failed to fetch search results in search ahead. " + error);
            }
        });
    }

})
