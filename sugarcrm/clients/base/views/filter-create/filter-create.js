({
    events: {
        'click a.filter-close': 'triggerClose',
        'click a.addme': 'addRow',
        'click a.removeme': 'removeRow',
        'click a.updateme': 'updateRow',
        'click .save_button': 'save',
        'click .delete_button': 'removeAll',
        'change .filter-header': 'editName',
        'change .field_name': 'chooseField',
        'change .operator': 'chooseOperator',
        'change .filter-value': 'modifyValue'
    },

    rowTemplate: Handlebars.compile('<article class="filter-body newRow">' +
'     <div class="row-fluid">' +
'       <div class="filter-field controls span3">' +
'         <select name="field" class="field_name chzn-select chzn-inherit-width" data-placeholder="Select field name..."> ' +
'             <option value=""></option> ' +
'             {{#each filterFields}}' +
'                 <option data-type="{{type}}" value="{{name}}">{{string}}</option>' +
'             {{/each}}' +
'         </select>' +
'       </div>' +
'       <div class="filter-operator hide controls span3"> ' +
'         <select name="operator" class="operator chzn-select chzn-inherit-width" data-placeholder="Select operator...">' +
'         </select>' +
'       </div>' +
'       <div class="filter-value hide controls span4">' +
'       </div>' +
'       <div class="filter-actions span2">' +
'         <a class="removeme btn btn-invisible btn-dark"><i class="icon-minus"></i></a>' +
'         <a class="updateme btn btn-invisible hide btn-dark"><i class="icon-refresh"></i></a>' +
'         <a class="addme btn btn-invisible hide btn-dark"><i class="icon-plus"></i></a>' +
'       </div>' +
'     </div>' +
'   </article>'),

    filterOperatorMap: {
        'enum': ['is', 'is not'],
        'varchar': ['matches', 'does not match', 'contains', 'does not contains', 'starts with', 'does not start with', 'ends with', 'does not end with'],
        'name': ['matches', 'does not match', 'contains', 'does not contains', 'starts with', 'does not start with', 'ends with', 'does not end with'],
        'currency': ['is equal to', 'is greater than', 'is greater than or equal to', 'is less than', 'is less than or equal to'],
        'int': ['is equal to', 'is greater than', 'is greater than or equal to', 'is less than', 'is less than or equal to'],
        'double': ['is equal to', 'is greater than', 'is greater than or equal to', 'is less than', 'is less than or equal to'],
        'datetime': ['datetime operator'],
        'base': ['fall through to this case']
    },

    initialize: function(opts) {
        // Remove the next line later:
        this.isSaved = false;

        var self = this;
        this.title = app.controller.context.get('module');
        app.view.View.prototype.initialize.call(this, opts);
        this.filterFields = [];
        _.each(app.metadata.getModule(this.title).fields, function(value, key) {
            self.filterFields.push({
                name: key,
                string: app.lang.getAppString(value.vname),
                type: value.type
            });
        });
        this.filterFields = _.filter(this.filterFields, function(el) {
            // Double-bang intended. Coerces values like 'undefined' to a bool.
            return !!self.filterOperatorMap[el.type];
        });
    },

    render: function() {
        app.view.View.prototype.render.call(this);
        this.addRow();
    },

    addRow: function(e) {
        var stuff = this.rowTemplate(this),
            target;
        if(_.isUndefined(e)) {
            target = this.$(".filter-options");
        } else {
            var $parent = this.$(e.currentTarget).parents('.filter-body'), old;
            target = this.$(e.currentTarget).parents('.filter-options');
            if($parent.hasClass('newRow')) {
                $parent.removeClass('newRow');
                $parent.find('.addme').addClass('hide');
            }
        }
        target.append(stuff);
        this.$(".newRow select.field_name").chosen();
    },

    editName: function(e) {
        if(this.$(e.currentTarget).find('input').val() === '') {
            this.$(".save_button").addClass("disabled");
        }
    },

    removeRow: function(e) {
        var $parent = this.$(e.currentTarget).parents('.filter-body');
        var newRow = $parent.hasClass('newRow');
        this.notSaved();
        this._disposeField($parent);
        $parent.remove();
        if(newRow) {
            this.addRow();
        }
    },

    chooseField: function(e) {
        this.notSaved();
        var $el = this.$(e.currentTarget),
            $parent = $el.parents('.filter-body'),
            fieldName = $el.val(),
            fieldType = app.metadata.getModule(this.title).fields[fieldName].type;
        $parent.find('.filter-operator').removeClass('hide').find('option').remove();
        $parent.find('.filter-value').addClass('hide').empty();
        var types = this.filterOperatorMap[fieldType] || this.filterOperatorMap['base'];
        if(types[0] !== '') types.unshift('');
        _.each(types, function(t) {
            $('<option />').appendTo($parent.find('select.operator')).attr('value', t).text(t);
        });
        $parent.find("select.operator").chosen({
            allow_single_deselect: true,
            disable_search_threshold: 10
        });
        $parent.find("select.operator").trigger("liszt:updated");
        this._disposeField($parent);
    },

    chooseOperator: function(e) {
        var $el = this.$(e.currentTarget),
            $parent = $el.parents('.filter-body'),
            operation = $el.val(),
            fieldName = $parent.find('select.field_name').val(),
            fieldType = app.metadata.getModule(this.title).fields[fieldName].type;

        this._disposeField($parent);

        if(operation !== '') {
            if(fieldType == 'datetime') {
                fieldType = 'datetimecombo';
            }
            var obj = {
                view: this,
                viewName: 'edit',
                def: {
                    type: fieldType
                }
            };
            if(fieldType == 'enum') {
                obj.def.options = app.lang.getAppListStrings(fieldName + '_dom');
            }
            var field = app.view.createField(obj);

            $parent.find('.filter-value').removeClass('hide').find('input, select').remove();
            $(field.getPlaceholder().string).appendTo($parent.find('.filter-value'));
            this._renderField(field);
            $parent.data('value_field', field);
        }
    },

    modifyValue: function(e) {
        var $el = this.$(e.currentTarget),
            $parent = $el.parents('.filter-body'),
            modified = false,
            kls = $parent.hasClass('newRow') ? '.addme' : '.updateme';
        _.each($el.find('input'), function(i) {
            if($(i).val() !== '') modified = true;
        });
        $parent.find(kls).toggleClass('hide', !modified);
    },

    updateRow: function(e) {
        var $el = this.$(e.currentTarget),
            $parent = $el.parents('.filter-body');
        $parent.find('.updateme').addClass('hide');
    },

    triggerClose: function() {
        this.layout.trigger("filter:create:close:fire");
    },

    notSaved: function() {
        if(this.$(".filter-header").find('input').val() !== '') {
            this.$(".save_button").removeClass("disabled");
        }
        this.$(".delete_button").removeClass("hide");
    },

    save: function() {
        this.$(".save_button").addClass("disabled");
        this.$(".delete_button").removeClass("hide");
    },

    removeAll: function() {
        // TODO: Make a delete request to the server.
        var self = this;
        _.each(this.$(".filter-body"), function(el) {
            self._disposeField($(el));
        });
        this.render();
    },

    _disposeField: function($parent) {
        if(_($parent.data('value_field')).isObject()) {
            $parent.data('value_field').dispose();
            $parent.data('value_field', '');
        }
        $parent.find('.addme').addClass('hide');
    }
})
