({

/**
 * View that displays search results.
 * @class View.Views.ProfileView
 * @alias SUGAR.App.layout.ProfileView
 * @extends View.View
 */
    events: {},
    initialize: function(options) {
        this.options.meta = this._meta;
        app.view.View.prototype.initialize.call(this, options);

        this.fallbackFieldTemplate = "detail"; // will use detail sugar fields
    },
    render: function() {
        var self = this, data, currentUserAttributes;

        // TODO: Stubbing for now .. prolly will be passed in later ;=)
        currentUserAttributes = {id: "1275a382-5f3b-37d6-3f3d-4fce979599ea"};

        app.alert.show('fetch_contact_record', {level:'process', title:'Loading'});
        app.api.records("read", "Contacts", currentUserAttributes, null, {
            success: function(data) {
                app.alert.dismiss('fetch_contact_record');
                if(data) {
                    self.updateModel(data);
                    app.view.View.prototype.render.call(self);
                } 
                self.renderSubnav(data);
            },
            error: function(xhr, error) {
                app.alert.dismiss('fetch_contact_record');
                app.error.handleHTTPError(xhr, error, self);
            }
        });
    },
    /**
     * Updates model for this contact.
     */
    updateModel: function(data) {
        var self = this, model;
        model = self.context.get('model');
        model.set(data, {silent: true});
    },
    renderSubnav: function(data) {
        var self = this, fullName = '', subnavModel = null;
        if (self.context.get('subnavModel')) {
            fullName = data.name ? data.full_name : data.first_name +' '+data.last_name;
            self.context.get('subnavModel').set({
                'title': fullName,
                'meta': {
                    "buttons": self._meta.buttons 
                }
            });
        }
    },

    // I assume this will eventually be in clients/base/views/profile-edit/profile-edit.php
    _meta: {
            "buttons": [
                {
                    "name": "edit_button",
                    "type": "button",
                    "label": "Edit",
                    "value": "edit",
                    "class": "edit-profile",
                    "primary": true,
                    'events': {
                        'click': "function(e){ this.app.router.navigate('profile/edit', {trigger:true});}"
                    }
                }
            ],
            "templateMeta": {
                "maxColumns": "2",
                "widths": [
                    {
                        "label": "10",
                        "field": "30"
                    },
                    {
                        "label": "10",
                        "field": "30"
                    }
                ],
                "formId": "ProfileEditView",
                "formName": "ProfileEditView",
                "useTabs": false
            },
            "panels": [
                {
                    "label": "default",
                    "fields": [
                        {
                            "label": "LBL_ACCOUNT",
                            "name": "account_name",
                            "colspan": 2,
                            "type": "text"
                        },
                        {
                            "name": "title",
                            // TODO: Add appropriate LBL to appstrings 
                            "label": "Title", 
                            "colspan": 2,
                            "type": "text"
                        },
                        {
                            "label": "LBL_PRIMARY_ADDRESS_STREET",
                            "name": "primary_address_street",
                            "colspan": 2,
                            "type": "text"
                        },
                        {
                            "label": "LBL_PRIMARY_ADDRESS_CITY",
                            "name": "primary_address_city",
                            "colspan": 1,
                            "type": "text"
                        },
                        {
                            "label": "LBL_PRIMARY_ADDRESS_STATE",
                            "name": "primary_address_state",
                            "colspan": 1,
                            "type": "text"
                        },
                        {
                            "label": "LBL_PRIMARY_ADDRESS_POSTALCODE",
                            "name": "primary_address_postalcode",
                            "colspan": 2,
                            "type": "text"
                        },
                        {
                            "name": "phone_home",
                            // TODO: Add appropriate LBL to appstrings for work/home/mobile phones - LBL_LIST_PHONE == 'Phone'
                            "label": "Home phone",
                            "colspan": 2,
                            "type": "text"
                        },
                        {
                            "name": "phone_work",
                            "label": "Work phone",
                            "colspan": 2,
                            "type": "text"
                        },
                        {
                            "name": "phone_mobile",
                            "label": "Mobile phone",
                            "colspan": 2,
                            "type": "text"
                        },
                    ]
                }
            ]
        }

})

