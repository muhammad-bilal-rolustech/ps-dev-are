describe('Emails.Field.ReplyAction', function() {
    var app;
    var field;
    var model;
    var user;

    beforeEach(function() {
        app = SugarTest.app;
        user = SUGAR.App.user;

        SugarTest.testMetadata.init();
        SugarTest.loadHandlebarsTemplate('reply-action', 'field', 'base', 'reply-header-html', 'Emails');
        SugarTest.loadComponent('base', 'field', 'button');
        SugarTest.loadComponent('base', 'field', 'rowaction');
        SugarTest.loadComponent('base', 'field', 'emailaction');
        SugarTest.testMetadata.set();

        //used by formatDate in the reply header template
        user.setPreference('datepref', 'Y-m-d');
        user.setPreference('timepref', 'h:i:s');

        model = app.data.createBean('Emails');

        field = SugarTest.createField({
            name: 'reply_action',
            type: 'reply-action',
            viewName: 'record',
            module: 'Emails',
            loadFromModule: true,
            model: model
        });
    });

    afterEach(function() {
        field.dispose();
        app.cache.cutAll();
        app.view.reset();
        delete field.model;
        field = null;
        SugarTest.testMetadata.dispose();
    });

    describe('_getReplyRecipients', function() {
        var recipients;

        beforeEach(function() {
            recipients = [];
            for (var i = 0; i < 5; i++) {
                recipients.push(app.data.createBean('Contacts', {
                    id: i,
                    name: 'Name' + i
                }));
            }
        });

        it('should return the original sender in the to field if reply', function() {
            var actual;
            field.model.set('from', app.data.createMixedBeanCollection([recipients[0]]));
            actual = field._getReplyRecipients(false);
            expect(actual).toEqual({
                to: [
                    {bean: recipients[0]}
                ],
                cc: []
            });
        });

        it('should return the original from, to and cc recipients if reply all', function() {
            var actual;
            var from = app.data.createMixedBeanCollection([recipients[0]]);
            var to = app.data.createMixedBeanCollection([
                recipients[1],
                recipients[2]
            ]);
            var cc = app.data.createMixedBeanCollection([
                recipients[3],
                recipients[4]
            ]);

            field.model.set({
                from: from,
                to: to,
                cc: cc
            });

            actual = field._getReplyRecipients(true);
            expect(actual).toEqual({
                to: [
                    {bean: recipients[0]},
                    {bean: recipients[1]},
                    {bean: recipients[2]}
                ],
                cc: [
                    {bean: recipients[3]},
                    {bean: recipients[4]}
                ]
            });
        });

        it('should ignore original bcc recipients if reply all', function() {
            var actual;
            var from = app.data.createMixedBeanCollection([recipients[0]]);
            var to = app.data.createMixedBeanCollection([recipients[1]]);
            var cc = app.data.createMixedBeanCollection([recipients[2]]);
            var bcc = app.data.createMixedBeanCollection([recipients[3]]);

            field.model.set({
                from: from,
                to: to,
                cc: cc,
                bcc: bcc
            });

            actual = field._getReplyRecipients(true);
            expect(actual).toEqual({
                to: [
                    {bean: recipients[0]},
                    {bean: recipients[1]}
                ],
                cc: [
                    {bean: recipients[2]}
                ]
            });
        });
    });

    describe('_getReplySubject', function() {
        using('original subjects', [
            {
                original: '',
                reply: 'Re: '
            },
            {
                original: 'My Subject',
                reply: 'Re: My Subject'
            },
            {
                original: 'Re: My Subject',
                reply: 'Re: My Subject'
            },
            {
                original: 'RE: re: Re: rE: My Subject',
                reply: 'Re: My Subject'
            },
            {
                original: 'RE: FWD: re: fwd: Re: Fwd: rE: fwD: My Subject',
                reply: 'Re: My Subject'
            }
        ], function(data) {
            it('should build the appropriate reply subject', function() {
                var actual = field._getReplySubject(data.original);
                expect(actual).toEqual(data.reply);
            });
        });
    });

    describe('_getReplyHeaderParams', function() {
        it('should produce proper reply header params', function() {
            var actual;
            var date = '2012-03-27 01:48';
            var expected = {
                from: '',
                to: '',
                cc: '',
                date: date,
                name: 'My Subject'
            };

            field.model.set({
                from: [], //_formatEmailList tested separately
                to: [], //_formatEmailList tested separately
                cc: [], //_formatEmailList tested separately
                date_sent: expected.date,
                name: expected.name
            });

            actual = field._getReplyHeaderParams();
            expect(actual).toEqual(expected);
        });
    });

    describe('_formatEmailList', function() {
        it('should return empty string if recipient list is empty', function() {
            var actual = field._formatEmailList([]);
            expect(actual).toEqual('');
        });

        it('should format email list properly', function() {
            var collection = new Backbone.Collection([
                {
                    name: 'Foo Bar',
                    email_address_used: 'foo@bar.com'
                },
                {
                    name: null,
                    email_address_used: 'bar@foo.com'
                }
            ]);
            var actual = field._formatEmailList(collection);
            expect(actual).toEqual('Foo Bar <foo@bar.com>, bar@foo.com');
        });
    });

    describe('_getReplyBody', function() {
        it('should strip the signature class from any div tags', function() {
            var original = 'My Content <div class="signature">My Signature</div>';
            var expected = 'My Content <div>My Signature</div>';
            var actual;

            field.model.set('description_html', original);
            actual = field._getReplyBody();
            expect(actual).toEqual(expected);
        });

        it('should return an empty string if email body is not set', function() {
            field.model.unset('description_html');
            expect(field._getReplyBody()).toEqual('');
        });
    });
});
