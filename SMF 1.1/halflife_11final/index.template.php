<?php
// Version: 1.1; index

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '1.1';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;
}

// The main sub template above the content.
function template_main_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '><head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title'], '" />', empty($context['robot_no_index']) ? '' : '
	<meta name="robots" content="noindex" />', '
	<meta name="keywords" content="PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum" />
	<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/script.js?rc3"></script>
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

	/* Internet Explorer 4/5 and Opera 6 just don't do font sizes properly. (they are big...)
		Thus, in Internet Explorer 4, 5, and Opera 6 this will show fonts one size smaller than usual.
		Note that this is affected by whether IE 6 is in standards compliance mode.. if not, it will also be big.
		Standards compliance mode happens when you use xhtml... */
	if ($context['browser']['needs_size_fix'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/fonts-compat.css" />';

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
			document.getElementById("upshrink").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

			document.getElementById("upshrinkHeader").style.display = mode ? "none" : "";
			document.getElementById("upshrinkHeader2").style.display = mode ? "none" : "";

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

    $item=floor(rand(0,4));
	echo '
				document.getElementById("upshrink_ic").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

				document.getElementById("upshrinkHeaderIC").style.display = mode ? "none" : "";

				current_header_ic = mode;
			}
		// ]]></script>
	<style type="text/css"><!--
		div#left{
			background: url('.$settings['images_url'].'/img/left-'.$item.'.jpg) top left no-repeat;
		}
	 --></style>
</head>
<body>';

	echo '
	<div id="mid">
		<div id="left">
			<div id="right"><div style="text-align: right; padding-right: 170px;">',template_menu(),'</div>
				<table cellpadding="0" cellspacing="0" width="100%" id="maintable">
					<tr>
						<td valign="top" width="99%">';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

    echo '
    					</td>
    					<td valign="top" width="130"><img src="',$settings['images_url'],'/blank.gif" width="130" height="8" alt="" /><br />';

	echo '<div class="smalltext" style="padding-top: 2em; padding-left: 2em; font-family: verdana, arial, sans-serif;">';

	if (!empty($context['user']['avatar']))
		echo '<div style="margin-right: 10px;margin-bottom: 10px;">' . $context['user']['avatar']['image'].'</div>';


	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
							', $txt['hello_member'], ' <b>', $context['user']['name'], '</b>';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
			echo '<br />', $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
		echo '.<br />';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
							<b>', $txt[616], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
							', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

		echo '
							<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br />
							<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a><br />';
	}
	// Otherwise they're a guest - so politely ask them to register or login.
	else
	{
		echo '
							', $txt['welcome_guest'], '<br />
							', $context['current_time'], '<br />

							<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>

								<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="margin: 3px 1ex 1px 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
								<div >
									<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" />
									<select name="cookielength">
										<option value="60">', $txt['smf53'], '</option>
										<option value="1440">', $txt['smf47'], '</option>
										<option value="10080">', $txt['smf48'], '</option>
										<option value="43200">', $txt['smf49'], '</option>
										<option value="-1" selected="selected">', $txt['smf50'], '</option>
									</select>
									<input type="submit" value="', $txt[34], '" /><br />
									<span class="middletext">', $txt['smf52'], '</span>
									<input type="hidden" name="hash_passwrd" value="" />
								</div>
							</form>';
	}

	echo '


					<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;">
						<div style="margin-top: 20px;">
							<div style="padding: 2px 5px 2px 2px;" class="titlebg"><b>', $txt[182], '</b></div>
								<div class="windowbg2" style="padding: 2px;"><input type="text" name="search" value="" style="margin-right: 8px;" />&nbsp;
							<input type="submit" name="submit" value="', $txt[182], '" style="margin-top: 5px;" /><br />
							<a href="', $scripturl, '?action=search;advanced">', $txt['smf298'], '</a>
							<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
							<input type="hidden" name="topic" value="', $context['current_topic'], '" />';

		// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
							<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '

					</form>';

	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '<br /><div style="padding: 2px 5px 2px 2px;" class="titlebg"><b>News</b></div>
	<div class="windowbg2" style="padding: 2px;">
						<div style="padding: 5px;" class="smalltext">', $context['random_news_line'], '</div></div>';

	// The "key stats" box.
	echo '<br /><div style="padding: 2px 5px 2px 2px;" class="titlebg"><b>Stats</b></div>
						<div class="windowbg2" style="padding: 2px;">
						<div style="padding: 5px;" class="smalltext">
							<b>', $context['common_stats']['total_posts'], '</b> ', $txt[95], ' ', $txt['smf88'], ' <b>', $context['common_stats']['total_topics'], '</b> ', $txt[64], ' ', $txt[525], ' <span style="white-space: nowrap;"><b>', $context['common_stats']['total_members'], '</b> ', $txt[19], '</span><br />
							', $txt[656], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
						</div>';


    echo '			</td>
    				</tr>
    			</table>
				<div class="smalltext" style="border-top: solid 1px #a0a0a0; margin-top: 1em; padding-top: 1em;">
          ', theme_copyright(), '
          |	<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
		<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a>
          <br />
          <b>Half-Life2</b> design by <a href="http.//www.blocweb.net">BlocWeb</a>';

		// Show the load time?
	if ($context['show_load_time'])
		echo ' | '. $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'];

	echo '
				</div>
    		</div>
    	</div>
	</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	global $context, $settings, $options;

	echo '<div class="nav" style="font-size: smaller; margin-bottom: 2ex; margin-top: 2ex;">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo '<b>', $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'], '</b>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;>&nbsp;';
	}

	echo '</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = 'home';
	if (in_array($context['current_action'], array('admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'search2')
		$current_action = 'search';

	if (isset($_GET['dl']))
		$current_action = 'dlmanager';

	if (isset($_GET['board']) || isset($_GET['topic']))
		$current_action = 'forum';

	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

         $ca=$current_action;
	$tab1 = '<span class="tabs';

	// Show the [home] and [help] buttons.
	echo $tab1, $ca== 'home' ? '1' : '2' , '"><a href="', $scripturl, '">'.$txt[103]. '</a></span>&nbsp;|&nbsp;';

	echo $tab1, $ca== 'help' ? '1' : '2' , '"><a href="', $scripturl, '?action=help">'.$txt[119]. '</a></span>&nbsp;|&nbsp;';

	// How about the [search] button?
	if ($context['allow_search'])
		echo $tab1, $ca== 'search' ? '1' : '2' , '"><a href="', $scripturl, '?action=search">'.$txt[182]. '</a></span>&nbsp;|&nbsp;';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo $tab1, $ca== 'admin' ? '1' : '2' , '"><a href="', $scripturl, '?action=admin">'.$txt[2]. '</a></span>&nbsp;|&nbsp;';


	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo $tab1, $ca== 'profile' ? '1' : '2' , '"><a href="', $scripturl, '?action=profile">'.$txt[467]. '</a></span>&nbsp;|&nbsp;';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo $tab1, $ca== 'pm' ? '1' : '2' , '"><a href="', $scripturl, '?action=pm">'.$txt['pm_short']. '', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a></span>&nbsp;|&nbsp;';


	// The [calendar]!
	if ($context['allow_calendar'])
		echo $tab1, $ca== 'calendar' ? '1' : '2' , '"><a href="', $scripturl, '?action=calendar">'.$txt['calendar24']. '</a></span>&nbsp;|&nbsp;';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo $tab1, $ca== 'mlist' ? '1' : '2' , '"><a href="', $scripturl, '?action=mlist">'.$txt[331]. '</a></span>&nbsp;|&nbsp;';

	// If the user is a guest, show [login] and [register] buttons.
	if ($context['user']['is_guest'])
	{
		echo $tab1, $ca== 'login' ? '1' : '2' , '"><a href="', $scripturl, '?action=login">'.$txt[34]. '</a></span>&nbsp;|&nbsp;';
		echo $tab1, $ca== 'register' ? '1' : '2' , '"><a href="', $scripturl, '?action=register">'.$txt[97]. '</a></span>';
	}
	// Otherwise, they might want to [logout]...
	else
		echo $tab1, $ca== 'logout' ? '1' : '2' , '"><a href="', $scripturl, '?action=logout;sesc='.$context['session_id'].'">'.$txt[108]. '</a></span>';


}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl;

	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (isset($value['test']) && empty($context[$value['test']]))
		{
			unset($button_strip[$key]);
			continue;
		}
		elseif (!isset($buttons[$key]) || $force_reset)
			$buttons[$key] = '<a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '>' . $txt[$value['text']] . '</a>';

		$button_strip[$key] = $buttons[$key];
	}

	if (empty($button_strip))
		return '<td>&nbsp;</td>';

	echo '
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
}

?>
