{*
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
*}
<div id="{$source_id}_add_tables" class="sources_table_div">
{foreach from=$display_data key=module item=data}

<table border="0" width="100%">
<tr>
<td colspan="2"><span><font size="3">{sugar_translate label=$module}</font></span></td></tr>
<tr>
<td width="150px"><b>{$mod.LBL_CONNECTOR_FIELDS}</b></td>
<td><b>{$mod.LBL_MODULE_FIELDS}</b></td>
</tr>
</table>

<table border="0" name="{$module}" id="{$module}" class="mapping_table" width="100%">
<tr>
<td colspan="2">
{foreach from=$data.field_keys key=field_id item=field}
{if $field_id != 'id'}
<div id="{$source_id}:{$module}:{$field}_div" style="width:500px; display:block; cursor:pointer">
<table border="0" cellpadding="1" cellspacing="1">
<tr>
<td width="150px">
{$field}
</td>
<td>
<select id="{$source_id}:{$module}:{$field_id}">
<option value="">---</option>
{foreach from=$data.available_fields key=available_field_id item=available_field}
<option value="{$available_field_id}" {if $data.field_mapping.$field_id == $available_field_id}SELECTED{/if}>{$available_field}</option>
{/foreach}
</select>
</td>
</tr>
</table>
</div>
{/if}
{/foreach}
</td>
</tr>
</table>

<hr/>
{/foreach}
</div>

{if $empty_mapping}
<h3>{$mod.ERROR_NO_SEARCHDEFS_DEFINED}</h3>
{/if}

{* //BEGIN SUGARCRM flav=int ONLY*}
  
<script type="text/javascript">
{literal}

var Dom = YAHOO.util.Dom; 
var Event = YAHOO.util.Event; 
var DDM = YAHOO.util.DragDropMgr;

(function() {

YAHOO.example.DDApp = { 
init: function() { 
{/literal}	    
{foreach from=$display_data key=module item=data}
{foreach from=$data.field_keys key=field_id item=field}
{if $field_id != 'id'}
new YAHOO.example.DDList("{$source_id}:{$module}:{$field}_div", "{$module}");
{/if}
{/foreach}
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
	        Dom.setStyle(dragEl, "cursor", "pointer");  
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
	        if (typeof(DDM.interactionInfo) != 'undefined' && DDM.interactionInfo.drop.length === 1) { 
	 
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
	 
	        if (destEl.nodeName.toLowerCase() == "div") { 
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

 
YAHOO.util.Event.onDOMReady(function(){
      YAHOO.example.DDApp.init();
 });

})();
{/literal}
</script>

{* //END SUGARCRM flav=int ONLY*}
