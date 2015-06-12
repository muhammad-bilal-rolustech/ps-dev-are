describe('Base.View.ShortcutsHelp', function() {
    var app, view, origMousetrap;

    beforeEach(function() {
        app = SugarTest.app;
        SugarTest.testMetadata.init();
        SugarTest.loadComponent('base', 'view', 'shortcuts-help');
        SugarTest.loadHandlebarsTemplate('shortcuts-help', 'view', 'base');
        SugarTest.loadHandlebarsTemplate('shortcuts-help', 'view', 'base', 'shortcuts-help-table');
        SugarTest.testMetadata.set();

        origMousetrap = Mousetrap;
        Mousetrap = {
            bind: sinon.stub(),
            unbind: sinon.stub()
        };

        view = SugarTest.createView('base', 'Contacts', 'shortcuts-help', undefined, undefined, undefined, undefined, false);
    });

    afterEach(function() {
        app.shortcuts._activeSession = null;
        app.shortcuts._savedSessions = [];
        app.shortcuts._globalShortcuts = {};
        Mousetrap = origMousetrap;

        view.dispose();

        app.cache.cutAll();
        app.view.reset();
        Handlebars.templates = {};
        SugarTest.testMetadata.dispose();
    });

    it('Should display global shortcut help table', function() {
        var expectedResult = '<tr><td>a</td><td>Foo</td></tr><tr><td>b, c</td><td>Bar</td></tr>';

        app.shortcuts.registerGlobal({
            id: 'foo',
            keys: 'a',
            component: new Backbone.View(),
            description: 'Foo',
            listener: $.noop
        });
        app.shortcuts.registerGlobal({
            id: 'bar',
            keys: ['b','c'],
            component: new Backbone.View(),
            description: 'Bar',
            listener: $.noop
        });

        view.render();

        expect(view.$('[data-render=global]').html()).toBe(expectedResult);
    });

    it('Should display contextual shortcut help table', function() {
        var expectedResult = '<tr><td>a</td><td>Foo</td></tr><tr><td>b, c</td><td>Bar</td></tr>',
            firstLayout = new Backbone.View();

        app.shortcuts.createSession(['foo','bar'], firstLayout);

        app.shortcuts.register({
            id: 'foo',
            keys: 'a',
            component: firstLayout,
            description: 'Foo',
            listener: $.noop
        });
        app.shortcuts.register({
            id: 'bar',
            keys: ['b','c'],
            component: firstLayout,
            description: 'Bar',
            listener: $.noop
        });
        app.shortcuts.saveSession();

        view.render();

        expect(view.$('[data-render=contextual]').html()).toBe(expectedResult);
    });

    describe('hasCommandKey', function() {
        var getCurrentPlatformStub;

        beforeEach(function() {
            view.dispose();
        });

        afterEach(function() {
            getCurrentPlatformStub.restore();
        });

        it('Should be enabled for Macs', function() {
            getCurrentPlatformStub = sinon.stub(view, 'getCurrentPlatform').returns('MacIntel');
            view.initialize({});

            expect(view.hasCommandKey).toBe(true);
        });

        it('Should be disabled for Windows', function() {
            getCurrentPlatformStub = sinon.stub(view, 'getCurrentPlatform').returns('Win32');
            view.initialize({});

            expect(view.hasCommandKey).toBe(false);
        });
    });

    describe('"mod" shortcut key', function() {
        it('Should display "command" when command key exists', function() {
            var expectedResult = '<tr><td>shift+command+a</td><td>Foo</td></tr><tr><td>command+shift+b, command+c</td><td>Bar</td></tr>',
                firstLayout = new Backbone.View();

            view.hasCommandKey = true;

            app.shortcuts.createSession(['foo','bar'], firstLayout);

            app.shortcuts.register({
                id: 'foo',
                keys: 'shift+mod+a',
                component: firstLayout,
                description: 'Foo',
                listener: $.noop
            });
            app.shortcuts.register({
                id: 'bar',
                keys: ['mod+shift+b','mod+c'],
                component: firstLayout,
                description: 'Bar',
                listener: $.noop
            });

            app.shortcuts.saveSession();

            view.render();

            expect(view.$('[data-render=contextual]').html()).toBe(expectedResult);
        });

        it('Should display "ctrl" when command key does not exist', function() {
            var expectedResult = '<tr><td>shift+ctrl+a</td><td>Foo</td></tr><tr><td>ctrl+shift+b, ctrl+c</td><td>Bar</td></tr>',
                firstLayout = new Backbone.View();

            view.hasCommandKey = false;

            app.shortcuts.createSession(['foo','bar'], firstLayout);

            app.shortcuts.register({
                id: 'foo',
                keys: 'shift+mod+a',
                component: firstLayout,
                description: 'Foo',
                listener: $.noop
            });
            app.shortcuts.register({
                id: 'bar',
                keys: ['mod+shift+b','mod+c'],
                component: firstLayout,
                description: 'Bar',
                listener: $.noop
            });

            app.shortcuts.saveSession();

            view.render();

            expect(view.$('[data-render=contextual]').html()).toBe(expectedResult);
        });
    });
});