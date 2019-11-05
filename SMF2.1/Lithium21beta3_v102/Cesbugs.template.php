<?php

// Lithium 1.0
// a rewrite theme

function ces_bugs()
{
	ces_topics('bugs');
}

function subtemplate_aside()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	if(isset($_GET['u']) && is_numeric($_GET['u']))
	{
		echo '
		<a href="' , $scripturl , '?action=profile;u=' . $context['cesblog_currentmember']['id'] . '">
			<img src="' , $context['cesblog_currentmember']['avatar']['href'] , '" alt="" class="avatar_topic floatleft" style="width: 70px; height: 70px; margin-left: 0;" />
			<span class="largetext floatleft" style="font-size: 2rem; margin-left: 2rem; line-height: 95px; display: block;"><b>' , $context['cesblog_currentmember']['name'] , '</b></span>
		</a>
		<hr class="clear">
		<div class="bwgrid">
			<div class="floatleft">' , $context['cesblog_currentmember']['group'] , '</div>
			<div class="floatright">' , $context['cesblog_currentmember']['posts'] , ' ' , $txt['posts'] , '</div>
		</div>
		<br class="clear">
		';
	}
	echo '
	<div>
		<div class="toppadding">
			<ul class="nolist">
				<li><a href="' , $scripturl , '?action=bugs;recent">', $txt['ces_recent'] ,'</a></li>
				<li><a href="' , $scripturl , '?action=bugs;top">', $txt['ces_top'] ,'</a></li>
				' , $context['user']['is_logged'] ? '<li><a href="' . $scripturl . '?action=bugs;u=' . $context['user']['id'] . '">'. $txt['ces_my'] .'</a></li>' : '' , '
				' , !empty($context['ces_can_post_new']) ? '<li><br><a href="' . $scripturl . '?action=post;board=' . $_GET['b'] . '">'. (sprintf($txt['ces_post'],$txt['ces_bug'])) .'</a></li>' : '' , '
			</ul>
		</div>
	</div>';
	
	if(!empty($context['cesboards']))
	{
		echo '
	<hr>';

		foreach($context['cesboards'] as $brd => $bdata)
		{
			echo '
	<div class="bwgrid">
		<div class="bwcell12">
			<a href="' , $scripturl , '?action=bugs;b=' . $brd . '"' , !empty($_GET['b']) && $_GET['b']==$brd ? ' class="chosen"' : '' , '>' , $bdata['name'] , '</a>
		</div>
		<div class="bwcell4">
			<div class="floatright">' , $bdata['num_topics'] , '</div>
		</div>
	</div>';
		}
	}
}

function subtemplate_headers()
{
	global $txt;

	echo '
	<title>', $txt['ces_bugs'], '</title>';
	
	return 1;
}

function template_main()
{
	global $scripturl, $context, $txt, $settings;

	echo '
	<h2 class="catbg">
		<span class="bwfloatright" style="font-size: 70%;">' , !empty($context['cesblog_header']) ? $context['cesblog_header'] : '' , '</span>
		<b>' , $txt['ces_bugs'] , '</b>
	</h2>';

	if(empty($context['posts']))
	{	
		echo '
		<div class="toppadding">', $txt['no_matches'] , '</div>';
		return;
	}
	echo '
	<div class="pagelinks">' , $context['page_index'] , '</div>';
		
	foreach ($context['posts'] as $w => $post)
	{
		echo '
	<div class="bwgrid">
		<div class="sub_bar tightbar" style="clear: left; border: none;">
			<h3 class="subbg">
				<span style="font-size: 1.2rem;" class="des">
					', isset($post['cesparams']['status']) ? '<span class="floatright ces_status'. $post['cesparams']['status'].'">'.$txt['ces_status'.$post['cesparams']['status']].'</span>' : '' , ' 		
					', !empty($post['cesparams']['tag']) ? '<span class="floatright ces_status_tag">'.$post['cesparams']['tag'].'</span>' : '' , ' 		
					', !empty($post['cesparams']['endversion']) ? '<span class="floatright ces_status2">'.$post['cesparams']['endversion'].'</span>' : '' , ' 		
				</span>
				<strong>
					<a href="' , $scripturl . '?topic=' , $post['topic'] , '">' , $post['subject'] , '</a>
					' , $post['is_new'] ? '<span class="new_icon"></span>' : '' , '
				</strong>
				<span style="font-size: 70%; margin: 1rem 0 0.5rem 0;" class="mob mob_newline">
					', isset($post['cesparams']['status']) ? '<span class="floatleft ces_status'. $post['cesparams']['status'].'">'.$txt['ces_status'.$post['cesparams']['status']].'</span>' : '' , ' 		
					', !empty($post['cesparams']['tag']) ? '<span class="floatleft ces_status_tag">'.$post['cesparams']['tag'].'</span>' : '' , ' 		
				</span>
			</h3>
		</div>
		<div style="opacity: 0.7;">
			' . $post['board']['link'] . ' | <span class="smaller">' , $post['time'] , '</span> | ' , $post['poster']['link']  , '
		</div>
	</div>';
	}
	echo '
	<div class="pagelinks">' , $context['page_index'] , '</div>';
}


?>