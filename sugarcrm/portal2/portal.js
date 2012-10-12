(function(app) {

    // Add custom events here for now
    app.events.on("app:init", function() {

        // Load dashboard route.
        app.router.route("", "dashboard", function() {
            app.controller.loadView({
                layout: "dashboard"
            });
        });

        // Load the search results route.
        app.router.route("search/:query", "search", function(query) {
            // For Safari and FF, the query always comes in as URI encoded.
            // Decode here so we don't accidently double encode it later. (bug55572)
            try{
                var decodedQuery = decodeURIComponent(query);
                app.controller.loadView({
                    mixed: true,
                    module: "Search",
                    layout: "search",
                    query: decodedQuery,
                    skipFetch: true
                });
            }catch(err){
                // If not a validly encoded URI, decodeURIComponent will throw an exception
                // If URI is not valid, don't navigate.
                app.logger.error("Search term not a valid URI component.  Will not route search/"+query);
            }

        });

        // Load the profile
        app.router.route("profile", "profile", function() {
            app.controller.loadView({
                layout: "profile"
            });
        });
        // Loadds profile edit
        app.router.route("profile/edit", "profileedit", function() {
            app.controller.loadView({
                layout: "profileedit"
            });
        });
    });

    var oRoutingBefore = app.routing.before;
    app.routing.before = function(route, args) {
        var dm, nonModuleRoutes;
        nonModuleRoutes = [
            "search",
            "error",
            "profile",
            "profileedit",
            "logout"
        ];

        app.logger.debug("Loading route. " + (route?route:'No route or undefined!'));

        if(!oRoutingBefore.call(this, route, args)) return false;

        function alertUser(msg) {
            // TODO: Error messages should later be put in lang agnostic app strings. e.g. also in layout.js alert.
            msg = msg || "LBL_PORTAL_MIN_MODULES";

            app.alert.show("no-sidecar-access", {
                level: "error",
                title: "LBL_PORTAL_ERROR",
                messages: [msg]
            });
        }

        // Handle index case - get default module if provided. Otherwise, fallback to Home if possible or alert.
        if (route === 'index') {
            dm = typeof(app.config) !== undefined && app.config.defaultModule ? app.config.defaultModule : null;
            if (dm && app.metadata.getModule(dm) && app.acl.hasAccess('read', dm)) {
                app.router.list(dm);
            } else if (app.acl.hasAccess('read', 'Home')) {
                app.router.index();
            } else {
                alertUser();
                return false;
            }
            // If route is NOT index, and NOT in non module routes, check if module (args[0]) is loaded and user has access to it.
        } else if (!_.include(nonModuleRoutes, route) && args[0] && !app.metadata.getModule(args[0]) || !app.acl.hasAccess('read', args[0])) {
            app.logger.error("Module not loaded or user does not have access. ", route);
            alertUser("LBL_PORTAL_ROUTE_ERROR");
            return false;
        }
        return true;
    };

    app.view.SupportPortalField = app.view.Field.extend({
        
        /**
         * Handles how validation errors are appended to the email "sub fields" (inputs).
         *
         * @param {Object} errors hash of validation errors
         */
        handleEmailValidationError: function(emailErrorsArray) {
            var self = this, emails;
            this.$el.find('.control-group.email').removeClass("error");
            emails = this.$el.find('.existing .email');
            
            // Remove any and all previous exclamation then add back per field error
            $(emails).removeClass("error").find('.add-on').remove();

            // For each error add to error help block
            _.each(emailErrorsArray, function(emailWithError, i) {

                // For each of our "sub-email" fields
                _.each(emails, function(e) {
                    var emailFieldValue = $(e).data('emailaddress');

                    // if we're on an email sub field where error occured, add error help block
                    if(emailFieldValue === emailWithError) {
                        
                        // First remove in case already there and then add back. Note add-on and help-block are adjacent
                        $(e).addClass("error").find('.row-fluid .help-block').remove().find('add-on').remove();
                        $(e).find('.row-fluid')
                            .append('<span class="add-on"><i class="icon-exclamation-sign"></i></span><p class="help-block">'+app.error.getErrorString('email', [emailFieldValue])+'</p>');
                    }
                });
            });
        },

        /**
         * Handles how validation errors are appended to the fields dom element
         *
         * By default errors are appended to the dom into a .help-block class if present
         * and the .error class is added to any .control-group elements in accordance with
         * bootstrap.
         *
         * @param {Object} errors hash of validation errors
         */
        handleValidationError: function(errors) {
            var self = this;

            // Email is special case as each input email is a sort of field within the one email 
            // field itself; and we need to append errors directly beneath said sub-fields
            if(self.type==='email') {
                self.handleEmailValidationError(errors.email);
                return;
            }

            // need to add error styling to parent view element
            this.$el.parent().parent().addClass("error");
            var ftag = this.fieldTag || '';

            // Reset Field
            if (this.$el.parent().parent().find('.input-append').length > 0) {
                this.$el.unwrap()
            }
            self.$('.help-block').html('');
            // Remove previous exclamation then add back.
            this.$('.add-on').remove();


            // Add error styling
            this.$el.wrap('<div class="input-append  '+ftag+'">');
            // For each error add to error help block
            _.each(errors, function(errorContext, errorName) {
                self.$('.help-block').append(app.error.getErrorString(errorName, errorContext));
            });
            $('<span class="add-on"><i class="icon-exclamation-sign"></i></span>').insertBefore(this.$('.help-block'));
        },


        bindDomChange: function() {
            if (!(this.model instanceof Backbone.Model)) return;
            var self = this;
            var el = this.$el.find(this.fieldTag);
            // need to clear error styling on data change
            el.on("change", function() {
                self.$el.parent().parent().removeClass("error");
            });
            app.view.Field.prototype.bindDomChange.call(this);
        }
    });

    app.Controller = app.Controller.extend({
        loadView: function(params) {
            var self = this;
            // TODO: Will it ever happen: app.config == undefined?
            // app.config should always be present because the logger depends on it
            if ((_.isUndefined(app.config) || (app.config && app.config.appStatus == 'offline')) && params.layout != 'login') {
                var callback = function(data) {
                    var params = {
                        module: "Login",
                        layout: "login",
                        create: true
                    };
                    app.Controller.__super__.loadView.call(self, params);
                    app.alert.show('appOffline', {
                        level: "error",
                        title: 'LBL_PORTAL_ERROR',
                        messages: 'LBL_PORTAL_OFFLINE',
                        autoclose: false
                    });
                };
                if(app.api.isAuthenticated()) {
                    app.logout({success: callback, error: callback}, {clear:true});
                } else {
                    callback();
                }
                return;
            }
            app.Controller.__super__.loadView.call(this, params);
        }
    });

    /**
     * Extends the `save` action to add `portal` specific params to the payload.
     *
     * @param {Object} attributes(optional) model attributes
     * @param {Object} options(optional) standard save options as described by Backbone docs and
     * optional `fieldsToValidate` parameter.
     */
    var __superBeanSave__ = app.Bean.prototype.save;
    app.Bean.prototype.save = function(attributes, options) {
        //Here is the list of params that must be set for portal use case.
        var defaultParams = {
            portal_flag: 1,
            portal_viewable: 1
        };
        var moduleFields = app.metadata.getModule(this.module).fields || {};
        for (var field in defaultParams) {
            if (moduleFields[field]) {
                this.set(field, defaultParams[field], {silent:true});
            }
        }
        //Call the prototype
        __superBeanSave__.call(this, attributes, options);
    };

    var _rrh = {
        /**
         * Handles `signup` route.
         */
        signup: function() {
            app.logger.debug("Route changed to signup!");
            app.controller.loadView({
                module: "Signup",
                layout: "signup",
                create: true
            });
        }
    };

    app.events.on("app:init", function() {
        // Register portal specific routes
        app.router.route("signup", "signup", _rrh.signup);
    });

})(SUGAR.App);
