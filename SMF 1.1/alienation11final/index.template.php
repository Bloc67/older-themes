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

	// extra strings
	loadLanguage('ThemeStrings');
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
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
			<td style="background: url('.$settings['images_url'].'/left.gif) repeat-y;"><img src="'.$settings['images_url'].'/left.gif" alt="" /></td>
			<td valign="top" width="99%">
				<table cellpadding="0" cellspacing="0" width="100%" border="0" style="margin-bottom: 1ex;">
					<tr style="background: url('.$settings['images_url'].'/logomid.gif) bottom left repeat-x;">
						<td valign="bottom" width="340" class="forumname" style="background: url('.$settings['images_url'].'/logoleft2.jpg) bottom left no-repeat;"><a href="',$scripturl,'">
						' , !empty($settings['header_logo_url']) ? '<img src="'.$settings['header_logo_url'].'" alt="'.$context['forum_name'].'" />' : $context['forum_name'] , '</a>
						<br /><br />
		<a href="javascript:void(0);" onclick="shrinkHeader(!current_header); return false;"><img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 2px 2ex 0px 0px;" border="0" /></a></td>
						<td style="background: url('.$settings['images_url'].'/logomid.gif) bottom left repeat-x;">&nbsp;</td>
						<td align="right"><img src="'.$settings['images_url'].'/logoright.jpg" alt="" /></td>
					</tr>
					<tr>
						<td colspan="3" style="background: url('.$settings['images_url'].'/thin.gif) repeat-x;"><img src="'.$settings['images_url'].'/thin.gif" alt="" /></td>
					</tr>
					<tr>
						<td colspan="3" style="background: url('.$settings['images_url'].'/thin.gif) repeat-x;">', template_menu(),'</td>
					</tr>
					<tr>
						<td valign="bottom" height="30" colspan="3" style="border-bottom: solid 1px #555; padding: 20px 0 8px 0  ; background: url('.$settings['images_url'].'/thin2.gif) repeat-x;">',theme_linktree2(),'</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr>
						<td  id="upshrinkHeader"', empty($options['collapse_header']) ? '' : ' style="display: none;"', ' valign="top" width="150" class="myborder" style="border-right: solid 1px #505050;">';
	if (!empty($context['user']['avatar']))
		echo $context['user']['avatar']['image'];

	echo '<div class="leftbox"><div class="smalltext" style="font-family: verdana, arial, sans-serif;">';

	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '<h2 style="margin-bottom: 0;padding-bottom: 2px;">', $txt['hello_member'], ' ', $context['user']['name'], '</h2>';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
			echo $txt['alien_pm'], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' <br />', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
		echo '.<br />';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
								<b>', $txt[616], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
								', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

		// Show the total time logged in?
		if (!empty($context['user']['total_time_logged_in']))
		{
			echo '
								', $txt['totalTimeLogged1'];

			// If days is just zero, don't bother to show it.
			if ($context['user']['total_time_logged_in']['days'] > 0)
				echo $context['user']['total_time_logged_in']['days'] . $txt['totalTimeLogged2'];

			// Same with hours - only show it if it's above zero.
			if ($context['user']['total_time_logged_in']['hours'] > 0)
				echo $context['user']['total_time_logged_in']['hours'] . $txt['totalTimeLogged3'];

			// But, let's always show minutes - Time wasted here: 0 minutes ;).
			echo $context['user']['total_time_logged_in']['minutes'], $txt['totalTimeLogged4'], '<br />';
		}

		echo '
							<a href="', $scripturl, '?action=unread">', $txt['alien_unread'], '</a><br />
							<a href="', $scripturl, '?action=unreadreplies">', $txt['alien_replies'], '</a><br />
							<a href="', $scripturl, '?action=profile;u='.$context['user']['id'].';sa=showPosts">', $txt['alien_own'], '</a><br />
							', $context['current_time'];
	}
	// Otherwise they're a guest - so politely ask them to register or login.
	else
	{
		echo '
							', $txt['welcome_guest'], '<br />
							', $context['current_time'], '<br />

							<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>

								<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="margin: 3px 1ex 1px 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
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
								</form>';
	}

	echo '
						</div></div>
						<div class="bdivide"></div>
					<form class="leftbox" action="', $scripturl, '?action=search2" method="post" style="margin: 0;">
						<div style="margin-top: 7px;">
							<b>', $txt[182], ': </b><br /><input type="text" name="search" value=""  />&nbsp;
							<input type="submit" name="submit" style="margin-top: 4px;" value="', $txt[182], '" />&nbsp;
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
						</div>
					</form>
				<div class="bdivide"></div>
				';

	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '<div class="leftbox"><div style="padding: 5px;" class="smalltext">', $context['random_news_line'], '</div></div><div class="bdivide"></div>';

	// The "key stats" box.
	echo '<div class="leftbox">
						<div style="', !$context['browser']['is_ie'] ? 'min-height: 35px;' : 'height: 4em;', ' padding: 5px;" class="smalltext">
							<b>', $context['common_stats']['total_posts'], '</b> ', $txt[95], ' ', $txt['smf88'], ' <b>', $context['common_stats']['total_topics'], '</b> ', $txt[64], ' ', $txt[525], ' <span style="white-space: nowrap;"><b>', $context['common_stats']['total_members'], '</b> ', $txt[19], '</span><br />
							', $txt[656], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
						</div></div>';

    // a sidepanel?
    if(!empty($settings['sidepanel']))
    	echo '<div class="bdivide"></div><div class="leftbox">'.$settings['sidepanel'].'</div>';

		echo '
						</td>
						<td valign="top">';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '</td></tr></table>';
	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '

	<div id="footerarea" class="smalltext" style="margin-bottom: 2ex; padding-top: 4ex; background: url('.$settings['images_url'].'/thin2.gif) repeat-x; text-align: center; padding-bottom: 1ex;', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? ' width: 100%;' : '', '">
					', theme_copyright(), '<br />
					<b>Alienation</b> design by <a href="http.//www.blocweb.net">BlocWeb</a> |
					<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
					<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '<br />
		<span class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</span>';

	// This is an interesting bug in Internet Explorer AND Safari. Rather annoying, it makes overflows just not tall enough.
	if (($context['browser']['is_ie'] && !$context['browser']['is_ie4']) || $context['browser']['is_mac_ie'] || $context['browser']['is_safari'] || $context['browser']['is_firefox'])
	{
		// The purpose of this code is to fix the height of overflow: auto div blocks, because IE can't figure it out for itself.
		echo '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[';

		// Unfortunately, Safari does not have a "getComputedStyle" implementation yet, so we have to just do it to code...
		if ($context['browser']['is_safari'])
			echo '
			window.addEventListener("load", smf_codeFix, false);

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if ((codeFix[i].className == "code" || codeFix[i].className == "post" || codeFix[i].className == "signature") && codeFix[i].offsetHeight < 20)
						codeFix[i].style.height = (codeFix[i].offsetHeight + 20) + "px";
				}
			}';
		elseif ($context['browser']['is_firefox'])
			echo '
			window.addEventListener("load", smf_codeFix, false);
			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if (codeFix[i].className == "code" && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0))
						codeFix[i].style.overflow = "scroll";
				}
			}';
		else
			echo '
			var window_oldOnload = window.onload;
			window.onload = smf_codeFix;

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = codeFix.length - 1; i > 0; i--)
				{
					if (codeFix[i].currentStyle.overflow == "auto" && (codeFix[i].currentStyle.height == "" || codeFix[i].currentStyle.height == "auto") && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0) && (codeFix[i].offsetHeight != 0 || codeFix[i].className == "code"))
						codeFix[i].style.height = (codeFix[i].offsetHeight + 36) + "px";
				}

				if (window_oldOnload)
				{
					window_oldOnload();
					window_oldOnload = null;
				}
			}';

		echo '
		// ]]></script>';
	}

	echo '
	</div>';

	echo '
			</td>
			<td style="background: url('.$settings['images_url'].'/right.gif) repeat-y;"><img src="'.$settings['images_url'].'/right.gif" alt="" /></td>
		</tr>
	</table>
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

function theme_linktree()
{
	return;
}
// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree2()
{
	global $context, $settings, $options;

	echo '<div class="nav" style="padding-left: 2ex; font-size: smaller;">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo ($link_num != count($context['linktree']) - 1) ? '' : '<b>' , $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'], ($link_num != count($context['linktree']) - 1) ? '' : '</b>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;:&nbsp;';
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
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'search2')
		$current_action = 'search';
	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	// Are we using right-to-left orientation?
	if ($context['right_to_left'])
	{
		$first = 'last';
		$last = 'first';
	}
	else
	{
		$first = 'first';
		$last = 'last';
	}

	// Show the start of the tab section.
	echo '
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="amaintab_back" width="29%">&nbsp;</td>';

	// Show the [home] button.
	echo  '
				<td class="amaintab_' , $current_action == 'home' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '">' , $txt[103] , '</a>
				</td>';

	// Show the [help] button.
	echo '
				<td class="amaintab_' , $current_action == 'help' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=help">' , $txt[119] , '</a>
				</td>';

	// How about the [search] button?
	if ($context['allow_search'])
		echo  '
				<td class="amaintab_' , $current_action == 'search' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=search">' , $txt[182] , '</a>
				</td>';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo '
				<td class="amaintab_' , $current_action == 'admin' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=admin">' , $txt[2] , '</a>
				</td>';

	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo  '
				<td class="amaintab_' , $current_action == 'profile' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=profile">' , $txt[79] , '</a>
				</td>';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo  '
				<td class="amaintab_' , $current_action == 'pm' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=pm">' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a>
				</td>';

	// The [calendar]!
	if ($context['allow_calendar'])
		echo  '
				<td class="amaintab_' , $current_action == 'calendar' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a>
				</td>';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo '
				<td class="amaintab_' , $current_action == 'mlist' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=mlist">' , $txt[331] , '</a>
				</td>' ;


	// If the user is a guest, show [login] button.
	if ($context['user']['is_guest'])
		echo  '
				<td class="amaintab_' , $current_action == 'login' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=login">' , $txt[34] , '</a>
				</td>';


	// If the user is a guest, also show [register] button.
	if ($context['user']['is_guest'])
		echo  '
				<td class="amaintab_' , $current_action == 'register' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=register">' , $txt[97] , '</a>
				</td>';


	// Otherwise, they might want to [logout]...
	if ($context['user']['is_logged'])
		echo  '
				<td class="amaintab_' , $current_action == 'logout' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">' , $txt[108] , '</a>
				</td>';

	// The end of tab section.
	echo '
				<td class="amaintab_back" width="69%">&nbsp;</td>
			</tr>
		</table>';

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
		<td class="maintab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>
		<td class="maintab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>
		<td class="maintab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
}

?>
