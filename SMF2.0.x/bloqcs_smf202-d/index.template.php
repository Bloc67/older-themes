<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

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
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = false;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
	$settings['theme_variants'] = array('red','blue','green','orange','black','purple');
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?fin20" />';

	if(!empty($context['theme_variant']))
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index' , $context['theme_variant'] , '.css?fin20" />';


	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<div id="wrapper" style="width: ' , !empty($settings['forum_width']) ? $settings['forum_width'] : '960px' , '; margin: auto;">
	<div id="topsection" class="mbox">
		<div class="floatright myborder">
			<img id="upshrink" src="', $settings['images_url'], '/upshrink.png" alt="*" title="', $txt['upshrink_description'], '" style="display: none; clear: right;"  />
		</div>';

	// the social icons
	echo '
	<div class="floatright" style="margin-top: 1em;">';

	if(!empty($settings['rs_icon']))
		echo '
		<a href="' , $scripturl , '?action=.xml;type=rss"><img src="' , $settings['images_url'] , '/icons/rs.png" alt="RSS" /></a>';

	if(!empty($settings['fb_icon']))
		echo '
		<a href="' , $settings['fb_icon'] , '"><img src="' , $settings['images_url'] , '/icons/fb.png" alt="Facebook" /></a>';

	if(!empty($settings['ln_icon']))
		echo '
		<a href="' , $settings['ln_icon'] , '"><img src="' , $settings['images_url'] , '/icons/ln.png" alt="LinkedIn" /></a>';

	if(!empty($settings['tw_icon']))
		echo '
		<a href="' , $settings['tw_icon'] , '"><img src="' , $settings['images_url'] , '/icons/tw.png" alt="Twitter" /></a>';

	if(!empty($settings['my_icon']))
		echo '
		<a href="' , $settings['my_icon'] , '"><img src="' , $settings['images_url'] , '/icons/my.png" alt="MySpace" /></a>';

	echo '
	</div>';
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
		echo '
		<div class="floatright" id="myreplies">
			<span class="colortext">' , $txt['mymenu'] , '</span>
			<a href="', $scripturl, '?action=unread">',$txt['my_unread'],'</a> |
			<a href="', $scripturl, '?action=unreadreplies">',$txt['my_replies'],'</a> 
		</div>';
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
		<form id="guest_form" class="floatright" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', ' style="margin: 1.2em 2em 0 0;">
			<input type="text" name="user" size="10"  />
			<input type="password" name="passwrd" size="10"  />
			<select name="cookielength">
				<option value="60">', $txt['one_hour'], '</option>
				<option value="1440">', $txt['one_day'], '</option>
				<option value="10080">', $txt['one_week'], '</option>
				<option value="43200">', $txt['one_month'], '</option>
				<option value="-1" selected="selected">', $txt['forever'], '</option>
			</select>
			<input type="submit" value="', $txt['login'], '" />	';

		if (!empty($modSettings['enableOpenID']))
			echo '
			<input type="text" name="openid_identifier" id="openid_url" size="25"  />';

		echo '
				<input type="hidden" name="hash_passwrd" value="" />
		</form>';
	}

	echo '
		<h1>
			<a href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? '<img src="' . $settings['images_url'] . '/logo.png" alt="' . $context['forum_name'] . '" />' : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a>
		</h1>	
	</div>
	' , template_menu() , '
	<div id="collapse_section" style="clear: both;', empty($options['collapse_header']) ? '' : ' display: none;', '">';
		
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
		<div id="news_section" class="mbox"><b>', $txt['news'], ': </b>', $context['random_news_line'], '</div>';

	echo '
		<div id="user_section">
				<form id="search_form" class="floatright" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<div id="myquicksearch"><input type="text" name="search" value="" class="myinput" /><input type="submit" name="submit" value="" class="mysubmit" /></div>
					<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
	// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '</form>';

	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		if (!empty($context['user']['avatar']))
			echo '
			<div class="floatleft" style="margin-right: 2em;">', $context['user']['avatar']['image'], '</div>';
		echo '
			<ul class="reset" style="padding-top: 1em;">
				<li class="greeting colortext"><strong>', $txt['hello_member_ndt'], ' ', $context['user']['name'], '</strong></li>';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
				<li class="notice">', $txt['maintain_mode_on'], '</li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
				<li>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
				<li><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

		echo '
			</ul>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
			', sprintf($txt['welcome_guest'], $txt['guest_title']);
	}

	echo '
		</div>
	</div>';

	// Show the navigation tree.
	theme_linktree();
	
	// Define the upper_section toggle in JavaScript.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var oMainHeaderToggle = new smc_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
				aSwappableContainers: [
					\'collapse_section\'
				],
				aSwapImages: [
					{
						sId: \'upshrink\',
						srcExpanded: smf_images_url + \'/upshrink.png\',
						altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
						srcCollapsed: smf_images_url + \'/upshrink2.png\',
						altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
					}
				],
				oThemeOptions: {
					bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
					sOptionName: \'collapse_header\',
					sSessionVar: ', JavaScriptEscape($context['session_var']), ',
					sSessionId: ', JavaScriptEscape($context['session_id']), '
				},
				oCookieOptions: {
					bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
					sCookieName: \'upshrink\'
				}
			});
		// ]]></script>';

	// The main content should go here.
	echo '
	<div id="mainsection">';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	</div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div id="footersection">
		<div class="floatright" style="text-align: right;"><strong>BloQcs</strong> design by <a href="http://www.blocweb.net">Bloc</a></div>
		', theme_copyright();

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</div>';

	echo '
	</div>
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
	{	
		return;
	}

	echo '
	<div class="mynav">
		<div class="cap1"><div class="cap2">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show the link, including a URL if it should have one.
		if($settings['linktree_link'] && isset($tree['url'])) 
			echo '<a href="' . $tree['url'] . '"><span class="oute"><span class="inne">' , isset($tree['extra_before']) ? $tree['extra_before'] : '' , substr($tree['name'],0,220) , isset($tree['extra_after']) ? $tree['extra_after'] : '' , '</span></span></a>';
		else
			echo '<span class="oute"><span class="inne">' , isset($tree['extra_before']) ? $tree['extra_before'] : '' , substr($tree['name'],0,220) , isset($tree['extra_after']) ? $tree['extra_after'] : '' , '</span></span>';

		echo '
			</li>';
	}
	echo '
		</ul>
		</div></div>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div id="mainmenu">
				<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '" class="', $button['active_button'] ? 'activefirst' : '', '" >
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>