/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
describe('Base.Fields.MassEmailButton', function() {
    var app, module, field, context, massCollection, sandbox;

    beforeEach(function() {
        app = SugarTest.app;
        sandbox = sinon.sandbox.create();
        module = 'Contacts';

        SugarTest.testMetadata.init();
        SugarTest.loadHandlebarsTemplate('mass-email-button', 'field', 'base', 'list-header');
        SugarTest.loadComponent('base', 'field', 'button');
        SugarTest.loadComponent('base', 'field', 'mass-email-button');
        SugarTest.testMetadata.set();
        SugarTest.loadPlugin('EmailClientLaunch');

        context = app.context.getContext();
        massCollection = app.data.createBeanCollection(module);
        context.set({
            mass_collection: massCollection
        });
        context.prepare();

        field = SugarTest.createField({
            name: 'mass_email_button',
            type: 'mass-email-button',
            viewName: 'list-header',
            context: context
        });
    });

    afterEach(function() {
        sandbox.restore();
        SugarTest.testMetadata.dispose();
        field.dispose();
        field = null;
        app.cache.cutAll();
        app.view.reset();
        Handlebars.templates = {};
    });

    it('should add recipients to mailto for external mail client', function() {
        var email1 = 'foo1@bar.com',
            email2 = 'foo2@bar.com',
            bean1,
            bean2;

        bean1 = app.data.createBean(module, {
            email: [
                {
                    email_address: email1,
                    primary_address: true,
                    invalid_email: false,
                    opt_out: false
                }
            ]
        });
        bean2 = app.data.createBean(module, {
            email: [
                {
                    email_address: email2,
                    primary_address: true,
                    invalid_email: false,
                    opt_out: false
                }
            ]
        });

        sandbox.stub(field, 'useSugarEmailClient').returns(false);
        massCollection.add(bean1);
        massCollection.add(bean2);
        expect(field.$('a').attr('href')).toEqual('mailto:' + email1 + ',' + email2);
    });

    it('should add recipients to mailto for internal mail client', function() {
        var email1 = 'foo1@bar.com',
            email2 = 'foo2@bar.com',
            bean1,
            bean2,
            drawerOpenOptions;

        bean1 = app.data.createBean(module, {
            email: [
                {
                    email_address: email1,
                    primary_address: true,
                    invalid_email: false,
                    opt_out: false
                }
            ]
        });
        bean2 = app.data.createBean(module, {
            email: [
                {
                    email_address: email2,
                    primary_address: true,
                    invalid_email: false,
                    opt_out: false
                }
            ]
        });

        app.drawer = {
            open: sandbox.stub()
        };
        sandbox.stub(field, 'useSugarEmailClient').returns(true);
        massCollection.add(bean1);
        massCollection.add(bean2);
        field.$('a').click();
        drawerOpenOptions = app.drawer.open.lastCall.args[0];
        expect(drawerOpenOptions.context.prepopulate.to.length).toEqual(2);
        expect(drawerOpenOptions.context.prepopulate.to[0].get('email_address')).toEqual(email1);
        expect(drawerOpenOptions.context.prepopulate.to[1].get('email_address')).toEqual(email2);
        app.drawer = null;
    });
});
