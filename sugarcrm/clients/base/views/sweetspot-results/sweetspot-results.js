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
 * @class View.Views.Base.SweetspotResultsView
 * @alias SUGAR.App.view.views.BaseSweetspotResultsView
 * @extends View.View
 */
({
    className: 'sweetspot-results',
    tagName: 'ul',

    /**
     * @inheritDoc
     *
     * - Listens to `sweetspot:results` on the layout to update the results.
     * - Listens to `keydown` on `window` to highlight an item.
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        /**
         * The list of results displayed.
         *
         * @type {Array}
         */
        this.results = [];

        /**
         * Stores the index of the currently highlighted list element.
         * This is used for keyboard navigation.
         *
         * @property {number}
         */
        this.activeIndex = null;

        this.layout.on('sweetspot:results', function(results) {
            this.results = this._formatResults(results);
            this.render();
        }, this);

        this.layout.on('sweetspot:status', this.toggleCallback, this);
    },

    /**
     * @inheritDoc
     */
    _render: function() {
        this._super('_render');
        this.activeIndex = 0;
        this._highlightActive();
    },

    /**
     * Formats the {@link #results} to:
     * -include labels if none are present by default.
     *
     * @param {Array} results The list of actions/commands.
     * @return {Array} The formatted list of actions/commands.
     */
    _formatResults: function(results) {
        if (_.isEmpty(results)) {
            return results;
        }
        _.each(results, function(item) {
            if (!item.label) {
                item.label = item.name.substr(0, 2);
            }
        });
        return results;
    },

    toggleCallback: function(isOpen) {
        if (isOpen) {
            $(window).on('keydown.' + this.cid, _.bind(this.keydownHandler, this));
        } else {
            $(window).off('keydown.' + this.cid);
        }
    },

    /**
     * Handle the keydown events.
     * @param {event} e The `keydown` event.
     */
    keydownHandler: function(e) {
        switch (e.keyCode) {
            case 13: // enter
                this.triggerAction();
                break;
            case 40: // down arrow
                this.moveForward();
                break;
            case 38: // up arrow
                this.moveBackward();
                break;
        }
    },

    /**
     * Triggers the action linked to the active element.
     *
     * Navigates to the view or calls the callback method.
     */
    triggerAction: function() {
        this.layout.toggle();
        var route = this.$('li.active > a').attr('href');
        if (route) {
            app.router.navigate(route, {trigger: true});
        }
        var action = this.$('a.hover').data('callback');
        if (action) {
            this.layout.triggerSystemAction(action);
        }
    },

    /**
     * Highlight the active element and unhighlight the rest of the elements.
     */
    _highlightActive: function() {
        this.$('.active').removeClass('active');
        var nthChild = this.activeIndex + 1;
        this.$('li:nth-child(' + nthChild + ')')
            .addClass('active');
    },

    /**
     * Moves to the next the active element.
     */
    moveForward: function() {
        // check to make sure we will be in bounds.
        this.activeIndex++;
        if (this.activeIndex < this.results.length) {
            // We're in bounds, just go to the next element in this view.
            this._highlightActive();
        } else {
            this.activeIndex = 0;
            this._highlightActive();

        }
    },

    /**
     * Moves to the previous the active element.
     */
    moveBackward: function() {
        // check to make sure we will be in bounds.
        if (this.activeIndex > 0) {
            // We're in bounds, just go to the previous element in this view
            this.activeIndex--;
            this._highlightActive();
        } else {
            this.activeIndex = this.results.length-1;
            this._highlightActive();
        }
    },

    /**
     * @inheritDoc
     */
    _dispose: function() {
        $(window).off('keydown.' + this.cid);
        this._super('_dispose');
    }
})
