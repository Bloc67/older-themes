<?php

// Lithium 1.0
// a rewrite theme

function ces_news()
{
	ces_topics('news');
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
				' , !empty($context['ces_can_post_new']) ? '<li><br><a href="' . $scripturl . '?action=post;board=' . $_GET['b'] . '">'. (sprintf($txt['ces_post'],$txt['ces_new'])) .'</a></li>' : '' , '
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
			<a href="' , $scripturl , '?action=news;b=' . $brd . '"' , !empty($_GET['b']) && $_GET['b']==$brd ? ' class="chosen"' : '' , '>' , $bdata['name'] , '</a>
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
	<title>', $txt['ces_news'], '</title>';
	
	return 1;
}

function template_main()
{
	global $scripturl, $context, $txt, $settings;

	echo '
	<h2 class="catbg">
		<span class="bwfloatright" style="font-size: 70%;">' , !empty($context['cesblog_header']) ? $context['cesblog_header'] : '' , '</span>
		<b>' , $txt['ces_news'] , '</b>
	</h2>';

	if(empty($context['posts']))
	{	
		echo '
		<div class="toppadding">', $txt['no_matches'] , '</div>';
		return;
	}
	echo '
	<div class="pagelinks">' , $context['page_index'] , '</div><br>';
		
	$pattern = array(
		16,0,
		6,10,0,
		10,6,0,
		8,8,0,
		16,0,
		8,8,0,
		6,10,0,
		10,6,0,
	);
	echo '
<div style="background: #f6f6f6; margin-bottom: 1rem; overflow: hidden;">
	<div class="bwgrid">';	
	$count =0;
	foreach ($context['posts'] as $w => $post)
	{
		if($pattern[$count]==0)
		{
			echo '
	</div><div class="bwgrid">';
			$count++;
			if($count>19)
				$count = 0;
		}
		echo '
		<div class="bwcell' . $pattern[$count] . '"><div class="bwgutter_news">
			<a href="' , $post['href'] , '" class="blog_attachment b' . $pattern[$count] . '" style="background-image: url(' , !empty($context['loaded_attachments'][$post['id']]) ? $context['loaded_attachments'][$post['id']] : $settings['images_url'].'/newsbg.jpg' , ');">&nbsp;</a>
			' , $post['num_replies'] > 0 ? '<a href="' . $scripturl . '?topic=' . $post['topic'] . '#comments"><span class="amt_bigger floatright"><span class="icon-bubble" style="vertical-align: -15%;margin-right: 3px;"></span>' . $post['num_replies'] . '</span></a>' : '', '
			<h3 class="h3_news_' . $pattern[$count] . '">
				' , !empty($post['cesparams']['slogan']) ? '<span style="font-size: 70%; font-weight: bold; display: block; padding-bottom: 0.5rem;">'.$post['cesparams']['slogan'].'</span>' : '' , '
				<a href="' , $post['href'] , '">
				' , $post['subject'] , '
				</a>
			</h3>
			<div class="news_' . $pattern[$count] . '">
				' , !empty($post['cesparams']['ingress']) ? $post['cesparams']['ingress'] : '' , '
			</div>
		</div></div>
			';
		$count++;
	}
	echo '
	</div>
</div>
<div class="pagelinks">' , $context['page_index'] , '</div>
';
}


?>