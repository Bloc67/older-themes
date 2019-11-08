<?php
// Version: 1.0.7; TPBlockLayout

function TP_block($block, $theme, $side)
{
	global $context , $scripturl, $settings, $language , $txt;

	// its a normal block..
	if(in_array($block['frame'],array('theme','frame','title','none')))
	{
		echo	'
	<div style="margin-bottom: 4px;" class="', (($theme || $block['frame']=='frame') ? 'tborder tp_'.$side.'block_frame' : 'tp_'.$side.'block_noframe'), '">';

		// show the frame and title
		if ($theme || $block['frame'] == 'title')
		{
			echo '
		<h3 style="margin: 0; font-size: 1em; padding: 5px;" class="catbg3 tp_'.$side.'block_title">';
			if($block['visible']=='' || $block['visible']=='1')
				echo '<a href="javascript: void(0); return false" onclick="toggle(\''.$block['id'].'\'); return false"><img id="blockcollapse'.$block['id'].'" style="margin: 0;" align="right" src="' .$settings['tp_images_url']. '/' , ((isset($context['TPortal']['upshrinkblocks'][$block['id']]) && $context['TPortal']['upshrinkblocks'][$block['id']]==1) || !isset($context['TPortal']['upshrinkblocks'][$block['id']])) ? 'TPcollapse' : 'TPexpand' , '.gif" border="0" alt="" title="'.$txt['block-upshrink_description'].'" /></a>';

			// can you edit the block?
			if($block['can_edit'] && !$context['TPortal']['blocks_edithide'])
				echo '<a href="',$scripturl,'?action=tpmod;sa=editblock'.$block['id'].';sesc='.$context['session_id'].'"><img style="margin-right: 4px;" border="0" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';
			elseif($block['can_manage'] && !$context['TPortal']['blocks_edithide'])
				echo '<a href="',$scripturl,'?action=tpadmin;blockedit='.$block['id'].';fromblock='.$context['TPortal']['serialize_url'].';sesc='.$context['session_id'].'"><img border="0" style="margin-right: 4px;" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';

			echo $block['title'],'
		</h3>';
		}
		else
		{
			if(($block['visible']=='' || $block['visible']=='1') && $block['frame']!='frame')
			{
				echo '
		<div style="padding: 4px;">';
				if($block['visible']=='' || $block['visible']=='1')
					echo '<a href="javascript: void(0); return false" onclick="toggle(\''.$block['id'].'\'); return false"><img id="blockcollapse'.$block['id'].'" style="margin: 0;" align="right" src="' .$settings['tp_images_url']. '/' , ((isset($context['TPortal']['upshrinkblocks'][$block['id']]) && $context['TPortal']['upshrinkblocks'][$block['id']]==1) || !isset($context['TPortal']['upshrinkblocks'][$block['id']])) ? 'TPcollapse' : 'TPexpand' , '.gif" border="0" alt="" title="'.$txt['block-upshrink_description'].'" /></a>';
				echo '&nbsp;
		</div>';
			}
		}
		echo '
		<div class="', (($theme || $block['frame']=='frame') ? 'windowbg tp_'.$side.'block_body' : ''), '" style="padding:' , (($theme || $block['frame']=='frame') ? '4' : '0') , 'px; ', (isset($context['TPortal']['upshrinkblocks'][$block['id']]) && $block['visible']==1 && $context['TPortal']['upshrinkblocks'][$block['id']]==0) ? 'display: none;' : ''  , '" id="block'.$block['id'].'">';

		$func = 'TPortal_' . $block['type'];
		if (function_exists($func))
			$func($block['id']);
		else
			echo doUBBC($block['body']);

		echo '
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
}

// the template for the blocks
function template_tp_above()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

	if(!empty($context['TPortal']['upshrinkpanel']))
		echo '<div style="float: right; margin-top: -1.4em;">', $context['TPortal']['upshrinkpanel'] , '</div>';

	if($context['TPortal']['toppanel']==1)
                     echo '
	<div id="tptopbarHeader"', (!empty($options['tpcollapse_topbar']) && $context['TPortal']['showcollapse']==1) ? ' style="display: none;"' : '' , '>' , TPortal_panel('top') , '</div>';	

	if($context['TPortal']['centerpanel']==1)
		echo '
	<div id="tpcenterbarHeader"', (!empty($options['tpcollapse_centerbar']) && $context['TPortal']['showcollapse']==1) ? ' style="display: none;"' : '' , '>' , TPortal_panel('center') , '</div>';	

}

function template_tp_below()
{
	global $context , $scripturl, $settings, $language , $txt, $options;

	if($context['TPortal']['lowerpanel']==1)
		echo '
	<div id="tplowerbarHeader"', (!empty($options['tpcollapse_lowerbar']) && $context['TPortal']['showcollapse']==1) ? ' style="display: none;"' : '' , '>' , TPortal_panel('lower') , '</div>';	

	if($context['TPortal']['bottompanel']==1)
		echo '
	<div id="tpbottombarHeader" style="clear: both;', (!empty($options['tpcollapse_bottombar']) && $context['TPortal']['showcollapse']==1) ? ' display: none;' : '' , '">' , TPortal_panel('bottom') , '</div>';	
		
}

?>