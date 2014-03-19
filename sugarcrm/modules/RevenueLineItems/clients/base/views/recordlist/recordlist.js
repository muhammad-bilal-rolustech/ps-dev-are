//FILE SUGARCRM flav=pro ONLY
/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2014 SugarCRM Inc. All rights reserved.
 */
({
    extendsFrom : 'RecordlistView',

    initialize: function(options) {
        this._super("initialize", [options]);
        this.layout.on("list:record:deleted", function(deletedModel){
            this.deleteCommitWarning(deletedModel);
        }, this);

        this.before('mergeduplicates', this._checkMergeModels, undefined, this);
    },

    /**
     * Event handler to make sure that before the merge drawer shows, make sure that all the models contain the first
     * records opportunity_id
     *
     * @param {Array} mergeModels
     * @returns {boolean}
     * @private
     */
    _checkMergeModels: function(mergeModels) {
        var primaryRecordOppId = _.first(mergeModels).get('opportunity_id');
        var invalid_models = _.find(mergeModels, function(model) {
            return !_.isEqual(model.get('opportunity_id'), primaryRecordOppId);
        });

        if (!_.isUndefined(invalid_models)) {
            app.alert.show("merge_duplicates_different_opps_warning", {
                level: "warning",
                messages: app.lang.get('WARNING_MERGE_RLIS_WITH_DIFFERENT_OPPORTUNITIES', this.module)
            });
            return false;
        }

        return true;
    },

    /**
     * @inheritDoc
     *
     * Augment to remove the fields that should not be displayed.
     */
    _createCatalog: function(fields) {
        var forecastConfig = app.metadata.getModule('Forecasts', 'config');

        if (forecastConfig.is_setup) {
            fields = _.filter(fields, function(fieldMeta) {
                if (fieldMeta.name.indexOf('_case') !== -1) {
                    var field = 'show_worksheet_' + fieldMeta.name.replace('_case', '');
                    return (forecastConfig[field] == 1);
                }
                return true;
            });
        } else {
            // Forecast is not setup
            fields = _.reject(fields, function(fieldMeta) {
                return (fieldMeta.name === 'commit_stage');
            });
        }

        var catalog = this._super('_createCatalog', [fields]);
        return catalog;
    },

    /**
     * Shows a warning message if a RLI that is included in a forecast is deleted.
     * @return string message
     */
    deleteCommitWarning: function(deletedModel) {
        var message = null;
        
        if (deletedModel.get("commit_stage") == "include") {
            var forecastModuleSingular = app.lang.get('LBL_MODULE_NAME_SINGULAR', 'Forecasts');
            message = app.lang.get("WARNING_DELETED_RECORD_RECOMMIT_1", "RevenueLineItems")
                + '<a href="#Forecasts">' + forecastModuleSingular + '</a>.  '
                + app.lang.get("WARNING_DELETED_RECORD_RECOMMIT_2", "RevenueLineItems")
                + '<a href="#Forecasts">' + forecastModuleSingular + '</a>.';
            app.alert.show("included_list_delete_warning", {
                level: "warning",
                messages: message,
                onLinkClick: function() {
                    app.alert.dismissAll();
                }
            });
        }
        
        return message;
    }
})

