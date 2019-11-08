<?php
// Version: 1.0 beta5; TPBlockLayout
// For use with SMF v2.0 RC2

function template_tp_above()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

	if(!empty($context['TPortal']['upshrinkpanel']))
		echo '
	<div style="float: right; margin-right: 0.5em; margin-top: -1.5em;">', $context['TPortal']['upshrinkpanel'] , '</div>';

	if($context['TPortal']['toppanel']==1)
		echo '
	<div id="tptopbarHeader" style="' , in_array('tptopbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , 'clear: both; padding: 0 5px; margin: 0 0 4px 0;">
			'	, TPortal_panel('top') , '
	</div>';	

	echo '
	<table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 0; table-layout: fixed;">
		<tr>
			<td width="100%" align="left" valign="top" style="padding-top: 0px; padding-bottom: 10px;">';
  
	if($context['TPortal']['centerpanel']==1)
		echo '
				<div id="tpcenterbarHeader" style="padding-bottom: 5px;' , in_array('tpcenterbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('center') , '
				</div>';	

}

function template_tp_below()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

	if($context['TPortal']['lowerpanel']==1)
		echo '
				<div id="tplowerbarHeader" style="padding-top: 4px;' , in_array('tplowerbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('lower') , '</div>';	

	echo '
			</td>
		</tr>
	</table>';

	if($context['TPortal']['bottompanel']==1)
		echo '
		<div id="tpbottombarHeader" style="clear: both;' , in_array('tpbottombarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('bottom') , '
		</div>';	
	echo '<div id="tportal" class="smalltext">' , tportal_version() , '</div>';
}

?>