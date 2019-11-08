<?php
// Version: 1.0.7; TPBlockLayout

function TP_block($block, $theme, $side, $double=false)
{
	global $context , $scripturl, $settings, $language , $txt;

	if(!empty($settings['centerpanel_tabs']) && $side=='center')
		echo '
	<div id="btab'.$block['id'].'" class="btabb', !empty($context['TPortal']['firsttab']) && $context['TPortal']['firsttab']==$block['id'] ? '' : 'not' , '">';
	
	// its a normal block..
	if(in_array($block['frame'],array('theme','frame','title','none')))
	{		
		if($theme || $block['frame']=='frame')
		{
			echo	'
		<div class="bframe">
			<div class="inner">
				<div class="inner2">';

			// show the frame and title
			if ($theme || $block['frame'] == 'title')
				echo '
					<h3 class="btitle">' . $block['title'], '</h3>';
		}
		else
			echo '
		<div class="nobframe">
			<div>
				<div>';
	
		$func = 'TPortal_' . $block['type'];
		if (function_exists($func))
		{
			if($double)
			{
				// figure out the height
				$h=$context['TPortal']['blockheight_'.$side];
				if(substr($context['TPortal']['blockheight_'.$side],strlen($context['TPortal']['blockheight_'.$side])-2,2)=='px')
					$nh = ((substr($context['TPortal']['blockheight_'.$side],0,strlen($context['TPortal']['blockheight_'.$side])-2)*2) + 43).'px';
				elseif(substr($context['TPortal']['blockheight_'.$side],strlen($context['TPortal']['blockheight_'.$side])-1,1)=='%')
					$nh= (substr($context['TPortal']['blockheight_'.$side],0,strlen($context['TPortal']['blockheight_'.$side])-1)*2).'%';
			}
			echo '<div class="bbody" style="overflow: auto;' , !empty($context['TPortal']['blockheight_'.$side]) ? 'height: '. ($double ? $nh : $context['TPortal']['blockheight_'.$side]) .';' : '' , '">';
			$func($block['id']);
			echo '</div>';
		}
		else
			echo '<div class="bbody" style="overflow: auto;' , !empty($context['TPortal']['blockheight_'.$side]) ? 'height: '.$context['TPortal']['blockheight_'.$side].';' : '' , '">' , doUBBC($block['body']) , '</div>';

		echo '	</div>
			</div>
		</div>';
	}
	// use a pre-defined layout
	else
	{
		// check if the layout actually exist
		if(!isset($context['TPortal']['blocktheme'][$block['frame']]['body']['before']))
			$context['TPortal']['blocktheme'][$block['frame']]=array(
				'frame' =>		array(
									'before' => '',
									'after' => ''),
				'title' =>		array(
									'before' => '',
									'after' => ''),
				'body' =>		array(
									'before' => '',
									'after' => '')
				);
				
		
		echo $context['TPortal']['blocktheme'][$block['frame']]['frame']['before'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['title']['before'];

		// can you edit the block?
		if($block['can_edit'] && !$context['TPortal']['blocks_edithide'])
			echo '<a href="',$scripturl,'?action=tpmod;sa=editblock'.$block['id'].';sesc='.$context['session_id'].'"><img style="margin-right: 4px;" border="0" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';
		elseif($block['can_manage'] && !$context['TPortal']['blocks_edithide'])
			echo '<a href="',$scripturl,'?action=tpadmin;blockedit'.substr($side,0,1).'='.$block['id'].';sesc='.$context['session_id'].'"><img border="0" style="margin-right: 4px;" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';

		echo $block['title'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['title']['after'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['body']['before'];

		$func = 'TPortal_' . $block['type'];
		if (function_exists($func))
			$func();
		else
			echo doUBBC($block['body']);

		echo $context['TPortal']['blocktheme'][$block['frame']]['body']['after'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['frame']['after'];
	}
	
	
	if(!empty($settings['centerpanel_tabs']) && $side=='center')
		echo '
	</div>';

}

// the template for the blocks
function template_tp_above()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

	echo '
	<table cellpadding="4" cellspacing="0" width="100%">
		<tr>';

	// TinyPortal integrated bars
	if($context['TPortal']['leftpanel']==1)
	{
		echo '
			<td style="width: ' ,$context['TPortal']['leftbar_width'], '; padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;" valign="top">
				<div id="tpleftbarHeader" style="padding-top: 5px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">
					' , TPortal_panel('left') , '		
				</div>
			</td>';

	}
	echo '		
			<td width="99%" align="left" valign="top" style="padding-top: 10px; padding-bottom: 10px;">';
  
	if($context['TPortal']['centerpanel']==1)
	{
		if(!empty($settings['centerpanel_tabs']))
		{
			$titles=TPortal_panel_titles('center');
			echo '
			<ul id="tabheaders">';
			$first=true;
			foreach($titles as $tt)
			{
				echo '<li><a class="' , $first ? 'tabchosen' : 'notchosen' , '" onclick="activebtab(\'btab'.$tt['id'].'\',\'headertab'.$tt['id'].'\')" id="headertab'.$tt['id'].'">'.$tt['title'].'</a></li>';
				$first=false;
			}	
			echo '</ul>
			<div id="tabspanel">',
			TPortal_panel('center') , '
			</div>';	
			
		}
		else
			echo '
				<div id="tpcenterbarHeader">' , TPortal_panel('center') , '</div>';	
	
	}
}

function template_tp_below()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

	if($context['TPortal']['lowerpanel']==1)
		echo '
				<div id="tplowerbarHeader">' , TPortal_panel('lower') , '</div>';	

	echo '
			</td>';

	// TinyPortal integrated bars
	if($context['TPortal']['rightpanel']==1 && count($context['TPortal']['rightblock']['blocks'])>0)
		echo '
			<td width="' ,$context['TPortal']['rightbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex;" valign="top">
				<div id="tprightbarHeader" style="padding-top: 5px; width: ' ,$context['TPortal']['rightbar_width'], 'px;">
					' , TPortal_panel('right') , '		
				</div>
			</td>';
	

	echo '
		</tr>
	</table>';

	if($context['TPortal']['bottompanel']==1)
		echo '
	<div id="tpbottombarHeader" style="clear: both;">' , TPortal_panel('bottom') , '</div>';	
		
}


// TPortal side bar, left or right.
function TPortal_panel_titles($side)
{
	global $context , $scripturl, $settings, $language , $txt;

	$titles=array();
	for ($i = 0, $n = count($context['TPortal'][$side . 'block']['blocks']); $i < $n; $i += 1)
	{
		$block =& $context['TPortal'][$side . 'block']['blocks'][$i];
		$theme = $block['frame'] == 'theme';

		if(!isset($first_id))
			$first_id=$block['id'];
		
		// check if a language title string exists
		$newtitle = TPgetlangOption($block['lang'], $context['user']['language']);
		if(!empty($newtitle))
			$titles[] = array('id' => $block['id'], 'title' => $newtitle);
		else
			$titles[] = array('id' => $block['id'], 'title' => $block['title']);
	}
	$context['TPortal']['firsttab']=$first_id;
	return $titles;
}

?>