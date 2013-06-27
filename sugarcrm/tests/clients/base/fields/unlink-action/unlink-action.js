describe('View.Fields.UnlinkAction', function() {

    var app, field, sandbox, relatedFields, moduleName = 'Contacts';

    beforeEach(function() {
        app = SugarTest.app;

        SugarTest.loadComponent('base', 'field', 'button');
        SugarTest.loadComponent('base', 'field', 'rowaction');
        SugarTest.loadComponent('base', 'field', 'unlink-action');
        field = SugarTest.createField("base", "unlink-action", "unlink-action", "edit", {
            'type':'rowaction',
            'css_class':'btn',
            'tooltip':'Unlink',
            'event':'list:unlinkrow:fire',
            'icon':'icon-trash',
            'acl_action':'delete'
        }, moduleName);

        sandbox = sinon.sandbox.create();
        sandbox.stub(app.data, "getRelateFields", function(){
            return relatedFields;
        });
        relatedFields = [{required: false}];
    });

    afterEach(function() {
        app.cache.cutAll();
        app.view.reset();
        delete Handlebars.templates;
        field = null;
        sandbox.restore();
    });

    it('should hide action if the user does not have access', function() {
        field.model = app.data.createBean(moduleName);
        var aclStub = sinon.stub(app.acl, "hasAccessToModel", function() {
            return false;
        });
        field.render();
        expect(field.isHidden).toBeTruthy();
        aclStub.restore();
    });

    it('should hide action if any related field is required', function() {
        field.model = app.data.createBean(moduleName);
        relatedFields = [{required: true}];
        field.render();
        expect(field.isHidden).toBeTruthy();

        relatedFields = [{required: false}, {required: true}];
        field.render();
        expect(field.isHidden).toBeTruthy();

        relatedFields = [{required: false}, {required: false}];
        field.render();
        expect(field.isHidden).toBeFalsy();
    });

});
