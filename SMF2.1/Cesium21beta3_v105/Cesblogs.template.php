<?php

// Cesium 1.0
// a rewrite theme

function ces_blogs()
{
	ces_topics('blogs');
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
				<li><a href="' , $scripturl , '?action=blogs;recent">', $txt['ces_recent'] ,'</a></li>
				<li><a href="' , $scripturl , '?action=blogs;top">', $txt['ces_top'] ,'</a></li>
				' , $context['user']['is_logged'] ? '<li><a href="' . $scripturl . '?action=blogs;u=' . $context['user']['id'] . '">'. $txt['ces_my'] .'</a></li>' : '' , '
				' , !empty($context['ces_can_post_new']) ? '<li><br><a href="' . $scripturl . '?action=post;board=' . $_GET['b'] . '">'. (sprintf($txt['ces_post'],$txt['ces_blog'])) .'</a></li>' : '' , '
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
			<a href="' , $scripturl , '?action=blogs;b=' . $brd . '"' , !empty($_GET['b']) && $_GET['b']==$brd ? ' class="chosen"' : '' , '>' , $bdata['name'] , '</a>
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
	<title>', $txt['ces_blogs'], '</title>';
	
	return 1;
}

function template_main()
{
	global $scripturl, $context, $txt, $settings;

	echo '
	<h2 class="catbg">
		<span class="bwfloatright" style="font-size: 70%;">' , !empty($context['cesblog_header']) ? $context['cesblog_header'] : '' , '</span>
		<b>' , $txt['ces_blogs'] , '</b>
	</h2>';

	if(empty($context['posts']))
	{	
		echo '
		<div class="toppadding">', $txt['no_matches'] , '</div>';
		return;
	}
	echo '
	<div class="pagelinks">' , $context['page_index'] , '</div><br>';
		
	foreach ($context['posts'] as $w => $post)
	{
		echo '
	<div class="bwgrid">	
		<div class="bwcell16"><div>
			<a href="' , $post['poster']['href'] , '" class="floatright">	
				<img src="' , $post['poster']['avatar']['href']. '" alt="" class="avatar_blog" />
			</a>
			<h3 class="blogheader">
				<strong style="font-size: 120%; font-weight: 900; letter-spacing: 0;">
					<a href="' , $scripturl . '?topic=' , $post['topic'] , '">' , $post['subject'] , '</a>
					' , $post['is_new'] ? '<span class="new_icon"></span>' : '' , '
				</strong>
				<div class="h3date">
					' , $post['time'] , ' <span class="smaller">' . $txt['in'] . '</span> ' . $post['board']['link'] . '
					<span class="smaller">' , $txt['by'] , '</span>
					<a href="' , $scripturl , '?action=blogs;u=' . $post['poster']['id'] . '"> 
						<span><b>' ,$post['poster']['name'] , '</b></span>
					</a>
					<a href="' , $post['poster']['href'] , '"> 
						<span class="icon-user" style="vertical-align: -10%;"></span>
					</a>
				</div>
			</h3>
			<div class="bwgrid">
				<div class="bwcell4">';
		
		if(!empty($context['loaded_attachments'][$post['id']]))
			echo '
					<div class="bwgutter_right blog_attach_container">
						<a href="' , $post['href'] , '" class="blog_attachment" style="background-image: url(' , $context['loaded_attachments'][$post['id']], ');"></a>
					</div>';
		echo '&nbsp;
				</div>
				<div class="bwcell12">
					<div class="blogpost">' , $post['body'] , '</div>
					<div class="blogtext smalltext toppadding"><hr style="opacity: 0.4;">' , $post['num_views'] , ' ' . $txt['views'] . ' &nbsp;&nbsp;' , $post['num_replies'] , ' ' . $txt['replies'] . '
					' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '</div>
				</div>
			</div></div>
		</div>
	</div><br>';
	}
	echo '
	<div class="pagelinks">' , $context['page_index'] , '</div>';
}


?>