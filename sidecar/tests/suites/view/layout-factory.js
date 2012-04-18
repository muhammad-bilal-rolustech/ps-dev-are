describe("View Manager", function() {
    var app;

    beforeEach(function() {
        app = sugarApp;
        app.metadata.set(fixtures.metadata);
    });

    describe("should be able to create instances of Layout class which is", function() {

        it('base class', function () {
            expect(app.view.createLayout({
                name : "edit",
                module: "Contacts"
            })).not.toBe(null);
        });

    });

});
