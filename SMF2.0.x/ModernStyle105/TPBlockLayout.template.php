<?php
/**
 * @package TinyPortal
 * @version 1.2
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2015 - The TinyPortal Team
 *
 */

function template_tp_above()
{
	global $context;

	if(!empty($context['TPortal']['upshrinkpanel']))
	{
		echo '
	<div class="clear nomarginb">';

		$panels = array('Left', 'Right', 'Center', 'Top', 'Bottom', 'Lower');
		if($context['TPortal']['showcollapse'] == 1)
		{
			foreach($panels as $pa => $pan)
			{
				$side = strtolower($pan);
				if($context['TPortal'][$side.'panel'] == 1)
				{
					// add to the panel
					if($pan == 'Left' || $pan == 'Right')
						ms_tp_hidepanel2('tp' . strtolower($pan) . 'barHeader', 'tp' . strtolower($pan) . 'barContainer', strtolower($pan).'-tp-upshrink_description');
					else
						ms_tp_hidepanel2('tp' . strtolower($pan) . 'barHeader', '', strtolower($pan).'-tp-upshrink_description');
			
				}
			}
		}

		echo '
	</div>';
	}
	if($context['TPortal']['toppanel']==1)
		echo '
	<div id="tptopbarHeader" style="' , in_array('tptopbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , 'clear: both;">
	'	, TPortal_panel('top') , '
	</div>';	

	echo '
	<table cellpadding="0" cellspacing="0" id="portalContainer" width="100%" style="margin: 0; padding: 0; table-layout: fixed; clear: both;">
		<tr id="portalc2">';

	// TinyPortal integrated bars
	if($context['TPortal']['leftpanel']==1)
	{
		echo '
			<td id="tpleftbarContainer" style="width:' , ($context['TPortal']['leftbar_width']) , 'px; ' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '" valign="top">
				<div id="tpleftbarHeader" style="' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , $context['TPortal']['useroundframepanels']==1 ?
				'<span class="upperframe"><span></span></span>
				<div class="roundframe" style="overflow: auto;">' : ''
				, TPortal_panel('left') , 
				$context['TPortal']['useroundframepanels']==1 ?
				'</div>
				<span class="lowerframe"><span></span></span>' : '' , '
				</div>
			</td>';

	}
	echo '		
			<td align="left" valign="top" width="100%" id="midbarContainer">
				<div id="tpcontentHeader">';
  
	if($context['TPortal']['centerpanel']==1)
		echo '
				<div id="tpcenterbarHeader" style="' , in_array('tpcenterbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('center') , '
				</div>';

	echo '
			</div>';
}

function ms_tp_hidepanel2($id, $id2, $alt)
{
	global $txt, $context, $settings;
	
	echo '
	<a title="'.$txt[$alt].'" style="cursor: pointer;" name="toggle_'.$id.'" onclick="ms_togglepanel(\''.$id.'\');ms_togglepanel(\''.$id2.'\')">
		<span id="toggle_' . $id . '" class="icon-toggle-' . (in_array($id, $context['tp_panels']) ? 'on' : 'off') . '"></span>
	</a>';
	
}

function template_tp_below()
{
	global $context, $scripturl;

	if($context['TPortal']['lowerpanel']==1)
		echo '
				<div id="tplowerbarHeader" style="' , in_array('tplowerbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('lower') , '</div>';	

	echo '</div>
			</td>';

	// TinyPortal integrated bars
	if($context['TPortal']['rightpanel']==1)
	{
		echo '
			<td id="tprightbarContainer" style="width:' ,$context['TPortal']['rightbar_width'], 'px;' , in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '" valign="top">
				<div id="tprightbarHeader" style="' , in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , $context['TPortal']['useroundframepanels']==1 ?
				'<span class="upperframe"><span></span></span>
				<div class="roundframe">' : ''
				, TPortal_panel('right') , 
				$context['TPortal']['useroundframepanels']==1 ?
				'</div>
				<span class="lowerframe"><span></span></span>' : '' , '
				</div>
			</td>';

	}
	
	echo '
		</tr>
	</table>

			<script type="text/javascript"><!-- // --><![CDATA[
				function ms_toggle( targetId )
				{
					var state = 0;
					var blockname = "block" + targetId;
					var blockimage = "blockcollapse" + targetId;

					if ( document.getElementById ) {
						target = document.getElementById( blockname );
						if ( target.style.display == "none" ) {
							target.style.display = "";
							state = 1;
						}
						else {
							target.style.display = "none";
							state = 0;
						}

						document.getElementById( blockimage ).className = state ? "icon-toggle-off" : "icon-toggle-on";
						var tempImage = new Image();
						tempImage.src = "'.$scripturl.'?action=tpmod;upshrink=" + targetId + ";state=" + state + ";" + (new Date().getTime());

					}
				}
		// ]]></script>
	  <script type="text/javascript"><!-- // --><![CDATA[
		' . (sizeof($context['tp_panels']) > 0 ? '
		var tpPanels = new Array(\'' . (implode("','",$context['tp_panels'])) . '\');' : '
		var tpPanels = new Array();') . '
		function ms_togglepanel( targetID )
		{
			var pstate = 0;
			var panel = targetID;
			var img = "toggle_" + targetID;
			var ap = 0;

			if ( document.getElementById ) {
				target = document.getElementById( panel );
				if ( target.style.display == "none" ) {
					target.style.display = "";
					pstate = 1;
					removeFromArray(targetID, tpPanels);
					document.cookie="tp_panels=" + tpPanels.join(",") + "; expires=Wednesday, 01-Aug-2040 08:00:00 GMT";
					document.getElementById(img).className = \'icon-toggle-off\';
				}
				else {
					target.style.display = "none";
					pstate = 0;
					tpPanels.push(targetID);
					document.cookie="tp_panels=" + tpPanels.join(",") + "; expires=Wednesday, 01-Aug-2040 08:00:00 GMT";
					document.getElementById(img).className = \'icon-toggle-on\';
				}
			}
		}
		// ]]></script>
			';
	

	if($context['TPortal']['bottompanel']==1)
		echo '
		<div id="tpbottombarHeader" style="clear: both;' , in_array('tpbottombarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('bottom') , '
		</div>';	
	
}

?>