/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/

({

        /**
         * Saved Labels to use for the Breadcrumbs
         */
        breadCrumbLabels: [],

        initialize: function (options) {
            var modelUrl = app.api.buildURL("Forecasts", "config"),
                modelSync = function(method, model, options) {
                    var url = _.isFunction(model.url) ? model.url() : model.url;
                    return app.api.call(method, url, model, options);
                };

            options.context.set("model", this._getConfigModel(options, modelUrl, modelSync));

            app.view.Layout.prototype.initialize.call(this, options);
        },

    /**
     * Gets a config model for the config settings dialog.
     *
     * If we're using this layout from inside the Forecasts module and forecasts already has a config model, config
     * will use that config model as our current context so we're updating a clone of the same model.
     * The clone facilitates not saving to a "live" model, so if a user hits cancel, the values will go back to the
     * correct setting the next time the admin panel is accessed.
     *
     * If we're not coming in from the Forecasts module (e.g. Admin)
     * creates a new model and config will use that to change/save
     * @return {Object} the model for config
     */
    _getConfigModel: function(options, syncUrl, syncFunction) {
        var settingsModel = {};
        if(_.has(options.context,'forecasts') && _.has(options.context.forecasts,'config') ) {
            // jQuery.extend is used with the `true` parameter to do a deep copy
            settingsModel = new (Backbone.Model.extend({
                defaults: $.extend(true, {}, options.context.forecasts.config.attributes),
                url: syncUrl,
                sync: syncFunction
            }))();
        } else {
            // if we're not coming in from the Forecasts module (e.g. Admin)
            // create a new model and use that to change/save
            settingsModel = new (Backbone.Model.extend({
                url: syncUrl,
                sync: syncFunction
            }))();
            settingsModel.fetch();
        }
        return settingsModel;
    },

        /**
         * Register a new breadcrumb label
         *
         * @param {string} label
         */
        registerBreadCrumbLabel : function(label) {
            var labelObj = {
                    'index': this.breadCrumbLabels.length,
                    'label': label
                },
                found = false;
            _.each(this.breadCrumbLabels, function(crumb) {
                if(crumb.label == label) {
                    found = true;
                }
            })
            if(!found) {
                this.breadCrumbLabels.push(labelObj);
            }
        },

        /**
         * Get the current registered breadcrumb labels
         *
         * @return {*}
         */
        getBreadCrumbLabels : function(){
            return this.breadCrumbLabels;
        },

        /**
         * override the render method
         * @private
         */
        _render : function() {
            app.view.Layout.prototype._render.call(this);

            // fix the display since we are using the same views as the Wizard
            this.$el.find('.modal-content:first').toggleClass('hide show');
            this.$el.find('.modal-navigation li:first').addClass('active');
        }
})
