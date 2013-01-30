(function(app) {
    app.events.on("app:init", function() {
        var routes;

        function recordHandler(module, id, action) {
            var opts = {
                module: module,
                layout: "record",
                action: (action || "detail")
            };

            if (id !== "create") {
                _.extend(opts, {modelId: id});
            } else {
                _.extend(opts, {create: true});
                opts.layout = "create";
            }

            var oldCollection = app.controller.context.get('collection');
            var oldListCollection = app.controller.context.get('listCollection');
            //If we come from a list view, we get the current collection
            if (oldCollection && oldCollection.module === module && oldCollection.get(id)) {
                opts.listCollection = oldCollection;
            }
            //If we come from a detail view, we need to get the cached collection
            if (oldListCollection && oldListCollection.module === module && oldListCollection.get(id)) {
                opts.listCollection = oldListCollection;
            }

            app.controller.loadView(opts);
        }

        routes = [
            {
                name: "index",
                route: "",
                callback: "index"
            },
            {
                name: "logout",
                route: "logout",
                callback: "logout"
            },
            {
                name: "logout",
                route: "logout/?clear=:clear",
                callback: "logout"
            },
            {
                name: "list",
                route: ":module",
                callback: function(module) {
                    app.controller.loadView({
                        module: module,
                        layout: "records"
                    });
                }
            },
            {
                name: "layout",
                route: ":module/layout/:view",
                callback: "layout"
            },
            {
                name: "create",
                route: ":module/create",
                callback: "create"
            },
            {
                name: "record action",
                route: ":module/:id/:action",
                callback: recordHandler
            },
            {
                name: "record",
                route: ":module/:id",
                callback: recordHandler
            }
        ];

        app.routing.setRoutes(routes);
        app.utils = _.extend(app.utils, {
                handleTooltip: function(event, viewComponent) {
                    var $el = viewComponent.$(event.target);
                    if( $el[0].offsetWidth < $el[0].scrollWidth ) {
                        $el.tooltip('show');
                    } else {
                        $el.tooltip('destroy');
                    }
                }
        });

    });
})(SUGAR.App);