<?php
// Version: 1.1.5; ie6

// The main sub template above the content.
function template_ie6_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
	if($context['page_title']=='' && isset($_GET['action']))
		$context['page_title']=$context['forum_name'].' - '.$_GET['action'];
	elseif($context['page_title']=='' && !isset($_GET['action']))
		$context['page_title']=$context['forum_name'];

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '><head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title'], '" />', empty($context['robot_no_index']) ? '' : '
	<meta name="robots" content="noindex" />', '
	<meta name="keywords" content="PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum" />
	<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/script.js?fin11"></script>
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";
	// ]]></script>
	<title>', $context['page_title'], '</title>';

	// The ?fin11 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css?fin11" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/print.css?fin11" media="print" />';


	// theme css
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/impulse.css?fin11" />';

	if($context['browser']['is_ie6'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/ie6.css?fin11" />';

	if($context['browser']['is_ie7'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/ie7.css?fin11" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" target="_blank" />
	<link rel="search" href="' . $scripturl . '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name'], ' - RSS" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="' . $scripturl . '?board=' . $context['current_board'] . '.0" />';

	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'], '

	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		var current_header = ', empty($options['collapse_header']) ? 'false' : 'true', ';

		function shrinkHeader(mode)
		{';

	// Guests don't have theme options!!
	if ($context['user']['is_guest'])
		echo '
			document.cookie = "upshrink=" + (mode ? 1 : 0);';
	else
		echo '
			smf_setThemeOption("collapse_header", mode ? 1 : 0, null, "', $context['session_id'], '");';

	echo '
			document.getElementById("upshrink").src = "', $settings['theme_url'],'/themeie6" + (mode ? "/user2.png" : "/user.png");

			document.getElementById("userbox").style.display = mode ? "none" : "";
			current_header = mode;
		}

	// ]]></script>';

	// the routine for the info center upshrink
	echo '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
			var current_header_ic = ', empty($options['collapse_header_ic']) ? 'false' : 'true', ';

			function shrinkHeaderIC(mode)
			{';

	if ($context['user']['is_guest'])
		echo '
				document.cookie = "upshrinkIC=" + (mode ? 1 : 0);';
	else
		echo '
				smf_setThemeOption("collapse_header_ic", mode ? 1 : 0, null, "', $context['session_id'], '");';

	echo '
				document.getElementById("upshrink_ic").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

				document.getElementById("upshrinkHeaderIC").style.display = mode ? "none" : "";

				current_header_ic = mode;
			}
		// ]]></script>
</head>
<body>';

	echo '
<div id="mainarea">
	<div id="topsection">';

	if (empty($settings['header_logo_url']))
		echo '
		<h1 id="mainheader"><a href="' , $scripturl , '">', $context['forum_name'], '</a></h1>';
	else
		echo '
		<a href="' , $scripturl , '"><img id="mainheader" src="', $settings['header_logo_url'], '" alt="', $context['forum_name'], '" /></a>';
	
	echo '
		<div id="user">
			<a href="#" onclick="shrinkHeader(!current_header); return false;"><img id="upshrink" src="', $settings['theme_url'], '/themeie6/', empty($options['collapse_header']) ? 'user.png' : 'user2.png', '" alt="*" title="', $txt['upshrink_description'], '" align="bottom" style="margin: 0 1ex;" /></a>
			<div id="userbox" class="content"', empty($options['collapse_header']) ? '' : ' style="display: none;"', '>';
	if($context['user']['is_logged'])
	{
		echo '<b>', $txt['hello_member_ndt'], ' ', $context['user']['name'] , '</b>
				<br />';
		if ($context['allow_pm'])
			echo $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
		echo '.<br />';
		
		echo '
				<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a> <br />
				<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a><br />';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
				<b>', $txt[616], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
				', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

	}
	else
		echo '	
				<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>
				<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="text-align: center;margin: 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" />
					<select name="cookielength">
						<option value="60">', $txt['smf53'], '</option>
						<option value="1440">', $txt['smf47'], '</option>
						<option value="10080">', $txt['smf48'], '</option>
						<option value="43200">', $txt['smf49'], '</option>
						<option value="-1" selected="selected">', $txt['smf50'], '</option>
					</select><br />
					<input type="submit" value="', $txt[34], '" /><br />
					<span class="middletext">', $txt['smf52'], '</span>
					<input type="hidden" name="hash_passwrd" value="" />
				</form>';

	if (!empty($context['user']['avatar']))
		echo '
				<div id="myavatar">', $context['user']['avatar']['image'], '</div>';

	echo '
			</div>
			<span class="footer"></span>
		</div>
	</div>
	<table id="topsection2" cellspacing="0" cellpadding="0" width="100%">
		<tr><td><span class="left"></span></td>
		<td width="99%" class="content"></td>
		<td><span class="right"></span></td>
		</tr>
	</table>
	<table id="topsection3" cellspacing="0" cellpadding="0" width="100%">
		<tr><td><span class="left"></span></td>
		<td class="content" width="99%"></td>
		<td><span class="right"></span></td>
	</tr></table>
	<div id="topmiddle">
		<div class="left">
			<div class="right">
				<div class="content">
					<h3 id="news">' , $txt['impulse_newsline'] , '</h3>
					' , $context['random_news_line'] , '
				</div>
			</div>
		</div>
	</div>
	<table cellpadding="0" cellspacing="0" width="100%" id="middlesection">
		<tr><td><span class="left"></span></td>
			<td width="99%" class="content">', template_menu(), '</td>
			<td><span class="right"></span></td>
		</tr>
	</table>
	<table cellpadding="0" width="100%" cellspacing="0" id="content" >
		<tr>
			<td class="left"></td>
			<td valign="top" class="mid">';

	// for TP 0.9.8
	if(!empty($context['TPortal']['version']) && $context['TPortal']['version']=='098')
		tpcode();

	echo '	<div style="overflow: auto;">';
}

function template_ie6_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '	</div>';	
	
		// for TP 0.9.8
	if(!empty($context['TPortal']['version']) && $context['TPortal']['version']=='098')
		tpcode2();

	echo '
			<div id="ie6notice">This theme was made for modern browsers. Several graphic aspects will look less than ideal on lesser browsers like for example IE6.</div>
			</td>
			<td class="right"></td>
		</tr>
	</table>
	<table id="footersection" cellspacing="0" cellpadding="0" width="100%">
		<tr><td><span class="left"></span></td>
		<td class="fcontent">
			<div id="copywrite">
			' , theme_copyright() , function_exists('tportal_version') ? '<div>' . tportal_version() . '</div>' : '' , '<br /><strong>imPulse</strong> theme by <a href="http.//www.blocweb.net">BlocWeb</a></div></td>
		<td><span class="right"></span></td>
	</tr></table>';
	
	// Show the load time?
	if ($context['show_load_time'])
		echo '
	<div id="rendertime">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</div>
</body></html>';
}

?>