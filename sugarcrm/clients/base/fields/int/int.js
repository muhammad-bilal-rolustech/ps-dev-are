({
    unformat:function(value){
        value = this.app.utils.formatNumber(value, 0, 0, "", ".");
        return value;
        },
    format:function(value){
        value = this.app.utils.formatNumber(value, 0, 0, this.fieldDef.number_group_seperator, ".");
        return value;
    }
})