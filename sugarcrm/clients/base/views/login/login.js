({
    plugins: ['error-decoration'],
    fallbackFieldTemplate: 'edit',
    /**
     * Login form view.
     * @class View.Views.LoginView
     * @alias SUGAR.App.view.views.LoginView
     */
    events: {
        "click [name=login_button]": "login",
        "keypress": "handleKeypress"
    },

    /**
     * Process login on key 'Enter'
     * @param e
     */
    handleKeypress: function(e) {
        if (e.keyCode === 13) {
            this.$("input").trigger("blur");
            this.login();
        }
    },

    /**
     * Get the fields metadata from panels and declare a Bean with the metadata attached
     * @param meta
     * @private
     */
    _declareModel: function(meta) {
        meta = meta || {};

        var fields = {};
        _.each(_.flatten(_.pluck(meta.panels, "fields")), function(field) {
            fields[field.name] = field;
        });
        /**
         * Fields metadata needs to be converted to this format for App.data.declareModel
         *  {
          *     "username": { "name": "username", ... },
          *     "password": { "name": "password", ... },
          *      ...
          * }
         */
        app.data.declareModel('Login', {fields: fields});
    },

    /**
     * @override
     * @param options
     */
    initialize: function(options) {
        // Declare a Bean so we can process field validation
        this._declareModel(options.meta);

        // Reprepare the context because it was initially prepared without metadata
        options.context.prepare(true);

        app.view.View.prototype.initialize.call(this, options);

        var config = app.metadata.getConfig();
        if (config && app.config.forgotpasswordON === true) {
            this.showPasswordReset = true;
        }

    },

    /**
     * @override
     * @private
     */
    _render: function() {
        if (app.config && app.config.logoURL) {
            this.logoURL = app.config.logoURL;
        }
        app.view.View.prototype._render.call(this);
        this.refreshAddtionalComponents();
        /**
         * Added browser version check for MSIE since we are dropping support
         * for MSIE 9.0 for SugarCon
         */
        if (!this._isSupportedBrowser()) {
            app.alert.show('unsupported_browser', {
                level:'warning',
                title: '',
                messages: [
                    app.lang.getAppString('LBL_ALERT_BROWSER_NOT_SUPPORTED'),
                    app.lang.getAppString('LBL_ALERT_BROWSER_SUPPORT')
                ]
            });
        }
        return this;
    },

    /**
     * Refresh additional components
     */
    refreshAddtionalComponents: function() {
        _.each(app.additionalComponents, function(component) {
            component.render();
        });
    },

    /**
     * Process Login
     */
    login: function() {
        var self = this;
        this.model.doValidate(null,
            _.bind(function(isValid) {
                if (isValid) {
                    app.$contentEl.hide();
                    var args = {password: this.model.get("password"), username: this.model.get("username")};

                    app.alert.show('login', {level: 'process', title: app.lang.getAppString('LBL_LOADING'), autoClose: false});
                    app.login(args, null, {
                        error: function() {
                            app.$contentEl.show();
                            app.logger.debug("login failed!");
                        },
                        success: function() {
                            app.logger.debug("logged in successfully!");
                            app.events.on('app:sync:complete', function() {
                                app.logger.debug("sync in successfully!");
                                this.postLogin();
                            }, self);
                        },
                        complete: function() {
                            app.alert.dismiss('login');
                        }
                    });
                }
            }, self)
        );
    },
    /**
     * After login and app:sync:complete, we need to see if there's any post login setup we need to do prior to
     * rendering the rest of the Sugar app
     */
    postLogin: function(){
        var showWizard = app.user.get('show_wizard'),
            wizardType = app.user.get('type');
        if (showWizard) {
            var callbacks = {
                complete: function(){
                    window.location.reload(); //Reload when done
                }
            };
            app.controller.loadView({
                layout: "first-login-wizard",
                module: "Users",
                modelId: app.user.get("id"),
                callbacks: callbacks,
                wizardName: wizardType
            });
            $("#header").hide();  //Hide the header bar
        } else {
            this.refreshAddtionalComponents();
        }
        app.$contentEl.show();
    },

    /**
     * Taken from sugar_3. returns true if the users browser is recognized
     * @return {Boolean}
     * @private
     */
    _isSupportedBrowser:function () {
        var supportedBrowsers = {
            msie:{min:9},
            mozilla:{min:18},
            // For Safari & Chrome jQuery.Browser returns the webkit revision instead of the browser version
            // and it's hard to determine this number.
            safari:{min:536},
            chrome:{min:537}
        };
        for (var b in supportedBrowsers) {
            if ($.browser[b]) {
                var current = parseInt($.browser.version);
                var supported = supportedBrowsers[b];
                return current >= supported.min;
            }
        }
    }
})
