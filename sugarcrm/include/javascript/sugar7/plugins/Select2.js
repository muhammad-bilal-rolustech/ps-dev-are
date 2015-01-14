/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
(function($) {
    $(function() {
        if (!window.Select2) {
            return;
        }
        var originalDestroy = window.Select2.class.abstract.prototype.destroy;

        _.extend(window.Select2.class.abstract.prototype, {
            /**
             * {@inheritDoc}
             *
             * Dispose safe select2 drop mask on destroy.
             */
            destroy: function() {
                if (this.propertyObserver && typeof(this.propertyObserver.disconnect) !== 'function') {
                    this.propertyObserver.disconnect = function() {};
                }
                originalDestroy.call(this);
                var mask = $('#select2-drop-mask');
                mask.remove();
            }
        });

    });
})(jQuery);
