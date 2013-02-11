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
    extendsFrom: 'IntField',

    events: {
        'mouseenter span.editable': 'togglePencil',
        'mouseleave span.editable': 'togglePencil',
        'click span.editable': 'onClick',
        'blur span.edit input': 'onBlur',
        'keyup span.edit input': 'onKeyup'
    },

    inputSelector: 'span.edit input',

    errorMessage: '',

    isErrorState: false,

    _canEdit: true,

    initialize: function (options) {
        app.view.fields.IntField.prototype.initialize.call(this, options);
        this.checkIfCanEdit();
    },

    /**
     * Utility Method to check if we can edit again.
     */
    checkIfCanEdit: function() {
        var selectedUser = this.context.forecasts.get('selectedUser');
        if (!_.isUndefined(this.context.forecasts) && !_.isUndefined(this.context.forecasts.config)) {
            this._canEdit = _.isEqual(app.user.get('id'), selectedUser.id) && !_.contains(
                // join the two variable together from the config
                this.context.forecasts.config.get("sales_stage_won").concat(
                    this.context.forecasts.config.get("sales_stage_lost")
                ), this.model.get('sales_stage'));
        }
    },

    /**
     * Overwrite bindDomChange
     *
     * Since we need to do custom logic when a field changes, we have to overwrite this with out ever calling
     * the parent.
     *
     */
    bindDomChange: function () {
        // override parent, do nothing
    },

    /**
     * Only one CTE field can be open/active at a time.
     * When a CTE field is clicked, it sends a message to inform the others.
     * If another field is open with an error, it sends a message
     * and any other open fields will immediately close. This keeps
     * other fields from opening while an errored field is active.
     */
    bindDataChange: function () {
        var self = this;
        self.context.on('field:editable:open', function() {
            // another CTE field has been opened
            if(self.isErrorState) {
                // I am open with an error, send the message
                self.context.trigger('field:editable:error', self.cid);
            }
        }, self);
        self.context.on('field:editable:error', function(cid) {
            if (!_.isEqual(cid, self.cid) && this.options.viewName == 'edit') {
                // some other field is open with an error, close myself
                self.renderDetail();
            }
        }, self);
    },

    /**
     * handle click/blur/keypress events in one place
     *
     * @param {Object} evt
     * @return {Boolean}
     */
    handleEvent: function (evt) {
        if(!_.isObject(evt)
            || this.options.viewName != 'edit'
            || !this.isEditable()
            || !(this.model instanceof Backbone.Model)) {
            return false;
        }
        var self = this;
        var el = this.$el.find(self.fieldTag);
        if(!_.isEqual(self.$el.find(self.inputSelector).val(), self.model.get(this.name))) {
            var value = self.parsePercentage(self.$el.find(self.inputSelector).val()),
                errorObj = self.isValid(value);
            if (!_.isObject(errorObj)) {
                self.model.set(self.name, self.unformat(value));
                self.renderDetail();
            } else {
                // render error
                self.isErrorState = true;
                var hb = Handlebars.compile("{{str_format key module args}}");
                self.errorMessage = hb({'key' : errorObj.labelId, 'module' : 'Forecasts', 'args' : errorObj.args});
                self.showErrors();
                self.$el.find(self.inputSelector).focus().select();
            }
            // Focus doesn't always change when tabbing through inputs on IE9 (Bug54717)
            // This prevents change events from being fired appropriately on IE9
            if ($.browser.msie && el.is("input")) {
                el.on("input", function () {
                    // Set focus on input element receiving user input
                    el.focus();
                });
            }
        } else {
            self.renderDetail();
        }
        return true;
    },

    /**
     * renders the detail view
     */
    renderDetail: function () {
        this.isErrorState = false;
        this.options.viewName = 'detail';
        this.render();
    },

    /**
     * Toggles the pencil icon on and off depending on the mouse state
     *
     * @param evt
     */
    togglePencil: function (evt) {
        evt.preventDefault();
        if (!this.isEditable()) return;
        if(evt.type == 'mouseenter') {
            this.$el.find('.edit-icon').removeClass('hide');
            this.$el.find('.edit-icon').addClass('show');
        } else {
            this.$el.find('.edit-icon').removeClass('show');
            this.$el.find('.edit-icon').addClass('hide');
        }
    },

    /**
     * Switch the view to the Edit view if the field is editable and it's clicked on
     * @param evt
     */
    onClick : function(evt) {
        evt.preventDefault();
        if (!this.isEditable()) return;

        this.options.viewName = 'edit';
        this.render();

        // put the focus on the input
        this.$el.find(this.inputSelector).focus().select();

        // inform other fields that I am opening
        this.context.trigger('field:editable:open');
    },

    /**
     * Handle when return/enter and tab keys are pressed
     *
     * @param evt
     */
    onKeyup: function (evt) {
        evt.preventDefault();
        if (evt.which == 27) {
            // esc key, cancel edits
            this.cancelEdits(evt);
        } else if (evt.which == 13 || evt.which == 9) {
            // enter or tab, handle event
            this.handleEvent(evt);
        }
    },

    /**
     * reset value to model and view detail template
     *
     * evt {Object}
     */
    cancelEdits: function(evt) {
        this.$el.find(this.inputSelector).val(this.value);
        this.renderDetail();
    },

    /**
     * Blur event handler
     *
     * This forces the field to re-render as the DetailView
     *
     * @param evt
     */
    onBlur : function(evt) {
        evt.preventDefault();
        this.handleEvent(evt);
    },

    /**
     * Is the new value valid for this field.
     *
     * @param value
     * @return {Boolean|String} true, or error id on error
     */
    isValid: function (value) {
        var regex = new RegExp("^[+-]?\\d+$");

        // always make sure that we have a string here, since match only works on strings
        if (_.isNull(value.toString().match(regex))) {
            return {'labelId': 'LBL_EDITABLE_INVALID', 'args': [app.lang.get(this.def.label,'Forecasts')]};
        }

        // we have digits, lets make sure it's int a valid range is one is specified
        if (!_.isUndefined(this.def.minValue) && !_.isUndefined(this.def.maxValue)) {
            // we have a min and max value
            if(value < this.def.minValue || value > this.def.maxValue) {
                return {'labelId': 'LBL_EDITABLE_INVALID_RANGE', 'args': [this.def.minValue, this.def.maxValue]};
            }
        }

        // the value passed all validation, return true
        return true;
    },

    /**
     * Can we edit this?
     *
     * @return {boolean}
     */
    isEditable: function () {
        return this._canEdit;
    },

    /**
     * Check the value to see if it's a percentage, if it is, then figure out the change.
     *
     * @param value
     * @return {*}
     */
    parsePercentage : function(value) {
        var orig = this.value;
        var parts = value.toString().match(/^([+-]?)(\d+(\.\d+)?)\%$/);
        if(parts) {
            // use original number to apply calculations
            value = app.math.mul(app.math.div(parts[2],100),orig);
            if(parts[1] == '+') {
                value = app.math.add(orig,value);
            } else if(parts[1] == '-') {
                value = app.math.sub(orig,value);
            }
            // we round to nearest integer for this field type
            value = app.math.round(value, 0);
        }
        return value;
    },

    /**
     * Method to show the error message
     */
    showErrors : function() {
        var self = this;
        // attach error styles
        this.$el.find('.error-message').html(this.errorMessage);
        this.$el.find('.control-group').addClass('error');
        this.$el.find('.help-inline.editable-error').removeClass('hide').addClass('show');
        // make error message button cancel edits
        this.$el.find('.btn.btn-danger').on("click", function(evt) {
            self.cancelEdits.call(self, evt);
        });
    }

})