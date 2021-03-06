/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/*
Represents header view PageObject
 */
import BaseView from './base-view';
import {seedbed} from '@sugarcrm/seedbed';
/**
 * @class RecordHeaderView
 * @extends BaseView
 */
export default class RecordHeaderView extends BaseView {

    constructor(options) {
        super(options);

        this.selectors = this.mergeSelectors({
            $: '.headerpane',
            buttons: {
                'create': 'a[name="create_button"]:not(.hide)',
                'copy': 'a[name="duplicate_button"]:not(.hide)',
                'cancel': 'a[name="cancel_button"]:not(.hide)',
                'close': 'a[name="close"]:not(.hide)',
                'add': 'a[name="link_button"]:not(.hide)',
                'save': 'a[name="save_button"]:not(.hide)',
                'edit': 'a[name="edit_button"]:not(.hide)',
                'delete': 'a[name="delete_button"]:not(.hide)',
                'createopportunity': 'a[name="convert_to_opportunity_button"]:not(.hide)',
                'generatequote': 'a[name="convert_to_quote_button"]:not(.hide)',
                'convert': 'a[name="lead_convert_button"]:not(.hide)',
                'actions': '.actions:not([style*="display: none"]) a.btn.dropdown-toggle',
                'reply': 'a[name="reply_button"]:not(.hide)',
                'eraseandcomplete': 'a[name="erase_complete_button"]:not(.hide)',
                'markforerasure': 'a[name="mark_for_erasure_button"]:not(.hide)',
                'reject': 'a[name="reject_button"]:not(.hide)',
            },

            title: {
                'old': 'h1 [data-name="title"] span.list-headerpane',
                'new': 'h1 [data-name="title"] span.list-headerpane div'
            }
        });
    }

    public async clickButton(buttonName) {
        return this.driver.click(this.$(`buttons.${buttonName.toLowerCase()}`));
    }

    public async checkIsButtonActive(buttonName) {
        let isDisabled = await this.driver.isExisting(this.$(`buttons.${buttonName.toLowerCase()}`) + '.disabled');
        return !isDisabled;
    }
}
