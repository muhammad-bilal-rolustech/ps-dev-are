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
import {Detail, List as RelateList, Edit as RelateEdit, Preview} from './relate-field';
import BaseField from './text-field';
import {seedbed} from '@sugarcrm/seedbed';

export class Edit extends BaseField {

    private itemSelector: string;
    private inputSelector: string;
    private newItemSelector: string;

    constructor(options) {
        super(options);

        this.selectors = this.mergeSelectors({
            $: '[field-name={{name}}]',
            field: {
                selector: '.select2-container.select2',
            }
        });

        this.itemSelector = '.select2-result-label=';
        this.inputSelector = '.select2-input.select2-focused';
        this.newItemSelector = '.select2-highlighted';

    }

    public async getText(selector: string): Promise<string> {

        let value: string | string[] = await this.driver.getText(selector);

        return value.toString().trim();

    }

    public async setValue(val: any): Promise<void> {

        await this.driver.click(this.$('field.selector'));
        await this.driver.setValue(this.inputSelector, val);

        // TODO remove this pause later!!!, waitForApp should handle this case for select2 control
        await this.driver.pause(4000);
        await this.driver.waitForApp();

        const elementExists = await this.driver.isExisting(`${this.itemSelector}${val}`);

        if (elementExists) {
            await this.driver.click(`${this.itemSelector}${val}`);
        } else {
            await this.driver.click(`${this.newItemSelector}`);
        }
    }

}

export class List extends RelateList {

    constructor(options) {
        super(options);

        this.selectors = this.mergeSelectors({
            field: {
                selector: 'a'
            }
        });

    }

    public async click(): Promise<void> {
        let selector = this.$('field.selector', {name: this.name});

        await this.driver.scroll(selector)
            .click(selector);
    }

}

export {Detail, Preview} from './relate-field';
