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
(function(app) {
    app.events.on('app:init', function() {
        app.plugins.register('MergeDuplicates', ['view'], {

            /**
             * Minimum number of records for merging.
             *
             * @property
             */
            _minRecordsToMerge: 2,

            /**
             * Maximum number of records for merging.
             *
             * @property
             */
            _maxRecordsToMerge: 5,

            /**
             * Merge records handler.
             *
             * @param {Backbone.Collection} mergeCollection Set of merging records.
             * @param {Data.Bean} primaryRecord (optional) Default Primary Model.
             */
            mergeDuplicates: function(mergeCollection, primaryRecord) {
                if (_.isEmpty(mergeCollection)) {
                    return;
                }

                if (!_.isEmpty(primaryRecord)) {
                    mergeCollection.add(primaryRecord, {at: 0, silent: true});
                }

                var models = this._validateModelsForMerge(mergeCollection.models);

                if (!this.triggerBefore('mergeduplicates', models)) {
                    return;
                }

                if (this._validateAcl(models, mergeCollection) === false) {
                    this._showAclAlert(models, mergeCollection, primaryRecord);
                    return;
                }

                if (this._validateIsAnyEditable(models) === false) {
                    this._showNoEditAlert();
                    return;
                }

                if (this._validateSize(models) === false) {
                    this._showSizeAlert();
                    return;
                }

                this._openMergeDrawer(models, mergeCollection, primaryRecord);
            },

            /**
             * Check if user is allowed to merge all chosen models.
             *
             * @param {Data.Bean[]} models Models with access.
             * @param {Data.Collection} collection Merge Collection to check access for merge.
             * @return {Boolean} `true` only if there is access to all models, otherwise `false`.
             * @protected
             */
            _validateAcl: function(models, collection) {
                return models.length === collection.models.length;
            },

            /**
             * Display acl validation error message in alert.
             * Set up handler on confirm to continue validation and show drawer.
             *
             * @param {Data.Bean[]} models Models with access.
             * @param {Data.Collection} collection Merge Collection to check access for merge.
             * @param {Daba.Bean} primary (optional) Default Primary Model.
             * @protected
             */
            _showAclAlert: function(models, collection, primary) {
                var self = this;
                app.alert.show('invalid-record-access', {
                    level: 'confirmation',
                    messages: app.lang.get('LBL_MERGE_NO_ACCESS_TO_A_FEW_RECORDS', this.module),
                    onConfirm: function() {
                        if (self._validateIsAnyEditable(models) === false) {
                            self._showNoEditAlert();
                            return;
                        }
                        if (self._validateSize(models) === false) {
                            self._showSizeAlert();
                            return;
                        }
                        self._openMergeDrawer(models, collection, primary);
                    }
                });
            },

            /**
             * Check if there is at least one editable model.
             *
             * @param {Array} models Array of merging record set.
             * @return {Boolean} `true` if there is at least one editable model, `false` otherwise.
             * @protected
             */
            _validateIsAnyEditable: function(models) {
                return _.some(models, function(model) {
                    return app.acl.hasAccessToModel('edit', model);
                });
            },

            /**
             * Display error message when there are no editable records.
             * @protected
             */
            _showNoEditAlert: function() {
                var msg = app.lang.get('LBL_MERGE_NO_ACCESS', this.module);
                app.alert.show('no-record-to-edit', {
                    level: 'error',
                    messages: msg,
                    autoClose: true
                });
            },

            /**
             * Check if the total of chosen models is within the predefined limits of records to merge.
             *
             * @param {Array} models Array of merging record set.
             * @return {Boolean} `true` only if it contains valid size of collection, `false` otherwise.
             * @protected
             */
            _validateSize: function(models) {
                var isValidSize = models.length && models.length >= this._minRecordsToMerge &&
                    models.length <= this._maxRecordsToMerge;

                return isValidSize;
            },

            /**
             * Display error message when range of selected records is incorrect.
             *
             * @protected
             */
            _showSizeAlert: function() {
                var msg = app.lang.get('TPL_MERGE_INVALID_NUMBER_RECORDS',
                    this.module,
                    {
                        minRecords: this._minRecordsToMerge,
                        maxRecords: this._maxRecordsToMerge
                    }
                );
                app.alert.show('invalid-record-count', {
                    level: 'error',
                    messages: msg
                });
            },

            /**
             * Open drawer with merge duplicate view.
             *
             * @param {Array} models Models with access.
             * @param {Data.Collection} collection Collection of beans to merge.
             * @param {Data.Bean} primary (Optional) Default Primary Model.
             * @protected
             */
            _openMergeDrawer: function(models, collection, primary) {

                var primaryId = (primary && primary.id) || null;

                app.drawer.open({
                    layout: 'merge-duplicates',
                    context: {
                        primary: primary || null,
                        selectedDuplicates: models
                    }
                }, _.bind(function(refresh, primary) {
                    if (refresh) {
                        this.trigger('mergeduplicates:complete', primary);
                        collection.reset();
                    } else {
                        collection.remove(primaryId);
                    }
                }, this));
            },

            /**
             * Check access for models selected for merge.
             *
             * @param {Data.Bean[]} models Array of merging record set.
             * @return {Data.Bean[]} Models with access.
             * @protected
             */
            _validateModelsForMerge: function(models) {
                return _.filter(models, function(model) {
                    return _.every(['view', 'delete'], function(acl) {
                        return app.acl.hasAccessToModel(acl, model);
                    });
                }, this);
            }
        });
    });
})(SUGAR.App);
