<?php
// Version: 1.0 beta5; TPBlockLayout
// For use with SMF v2.0

function template_tp_above()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

	if(!empty($context['TPortal']['upshrinkpanel']))
		echo '
	<div style="float: right; margin-right: 0.5em; margin-top: -1.5em;">', $context['TPortal']['upshrinkpanel'] , '</div>';

	if($context['TPortal']['toppanel']==1)
		echo '
	<div id="tptopbarHeader" style="' , in_array('tptopbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , 'clear: both;">
	'	, TPortal_panel('top') , '
	</div>';	

	echo '
	<table cellpadding="0" cellspacing="0" width="100%" style="margin: 0; padding: 0; table-layout: fixed; clear: both;">
		<tr>';

	// TinyPortal integrated bars
	if($context['TPortal']['leftpanel']==1)
	{
		echo '
			<td id="tpleftbarContainer" style="width:' , ($context['TPortal']['leftbar_width']) , 'px; ' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '" valign="top">
				<div id="tpleftbarHeader" style="' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">'
				, TPortal_panel('left') , '
				</div>
			</td>';

	}
	echo '		
			<td align="left" valign="top" width="100%">
				<div id="tpcontentHeader">';
  
	if($context['TPortal']['centerpanel']==1)
		echo '
				<div id="tpcenterbarHeader" style="' , in_array('tpcenterbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('center') , '
				</div>';	

}

function template_tp_below()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

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
				<div id="tprightbarHeader" style="' , in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">'
				, TPortal_panel('right') , '
				</div>
			</td>';

	}

	
	echo '
		</tr>
	</table>';

	if($context['TPortal']['bottompanel']==1)
		echo '
		<div id="tpbottombarHeader" style="clear: both;' , in_array('tpbottombarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('bottom') , '
		</div>';	
	
}

?>