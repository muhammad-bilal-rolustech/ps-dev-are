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
*}

<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='modules/Connectors/tpls/tabs.css'}"/>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td colspan='100'><h2>{$title}</h2></td></tr>
<tr><td colspan='100'>
{$description}
</td></tr><tr><td><br></td></tr><tr><td colspan='100'>

<form name="ConfigureSubs" method="POST"  method="POST" action="index.php">
	
			<form enctype="multipart/form-data" id="SubsForm" name="SubsForm" method="POST" action="index.php">
			<input type="hidden" name="module" value="Campaigns">
			<input type="hidden" name="action" value="Subscriptions">
			<input type="hidden" name="enabled_subs" value="">
			<input type="hidden" name="disabled_subs" value="">
			<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
			<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
			<input type="hidden" name="module_tab" value="{$smarty.request.module_tab}">
			<input type="hidden" name="orig_disabled_values" id="orig_disabled_values" value="{$disabled_subs_string}">						
			<input type="hidden" name="orig_enabled_values" id="orig_enabled_values" value="{$enabled_subs_string}">						
			<input type="hidden" name="record" value="{$RECORD}">
			<input type="hidden" name="subs_action" value="process">			


	<table border="0" cellspacing="1" cellpadding="1">
		<tr>
			<td>
				<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button" onclick="save();this.form.action.value='Subscriptions'; " type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " > 
				<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="this.form.action.value='{$RETURN_ACTION}'; this.form.module.value='{$RETURN_MODULE}';" type="submit" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
			</td>
		</tr>
	</table>

	<div class='add_table' style='margin-bottom:5px'>
		<table id="ConfigureSubs" class="themeSettings edit view" style='margin-bottom:0px;' border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><span><b>{$MOD.LBL_ALREADY_SUBSCRIBED_HEADER}</b></span></td>
				<td><span><b>{$MOD.LBL_UNSUBSCRIBED_HEADER}</b>{sugar_help text=$MOD.LBL_UNSUBSCRIBED_HEADER_EXPL }
				
				</span></td>
			</tr>
			<tr>
				<td width='1%'>
					<div id="enabled_div" class="enabled_tab_workarea">
						<ul id="enabled_ul" class="module_draglist">
						{foreach from=$enabled_subs key=dirname item=name}
							<li id="{$dirname}" class="noBullet2">{$name}</li>
						{/foreach}
						</ul>
					</div>
				</td>
				<td>
					<div id="disabled_div" class="disabled_tab_workarea">
						<ul id="disabled_ul" class="module_draglist">
						{foreach from=$disabled_subs key=dirname item=name}
							<li id="{$dirname}" class="noBullet2">{$name}</li>
						{/foreach}
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</div>
	
	<table border="0" cellspacing="1" cellpadding="1">
		<tr>
			<td>
				<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button" onclick="save();this.form.action.value='Subscriptions'; " type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " > 
				<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="this.form.action.value='{$RETURN_ACTION}'; this.form.module.value='{$RETURN_MODULE}';" type="submit" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">

			</td>
		</tr>
	</table>
</form>


<script type="text/javascript">
{literal}

var Dom = YAHOO.util.Dom; 
var Event = YAHOO.util.Event; 
var DDM = YAHOO.util.DragDropMgr;

function save() {
    var enabled_display_vals = '';
    var disabled_display_vals = '';

    //Get the enabled div elements
    var elements = document.getElementById('enabled_div');
    //Get the li elements
 	var enabled_list = YAHOO.util.Dom.getElementsByClassName('noBullet2', 'li', elements);
    for(var li in enabled_list) {
        if(typeof enabled_list[li] != 'function') {
            enabled_display_vals += ',' + enabled_list[li].getAttribute('id');
        }
    }
    document.ConfigureSubs.enabled_subs.value = enabled_display_vals != '' ? enabled_display_vals.substr(1,enabled_display_vals.length) : '';
    
    var elements = document.getElementById('disabled_div');
    //Get the li elements
 	var disabled_list = YAHOO.util.Dom.getElementsByClassName('noBullet2', 'li', elements);
    for(var li in disabled_list) {
        if(typeof disabled_list[li] != 'function') {
            disabled_display_vals += ',' + disabled_list[li].getAttribute('id');
        }
    }
    
    document.ConfigureSubs.disabled_subs.value = disabled_display_vals != '' ? disabled_display_vals.substr(1,disabled_display_vals.length) : '';
}


(function() {

YAHOO.example.DDApp = { 
init: function() { 
{/literal}	    
	new YAHOO.util.DDTarget("enabled_ul"); 
	new YAHOO.util.DDTarget("disabled_ul");
	
	{foreach from=$enabled_subs key=module item=moduleDisplay}
    {if $module != $currentTheme}new YAHOO.example.DDList("{$module}");{/if}
	{/foreach}	
	 
	{foreach from=$disabled_subs key=module item=moduleDisplay}
	     new YAHOO.example.DDList("{$module}");
	{/foreach} 
{literal}	        
}
};

YAHOO.example.DDList = function(id, sGroup, config) { 
    YAHOO.example.DDList.superclass.constructor.call(this, id, sGroup, config); 
    var el = this.getDragEl(); 
    Dom.setStyle(el, "opacity", 0.67);
    this.goingUp = false; 
    this.lastY = 0; 
}; 


YAHOO.extend(YAHOO.example.DDList, YAHOO.util.DDProxy, {

    startDrag: function(x, y) {
        
        // make the proxy look like the source element
        var dragEl = this.getDragEl();
        var clickEl = this.getEl();
        Dom.setStyle(clickEl, "visibility", "hidden");

        dragEl.innerHTML = clickEl.innerHTML;

        Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
        Dom.setStyle(dragEl, "border", "2px solid gray");
    },

    endDrag: function(e) {

        var srcEl = this.getEl();
        var proxy = this.getDragEl();

        // Show the proxy element and animate it to the src element's location
        Dom.setStyle(proxy, "visibility", "");
        var a = new YAHOO.util.Motion( 
            proxy, { 
                points: { 
                    to: Dom.getXY(srcEl)
                }
            }, 
            0.2, 
            YAHOO.util.Easing.easeOut 
        )
        var proxyid = proxy.id;
        var thisid = this.id;

        // Hide the proxy and show the source element when finished with the animation
        a.onComplete.subscribe(function() {
                Dom.setStyle(proxyid, "visibility", "hidden");
                Dom.setStyle(thisid, "visibility", "");
            });
        a.animate();
    },

    onDragDrop: function(e, id) {

        // If there is one drop interaction, the li was dropped either on the list,
        // or it was dropped on the current location of the source element.
        if (DDM.interactionInfo.drop.length === 1) {

            // The position of the cursor at the time of the drop (YAHOO.util.Point)
            var pt = DDM.interactionInfo.point; 

            // The region occupied by the source element at the time of the drop
            var region = DDM.interactionInfo.sourceRegion; 

            // Check to see if we are over the source element's location.  We will
            // append to the bottom of the list once we are sure it was a drop in
            // the negative space (the area of the list without any list items)
            if (!region.intersect(pt)) {
                var destEl = Dom.get(id);
                var destDD = DDM.getDDById(id);
                destEl.appendChild(this.getEl());
                destDD.isEmpty = false;
                DDM.refreshCache();
            }

        }
    },

    onDrag: function(e) {

        // Keep track of the direction of the drag for use during onDragOver
        var y = Event.getPageY(e);

        if (y < this.lastY) {
            this.goingUp = true;
        } else if (y > this.lastY) {
            this.goingUp = false;
        }

        this.lastY = y;
    },

    onDragOver: function(e, id) {
    
        var srcEl = this.getEl();
        var destEl = Dom.get(id);

        // We are only concerned with list items, we ignore the dragover
        // notifications for the list.
        if (destEl.nodeName.toLowerCase() == "li") {
            var orig_p = srcEl.parentNode;
            var p = destEl.parentNode;

            if (this.goingUp) {
                p.insertBefore(srcEl, destEl); // insert above
            } else {
                p.insertBefore(srcEl, destEl.nextSibling); // insert below
            }

            DDM.refreshCache();
        }
    }
});

Event.onDOMReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);

})();
{/literal}
</script>
<!-- END: main -->

