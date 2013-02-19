/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement (""License"") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the ""Powered by SugarCRM"" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
({
    extendsFrom: 'EditableView',

    /**
     * View that displays a list of models pulled from the context's collection.
     * @class View.Views.ListView
     * @alias SUGAR.App.layout.ListView
     * @extends View.View
     */
    events: {
        'click [class*="orderBy"]':'setOrderBy',
        'mouseenter .rowaction': 'showTooltip',
        'mouseleave .rowaction': 'hideTooltip',
        'mouseenter tr':'showActions',
        'mouseleave tr':'hideActions',
        'mouseenter .ellipsis_inline':'addTooltip',
        'click th.morecol': 'toggleDropdown',
        'click th.morecol li a' : 'toggleColumn'
    },

    defaultLayoutEvents: {
        "list:search:fire": "fireSearch",
        "list:paginate:success": "paginateSuccess",
        "list:filter:toggled": "filterToggled",
        "list:alert:show": "showAlert",
        "list:alert:hide": "hideAlert",
        "list:sort:fire": "sort"
    },

    defaultContextEvents: {},

    // Model being previewed (if any)
    _previewed: null,
    //Store left column fields
    _leftActions: [],
    //Store right column fields
    _rowActions: [],
    //Store default and available(+visible) field names
    _fields: {},

    addTooltip: function(event){
        if (_.isFunction(app.utils.handleTooltip)) {
            app.utils.handleTooltip(event, this);
        }
    },

    initialize: function(options) {
        //Grab the list of fields to display from the main list view (assuming initialize is being called from a subclass)
        var listViewMeta = JSON.parse(JSON.stringify(app.metadata.getView(options.module, 'list') || {}));
        //Extend from an empty object to prevent polution of the base metadata
        options.meta = _.extend({}, listViewMeta, JSON.parse(JSON.stringify(options.meta || {})));
        options.meta.type = options.meta.type || 'list';

        this.parseFields(options.meta);

        app.view.View.prototype.initialize.call(this, options);

        this.fallbackFieldTemplate = 'list-header';
        this.action = 'list';

        this.attachEvents();
        
        //When clicking on eye icon, we need to trigger preview:render with model&collection
        this.context.on("list:preview:fire", function(model) {
            app.events.trigger("preview:render", model, this.collection, true);
        }, this);
        
        //When switching to next/previous record from the preview panel, we need to update the highlighted row
        app.events.on("list:preview:decorate", this.decorateRow, this);
        app.events.on("list:filter:fire", this.filterList, this);

        // Dashboard layout injects shared context with limit: 5. 
        // Otherwise, we don't set so fetches will use max query in config.
        this.limit = this.context.has('limit') ? this.context.get('limit') : null;
    },

    /**
     * Takes the defaultListEventMap and listEventMap and binds the events. This is to allow views that
     * extend ListView to specify their own events.
     */
    attachEvents: function() {
        this.layoutEventsMap = _.extend(this.defaultLayoutEvents, this.layoutEvents); // If undefined nothing will be added.
        this.contextEventsMap = _.extend(this.defaultContextEvents, this.contextEvents);

        if (this.layout) {
            _.each(this.layoutEventsMap, function(callback, event) {
                this.layout.on(event, this[callback], this);
            }, this);
        }

        if (this.context) {
            _.each(this.contextEventsMap, function(callback, event) {
                this.context.on(event, this[callback], this);
            }, this);
        }
    },

    previewRow: function(model) {
        app.events.trigger("preview:render", model, this.collection, true);
    },

    paginateSuccess: function() {
        //When fetching more records, we need to update the preview collection
        app.events.trigger("preview:collection:change", this.collection);
        this.render();
    },

    sort: function() {
        //When sorting the list view, we need to close the preview panel
        app.events.trigger("preview:close");
    },

    /**
     * Parse fields to identificate default and available(+visible) fields
     * @param {Object} view metadata
     * @return {Object} view metadata
     */
    parseFields: function(viewMeta){
        this._fields = {
            "default": [], //Fields visible by default
            "available": {
                "visible": [], //Fields user wants to see
                "all": [] //Fields hidden by default
            }
        };
        // TODO: load field prefs and store names in this._fields.available.visible
        // no prefs so use viewMeta as default and assign hidden fields
        _.each(viewMeta.panels, function(panel){
            _.each(panel.fields, function(fieldMeta, i) {
                if (fieldMeta["default"] === false) {
                    this._fields["available"].all.push(fieldMeta.name);
                } else {
                    this._fields["default"].push(fieldMeta.name);
                }
            }, this);
        }, this);
        return viewMeta;

    },

    filterList: function(filterDef, isNewFilter, scope) {
        var self = this;

        this.collection.fetch({
            relate: !!this.context.get('link'), // Double bang for boolean coercion.
            filter: filterDef,
            success: function() {
                if(isNewFilter) {
                    var method = "update";
                    if(scope.currentFilter === "all_records") {
                        method = "delete";
                    }
                    // We're dealing with a new collection that may not have the current preview record in the collection.
                    // Closing the preview will keep it from getting out of sync
                    app.events.trigger("preview:close");
                    var url = app.api.buildURL('Filters/' + self.options.module + '/used');
                    app.api.call(method, url, {filters: [scope.currentFilter]}, {});
                }
            }
        });
    },
    /**
     * Add actions to left and right columns
     * @param {Object} Backbone options object
     */
    addActions: function(options) {
        this._rowActions = [];
        this._leftActions = [];
        var meta = options.meta;
        if(meta.selection) {
            switch (meta.selection.type) {
                case "single":
                    this.addSingleSelectionAction(options);
                    break;
                case "multi":
                    this.addMultiSelectionAction(options);
                    break;
                default:
                    break;
            }
        }
        if(meta && meta.rowactions) {
            this.addRowActions(options);
        }
    },

    _render:function () {
        this.addActions(this);
        var lastActionColumn =_.last(this._rowActions);
        if (lastActionColumn) lastActionColumn.isColumnDropdown = true;

        app.view.View.prototype._render.call(this);
    },

    showAlert: function(message) {
        this.$(".alert .container").html(message);
        this.$(".alert").removeClass("hide");
    },
    hideAlert: function() {
        this.$(".alert").addClass("hide");
    },
    filterToggled:function (isOpened) {
        this.filterOpened = isOpened;
    },
    fireSearch:function (term) {
        var options = {
            limit:this.limit || null,
            params:{},
            fields:this.collection.fields || {}
        };
        if(term) {
            options.params.q = term;
        }
        //TODO: This should be handled automagically by the collection by checking its own tie to the context
        if (this.context.get('link')) {
            options.relate = true;
        }
        this.collection.fetch(options);
    },

    /**
     * Sets order by on collection and view
     * @param {Object} event jquery event object
     */
    setOrderBy:function (event) {
        var orderMap, collection, fieldName, nOrder, options, eventTarget, orderBy;
        var self = this;
        //set on this obj and not the prototype
        self.orderBy = self.orderBy || {};

        //mapping for css
        orderMap = {
            "desc":"_desc",
            "asc":"_asc"
        };

        //TODO probably need to check if we can sort this field from metadata
        collection = self.collection;
        eventTarget = self.$(event.currentTarget);
        fieldName = eventTarget.data('fieldname');

        // first check if alternate orderby is set for column
        orderBy = eventTarget.data('orderby');
        // if no alternate orderby, use the field name
        if (!orderBy) {
            orderBy = eventTarget.data('fieldname');
        }

        if (!collection.orderBy) {
            collection.orderBy = {
                field:"",
                direction:"",
                columnName:""
            };
        }

        nOrder = "desc";

        // if same field just flip
        if (orderBy === collection.orderBy.field) {
            if (collection.orderBy.direction === "desc") {
                nOrder = "asc";
            }
            collection.orderBy.direction = nOrder;
        } else {
            collection.orderBy.field = orderBy;
            collection.orderBy.direction = "desc";
        }
        collection.orderBy.columnName = fieldName;

        // set it on the view
        self.orderBy.field = orderBy;
        self.orderBy.direction = orderMap[collection.orderBy.direction];
        self.orderBy.columnName = fieldName;

        // Treat as a "sorted search" if the filter is toggled open
        options = self.filterOpened ? self.getSearchOptions() : {};

        // If injected context with a limit (dashboard) then fetch only that
        // amount. Also, add true will make it append to already loaded records.
        options.limit = self.limit || null;
        options.success = function () {
            // Hide loading message
            app.alert.dismiss('loading_' + self.cid);

            self.layout.trigger("list:sort:fire", collection, self);
            self.render();
        };
        if (this.context.get('link')) {
            options.relate = true;
        }

        // Display Loading message
        app.alert.show('loading_' + self.cid, {level:'process', title:app.lang.getAppString('LBL_LOADING')});

        // refetch the collection
        collection.fetch(options);
    },
    getSearchOptions:function () {
        var collection, options, previousTerms, term = '';
        collection = this.context.get('collection');

        // If we've made a previous search for this module grab from cache
        if (app.cache.has('previousTerms')) {
            previousTerms = app.cache.get('previousTerms');
            if (previousTerms) {
                term = previousTerms[this.module];
            }
        }
        // build search-specific options and return
        options = {
            params:{},
            fields:collection.fields ? collection.fields : this.collection
        };
        if (term) {
            options.params.q = term;
        }
        if (this.context.get('link')) {
            options.relate = true;
        }
        return options;
    },
    /**
     * Decorate a row in the list that is being shown in Preview
     * @param model Model for row to be decorated.  Pass a falsy value to clear decoration.
     */
    decorateRow: function(model){
        // If there are drawers, make sure we're updating only list views on active drawer.
        if(_.isUndefined(app.drawer) || app.drawer.isActive(this.$el)){
            this._previewed = model;
            this.$("tr.highlighted").removeClass("highlighted current above below");
            if(model){
                var rowName = model.module+"_"+ model.get("id");
                var curr = this.$("tr[name='"+rowName+"']");
                curr.addClass("current highlighted");
                curr.prev("tr").addClass("highlighted above");
                curr.next("tr").addClass("highlighted below");
            }
        }
    },
    /**
     * Add single selection field to left column
     * @param {Object} Backbone options object
     */
    addSingleSelectionAction: function(options) {
        var _generateMeta = function (name, label) {
            return {
                    'type':'selection',
                    'name': name,
                    'sortable':false,
                    'label': label || ''
                };
        }
        var def = options.meta.selection;
        this._leftActions[0] = _generateMeta(def.name || options.module + '_select', def.label);
    },
    /**
     * Add multi selection field to left column
     * @param {Object} Backbone options object
     */
    addMultiSelectionAction: function(options) {
        var _generateMeta = function (buttons) {
            return {
                    'type':'fieldset',
                    'fields':[
                        {
                            'type':'actionmenu',
                            'buttons': buttons || []
                        }
                    ],
                    'value':false,
                    'sortable':false
                };
        }
        var buttons = options.meta.selection.actions;
        this._leftActions[0] = _generateMeta(buttons);
    },
    /**
     * Add fieldset of rowactions to the right column
     * @param {Object} Backbone options object
     */
    addRowActions: function(options) {
        var _generateMeta = function (label, css_class, buttons) {
            return {
                    'type':'fieldset',
                    'fields':[
                        {
                            'type':'rowactions',
                            'label':label || '',
                            'css_class':css_class,
                            'buttons': buttons || []
                        }
                    ],
                    'value':false,
                    'sortable':false
                };
        }
        var def = options.meta.rowactions;
        this._rowActions[0] =  _generateMeta(def.label, def.css_class, def.actions);
    },
    /**
     * Manually toggle the dropdown on cell click
     * @param {Object}(optional) event jquery event object
     */
    toggleDropdown: function(event) {
        var self = this;
        var $dropdown = self.$('.morecol > div');
        if ($dropdown.length > 0 && _.isFunction($dropdown.dropdown)) {
            $dropdown.toggleClass('open');
            if (event) event.stopPropagation();
            $('html').one('click', function () {
                $dropdown.removeClass('open');
            });
        }
    },
    /**
     * Toggle the 'visible' state of an available field
     * @param {Object} event jquery event object
     */
    toggleColumn: function(event) {
        if (!event) return;
        event.stopPropagation();

        var $li = this.$(event.currentTarget).closest('li'),
            column = $li.data('fieldname');

        if (_.indexOf(this._fields.available.visible, column) !== -1) {
            this._fields.available.visible = _.without(this._fields.available.visible, column);
        }
        else {
            this._fields.available.visible.push(column);
        }
        this.render();
        this.toggleDropdown();
    },
    showTooltip: function(e) {
        this.$(e.currentTarget).tooltip("show");
    },
    hideTooltip: function(e) {
        this.$(e.currentTarget).tooltip("hide");
    },
    showActions:function (e) {
        $(e.currentTarget).children("td").children("span").children(".btn-group").show();
    },
    hideActions:function (e) {
        $(e.currentTarget).children("td").children("span").children(".btn-group").hide();
    },
    bindDataChange:function () {
        if (this.collection) {
            this.collection.on("reset", this.render, this);
        }
    }
})
