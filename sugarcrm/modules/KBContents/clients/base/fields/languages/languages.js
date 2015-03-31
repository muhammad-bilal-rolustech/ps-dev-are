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

({
    extendsFrom: 'FieldSet',

    events: {
        'click .btn[data-action=add-field]': 'addItem',
        'click .btn[data-action=remove-field]': 'removeItem',
        'click .btn[data-action=set-primary-field]': 'setPrimaryItem'
    },

    intKey: null,

    deletedLanguages: [],

    plugins: ['Tooltip'],

    /**
     * {@inheritDoc}
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._currentIndex = 0;
        this.model.unset('deleted_languages', {silent: true});
    },

    /**
     * {@inheritDoc}
     */
    format: function(value) {
        var result = [],
            numItems = 0;
        value = app.utils.deepCopy(value);

        if (_.isString(value)) {
            value = [{'': value, primary: false}];
        }

        // Place the add button as needed
        if (_.isArray(value) && value.length > 0) {
            _.each(value, function(item, ind) {
                delete item.remove_button;
                delete item.add_button;
                result[ind] = {
                    name: this.name,
                    primary: item.primary || false
                };
                delete item.primary;
                result[ind].items = item;
            }, this);
            if (!result[this._currentIndex]) {
                result[this._currentIndex] = {};
            }
            result[value.length - 1].add_button = true;
            // number of valid teams
            numItems = _.filter(result, function(item) {
                return _.isUndefined(item.items['']);
            }).length;
            // Show remove button for all unset combos and only set combos if there are more than one
            _.each(result, function(item) {
                if (!_.isUndefined(item.items['']) || numItems > 1) {
                    item.remove_button = true;
                }
            });
        }
        return result;
    },

    /**
     * {@inheritDoc}
     */
    unformat: function(value) {
        var result = [];
        _.each(value, function(item) {
            result.push(_.extend({}, item.items, {primary: item.primary}));
        }, this);
        return result;
    },

    /**
     * Set primary item.
     * @param {number} index
     * @return {boolean}
     */
    setPrimary: function(index) {
        var value = this.unformat(this.value);
        _.each(value, function(item) {
            item.primary = false;
        }, this);
        value[index].primary = true;
        this.model.set(this.name, value);
        return (this.value[index]) ? this.value[index].primary : false;
    },

    /**
     * {@inheritDoc}
     */
    bindDomChange: function() {
        var self = this,
            el = null;
        if (this.model) {
            el = this.$el.find('div[data-name=languages_' + this.name + '] input[type=text]');
            el.on('change', function() {
                var value = self.unformatValue();
                self.model.set(self.name, value, {silent: true});
                self.value = self.format(value);
            });
        }
    },

    /**
     * {@inheritDoc}
     */
    bindDataChange: function() {
        if (this.model) {
            this.model.on('change', function() {
                if (this.disposed) {
                    return;
                }
                this.render();
            }, this);
        }
    },

    /**
     * Get value from view data.
     * @return [{}]
     */
    unformatValue: function() {
        var container = $(this.$('div[data-name=languages_' + this.name + ']')),
            input = container.find('input[type=text]'),
            value = [],
            val,
            k,
            v,
            pr,
            i;
        for (i = 0; i < input.length / 2; i = i + 1) {
            val = {};
            k = container.find('input[data-index=' + i + '][name=key_' + this.name + ']').val();
            v = container.find('input[data-index=' + i + '][name=value_' + this.name + ']').val();
            pr = container.find('button[data-index=' + i + '][name=primary]').hasClass('active');

            val[k] = v;
            val.primary = pr;
            value.push(val);
        }
        return value;
    },

    /**
     * Add item to list.
     * @param {Event} evt DOM event.
     */
    addItem: function(evt) {
        var index = $(evt.currentTarget).data('index'),
            value = this.unformat(this.value);
        if (!index || _.isUndefined(this.value[this.value.length - 1].items[''])) {
            value.push({'': ''});
            this._currentIndex += 1;
            this.model.set(this.name, value);
        }
    },

    /**
     * Remove item from list.
     * @param {Event} evt DOM event.
     */
    removeItem: function(evt) {
        this._currentTarget = evt.currentTarget;
        this.warnDelete();
    },

    /**
     * Popup dialog message to confirm delete action.
     */
    warnDelete: function() {
        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_DELETE_CONFIRMATION_LANGUAGE', this.module),
            onConfirm: _.bind(this.confirmDelete, this),
            onCancel: _.bind(this.cancelDelete, this)
        });
    },

    /**
     * Predefined function for confirm delete.
     */
    confirmDelete: function() {
        var index = $(this._currentTarget).data('index'),
            value = null,
            removed = null;

        if (_.isNumber(index)) {
            if (index === 0 && this.value.length === 1) {
                return;
            }
            if (this._currentIndex === this.value.length - 1) {
                this._currentIndex -= 1;
            }

            value = this.unformat(this.value);
            removed = value.splice(index, 1);
            if (removed && removed.length > 0 && removed[0].primary) {
                value[0].primary = this.setPrimary(0);
            }
            for (var key in removed[0]) {
                if (key !== 'primary' && 2 == key.length) {
                    if (-1 === this.deletedLanguages.indexOf(key)) {
                        this.deletedLanguages.push(key);
                    }
                }
            }
            if (value) {
                this.model.set(this.name, value);
            }

            if (_.size(this.deletedLanguages) > 0) {
                this.model.set({'deleted_languages': this.deletedLanguages}, {silent: true});
            }
        }
    },

    /**
     * Predefined function for cancel delete.
     * @param {Event} evt DOM event.
     */
    cancelDelete: function(evt) {
    },

    /**
     * Set primary item.
     * @param {Event} evt DOM event.
     */
    setPrimaryItem: function(evt) {
        var index = $(evt.currentTarget).data('index');

        if (!this.value[index] ||
            !_.isUndefined(this.value[index].items['']) ||
            $(evt.currentTarget).hasClass('active')) {
            return;
        }
        this.$('.btn[name=primary]').removeClass('active');
        if (this.setPrimary(index)) {
            this.$('.btn[name=primary][data-index=' + index + ']').addClass('active');
        }
    },

    /**
     * {@inheritDoc}
     */
    _dispose: function() {
        this.$el.off();
        this.model.off('change');
        this._super('_dispose');
    }
})