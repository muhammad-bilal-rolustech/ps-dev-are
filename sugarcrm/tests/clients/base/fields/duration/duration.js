describe('Base.Field.Duration', function() {
    var app, field, sandbox;

    beforeEach(function() {
        app = SugarTest.app;
        SugarTest.testMetadata.init();
        field = SugarTest.createField('base', 'duration', 'duration');
        SugarTest.testMetadata.set();

        sandbox = sinon.sandbox.create();
        sandbox.stub(app.user, 'getPreference')
            .withArgs('datepref')
            .returns('m/d/Y')
            .withArgs('timepref')
            .returns('h:ia');

        app.metadata._dev_data.app_strings.LBL_START_AND_END_DATE_SAME_DAY = '{{date}} {{start}} - {{end}} ({{duration}})';
        app.metadata._dev_data.app_strings.LBL_START_AND_END_DATE = '{{start}} - {{end}} ({{duration}})';
        app.metadata._dev_data.app_strings.LBL_DURATION_HOUR = 'hour';
        app.metadata._dev_data.app_strings.LBL_DURATION_HOURS = 'hours';
        app.metadata._dev_data.app_strings.LBL_DURATION_DAYS = 'days';
        app.metadata._dev_data.app_strings.LBL_DURATION_DAY = 'day';
        app.metadata._dev_data.app_strings.LBL_DURATION_MINUTES = 'minutes';
        app.metadata._dev_data.app_strings.LBL_DURATION_MINUTE = 'minute';
    });

    afterEach(function() {
        sandbox.restore();
        field.dispose();
        SugarTest.testMetadata.dispose();
        app.cache.cutAll();
        app.view.reset();
        Handlebars.templates = {};
    });

    describe('getFormattedValue()', function() {
        it('should display start and end dates if they are different', function() {
            field.model.set({
                date_start: '2014-07-17T11:28',
                date_end: '2014-07-18T12:28'
            });
            expect(field.getFormattedValue()).toBe('07/17/2014 11:28am - 07/18/2014 12:28pm (1 day 1 hour)');
        });

        it('should display date once if they are the same', function() {
            field.model.set({
                date_start: '2014-07-17T11:28',
                date_end: '2014-07-17T12:28'
            });

            expect(field.getFormattedValue()).toBe('07/17/2014 11:28am - 12:28pm (1 hour)');
        });

        it('should display 0 duration if the start date is not before the end date', function() {
            field.model.set({
                date_start: '2014-07-17T11:28',
                date_end: '2014-07-17T10:28'
            });

            expect(field.getFormattedValue()).toBe('07/17/2014 11:28am - 10:28am (0 minutes)');
        });

        it('should display 0 duration if the start and end dates are the same', function() {
            field.model.set({
                date_start: '2014-07-17T11:28',
                date_end: '2014-07-17T11:28'
            });

            expect(field.getFormattedValue()).toBe('07/17/2014 11:28am - 11:28am (0 minutes)');
        });
    });

    describe('setDefaultStartDateTime()', function() {
        it('should set the default start date time to be the upcoming hour if the current time is past the half hour', function() {
            var dateTime = app.date('2014-07-17T09:31');
            field.setDefaultStartDateTime(dateTime);
            expect(field.model.get('date_start')).toMatch('2014-07-17T10:00');
        });

        it('should set the default start date time to be the upcoming half hour if the current time is before the half hour', function() {
            var dateTime = app.date('2014-07-17T09:29');
            field.setDefaultStartDateTime(dateTime);
            expect(field.model.get('date_start')).toMatch('2014-07-17T09:30');
        });

        it('should set the default start date time to be at half hour if the current time is at the half hour', function() {
            var dateTime = app.date('2014-07-17T09:30');
            field.setDefaultStartDateTime(dateTime);
            expect(field.model.get('date_start')).toMatch('2014-07-17T09:30');
        });

        it('should set the default start date time to be at the hour if the current time is at the hour', function() {
            var dateTime = app.date('2014-07-17T09:00');
            field.setDefaultStartDateTime(dateTime);
            expect(field.model.get('date_start')).toMatch('2014-07-17T09:00');
        });
    });

    describe('modifyEndDateToRetainDuration()', function() {
        it('should set the end date to be an hour from the start date if the end date is empty', function() {
            field.model.set('date_start', '2014-07-17T09:00');
            field.modifyEndDateToRetainDuration();
            expect(field.model.get('date_end')).toMatch('2014-07-17T10:00');
        });

        it('should not modify the end date if start date and end date have been set at the same time', function() {
            field.model.set({
                date_start: '2014-07-17T11:00',
                date_end: '2014-07-17T12:30'
            });
            field.modifyEndDateToRetainDuration();
            expect(field.model.get('date_end')).toMatch('2014-07-17T12:30');
        });

        it('should set the end date to match the previous duration if start date and end date had already been set', function() {
            field.model.set({
                date_start: '2014-07-17T11:00',
                date_end: '2014-07-17T12:30'
            });
            field.model.set('date_start', '2014-07-17T10:00');
            field.modifyEndDateToRetainDuration();
            expect(field.model.get('date_end')).toMatch('2014-07-17T11:30');
        });

        it('should not modify the end date if the previous duration had a negative duration', function() {
            field.model.set({
                date_start: '2014-07-17T11:00',
                date_end: '2014-07-17T10:00'
            });
            field.model.set('date_start', '2014-07-17T09:30');
            field.modifyEndDateToRetainDuration();
            expect(field.model.get('date_end')).toMatch('2014-07-17T10:00');
        });

        it('should modify the end date if the previous duration had a zero duration', function() {
            field.model.set({
                date_start: '2014-07-17T11:00',
                date_end: '2014-07-17T11:00'
            });
            field.model.set('date_start', '2014-07-17T09:30');
            field.modifyEndDateToRetainDuration();
            expect(field.model.get('date_end')).toMatch('2014-07-17T09:30');
        });

        it('should not modify the end date if start date is empty', function() {
            field.modifyEndDateToRetainDuration();
            expect(field.model.get('date_end')).toBeUndefined();
        });
    });

    describe('Verify that when the end date changes, the duration fields are correctly recalculated', function() {
        it('should calculate the duration based on the new end date value', function() {
            field.model.set({
                date_start: '2014-07-17T08:00',
                date_end: '2014-07-17T08:00'
            });
            field.model.set('date_end', '2014-07-17T09:15');
            expect(field.model.get('duration_hours')).toEqual(1);
            expect(field.model.get('duration_minutes')).toEqual(15);
        });
    });

    describe('isDateRangeValid()', function() {
        it('should have valid date range if the start date is before the end date', function() {
            field.model.set({
                date_start: '2014-07-17T10:00',
                date_end: '2014-07-17T11:00'
            });
            expect(field.isDateRangeValid()).toBe(true);
        });

        it('should not have valid date range if the start date is after the end date', function() {
            field.model.set({
                date_start: '2014-07-17T12:00',
                date_end: '2014-07-17T11:00'
            });
            expect(field.isDateRangeValid()).toBe(false);
        });

        it('should have valid date range if the start date is the same as the end date', function() {
            field.model.set({
                date_start: '2014-07-17T12:00',
                date_end: '2014-07-17T12:00'
            });
            expect(field.isDateRangeValid()).toBe(true);
        });

        it('should not have valid date range if the start date is empty', function() {
            field.model.set({
                date_end: '2014-07-17T12:00'
            });
            expect(field.isDateRangeValid()).toBe(false);
        });

        it('should not have valid date range if the end date is empty', function() {
            field.model.set({
                date_start: '2014-07-17T11:00',
                date_end: '2014-07-17T12:00'
            });
            field.model.unset('date_end');
            expect(field.isDateRangeValid()).toBe(false);
        });
    });

    describe('perform validation', function() {
        it('should add a validation task to the model when the field is initialized', function() {
            var task = 'duration_validator_' + field.cid;
            expect(_.has(field.model._validationTasks, task)).toBe(true);
        });

        it('should remove the validation task from the model when the field is disposed', function() {
            var model, task;
            // keep a copy of the model to test that the task was removed
            model = field.model;
            task = 'duration_validator_' + field.cid;
            field.dispose();
            expect(_.has(model._validationTasks, task)).toBe(false);
        });

        var validDurations = [
            {hours: 0, minutes: 0},
            {hours: '0', minutes: '0'},
            {hours: 1, minutes: 1},
            {hours: '1', minutes: '1'}
        ];
        using('valid durations', validDurations, function(durations) {
            it('should not report errors when duration_hours and duration_minutes are valid', function() {
                var callback = sandbox.spy();
                field.model.set('duration_hours', durations.hours);
                field.model.set('duration_minutes', durations.minutes);
                field.doValidateDuration({}, {}, callback);
                expect(_.size(callback.args[0][2])).toBe(0);
            });
        });

        var invalidDurations = [
            {hours: null, minutes: 1},
            {hours: 1, minutes: null},
            {hours: undefined, minutes: 1},
            {hours: 1, minutes: undefined},
            {hours: '', minutes: 1},
            {hours: 1, minutes: ''},
            {hours: 0, minutes: -1},
            {hours: -1, minutes: 0},
            {hours: 0.5, minutes: 0},
            {hours: 0, minutes: 0.5},
            {hours: 'a', minutes: 1},
            {hours: 1, minutes: 'a'}
        ];
        using('invalid durations', invalidDurations, function(durations) {
            it('should report errors when duration_hours and/or duration_minutes are invalid', function() {
                var callback = sandbox.spy();
                field.model.set('duration_hours', durations.hours);
                field.model.set('duration_minutes', durations.minutes);
                field.doValidateDuration({}, {}, callback);
                expect(_.size(callback.args[0][2])).toBe(1);
            });
        });
    });
});
