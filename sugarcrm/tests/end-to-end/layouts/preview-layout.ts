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

import {BaseView, seedbed} from '@sugarcrm/seedbed';
import PreviewView from '../views/preview-view';
import PreviewHeaderView from '../views/preview-header-view';
import EnrichedView from '../views/hint/enriched-view';
import NewsView from '../views/hint/news-view';

/**
 * Represents Preview page layout.
 *
 * @class PreviewLayout
 * @extends BaseView
 */
export default class PreviewLayout extends BaseView {

    public PreviewView: PreviewView;
    public defaultView: PreviewView;
    public EnrichedView: EnrichedView;
    public NewsView: NewsView;
    public PreviewHeaderView: PreviewHeaderView;

    constructor(options) {

        super(options);

        this.selectors = this.mergeSelectors({
            $: '#sugarcrm .preview-pane.active',
            showMoreBtn: '.btn.more',
            showLessBtn: '.btn.less',
        });

        this.defaultView = this.PreviewView = this.createComponent(PreviewView);
        this.PreviewHeaderView = this.createComponent(PreviewHeaderView);
        this.EnrichedView = this.createComponent(EnrichedView);
        this.NewsView = this.createComponent(NewsView);

    }
    public async showMore() {
        if (await this.driver.isVisible(this.$('showMoreBtn'))) {
            await this.driver.click(this.$('showMoreBtn'));
        }
    }
    public async showLess() {
        if (await this.driver.isVisible(this.$('showLessBtn'))) {
            await this.driver.click(this.$('showLessBtn'));
        }
    }
}
