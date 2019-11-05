<?php

// Lithium 1.0
// a rewrite theme

function ces_galleries()
{
	ces_topics('galleries');
	
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
				<li><a href="' , $scripturl , '?action=galleries;recent">', $txt['ces_recent'] ,'</a></li>
				<li><a href="' , $scripturl , '?action=galleries;top">', $txt['ces_top'] ,'</a></li>
				' , $context['user']['is_logged'] ? '<li><a href="' . $scripturl . '?action=galleries;u=' . $context['user']['id'] . '">'. $txt['ces_my'] .'</a></li>' : '' , '
				' , !empty($context['ces_can_post_new']) ? '<li><br><a href="' . $scripturl . '?action=post;board=' . $_GET['b'] . '">'. (sprintf($txt['ces_post'],$txt['ces_gallery'])) .'</a></li>' : '' , '
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
			<a href="' , $scripturl , '?action=galleries;b=' . $brd . '"' , !empty($_GET['b']) && $_GET['b']==$brd ? ' class="chosen"' : '' , '>' , $bdata['name'] , '</a>
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
	<link href="https://fonts.googleapis.com/css?family=Catamaran:400,700,900" rel="stylesheet">
	<title>', $txt['ces_galleries'], '</title>';

	return 1;
}

function template_main()
{
	global $scripturl, $context, $txt, $settings;

	echo '
	<h2 class="catbg">
		<span class="bwfloatright" style="font-size: 70%;">' , !empty($context['cesblog_header']) ? $context['cesblog_header'] : '' , '</span>
		<b>' , $txt['ces_galleries'] , '</b>
	</h2>';

	if(empty($context['posts']))
	{	
		echo '
		<div class="toppadding">', $txt['no_matches'] , '</div>';
		return;
	}
	echo '
	<div class="pagelinks">' , $context['page_index'] , '</div><br>
	<div class="bwgrid">';

	foreach ($context['posts'] as $w => $post)
	{
		echo '
		<div class="bwcell33">
			<div class="bwgallery">
				<a href="' , $post['href'] . '"
					style="font-size: 2rem; color: white; display: block; width: 100%; background-image: url(';
		
		if(!empty($context['loaded_attachments'][$post['id']]))
			echo $context['loaded_attachments'][$post['id']];
		else
			echo $settings['images_url'].'/gallerybg.jpg';
			
		echo '); background-size: cover; background-repeat: no-repeat; padding-top: 80%; overflow: hidden;">
				</a>
				<a href="' , $post['href'] , '">
					<span style="font-family: \'Catamaran\'; display: block; padding: 1rem 0 0 1rem; font-size: 1.7rem;">' , $post['subject'] , '</span>
					<span style="font-family: \'Catamaran\';display: block; font-size: 1.4rem; opacity: 0.8; padding: 0 0 0 1rem;">' , $post['time'] , '</span>
				</a>
				
				<span style="font-family: \'Catamaran\';display: block; font-size: 1.4rem; opacity: 0.8; padding: 0 0 1rem 1rem;">
					<a href="' , $scripturl , '?action=galleries;u=' . $post['poster']['id'] . '"> 
						<span>' ,$post['poster']['name'] , '</span>
					</a>
					<a href="' , $post['poster']['href'] , '"> 
						<span class="icon-user" style="vertical-align: -10%;"></span>
					</a>
				</span>
			</div>
		</div>';
	}
	echo '
	</div>
	<div class="pagelinks">' , $context['page_index'] , '</div>';
}


?>