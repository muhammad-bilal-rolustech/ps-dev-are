describe("Relationships", function() {

    var dm = SUGAR.App.dataManager, metadata, server;

    beforeEach(function() {
        dm.reset();
        metadata = SugarTest.loadJson("metadata");
    });

    afterEach(function() {
        if (server && server.restore) server.restore();
    });

    it("should be able to create a relation instance for ID", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities");
        opportunity.id = "opp-1";

        var attrs = { first_name: "John", last_name: "Smith", contact_role: "Decision Maker" };
        var contact = dm.createRelation(opportunity, "contact-1", "contacts", attrs);

        expect(contact.relation).toBeDefined();
        expect(contact.relation.link).toEqual("contacts");
        expect(contact.relation.bean).toEqual(opportunity);
        expect(contact.id).toEqual("contact-1");
        expect(contact.get("first_name")).toEqual("John");
        expect(contact.get("last_name")).toEqual("Smith");
        expect(contact.get("contact_role")).toEqual("Decision Maker");
    });

    it("should be able to create a relation instance for bean", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities");
        opportunity.id = "opp-1";

        var attrs = { id: "contact-1", first_name: "John", last_name: "Smith", contact_role: "Decision Maker" };
        var contact = dm.createBean("Contacts", attrs);
        var relation = dm.createRelation(opportunity, contact, "contacts");

        expect(contact).toEqual(relation);
        expect(contact.relation).toBeDefined();
        expect(contact.relation.link).toEqual("contacts");
        expect(contact.relation.bean).toEqual(opportunity);
        expect(contact.id).toEqual("contact-1");
        expect(contact.get("first_name")).toEqual("John");
        expect(contact.get("last_name")).toEqual("Smith");
        expect(contact.get("contact_role")).toEqual("Decision Maker");
    });

    it("should be able to create a new relation instance", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities");
        opportunity.id = "opp-1";

        var attrs = { id: "contact-1", first_name: "John", last_name: "Smith", contact_role: "Decision Maker" };
        var contact = dm.createRelation(opportunity, null, "contacts", attrs);

        expect(contact.relation).toBeDefined();
        expect(contact.relation.link).toEqual("contacts");
        expect(contact.relation.bean).toEqual(opportunity);
        expect(contact.id).toEqual("contact-1");
        expect(contact.get("first_name")).toEqual("John");
        expect(contact.get("last_name")).toEqual("Smith");
        expect(contact.get("contact_role")).toEqual("Decision Maker");
    });

    it("should be able to create a relation collection", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities");
        opportunity.id = "opp-1";

        var contacts = dm.createRelationCollection(opportunity, "contacts");

        expect(contacts.module).toEqual("Contacts");
        expect(contacts.beanType).toEqual("Contact");
        expect(contacts.relation).toBeDefined();
        expect(contacts.relation.link).toEqual("contacts");
        expect(contacts.relation.bean).toEqual(opportunity);
    });

    it("should be able to fetch related beans", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities");
        opportunity.id = "opp-1";

        var payload = SugarTest.loadJson("opportunity_contacts");

        server = sinon.fakeServer.create();
        server.respondWith("GET", "/rest/v10/Opportunities/opp-1/contacts",
            [200, {  "Content-Type": "application/json"},
                JSON.stringify(payload)]);

        var contacts = dm.createRelationCollection(opportunity, "contacts");
        contacts.fetch();
        server.respond();

        expect(contacts.length).toEqual(3);
        _.each(["x1", "x2", "x3"], function(id) {
            var contact = contacts.get(id);
            expect(contact).toBeDefined();
            expect(contact.get("first_name")).toBeDefined();
            expect(contact.get("last_name")).toBeDefined();
            expect(contact.relation).toEqual(contacts.relation);
        });
    });

    xit("should be able to add related beans", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities");
        opportunity.id = "opp-1";
        var call = dm.createBean("Contacts");
        call.id = "call-1";

        var mock = sinon.mock(Backbone);
        mock.expects("sync").once().withArgs("update");
        opportunity.addRelated("calls", call);
        mock.verify();
    });

    xit("should be able to remove related bean", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities");
        opportunity.id = "opp-1";

        var mock = sinon.mock(Backbone);
        mock.expects("sync").once().withArgs("delete");
        opportunity.removeRelated("contacts", "contact-1");
        mock.verify();
    });

    xit("should be able to set an attribute of type 'relate'", function() {
        dm.declareModels(metadata);

        var opportunity = dm.createBean("Opportunities", {
            account_name: "Account 1",
            account_id: "account-1"
        }, undefined);

        opportunity.id = "opp-1";

        var account = dm.createBean("Accounts", { name: 'Account-2'});
        account.id = "account-2";

        var mock = sinon.mock(Backbone);
        mock.expects("sync").once().withArgs("update");
        opportunity.setRelated("account_name", account);
        mock.verify();

    });


});