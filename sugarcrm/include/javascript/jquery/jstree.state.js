/*
 * jsTree state plugin (Copied from `cookies` plugin and simple to `state` plugin of new version)
 * Stores the currently opened/selected nodes in a cookie/localStorage and then restores them
 */
(function ($) {
    $.jstree.plugin('state', {
        __init: function() {
            this.get_container()
                .one(
                ( this.data.ui ? 'reselect' : 'reopen' ) + '.jstree',
                $.proxy(function() {
                        this.get_container()
                            .bind(
                            'open_node.jstree close_node.jstree select_node.jstree deselect_node.jstree',
                            $.proxy(function (e) {
                                    if (this._get_settings().state.auto_save) {
                                        this.save_state(
                                            (e.handleObj.namespace + e.handleObj.type).replace('jstree', '')
                                        );
                                    }
                                },
                                this)
                        );
                    },
                    this)
            );
        },
        defaults : {
            save_loaded: 'jstree_load',
            save_opened: 'jstree_open',
            save_selected: 'jstree_select',
            auto_save: true,
            options: {},
            storage: $.cookie
        },
        _fn : {
            save_state: function(c) {
                if (this.data.core.refreshing) {
                    return;
                }
                var s = this._get_settings().state;
                if (!c) { // if called manually and not by event
                    if (s.save_loaded) {
                        this.save_loaded();
                        s.storage(s.save_loaded, this.data.core.to_load.join(','), s.options);
                    }
                    if (s.save_opened) {
                        this.save_opened();
                        s.storage(s.save_opened, this.data.core.to_open.join(','), s.options);
                    }
                    if (s.save_selected && this.data.ui) {
                        this.save_selected();
                        s.storage(s.save_selected, this.data.ui.to_select.join(','), s.options);
                    }
                    return;
                }
                switch(c) {
                    case 'open_node':
                    case 'close_node':
                        if (!!s.save_opened) {
                            this.save_opened();
                            s.storage(s.save_opened, this.data.core.to_open.join(','), s.options);
                        }
                        if (!!s.save_loaded) {
                            this.save_loaded();
                            s.storage(s.save_loaded, this.data.core.to_load.join(','), s.options);
                        }
                        break;
                    case 'select_node':
                    case 'deselect_node':
                        if (!!s.save_selected && this.data.ui) {
                            this.save_selected();
                            s.storage(s.save_selected, this.data.ui.to_select.join(','), s.options);
                        }
                        break;
                }
            },
            load_state: function() {
                var s = this._get_settings().state,
                    tmp,
                    rslt = {
                        load: [],
                        open: [],
                        select: []
                    };
                if (!!s.save_loaded) {
                    tmp = s.storage(s.save_loaded);
                    if (tmp && tmp.length) {
                        this.data.core.to_load = tmp.split(',');
                        rslt.load = this.data.core.to_load;
                        this.reload_nodes(false);
                    }
                }
                if (!!s.save_opened) {
                    tmp = s.storage(s.save_opened);
                    if (tmp && tmp.length) {
                        this.data.core.to_open = tmp.split(',');
                        rslt.open = this.data.core.to_open;
                        this.reopen();
                    }
                }
                if (!!s.save_selected) {
                    tmp = s.storage(s.save_selected);
                    if (tmp && tmp.length && this.data.ui) {
                        this.data.ui.to_select = tmp.split(',');
                        rslt.select = this.data.core.to_select;
                        this.reselect();
                    }
                }
                $.each(rslt, function(rsltInd, rsltValue) {
                    $.each(rsltValue, function (ind, value) {
                        rslt[rsltInd][ind] = value.replace('#', '');
                    })
                });
                this.__callback(rslt);
            }
        }
    });
})(jQuery);
