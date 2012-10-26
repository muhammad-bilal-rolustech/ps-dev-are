/**
 * View that displays committed forecasts for current user.  If the manager view is selected, the Forecasts
 * of Rollup type are shown; otherwise the Forecasts of Direct type are shown.
 *
 * @class View.Views.GridView
 * @alias SUGAR.App.layout.GridView
 * @extends View.View
 */
({
    /**
     * The url for the REST endpoint
     */
    url : 'rest/v10/Forecasts/committed',

    /**
     * The class selector representing the element which contains the view output
     */
    viewSelector : '.forecastsCommitted',

    /**
     * Stores the Backbone collection of Forecast models
     */
    _collection : {},

    /*
     * Stores the name to display in the view
     */
    fullName : '',

    /**
     * Stores the best case to display in the view
     */
    bestCase : 0,

    /**
     * Stores the likely case to display in the view
     */
    likelyCase : 0,

    /**
     * Stores the worst case to display in the view
     */
    worstCase : 0,

    /**
     * Used to query for the user_id value in Forecasts
     */
    userId : '',

    /**
     * Used to query for the timeperiod_id value in Forecasts
     */
    timePeriodId : '',

    /**
     * Used to query for the forecast_type value in Forecasts
     */
    forecastType : 'Direct',

    /**
     * Stores the historical log of the Forecast entries
     */
    historyLog : Array(),

    /**
     * Stores the Forecast totals to use when creating a new entry
     */
    totals : null,

    /**
     * Template to use when updating the bestCase on the committed bar
     */
    bestTemplate : _.template('<%= bestCase %>&nbsp;<span class="icon-sm committed_arrow<%= bestCaseCls %>"></span>'),

    /**
     * Template to use wen updating the likelyCase on the committed bar
     */
    likelyTemplate : _.template('<%= likelyCase %>&nbsp;<span class="icon-sm committed_arrow<%= likelyCaseCls %>"></span>'),

    /**
     * Template to use wen updating the WorstCase on the committed bar
     */
    worstTemplate : _.template('<%= worstCase %>&nbsp;<span class="icon-sm committed_arrow<%= worstCaseCls %>"></span>'),

    runningFetch : false,

    /**
     * Used to determine whether or not to visibly show the Commit log
     */
    showHistoryLog : false,

    /**
     * Used to determine whether or not to visibly show the extended Commit log
     */
    showMoreLog : false,

    /**
     * the timeperiod field metadata that gets used at render time
     */
    timeperiod: {},

    events : {
        'click i[id=show_hide_history_log]' : 'showHideHistoryLog'
    },

    initialize : function(options) {
        app.view.View.prototype.initialize.call(this, options);
        this._collection = this.context.forecasts.committed;

        this.fullName = app.user.get('full_name');
        this.userId = app.user.get('id');
        this.forecastType = (app.user.get('isManager') == true && app.user.get('showOpps') == false) ? 'Rollup' : 'Direct';
        this.timePeriodId = app.defaultSelections.timeperiod_id.id;
        this.selectedUser = {id: app.user.get('id'), "isManager":app.user.get('isManager'), "showOpps": false};

        this.bestCase = 0;
        this.likelyCase = 0;
        this.worstCase = 0;
        this.showHistoryLog = false;
    },

    /**
     * Switch showHistoryLog flag for expanding/collapsing log after commit
     */
    showHideHistoryLog: function() {
        this.$el.find('i[id=show_hide_history_log]').toggleClass('icon-caret-down icon-caret-up');
        this.$el.find('div[id=history_log_results]').toggleClass('hide');

    },

    /**
     * Renders the component
     */
    _renderHtml : function(ctx, options) {
        app.view.View.prototype._renderHtml.call(this, ctx, options);

        if(this.showHistoryLog) {

            if(this.showMoreLog) {
                this.$el.find('div[id=more_log_results]').removeClass('hide');
                this.$el.find('div[id=more]').html('<p><span class=" icon-minus-sign">&nbsp;' + App.lang.get('LBL_LESS', 'Forecasts') + '</span></p><br />');
            }
        }

        this.$el.parents('div.topline').find("span.lastBestCommit").html(this.previousBestCase);
        this.$el.parents('div.topline').find("span.lastLikelyCommit").html(this.previousLikelyCase);
        this.$el.parents('div.topline').find("span.lastWorstCommit").html(this.previousWorstCase);
    },



    bindDataChange: function() {
        var self = this;
        this._collection = this.context.forecasts.committed;
        this._collection.on("reset", function() {
            self.buildForecastsCommitted();
        }, this);
        this._collection.on("change", function() { self.buildForecastsCommitted(); }, this);
    },

    /**
     * Utility method to get the arrow and color depending on how the values match up.
     *
     * @param newValue
     * @param currentValue
     * @return {String}
     */
    getColorArrow: function(newValue, currentValue)
    {
        var cls = '';

        cls = (newValue > currentValue) ? ' icon-arrow-up font-green' : ' icon-arrow-down font-red';
        cls = (newValue == currentValue) ? '' : cls;

        return cls
    },

    buildForecastsCommitted: function() {
        var self = this;
        var count = 0;
        var previousModel;

        //Reset the history log
        self.historyLog = [];
        self.moreLog = [];

        _.each(self._collection.models, function(model)
        {
            //Get the first entry
            if(count == 0) {
                previousModel = model;
                var dateEntered = new Date(Date.parse(previousModel.get('date_entered')));
                if (dateEntered == 'Invalid Date') {
                    dateEntered = previousModel.get('date_entered');
                }
                self.previousDateEntered = app.date.format(dateEntered, app.user.get('datepref') + ' ' + app.user.get('timepref'));
            } else {
                self.historyLog.push(app.forecasts.utils.createHistoryLog(model, previousModel));
                previousModel = model;
            }
            count++;
        });

        /**
         * log records are sorted by date_entered last to first (last at the top of list)
         * for the last log record there is no other record with which it can be compared,
         * so add new log record that is compared with empty value - it is first commit
         */
        if ( previousModel )
        {
            self.historyLog.push(app.forecasts.utils.createHistoryLog('', previousModel));
        }

        self.render();
    }
})
