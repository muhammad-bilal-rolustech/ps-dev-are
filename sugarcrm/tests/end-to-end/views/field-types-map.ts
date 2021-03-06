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
import * as TextField from '../fields/text-field';
import * as TextareaField from '../fields/textarea-field';
import * as NameField from '../fields/name-field';
import * as EnumField from '../fields/enum-field';
import * as PricingFormula from '../fields/pricing_formula';
import * as IntField from '../fields/int-field';
import * as FloatField from '../fields/float-field';
import * as DateField from '../fields/date-field';
import * as RelateField from '../fields/relate-field';
import * as QuoteDataRelateField from '../fields/quote-data-relate';
import * as CopyField from '../fields/copy-field';
import * as CurrencyField from '../fields/currency-field';
import * as UrlField from '../fields/url-field';
import * as FullnameField from '../fields/fullname-field';
import * as TagField from '../fields/tag-field';
import * as EmailField from '../fields/email-field';
import * as DiscountSelectField from '../fields/discount-select-field';
import * as BoolField from '../fields/bool-field';
import * as TeamsetField from '../fields/teamset-field';
import * as ParentField from '../fields/parent-field';
import * as BadgeField from '../fields/badge-field';
import * as QuoteGroupTitleField from '../fields/quote-group-title-field';

export const FIELD_TYPES__MAP = {
    name: NameField,
    phone: TextField,
    discount: FloatField,
    fullname: FullnameField,
    url: UrlField,
    text: TextField,
    textarea: TextareaField,
    enum: EnumField,
    tag: TagField,
    int: IntField,
    date: DateField,
    float: FloatField,
    relate: RelateField,
    taxrate: RelateField,
    'quote-data-relate': QuoteDataRelateField,
    checkbox: CopyField,
    bool:BoolField,
    copy: CopyField,
    currency: CurrencyField,
    email: EmailField,
    'currency-type-dropdown': EnumField,
    'currency_id': EnumField,
    'quote-footer-currency': CurrencyField,
    'discount-select':DiscountSelectField,
    'pricing-formula':PricingFormula,
    teamset: TeamsetField,
    parent: ParentField,
    badge: BadgeField,
    'quote-group-title': QuoteGroupTitleField
};

