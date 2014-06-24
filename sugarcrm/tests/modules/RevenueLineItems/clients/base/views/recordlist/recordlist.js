//FILE SUGARCRM flav=pro ONLY
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
describe("RevenueLineItems.Base.Views.RecordList", function() {
    var app, view, options, context, layout, message;

    beforeEach(function() {
        app = SugarTest.app;
        context = app.context.getContext();
        context.set({
            module: 'RevenueLineItems'
        });
        context.prepare();
        
        options = {
            meta: {
                panels: [{
                    fields: [{
                        name: "commit_stage"
                    },{
                        name: "best_case"
                    },{
                        name: "likely_case"
                    },{
                        name: "name"
                    }]
                }]
            }
        };

        
        SugarTest.testMetadata.init();
        SugarTest.loadComponent('base', 'view', 'list');
        SugarTest.loadComponent('base', 'view', 'flex-list');
        SugarTest.loadComponent('base', 'view', 'recordlist');
        SugarTest.testMetadata.set();
        
        SugarTest.seedMetadata(true);
        app.metadata.getModule("Forecasts", "config").is_setup = 1;
        layout = SugarTest.createLayout("base", "RevenueLineItems", "list", null, null);
        
    });
    
    afterEach(function() {
        app.metadata.getModule("Forecasts", "config").show_worksheet_best = null;
        view.dispose();
        layout.dispose();
    });

    it("should not contain best_case field", function() {
        app.metadata.getModule("Forecasts", "config").show_worksheet_best = 0;
        view = SugarTest.createView('base', 'RevenueLineItems', 'recordlist', options.meta, context, true, layout);
        expect(view._fields.visible.length).toEqual(3);
        _.each(view._fields.visible, function(field) {
            expect(field.name).not.toEqual('best_case');
        });
    });

    it("should not contain commit_stage field", function() {
        app.metadata.getModule("Forecasts", "config").is_setup = 0;
        view = SugarTest.createView('base', 'RevenueLineItems', 'recordlist', options.meta, context, true, layout);
        expect(view._fields.visible.length).toEqual(3);
        _.each(view._fields.visible, function(field) {
            expect(field.name).not.toEqual('commit_stage');
        });
    });
    
    describe("when deleteCommitWarning is called", function() {
        var model;
        beforeEach(function() {
            message = null;
            model = new Backbone.Model({
                id: "aaa",
                name: "boo",
                module: "RevenueLineItems"
            });
            view = SugarTest.createView('base', 'RevenueLineItems', 'recordlist', options.meta, context, true, layout);
        });
        
        afterEach(function() {
            model = null;
        });
        
        it("should should return WARNING_DELETED_RECORD_RECOMMIT_1 and _2 combined when commit_stage = include", function() {
            model.set("commit_stage", "include");
            message = view.deleteCommitWarning(model);
            expect(message).toEqual('WARNING_DELETED_RECORD_RECOMMIT_1<a href="#Forecasts">LBL_MODULE_NAME_SINGULAR</a>.  WARNING_DELETED_RECORD_RECOMMIT_2<a href="#Forecasts">LBL_MODULE_NAME_SINGULAR</a>.');
        });
        
        it("should should return NULL when commit_stage != include", function() {
            model.commit_stage = "exclude";
            message = view.deleteCommitWarning(model);
            expect(message).toEqual(null);
        });
    });

    describe('_checkMergeModels', function() {
        var model1, model2, models = [], view;
        beforeEach(function() {
            sinon.stub(app.alert, 'show', function() {});
            model1 = new Backbone.Model({opportunity_id : 'test_1'});
            model2 = new Backbone.Model({opportunity_id : 'test_1'});
            models = [model1, model2];
            view = SugarTest.createView('base', 'RevenueLineItems', 'recordlist', options.meta, context, true, layout);
        });

        afterEach(function() {
            app.alert.show.restore();
        });

        it('should return true', function() {
            var ret = view._checkMergeModels(models);

            expect(ret).toBeTruthy();
            expect(app.alert.show).not.toHaveBeenCalled();
        });

        it('should return false', function() {
            model2.set('opportunity_id', 'test_2');

            var ret = view._checkMergeModels(models);

            expect(ret).toBeFalsy();
            expect(app.alert.show).toHaveBeenCalled();
        });
    });
});
