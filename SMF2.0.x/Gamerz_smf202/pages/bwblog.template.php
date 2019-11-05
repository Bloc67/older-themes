<?php

function template_main()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	loadtemplate('pages/blog');
	
	$blogboards = '';

	if(!empty($settings['no_blog']) || empty($settings['blogboards']))
		redirectexit();
	
	$blogboards = $settings['blogboards'];

	// display blogroll
	echo '
	<div class="bwgrid">
		<div class="bwcell12">';

	// get any pages
	if(!empty($_GET['start']) && is_numeric($_GET['start']))
		$start = $_GET['start'];
	else
		$start = 0;

	// get any boards?
	if(!empty($_GET['b']) && is_numeric($_GET['b']))
		$b = $_GET['b'];
	else
		$b = $blogboards;

	
	$blogroll = bw_fetchrecentblogsfull($b, $start);
	
	echo '<br />';
	
	foreach($blogroll as $o => $orig)
	{
		echo '
			<div class="bwgrid">
				<div class="bwcell2">
					<div class="blogdate blogdate_new">' , trueblogDate($orig['poster_time']) , '</div><br style="clear: both;" /><br />
					<ul class="reset" style="clear: left;">
						<li></li>
					</ul>
				</div>
				<div class="bwcell14"><div style="padding-left: 1em;">
					<h2 class="trueblog_header"><a href="' . $scripturl . '?topic='.$orig['id_topic'].'.0">' , $orig['subject'] , '</a></h2>
					<em>written by <a href="' . $scripturl . '?action=profile;u=' . $orig['id_member'] . '">' , $orig['real_name'] , '</a>
					in the blog "<a href="' . $scripturl . '?action=bwblog;b'.$orig['id_board'].'"><b>' . $orig['board_name']  . '</b>"</a>
					</em>
					<div class="post"><div class="blogbody">' , parse_bbc($orig['body']) , '</div></div>';

				echo '
					<div style="font-weight: bold; font-size: 90%; overflow: hidden; padding: 2em 1em 0 1em;">
						<div class="floatleft">
							<a href="' . $scripturl . '?topic='.$orig['id_topic'].'.0#comments">' .$orig['num_replies'] . ' comments</a>
						</div>'; 
				echo '
					</div>
				</div></div>
			</div><br /><br />';
	}
	echo '
		<div style="clear: both; padding: 1em;" class="middletext">
			', $txt['pages'], ': ', $context['page_index'], '
		</div>';

	echo '
		</div>
		<div class="bwcell4">
			<div style="padding-left: 2em;">
				' , bw_blogrss() , '
				' , bw_blogboards() , '
				' , bw_blogsearch() , '
				' , bw_blogrecent() , '
				' , bw_checks() , '
			</div>
		</div>
	</div>';

}

function trueblogDate($tid)
{
	echo '<span class="day">' , date("d",$tid) , '</span><span class="month" style="padding-bottom: 0;"><b>' , date("M",$tid) , '</b></span><span class="month" style="padding-top: 0;">' , date("Y",$tid) , '</span>';
}
	




?>