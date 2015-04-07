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
 * @class View.Layouts.Base.SweetspotLayout
 * @alias SUGAR.App.view.layouts.BaseSweetspotLayout
 * @extends View.Layout
 */
({
    /**
     * @inheritDoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        app.shortcuts.register(app.shortcuts.GLOBAL + 'Sweetspot', 'shift+space', this.toggle, this, true);
        app.events.on('app:logout', this.hide, this);

        this.on('sweetspot:config', this.openConfigPanel, this);

        /**
         * Flag to indicate the visible state of the sweet spot.
         *
         * @type {boolean}
         * @private
         */
        this._isVisible = false;
    },

    /**
     * @inheritDoc
     */
    _render: function() {
        if (!app.api.isAuthenticated()) {
            return;
        }
        this._super('_render');
    },

    /**
     * Binds the `esc` keydown event.
     */
    bindEsc: function() {
        $(document).on('keydown.' + this.cid, _.bind(function(evt) {
            if (evt.keyCode == 27) {
                this.hide();
            }
        }, this));
    },

    /**
     * Unbinds the `esc` keydown event.
     */
    unbindEsc: function() {
        $(document).off('keydown.' + this.cid);
    },

    /**
     * @override
     */
    isVisible: function() {
        return this._isVisible;
    },

    /**
     * @override
     */
    show: function() {
        if (this.isVisible()) {
            return;
        }
        if (!this.triggerBefore('show')) {
            return false;
        }
        this._isVisible = true;
        this.$('input').val('');
        this.$el.fadeToggle(50, 'linear', _.bind(this.focusInput, this));
        this.trigger('show');
        this.bindEsc();
    },

    /**
     * @override
     */
    hide: function() {
        if (!this.isVisible()) {
            return;
        }
        if (!this.triggerBefore('hide')) {
            return false;
        }

        this._isVisible = false;
        this.unbindEsc();
        this.$el.fadeToggle(50, 'linear');
        this.trigger('hide');
},
    /**
     * Toggles the Sweet Spot.
     */
    toggle: function() {
        if (this.isVisible()) {
            this.hide();
        } else {
            this.show();
        }
    },

    /**
     * Focuses on the Sweet Spot input.
     */
    focusInput: function() {
        this.$('input').focus();
    },

    /**
     * Opens a drawer with the {@link View.Layouts.Base.SweetspotConfigLayout}
     * to configure the Sweet Spot.
     */
    openConfigPanel: function() {
        // TODO: This is bad and there should be an option in drawer.js to
        // prevent opening an already-open drawer of the same type.
        var activeDrawerLayout = app.drawer.getActiveDrawerLayout();
        if (activeDrawerLayout.type === 'sweetspot-config') {
            return;
        }

        app.drawer.open({
            layout: 'sweetspot-config',
            context: {
                skipFetch: true,
                forceNew: true
            }
        });
    },

    /**
     * Trigger a system action.
     *
     * @param {string} method Name of the method in {@link #_systemActions}.
     */
    triggerSystemAction: function(method) {
        if (!_.isFunction(this._systemActions[method])) {
            return;
        }
        this._systemActions[method].call(this);
    },

    /**
     * List of system action callbacks.
     *
     * Use {@link #triggerSystemAction} to trigger them.
     */
    _systemActions: {
        toggleHelp: function() {
            app.events.trigger('app:help');
        }
    },

    /**
     * @inheritDoc
     */
    _dispose: function() {
        this.unbindEsc();
        this._super('_dispose');
    }
})
