//FILE SUGARCRM flav=pro ONLY
/********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
describe("The forecastCommitted view", function(){

    var app, view, formatAmountLocaleStub, createHistoryLogStub;

    beforeEach(function() {
        app = SugarTest.app;
        view = SugarTest.loadFile("../modules/Forecasts/clients/base/views/forecastsCommitLog", "forecastsCommitLog", "js", function(d) { return eval(d); });
        SugarTest.loadFile("../sidecar/src/utils", "currency", "js", function(d) { return eval(d); });
        SugarTest.loadFile("../modules/Forecasts/clients/base/lib", "ForecastsUtils", "js", function(d) { return eval(d); });

        view.render = function() {};
    });

    describe("test buildForecastsCommitted function", function() {

        beforeEach(function(){
            var model1 = new Backbone.Model({date_entered:"2012-12-05T11:14:25-04:00", best_case : 100, likely_case : 90, base_rate : 1 });
            var model2 = new Backbone.Model({date_entered:"2012-10-05T11:14:25-04:00", best_case : 110, likely_case : 100, base_rate : 1 });
            var model3 = new Backbone.Model({date_entered:"2012-11-05T11:14:25-04:00", best_case : 120, likely_case : 110, base_rate : 1 });
            view._collection = new Backbone.Collection([model1, model2, model3]);

            formatAmountLocaleStub = sinon.stub(app.currency, "formatAmountLocale", function(value) {
                return value;
            });

            createHistoryLogStub = sinon.stub(app.forecasts.utils, "createHistoryLog", function(model, previousModel) {
                return "createHistoryLog";
            });
        });

        afterEach(function() {
            formatAmountLocaleStub.restore();
            createHistoryLogStub.restore();
            // empty out the collection
            view._collection = new Backbone.Collection([]);
        });

        it("should have an empty historyLog", function() {
            view._collection = new Backbone.Collection([]);

            view.buildForecastsCommitted();
            expect(_.isEmpty(view.historyLog)).toBeTruthy();
        });

        it("should create one historyLog entry", function() {
            var model1 = new Backbone.Model({date_entered:"2012-12-05T11:14:25-04:00", best_case : 100, likely_case : 90, base_rate : 1 });
            view._collection = new Backbone.Collection([model1]);

            view.buildForecastsCommitted();
            expect(view.historyLog.length == 1).toBeTruthy();
        });

        it("should create three historyLog entries", function() {
            view.buildForecastsCommitted();
            expect(view.historyLog.length == 3).toBeTruthy();
        });



        describe("previousDateEntered should", function(){

            var userStub;

            beforeEach(function(){
                userStub = sinon.stub(app.user, "get", function(type){
                   switch(type) {
                       case "datepref":
                           return "m/d/Y";
                           break;
                       case "timepref":
                           return "h:m:s";
                           break;
                   }
                });
            });

            afterEach(function(){
                userStub.restore();
            });

            it("be set", function(){
                view.buildForecastsCommitted();
                expect(_.isEmpty(view.previousDateEntered)).toBeFalsy()
            });

            it("be in the user format", function(){
                view.buildForecastsCommitted();
                expect(view.previousDateEntered.match(/^\d{1,2}\/\d{1,2}\/\d{4} \d{1,2}:\d{1,2}:\d{1,2}$/)).toBeTruthy();
            });
        })
    });
});
