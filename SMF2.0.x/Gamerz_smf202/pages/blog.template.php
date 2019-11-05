<?php

if(!function_exists('template_main'))
{
	function template_main() { redirectexit(); }
}

// show the boards the blog uses.
function bw_blogboards()
{
	global $settings, $context, $txt, $scripturl, $boardurl;
	
	if(AllowedTo('can_post_new'))
	{
		if(empty($context['current_board']))
		{
			if(empty($settings['blogboards']))
				return;

			$b = explode(',' , $settings['blogboards']);
			$context['current_board'] = $b[0];
		}

		echo '
	<h3 class="trueblog blogposting"><a href="' . $scripturl . '?action=post;board=' . $context['current_board'] . '">Post New Blog</a></h3>';
	
	
	}
	if(empty($settings['blogboards']))
		return;


	echo '
	<h3 class="trueblog">Other blogs</h3>
		<ul class="sections smalltext">';
	$boards = bw_fetchboardnames($settings['blogboards']);
	foreach($boards as $board => $name)
		echo '<li><a href="' . $scripturl . '?action=bwblog;b=' . $board . '">' . $name . '</a></li>';
	
	echo '</ul>';
}
// search blogs
function bw_blogsearch($all = true)
{
	global $settings, $context, $txt, $scripturl;
	
	echo '
	<h3 class="trueblog">Search</h3>
	<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
		<input type="text" name="search" value="" class="input_text" style="width: 94%;"  />
		<input type="hidden" name="advanced" value="0" />';

	if(!empty($settings['trueblogboards']))
	{
		foreach(explode(",",$settings['blogboards']) as $b)
			echo '
		<input type="hidden" name="brd[', $b, ']" value="', $b, '" />';
	}
	echo '</form><br />';

}
// recent posts
function bw_blogrecent()
{
	global $settings, $context, $txt, $scripturl;
	echo '
	<h3 class="trueblog">Recent Blog Posts</h3>
		<ul class="sections smalltext">';
	$topics = bw_fetchrecentblogs($settings['blogboards'], 6);
	foreach($topics as $topic => $blog)
		echo '<li><a href="' . $scripturl . '?topic=' . $topic . '"><b>' , ($blog['subject']), '</b></a>
	<span class="smalltext greytext">in <a href="index.php?action=bwblog;b=' . $blog['id_board'] . '">' , ($blog['boardname']) , '</a></span>
	</li>';
	
	echo '</ul>';
}
// show the boards the blog uses.
function bw_blogrss()
{
	global $settings, $context, $txt, $scripturl, $modSettings;

	if(empty($modSettings['xmlnews_enable']))
		return;

	if(empty($context['current_board']))
		$brds = $settings['blogboards'];
	else
		$brds = $context['current_board'];

	echo '<br /><br />
	<a href="' . $scripturl .'?action=.xml;sa=news;board='.$brds.';type=rss">
		<img src="' . $settings['images_url'] . '/rss48.png" style="vertical-align: middle;" alt="Blog feed" /> Subscribe by RSS
	</a>
	<br /><br />
	';
}

// show the boards the blog uses.
function bw_fetchboardnames($boards)
{
	global $settings, $context, $txt, $scripturl, $smcFunc;
	
	$more=array();
	$more = cache_get_data('bwthemeboards', 30);
	
	if($more  == null)
	{
		$request = $smcFunc['db_query']('','SELECT id_board, name FROM {db_prefix}boards WHERE id_board IN(' . $boards . ')');
		if($smcFunc['db_num_rows']($request)>0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
				$more[$row['id_board']] = $row['name'];

			$smcFunc['db_free_result']($request);
		}
		else
			$more = array();
		
		cache_put_data('bwthemeboards', $more, 30);
		return $more;
	}
	else
		return $more;

}
// show the boards the blog uses.
function bw_fetchrecentblogs($boards, $limit = 5)
{
	global $settings, $context, $txt, $scripturl, $smcFunc;
	
	$more=array();
	$more = cache_get_data('bwrecentblogs', 30);
	
	if($more  == null)
	{
		$request = $smcFunc['db_query']('','SELECT t.id_board, b.name as boardname, t.id_topic, m.subject FROM ({db_prefix}topics as t, {db_prefix}messages as m) 
		LEFT JOIN {db_prefix}boards as b ON (t.id_board = b.id_board)
		WHERE t.id_board IN(' . $boards . ') 
		AND t.id_first_msg = m.id_msg
		ORDER BY t.id_topic DESC LIMIT '.$limit);
		if($smcFunc['db_num_rows']($request)>0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
				$more[$row['id_topic']] = $row;

			$smcFunc['db_free_result']($request);
		}
		else
			$more = array();
		
		cache_put_data('bwrecentblogs', $more, 30);
		return $more;
	}
	else
		return $more;

}
// show the most recent blogs.
function bw_fetchrecentblogsfull($boardpool, $start)
{
	global $context, $settings, $scripturl, $txt, $db_prefix, $user_info;
	global $modSettings, $smcFunc;

	$posts = array();
	$request = $smcFunc['db_query']('', '
		SELECT
			m.subject, m.id_msg as id, t.id_topic, m.body, t.num_replies, t.num_views,m.poster_time, mem.id_member, mem.real_name, m.approved
			, t.id_board, b.name as board_name
			FROM ({db_prefix}topics as t,{db_prefix}messages as m) 
			LEFT JOIN {db_prefix}members as mem ON (m.id_member = mem.id_member)
			LEFT JOIN {db_prefix}boards as b ON (t.id_board = b.id_board)
		WHERE t.id_board IN (' . $boardpool . ')
		AND t.id_first_msg = m.id_msg
		ORDER BY m.poster_time DESC
		LIMIT ' . $start . ',5' );
	
	if($smcFunc['db_num_rows']($request)<1)
		exit('no topics');

	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		// Censor the subject.
		censorText($row['subject']);
		$posts[] = $row;
	}
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query']('', '
		SELECT
			COUNT(m.id_msg) as total
			FROM ({db_prefix}topics as t,{db_prefix}messages as m) 
		WHERE t.id_board IN (' . $boardpool . ')
		AND t.id_first_msg = m.id_msg' );

	$t = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	
	$context['page_index'] = constructPageIndex($scripturl . '?action=bwblog;start=%1$d', $start, $t['total'], 5, true);

	return $posts;
}

function bw_checks()
{
	global $context, $settings, $scripturl, $modSettings;

	if(allowedTo('manage_forum'))
	{
		// check if XML news is set or not
		if(empty($modSettings['xmlnews_enable']))
			echo '
		<p class="information">XML news feature is NOT enabled. if you wish to use RSS feeds for the blog you need to turn it on. 
		<br />( <a href="' . $scripturl . '?action=admin;area=news;sa=settings;'.$context['session_var'].'='.$context['session_id'].'">enable XML news</a> )</p>';
	}
}
?>