describe("Convert Lead Layout", function(){

    var convertLayout,
        app = SUGAR.app;

    beforeEach(function() {
        var convertMeta = {
            "Contacts": {
                "required": true,
                "leadRelationship": "contact_leads",
                "additionalFieldMapping": []
            },
            "Accounts": {
                "required": true,
                "leadRelationship": "account_leads",
                "additionalFieldMapping": {
                    "name": "account_name"
                }
            },
            "Opportunities": {
                "required": true,
                "leadRelationship": "opportunity_leads",
                "additionalFieldMapping": {
                    "name": "opportunity_name",
                    "phone_work": "phone_office"
                }
            }
        };

        convertLayout = SugarTest.createLayout('base', 'Leads', 'convert', convertMeta, undefined, true);
    });

    describe("initialization", function() {

        it("should create convertModel", function() {
            expect(_.isEmpty(convertLayout.context.convertModel)).toBeFalsy();
        });

        it("should save convertModel when lead:convert event has been triggered", function() {
            var syncSpy = sinon.spy(convertLayout.context.convertModel, 'sync'),
                showAlertStub = sinon.stub(SugarTest.app.alert, 'show', $.noop());

            convertLayout.context.trigger('lead:convert');

            expect(syncSpy.calledOnce).toBeTruthy();

            syncSpy.restore();
            showAlertStub.restore();
        });

    });

});