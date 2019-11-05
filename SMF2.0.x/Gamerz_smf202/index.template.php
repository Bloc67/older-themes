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

	// check if we are on smartphones, bail out if its already defined(bwPortal)
	if(!isset($settings['bwsmartphone']))
	{
		$agents = array(
			array('iPhone', 'iphone'),
			array('iPod', 'ipod'),
			array('PocketIE', 'iemobile'),
			array('Opera Mini', isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])),
			array('Opera Mobile', 'Opera Mobi'),
			array('Android', 'android'),
			array('Symbian', 'symbian'),
			array('Symbian S60', 'series60'),
			array('Symbian S70', 'series70'),
			array('Symbian S80', 'series80'),
			array('Symbian S90', 'series90'),
			array('Symbian S60', 'series 60'),
			array('Symbian S70', 'series 70'),
			array('Symbian S80', 'series 80'),
			array('Symbian S90', 'series 90'),
			array('BlackBerry', 'blackberry'),
			array('BlackBerry Storm', 'blackberry05'),
			array('Palm', 'palm'),
			array('Web OS', 'webos'),
		);
		foreach ($agents as $phone)
		{
			$pos = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), $phone[1]);
			if($pos !== false)
			{
				$settings['bwsmartphone'] = $phone[0];
				break;
			}
		}
	}
	// be able to simply specify it too
	if(isset($_GET['smartphone']))
		$settings['bwsmartphone'] = 'iPhone';

	$settings['catch_action'] = array('layers' => array('myinit','html','body','pages'));

}


// any special pages?
function template_myinit_above()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	if(isset($_GET['action']))
		$what=$_GET['action'];

	if(file_exists($settings['theme_dir'] . '/pages/' . $what. '.init.php'))
		require_once($settings['theme_dir'] . '/pages/' . $what. '.init.php');
}

function template_myinit_below()
{
	return;
}

// any special pages?
function template_pages_above()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	echo '<div>';
	if(isset($_GET['action']))
	{
		$act=$_GET['action'];
		// don't allow outside the pages folder
		$what = preg_replace('/[^a-z_]/', '', $act);
		if(file_exists($settings['theme_dir'] . '/pages/' . $what. '.template.php'))
		{
			// any preset action? Limited use.
			if(file_exists($settings['theme_dir'] . '/pages/' . $what. '.include.php'))
				require_once($settings['theme_dir'] . '/pages/' . $what. '.include.php');
			loadtemplate('pages/'.$what);
		}
		else
			loadtemplate('pages/blank');
	}
}
function template_pages_below() 
{	
	echo '</div>'; 
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

	$blogcss = false;
	// any blog boards then?
	if(!empty($settings['blogboards']) && isset($context['current_board']) && in_array($context['current_board'], explode(",",$settings['blogboards'])))
		$blogcss = true;
	if(!empty($settings['galleryboards']) && isset($context['current_board']) && in_array($context['current_board'], explode(",",$settings['galleryboards'])))
		$blogcss = true;

	if($blogcss)
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/blog.css?fin20" />';

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

	if(!empty($settings['gamez']))
		echo '<link rel="stylesheet" href="', $settings['theme_url'], '/css/'.$settings['gamez'].'.css?fin11" type="text/css" media="screen" charset="utf-8" />';

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<div id="wrapper"' , !empty($settings['forum_width']) ? ' style="width: ' . $settings['forum_width'] . '"' : '' , '>
			<img class="floatright" id="upshrink" src="', $settings['images_url'], '/upshrink.png" alt="*" title="', $txt['upshrink_description'], '" style="display: none;" />
			' , template_menu() , '
	<div id="header"><div class="frame">
		<div id="top_section" style="vertical-align: bottom;">
			<h1 class="forumtitle floatleft">
				<a href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? '<img src="' . $settings['images_url'] . '/theme/logolayer.png" alt="' . $context['forum_name'] . '" />' : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a>
			</h1>
			<div class="floatright" id="user_layer">';

	if ($context['user']['is_logged'])
	{
		echo '
				<div id="userdetails">
					<h3 class="floatleft">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></h3>
					<span id="unreadreplies">
						<a href="', $scripturl, '?action=unread">', $txt['theme_unread'], '</a>
						<a href="', $scripturl, '?action=unreadreplies">', $txt['theme_replies'], '</a>
					</span>
				</div>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<input type="text" name="user" class="top_text" />
					<input type="password" name="passwrd" class="top_text" />
					<span id="loginregister">
						<input type="submit" value="' . $txt['login'] . '" />
						<a href="' , $scripturl , '?action=register">' ,  $txt['register'], '</a>
					</span>
					<input type="hidden" name="cookielength" value="-1" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
				</form>';
	}
	
	echo '
			</div>
		</div>
	</div></div>';

	// The main content should go here.
	echo '
	<div id="content_section"><div class="frame">
		<div id="upper_section"', empty($options['collapse_header']) ? '' : ' style="display: none;"', '>
			<div class="middletext" style="padding: 0 4px;">
				<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" class="floatright">
					<input type="text" name="search" value="'.$txt['search'].'..." onfocus="this.value = \'\';" onblur="if(this.value==\'\') this.value=\''.$txt['search'].'...\';" style="width: 195px; height: 22px; margin-left: 10px;" />
					<input type="submit" name="submit" value="" style="width: 40px;height: 22px; cursor: pointer;" />
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
				<ul class="reset" id="topdetails">';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
					<li><a name="#topofforum" style="color: white;"><b>', $txt['theme_maintain'], '</b> | </a></li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
					<li><a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $txt['theme_approval'] , ' <em class="pm_tip_down">' , $context['unapproved_members'] , '</em> | </a></li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					<li><a href="', $scripturl, '?action=moderate;area=reports">', $txt['theme_open'] , ' <em class="pm_tip_down">' , $context['open_mod_reports'], '</em> | </a></li>';

		echo '
					<li><a name="timeforum">', $context['current_time'], '</a></li>
				</ul>';
	}


	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
				<p><b>', $txt['news'], ': </b>', $context['random_news_line'], '</p>';

	echo '
			</div><br />';
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var oMainHeaderToggle = new smc_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
				aSwappableContainers: [
					\'upper_section\'
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

	echo '
		</div>
		<div id="content_layer">';

	// Custom banners and shoutboxes should be placed here, before the linktree.

	// Show the navigation tree.
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	if(!empty($settings['area1_where']) && $settings['area1_where']==1)
		echo '<hr><div style="text-align: center; margin-bottom: 7px;">', $settings['area1'], '</div>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==1)
		echo '<hr><div style="text-align: center; margin-bottom: 7px;">', $settings['area2'], '</div>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==1)
		echo '<hr><div style="text-align: center; margin-bottom: 7px;">', $settings['area3'], '</div>';

	echo '<br />
		</div>
	</div></div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div id="footer_section"><div class="frame">
		<a id="gotop" href="#wrapper">TOP</a>
		<div style="text-transform: uppercase; font-weight: bold; margin: 1em 0 2px 0;">', theme_copyright(), '</div>
		';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</div>';

	echo '
		<div><a href="http://www.blocweb.net"><b>Gamerz design by BlocWebDesign</b></a></div>
	</div></div>
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
' , !empty($settings['google']) ? $settings['google'] : '' , '
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
	<div class="navigate_section">
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
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &#187;';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// change the PM button
	if(isset($context['menu_buttons']['pm']) && $context['user']['unread_messages']>0)
		$context['menu_buttons']['pm']['title'] = $txt['pm_short'] . ' <em class="pm_tip">' . $context['user']['unread_messages'] . '</em>';
	
	// add admin option to change settings
	if(allowedTo('admin_forum'))
		$context['menu_buttons']['admin']['sub_buttons']['mysettings'] = array(
			'title' => 'My Theme Settings',
			'href' => $scripturl. '?action=mysettings',
			'active_button' => $context['current_action'] == 'mysettings',
		);
	
	// add the blog button
	if(empty($settings['no_blog']))
	{
		$context['menu_buttons']['bwblog'] = array(
			'title' => $txt['bwblog'],
			'href' => $scripturl. '?action=bwblog',
			'active_button' => $context['current_action'] == 'bwblog',
		);
		if($context['current_action'] == 'bwblog')
			$context['menu_buttons']['home']['active_button'] = false;
	}
	
	// add the about button
	if(empty($settings['no_about']))
	{
		$context['menu_buttons']['about'] = array(
			'title' => $txt['bloc_about'],
			'href' => $scripturl. '?action=about',
			'active_button' => $context['current_action'] == 'about',
		);
		if($context['current_action'] == 'about')
			$context['menu_buttons']['home']['active_button'] = false;
	}

	// add the gallery button
	if(empty($settings['no_gallery']))
	{
		$context['menu_buttons']['bwgallery'] = array(
			'title' => $txt['bloc_gallery'],
			'href' => $scripturl. '?action=bwgallery',
			'active_button' => $context['current_action'] == 'bwgallery',
		);
		if($context['current_action'] == 'bwgallery')
			$context['menu_buttons']['home']['active_button'] = false;
	}
	//the extra items.. :)
	if(!empty($settings['extramenus']))
	{
		$menus = unserialize($settings['extramenus']);
		foreach($menus as $it)
		{
				$context['menu_buttons'][$it['id']] = array(
					'id' => $it['id'],	
					'title' => $it['title'],	
					'href' => $it['href'],	
					'active_button' => isset($it['action']) && $context['current_action'] == $it['action'] ? true : false,	
					'sub_buttons' => array(),	
					'extra' => true,
				);
		}
	}

	// check if we are using submenus
	if(!empty($settings['topmenus']))
		$singles = explode(',',$settings['topmenus']);
	else
		$singles = array();
	
	// check if we got submenus
	if(!empty($settings['childmenus']))
		$childmenus = unserialize($settings['childmenus']);
	else
		$childmenus = array();

	$childs = array();
	foreach($childmenus as $c => $d)
		$childs[] = $c;

	$loose = array();
	// collect up "loose" items
	foreach($context['menu_buttons'] as $b => $bu)
	{
		if(!in_array($b, $singles) && !in_array($b, $childs))
			$loose[] = $b;
	}

	echo '
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';
	
	if(!empty($singles))
	{
		foreach ($singles as $single)
		{
			$act = $button = '';
			// search for it
			if(isset($context['menu_buttons'][$single]))
			{
				$act = $single;
				$button = $context['menu_buttons'][$single];
			}
			else
				continue;

			echo '
					<li id="button_', $act, '">
						<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
							<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
						</a>';

			if (!empty($button['sub_buttons']) || !empty($childs) || ($act == 'home' && !empty($loose)))
			{
				echo '
						<ul>';
				if(!empty($button['sub_buttons']))
				{
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
				}
				// we got any assigned childs of this button then?
				if(!empty($childs))
				{
					foreach($childmenus as $child => $parent)
					{
						if($parent == $act && isset($context['menu_buttons'][$child]))
						{
							$childbutton = $context['menu_buttons'][$child];
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
					}
				}
				// finally, the "loose" items gets added
				if(!empty($loose) && $act=='home')
				{
					foreach($loose as $lil => $l)
					{
						if(isset($context['menu_buttons'][$l]))
						{
							$childbutton = $context['menu_buttons'][$l];
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
					}
				}
				echo '
						</ul>';
			}
			echo '
					</li>';
		}
	}
	else
	{
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
				<li><a ' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul class="reset">',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>