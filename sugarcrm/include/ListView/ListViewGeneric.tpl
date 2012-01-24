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

// $Id: ListViewGeneric.tpl 56945 2010-06-14 19:51:27Z jmertic $

*}

<script type='text/javascript' src='{sugar_getjspath file='include/javascript/popup_helper.js'}'></script>


<script>
{literal}
	$(document).ready(function(){
	    $("ul.clickMenu").each(function(index, node){
	  		$(node).sugarActionMenu();
	  	});
	});
{/literal}	
</script>
{if $overlib}
	<script type='text/javascript' src='{sugar_getjspath file='cache/include/javascript/sugar_grp_overlib.js'}'></script>
	<div id='overDiv' style='position:absolute; visibility:hidden; z-index:1000;'></div>
{/if}
{assign var="currentModule" value = $pageData.bean.moduleDir}
{assign var="singularModule" value = $moduleListSingular.$currentModule}
{assign var="hideTable" value=false}
{if count($data) == 0}
	{assign var="hideTable" value=true}
	<div class="list view listViewEmpty">
	{if strlen($query) == 0}
		<p> 
			{capture assign="createLink"}<a href="?module={$pageData.bean.moduleDir}&action=EditView&return_module={$pageData.bean.moduleDir}&return_action=DetailView">{$APP.LBL_CREATE_BUTTON_LABEL}</a>{/capture}
			{capture assign="importLink"}<a href="?module=Import&action=Step1&import_module={$pageData.bean.moduleDir}&return_module={$pageData.bean.moduleDir}&return_action=index">{$APP.LBL_IMPORT}</a>{/capture}
			{capture assign="helpLink}<a href="">{$APP.LBL_CLICK_HERE}</a>{/capture}
			<p class="msg">
				{$APP.MSG_EMPTY_LIST_VIEW_NO_RESULTS|replace:"<item2>":$createLink|replace:"<item3>":$importLink}
			</p>
			<p class="submsg">
				{$APP.MSG_EMPTY_LIST_VIEW_NO_RESULTS_SUBMSG|replace:"<item1>":$pageData.bean.moduleDir|replace:"<item4>":$helpLink}
			</p>
		</p>
	{else}
		<p class="msg">
			{capture assign="quotedQuery"}"{$query}"{/capture}
			{$APP.MSG_LIST_VIEW_NO_RESULTS|replace:"<item1>":$quotedQuery}
		</p>
		<p class = "submsg">
			<a href="?module={$pageData.bean.moduleDir}&action=EditView&return_module={$pageData.bean.moduleDir}&return_action=DetailView">
				{$APP.MSG_LIST_VIEW_NO_RESULTS_SUBMSG|replace:"<item1>":$quotedQuery|replace:"<item2>":$singularModule}
			</a>
			
		</p>
	{/if}
	</div>
{/if}
{$multiSelectData}

{if hideTable == false}
	<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view'>
	{include file='include/ListView/ListViewPagination.tpl'}
	<tr height='20'>
			{if $prerow}
				<td width='1%' class="td_alt">
					&nbsp;
				</td>
			{/if}
			{* //BEGIN SUGARCRM flav=pro ONLY *}
			{if $favorites}
			<td class='td_alt' >
					&nbsp;
			</td>
			{/if}
			{* //END SUGARCRM flav=pro ONLY *}
			{if !empty($quickViewLinks)}
			<td class='td_alt' width='1%' style="padding: 0px;">&nbsp;</td>
			{/if}
			{counter start=0 name="colCounter" print=false assign="colCounter"}
			{foreach from=$displayColumns key=colHeader item=params}
				<th scope='col' width='{$params.width}%'>
					<div style='white-space: normal;'width='100%' align='{$params.align|default:'left'}'>
	                {if $params.sortable|default:true}
	                    {if $params.url_sort}
	                        <a href='{$pageData.urls.orderBy}{$params.orderBy|default:$colHeader|lower}' class='listViewThLinkS1'>
	                    {else}
	                        {if $params.orderBy|default:$colHeader|lower == $pageData.ordering.orderBy}
	                            <a href='javascript:sListView.order_checks("{$pageData.ordering.sortOrder|default:ASCerror}", "{$params.orderBy|default:$colHeader|lower}" , "{$pageData.bean.moduleDir}{"2_"}{$pageData.bean.objectName|upper}{"_ORDER_BY"}")' class='listViewThLinkS1'>
	                        {else}
	                            <a href='javascript:sListView.order_checks("ASC", "{$params.orderBy|default:$colHeader|lower}" , "{$pageData.bean.moduleDir}{"2_"}{$pageData.bean.objectName|upper}{"_ORDER_BY"}")' class='listViewThLinkS1'>
	                        {/if}
	                    {/if}
	                    {sugar_translate label=$params.label module=$pageData.bean.moduleDir}
						&nbsp;&nbsp;
						{if $params.orderBy|default:$colHeader|lower == $pageData.ordering.orderBy}
							{if $pageData.ordering.sortOrder == 'ASC'}
								{capture assign="imageName"}arrow_down.{$arrowExt}{/capture}
	                            {capture assign="alt_sort"}{sugar_translate label='LBL_ALT_SORT_DESC'}{/capture}
								{sugar_getimage name=$imageName attr='align="absmiddle" border="0" ' alt="$alt_sort"}
							{else}
								{capture assign="imageName"}arrow_up.{$arrowExt}{/capture}
	                            {capture assign="alt_sort"}{sugar_translate label='LBL_ALT_SORT_ASC'}{/capture}
								{sugar_getimage name=$imageName attr='align="absmiddle" border="0" ' alt="$alt_sort"}
							{/if}
						{else}
							{capture assign="imageName"}arrow.{$arrowExt}{/capture}
	                        {capture assign="alt_sort"}{sugar_translate label='LBL_ALT_SORT'}{/capture}
							{sugar_getimage name=$imageName attr='align="absmiddle" border="0" ' alt="$alt_sort"}
						{/if}
	                    </a>
					{else}
	                    {if !isset($params.noHeader) || $params.noHeader == false} 
						  {sugar_translate label=$params.label module=$pageData.bean.moduleDir}
	                    {/if}
					{/if}
					</div>
				</th>
				{counter name="colCounter"}
			{/foreach}
			<td class='td_alt' nowrap="nowrap" width='1%'>&nbsp;</td>
		</tr>
			
		{counter start=$pageData.offsets.current print=false assign="offset" name="offset"}	
		{foreach name=rowIteration from=$data key=id item=rowData}
		    {counter name="offset" print=false}
	        {assign var='scope_row' value=true}
	
			{if $smarty.foreach.rowIteration.iteration is odd}
				{assign var='_rowColor' value=$rowColor[0]}
			{else}
				{assign var='_rowColor' value=$rowColor[1]}
			{/if}
			<tr height='20' class='{$_rowColor}S1'>
				{if $prerow}
				<td width='1%' class='nowrap'>
				 {if !$is_admin && is_admin_for_user && $rowData.IS_ADMIN==1}
						<input type='checkbox' disabled="disabled" class='checkbox' value='{$rowData.ID}'>
				 {else}
	                    <input title="{sugar_translate label='LBL_SELECT_THIS_ROW_TITLE'}" onclick='sListView.check_item(this, document.MassUpdate)' type='checkbox' class='checkbox' name='mass[]' value='{$rowData.ID}'>
				 {/if}
				</td>
				{/if}
				{* //BEGIN SUGARCRM flav=pro ONLY *}
				{if $favorites}
					<td>{$rowData.star}</td>
				{/if}
				{* //END SUGARCRM flav=pro ONLY *}
				{if !empty($quickViewLinks)}
	            {capture assign=linkModule}{if $params.dynamic_module}{$rowData[$params.dynamic_module]}{else}{$pageData.bean.moduleDir}{/if}{/capture}
	            {capture assign=action}{if $act}{$act}{else}EditView{/if}{/capture}
				<td width='2%' nowrap>
	                {if $pageData.rowAccess[$id].edit}
	                <a title='{$editLinkString}' id="edit-{$rowData.ID}"
	href="index.php?module={$linkModule}&offset={$offset}&stamp={$pageData.stamp}&return_module={$linkModule}&action={$action}&record={$rowData.ID}"
	{* //BEGIN SUGARCRM flav=pro ONLY *}
	data-record='{$rowData.ID}' data-module='{if $params.dynamic_module}{$rowData[$params.dynamic_module]}{else}{$pageData.bean.moduleDir}{/if}'
	 data-list = 'true' class="quickEdit"
	{* //END SUGARCRM flav=pro ONLY *}
	                >
	                    {capture name='tmp1' assign='alt_edit'}{sugar_translate label="LNK_EDIT"}{/capture}
	                    {sugar_getimage name="edit_inline.gif" attr='border="0" ' alt="$alt_edit"}</a>
	                {/if}
	            </td>
	
				{/if}
				{counter start=0 name="colCounter" print=false assign="colCounter"}
				{foreach from=$displayColumns key=col item=params}
				    {strip}
					<td {if $scope_row} scope='row' {/if} align='{$params.align|default:'left'}' valign="top" class="{if ($params.type == 'teamset')}nowrap{/if}{if preg_match('/PHONE/', $col)} phone{/if}">
						{if $col == 'NAME' || $params.bold}<b>{/if}
					    {if $params.link && !$params.customCode}
	{capture assign=linkModule}{if $params.dynamic_module}{$rowData[$params.dynamic_module]}{else}{$params.module|default:$pageData.bean.moduleDir}{/if}{/capture}
	{capture assign=action}{if $act}{$act}{else}DetailView{/if}{/capture}
	{capture assign=record}{$rowData[$params.id]|default:$rowData.ID}{/capture}
	{capture assign=url}index.php?module={$linkModule}&offset={$offset}&stamp={$pageData.stamp}&return_module={$linkModule}&action={$action}&record={$record}{/capture}
	                        <{$pageData.tag.$id[$params.ACLTag]|default:$pageData.tag.$id.MAIN} href="{sugar_ajax_url url=$url}">
						{/if}
						{if $params.customCode} 
							{sugar_evalcolumn_old var=$params.customCode rowData=$rowData}
						{else}	
	                       {sugar_field parentFieldArray=$rowData vardef=$params displayType=ListView field=$col}
	                       
						{/if}
						{if empty($rowData.$col) && empty($params.customCode)}&nbsp;{/if}
						{if $params.link && !$params.customCode}
							</{$pageData.tag.$id[$params.ACLTag]|default:$pageData.tag.$id.MAIN}>
	                    {/if}
	                    {if $col == 'NAME' || $params.bold}</b>{/if}
					</td>
					{/strip}
	                {assign var='scope_row' value=false}
					{counter name="colCounter"}
				{/foreach}
				<td align='right'>{$pageData.additionalDetails.$id}</td>
		    	</tr>
		{foreachelse}
		<tr height='20' class='{$rowColor[0]}S1'>
		    <td colspan="{$colCount}">
		        <em>{$APP.LBL_NO_DATA}</em>
		    </td>
		</tr> 
		{/foreach}
	{include file='include/ListView/ListViewPagination.tpl'}
	</table>
{/if}
{if $contextMenus}
<script type="text/javascript">
{$contextMenuScript}
{literal}
function lvg_nav(m,id,act,offset,t){
    if(t.href.search(/#/) < 0){return;}
    else{
        if(act=='pte'){
            act='ProjectTemplatesEditView';
        }
        else if(act=='d'){
            act='DetailView';
        }else if( act =='ReportsWizard'){
            act = 'ReportsWizard';
        }else{
            act='EditView';
        }
    {/literal}
        url = 'index.php?module='+m+'&offset=' + offset + '&stamp={$pageData.stamp}&return_module='+m+'&action='+act+'&record='+id;
        t.href=url;
    {literal}
    }
}{/literal}
{literal}
    function lvg_dtails(id){{/literal}
        return SUGAR.util.getAdditionalDetails( '{$pageData.bean.moduleDir|default:$params.module}',id, 'adspan_'+id);{literal}}{/literal}
{* //BEGIN SUGARCRM flav=pro ONLY *}
{literal}
    if(typeof(qe_init) != 'undefined'){
        qe_init(); //qe_init is defined in footer.tpl
    }
{/literal}
{* //END SUGARCRM flav=pro ONLY *}
</script>
{/if}
