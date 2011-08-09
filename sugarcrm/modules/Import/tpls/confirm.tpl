{*

/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: step3.tpl 25541 2007-01-11 21:57:54Z jmertic $

*}
{literal}
<style >


</style>
{/literal}

{$INSTRUCTION}

<div class="hr"></div>

<form enctype="multipart/form-data" real_id="importconfirm" id="importconfirm" name="importconfirm" method="POST" action="index.php">
<input type="hidden" name="module" value="Import">
<input type="hidden" name="type" value="{$TYPE}">
<input type="hidden" name="source" id="source" value="{$SOURCE}">
<input type="hidden" name="source_id" value="{$SOURCE_ID}">
<input type="hidden" name="action" value="Step3">
<input type="hidden" name="import_module" value="{$IMPORT_MODULE}">
<input type="hidden" name="import_type" value="{$TYPE}">
<input type="hidden" name="file_name" value="{$FILE_NAME}">
<input type="hidden" name="current_step" value="{$CURRENT_STEP}">
<input type="hidden" name="from_admin_wizard" value="{$smarty.request.from_admin_wizard}">
    
{if $AUTO_DETECT_ERROR != ''}
    <div class="errorMessage">
        <span class="error">{$AUTO_DETECT_ERROR}</span>
    </div>
{/if}

<div id="confirm_table" class="confirmTable">
{include file='modules/Import/tpls/confirm_table.tpl'}
</div>



    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left" colspan="4" style="background: transparent;">
                <input title="{$MOD.LBL_SHOW_ADVANCED_OPTIONS}" accessKey="" id="toggleImportOptions" class="button" type="button"
                       name="button" value="  {$MOD.LBL_SHOW_ADVANCED_OPTIONS}  "> {sugar_help text=$MOD.LBL_IMPORT_FILE_SETTINGS_HELP}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
            <div style="overflow: auto; width: 1056px;">
                <table border=0 class="edit view noBorder" style="display: none;" id="importOptions">
                    <tr>
                        <td scope="row">
                            <slot>{$MOD.LBL_CHARSET}</slot>
                        </td>
                        <td>
                            <slot><select tabindex='4' name='importlocale_charset'>{$CHARSETOPTIONS}</select></slot>
                        </td>
                        <td scope="row">
                            <slot>{$MOD.LBL_CUSTOM_DELIMITER}</slot>
                        </td>
                        <td>
                            <slot><input type="text" id="custom_delimiter" name="custom_delimiter" value="{$CUSTOM_DELIMITER}" style="width: 5em;" maxlength="1" />
                            {sugar_help text=$MOD.LBL_FIELD_DELIMETED_HELP}
                            </slot>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <slot>{$MOD.LBL_CUSTOM_ENCLOSURE}</slot>
                        </td>
                        <td>
                            <slot>
                                <select name="custom_enclosure" id="custom_enclosure">
                                {$IMPORT_ENCLOSURE_OPTIONS}
                                </select>
                                <input type="text" name="custom_enclosure_other" id="custom_enclosure_other" style="display: none; width: 5em;" maxlength="1" />
                            {sugar_help text=$MOD.LBL_ENCLOSURE_HELP}
                            </slot>
                        </td>
                        <td scope="row">
                        {$MOD.LBL_HAS_HEADER}
                        </td>
                        <td>
                            <input class="checkBox" value='on' type="checkbox" name="has_header" id="has_header" {$HAS_HEADER_CHECKED}> {sugar_help text=$MOD.LBL_HEADER_ROW_OPTION_HELP}
                        </td>
                    </tr>
                    <tr>
                        <td scope="row"><slot>{$MOD.LBL_DATE_FORMAT}</slot></td>
                        <td ><slot><select tabindex='4' name='importlocale_dateformat'>{$DATEOPTIONS}</select></slot></td>
                        <td scope="row"><slot>{$MOD.LBL_TIME_FORMAT}</slot></td>
                        <td ><slot><select tabindex='4' name='importlocale_timeformat'>{$TIMEOPTIONS}</select></slot></td>
                    </tr>
                    <tr>
                        <td scope="row"><slot>{$MOD.LBL_TIMEZONE}</slot></td>
                        <td ><slot><select tabindex='4' name='importlocale_timezone'>{html_options options=$TIMEZONEOPTIONS selected=$TIMEZONE_CURRENT}</select></slot></td>
                        <td scope="row"><slot>{$MOD.LBL_CURRENCY}</slot></td>
                        <td ><slot>
                            <select tabindex='4' id='currency_select' name='importlocale_currency' onchange='setSymbolValue(this.selectedIndex);setSigDigits();'>{$CURRENCY}</select>
                            <input type="hidden" id="symbol" value="">
                        </slot></td>
                    </tr>
                    <tr>
                        <td scope="row"><slot>{$MOD.LBL_CURRENCY_SIG_DIGITS}:</slot></td>
                        <td ><slot><select id='sigDigits' onchange='setSigDigits(this.value);' name='importlocale_default_currency_significant_digits'>{$sigDigits}</select>
                        </slot></td>
                        <td scope="row"><slot><i>{$MOD.LBL_LOCALE_EXAMPLE_NAME_FORMAT}</i>:</slot></td>
                        <td ><slot><input type="text" disabled id="sigDigitsExample" name="sigDigitsExample"></slot></td>
                    </tr>
                    <tr>
                        <td scope="row"><slot>{$MOD.LBL_NUMBER_GROUPING_SEP}</slot></td>
                        <td ><slot>
                            <input tabindex='4' name='importlocale_num_grp_sep' id='default_number_grouping_seperator'
                                   type='text' maxlength='1' size='1' value='{$NUM_GRP_SEP}' onkeydown='setSigDigits();' onkeyup='setSigDigits();'>
                        </slot></td>
                        <td scope="row"><slot>{$MOD.LBL_DECIMAL_SEP}</slot></td>
                        <td ><slot>
                            <input tabindex='4' name='importlocale_dec_sep' id='default_decimal_seperator'
                                   type='text' maxlength='1' size='1' value='{$DEC_SEP}' onkeydown='setSigDigits();' onkeyup='setSigDigits();'>
                        </slot></td>
                    </tr>
                    <tr>
                        <td scope="row" valign="top">{$MOD.LBL_LOCALE_DEFAULT_NAME_FORMAT}: </td>
                        <td  valign="top">
                            <input onkeyup="setPreview();" onkeydown="setPreview();" id="default_locale_name_format" type="text" tabindex='4' name="importlocale_default_locale_name_format" value="{$default_locale_name_format}">
                            <br />{$MOD.LBL_LOCALE_NAME_FORMAT_DESC}
                        </td>
                        <td scope="row" valign="top"><i>{$MOD.LBL_LOCALE_EXAMPLE_NAME_FORMAT}:</i> </td>
                        <td  valign="top"><input tabindex='4' id="nameTarget" name="no_value" id=":q" value="" style="border: none;" disabled size="50"></td>
                    </tr>
                </table>
            </div>
                
            </td>
        </tr>
        <tr>
            <td colspan="2"><div class="hr" style="margin-top: 0px;"></div></td>
        </tr>
        <tr>
            <td colspan="2"><h3>{$MOD.LBL_THIRD_PARTY_CSV_SOURCES}&nbsp;{sugar_help text=$MOD.LBL_THIRD_PARTY_CSV_SOURCES_HELP}</h3></td>
        </tr>
        <tr>
            <td colspan="2" scope="row"><input class="radio" type="radio" name="external_source" value="" id='none'/>&nbsp;{$MOD.LBL_NONE}</td>
        </tr>
        <tr>
            <td colspan="2" scope="row"><input class="radio" type="radio" name="external_source" value="salesforce" id='sf_map'/>&nbsp;{$MOD.LBL_SALESFORCE}</td>
        </tr>
        <tr>
            <td colspan="2" scope="row"><input class="radio" type="radio" name="external_source" value="outlook" id='outlook_map'/>&nbsp;{$MOD.LBL_MICROSOFT_OUTLOOK}&nbsp;{sugar_help text=$MOD.LBL_MICROSOFT_OUTLOOK_HELP}</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
