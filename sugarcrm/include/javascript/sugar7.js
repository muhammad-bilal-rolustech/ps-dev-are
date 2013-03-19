(function(app) {
    app.events.on("app:init", function() {
        var routes;

        routes = [
            {
                name: "index",
                route: ""
            },
            {
                name: "logout",
                route: "logout/?clear=:clear"
            },
            {
                name: "logout",
                route: "logout"
            },
            {
                name: "activities",
                route: "activities",
                callback: function(){
                    app.controller.loadView({
                        layout: "activities",
                        module: "Activities"
                    });
                }
            },
            {
                name: "bwc",
                route: "bwc/*url",
                callback: function(url) {
                    app.logger.debug("BWC: " + url);

                    var frame = $('#bwc-frame');
                    if (frame.length === 1 &&
                        'index.php' + frame.get(0).contentWindow.location.search === url
                        ) {
                        // update hash link only
                        return;
                    }

                    // if only index.php is given, redirect to Home
                    if (url === 'index.php') {
                        app.router.navigate('#Home', {trigger: true});
                        return;
                    }
                    var params = {
                        layout: 'bwc',
                        url: url
                    };
                    var module = /module=([^&]*)/.exec(url);

                    if (!_.isNull(module) && !_.isEmpty(module[1])) {
                        params.module = module[1];
                        // on BWC import we want to try and take the import module as the module
                        if (module[1] === 'Import') {
                            module = /import_module=([^&]*)/.exec(url);
                            if (!_.isNull(module) && !_.isEmpty(module[1])) {
                                params.module = module[1];
                            }
                        }
                    }

                    app.controller.loadView(params);
                }
            },
            {
                name: "list",
                route: ":module"
            },
            {
                name: "create",
                route: ":module/create",
                callback: function(module){
                    if(module === "Home") {
                        app.controller.loadView({
                            module: module,
                            layout: "record"
                        });

                        return;
                    }

                    app.controller.loadView({
                        module: module,
                        layout: "records"
                    });

                    app.drawer.open({
                        layout:'create',
                        context:{
                            create:true
                        }
                    }, _.bind(function (refresh) {
                        if (refresh) {
                            var collection = app.controller.context.get('collection');
                            if (collection) {
                                collection.fetch();
                            }
                        }
                    }, this));
                }
            },
            {
                name: "vcardImport",
                route: ":module/vcard-import",
                callback: function(module){
                    app.controller.loadView({
                        module: module,
                        layout: "records"
                    });

                    app.drawer.open({
                        layout:'vcard-import'
                    });
                }
            },
            {
                name: "layout",
                route: ":module/layout/:view"
            },
            {
                name: "record",
                route: ":module/:id"
            },
            {
                name: "record",
                route: ":module/:id/:action"
            }
        ];

        app.routing.setRoutes(routes);
    });
})(SUGAR.App);
