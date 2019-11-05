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
	$settings['message_index_preview'] = true;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// any pagelinks?
	if(!empty($context['page_index']))
	{
		$context['page_index'] = str_replace(array('[',']'),array('<span class="active">','</span>'),$context['page_index']);
	}

	// Show right to left and the character set for ease of translating.
	echo '
<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

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

	// Is the forum in maintenance mode?
	if ($context['in_maintenance'] && $context['user']['is_admin'])
		echo '
<div class="error" style="padding: 0.5em; background: black; text-align: center; margin-top: -16px;">', $txt['maintain_mode_on'], '</div>';

	echo '
<div id="wrapper"> 
	<header>
		<div class="bwgrid">
			<div class="bwcell8">		
				<h1 class="forumtitle">
					<a href="', $scripturl, '"><img src="' , !empty($settings['header_logo_url']) ? $settings['header_logo_url'] : $settings['images_url'] . '/theme/logo.png' , '" alt="' . $context['forum_name'] . '" /></a>
				</h1>
				<div id="siteslogan">
				', empty($settings['site_slogan']) ? '' : $settings['site_slogan'], '
				</div>
			</div>
			<div class="bwcell8" style="text-align: right;padding-top: 1em;">
				<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<input type="text" name="search" value="" class="top_input_text" />
					<input type="submit" name="submit" value="', $txt['search'], '" class="top_button_submit" />
					<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
	// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '	</form>';

	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
				<div id="memberarea"><span>', $txt['hello_member_ndt'], ' ', $context['user']['name'], '</span>
					<a href="', $scripturl, '?action=unread">', $txt['bloc_unread'], '</a> | <a href="', $scripturl, '?action=unreadreplies">', $txt['bloc_replies'], '</a>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
					| <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">'. $txt['bloc_approvals'] . ' (' . $context['unapproved_members'] . ' </a>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					| <a href="', $scripturl, '?action=moderate;area=reports">', $txt['bloc_reports'], ' (' , $context['open_mod_reports'], ')</a>';

		echo '
				</div>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<div class="memberarea">
					<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
						<input type="text" name="user" size="10" class="top_input_text" />
						<input type="password" name="passwrd" size="10" class="top_input_text" />
						<select name="cookielength" class="top_input_text">
							<option value="60">', $txt['one_hour'], '</option>
							<option value="1440">', $txt['one_day'], '</option>
							<option value="10080">', $txt['one_week'], '</option>
							<option value="43200">', $txt['one_month'], '</option>
							<option value="-1" selected="selected">', $txt['forever'], '</option>
						</select>
						<input type="submit" value="', $txt['login'], '" class="top_button_submit" />';

		if (!empty($modSettings['enableOpenID']))
			echo '
						<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="top_input_text openid_login" />';

		echo '
						<input type="hidden" name="hash_passwrd" value="" />
					</form>
				</div>';
	}

	echo '
			</div>
		</div>
	</header>
	<div id="pagetitle" class="catbg">' , $context['page_title_html_safe'] , '</div>';
	
	// test side custom
	if(!empty($settings['customside']) && (!empty($settings['box1']) || !empty($settings['box2']) || !empty($settings['box3'])))
		echo '
	<div class="bwgrid" id="asidebody">
		<div class="bwcell13">';

	echo '
	<div id="wrapcontent">
		' , template_menu(), '
		<div id="mainbox">
			<div id="contentbox">
				' , theme_linktree(),'
				<article>';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
				</article>
			</div>
		</div>
		' , template_copyright() ,  '
		' , template_custom() ,  '
		<footer id="footerarea">
			', theme_copyright(), ' | <a href="http://www.bjornhkristiansen.com">Envision2013 theme by Bloc</a>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
			<br>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'];

	echo '
		</footer>
	</div>';

	if(!empty($settings['customside']) && (!empty($settings['box1']) || !empty($settings['box2']) || !empty($settings['box3'])))
		echo 
		'</div>
		<div class="bwcell3">', template_custom_side(),  '</div>
	</div>';

	echo '
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
		return;

	echo '
	<nav class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '	<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp; &#47;';

		echo '</li>';
	}
	echo '</ul>
	</nav>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<menu id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
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
		</menu>';
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

function template_copyright()
{
	global $settings, $context, $txt, $scripturl;

	
	echo '
				<div id="copyrite" style="overflow: hidden;">
					<div class="floatright">
						<a class="socialbuttons" id="rs_button" href="' , $scripturl , '?action=.xml;type=rss2">&nbsp;</a>';

	if(!empty($settings['linkedin']))
		echo '<a class="socialbuttons" id="ln_button" href="' , $settings['linkedin'] , '">&nbsp;</a>';
	if(!empty($settings['twitter']))
		echo '<a class="socialbuttons" id="tw_button" href="' , $settings['twitter'] , '">&nbsp;</a>';
	if(!empty($settings['facebook']))
		echo '<a class="socialbuttons" id="fb_button" href="' , $settings['facebook'] , '">&nbsp;</a>';

	echo '
					</div>';
	if(!empty($settings['customcopyrite']))
		echo $settings['customcopyrite'];
	echo '
				</div>';
}

function template_custom()
{
	global $settings, $context, $txt, $scripturl;

	if(!empty($settings['customside']))
		return;
	
	// work ut how many
	$count = 0; $widths = array('1' => 16, '2' => 8, '3' => 33);

	if(!empty($settings['box1']))
		$count++;
	if(!empty($settings['box2']))
		$count++;
	if(!empty($settings['box3']))
		$count++;

	if(!empty($count))
	{
		echo '
				<div id="custom">
					<img class="icon floatright" id="custombodyupshrink" src="', $settings['images_url'], '/collapse.gif" alt="*" title="', $txt['upshrink_description'], '" style="display: none;" />
					<div id="custombody" class="bwgrid">';
		
		foreach(array(1,2,3) as $n)
			if(!empty($settings['box'.$n]))
				echo '	<div class="bwcell' . $widths[$count] . '">' , !empty($settings['box'.$n.'title']) ? '<strong style="padding: 0 1em;">' . $settings['box'.$n.'title'] . '</strong>' : '' , '<p style="padding: 0 1em;">' .$settings['box'.$n], '</p></div>';

		echo '
					</div>
				</div>';
		
		makeToggleObject('custombody');
	}
}

function template_custom_side()
{
	global $settings, $context, $txt, $scripturl;

	echo '
	<aside>
		<div id="custom">';
		
		foreach(array(1,2,3) as $n)
			if(!empty($settings['box'.$n]))
				echo '	
			<div>' , !empty($settings['box'.$n.'title']) ? '<strong style="padding: 0 1em 0 0;">' . $settings['box'.$n.'title'] . '</strong>' : '' , '<p style="padding: 0 1em 0 0;">' .$settings['box'.$n], '</p></div>';

	echo '
		</div>
	</aside>';
}

function makeToggleObject($name)
{
	global $options, $context, $txt;

	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		// Create the fader toggle.
		var smf' . $name . 'FadeToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_' . $name . '_fader']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'' . $name . '\'
			],
			aSwapImages: [
				{
					sId: \'' . $name . 'upshrink\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_' . $name . '_fader\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'' . $name . 'upshrink\'
			}
		});
	// ]]></script>';
}
?>