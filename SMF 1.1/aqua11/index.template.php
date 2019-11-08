<?php
// Version: 1.1.5; index

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
	$settings['use_tabs'] = false;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;
	// make sure undefined actions use their own template
	$settings['catch_action'] = array('layers' => array('html','body','pages'));
	// split up the links if any
	$context['sitemenu']=array();
	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = true;
		
	if(!empty($settings['custom_pages']))
	{
		$pag=explode('|',$settings['custom_pages']);
		foreach($pag as $menu => $value)
		{
			$what=explode(',',$value);
			$context['sitemenu'][]=array($what[0],$what[1],$what[2]);
		}
	}

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
}
// any special pages?
function template_pages_above()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	echo '<div id="pages">';
	if(isset($_GET['action']))
		$what=$_GET['action'];

	if(file_exists($settings['theme_dir'] . '/pages/' . $what. '.template.php'))
		loadtemplate('pages/'.$what);
	else
		loadtemplate('pages/blank');
}

function template_pages_below()
{
	echo '</div>';
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl;

	if($context['page_title']=='' && isset($_GET['action']))
		$context['page_title']=$_GET['action'];
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

	// The ?rc2 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?rc2" />
	<link rel="stylesheet" href="', $settings['theme_url'], '/css/MenuMatic.css?fin11" type="text/css" media="screen" charset="utf-8" />
	<!--[if lt IE 7]>
		<link rel="stylesheet" href="', $settings['theme_url'], '/css/MenuMatic-ie6.css?fin11" type="text/css" media="screen" charset="utf-8" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/print.css?rc2" media="print" />';

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
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
	{
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);
		$options['collapse_header_ic'] = !empty($_COOKIE['upshrinkIC']);
	}

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

	echo '
				document.getElementById("upshrink_ic").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

				document.getElementById("upshrinkHeaderIC").style.display = mode ? "none" : "";

				current_header_ic = mode;
			}
		// ]]></script>
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	<div id="mframe"><div' , !empty($settings['forum_width']) ? ' style="margin: 0 auto; width: '.$settings['forum_width'].';"' : '' , '>
		<div id="mytop">
			<div id="userspot' , !empty($settings['userwindowon']) ? '' : '2', '"><div' , !empty($settings['userwindowon']) ? '' : ' style="display:none;"'  , '>';
		// If the user is logged in, display stuff like their name, new messages, etc.
		if ($context['user']['is_logged'])
		{
			if (!empty($context['user']['avatar']))
				echo '
				<div style="float: right; margin-left: 5px;">', $context['user']['avatar']['image'], '</div>';
			echo '
				<ul class="reset">
					<li class="greeting">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></li>
					<li><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
					<li><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>';

			// Is the forum in maintenance mode?
			if ($context['in_maintenance'] && $context['user']['is_admin'])
				echo '
					<li class="notice">', $txt[616], '</li>';

			// Are there any members waiting for approval?
			if (!empty($context['unapproved_members']))
				echo '
								<li>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';


			echo '
				</ul>';
		}
		// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	else
		{
			echo '
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
		// Show a random news item? (or you could pick one from news_lines...)
		if (!empty($settings['enable_news']))
			echo '
				<h2 id="news">', $txt['news'], ': </h2>
				<div>', $context['random_news_line'], '</div>';

	echo '
		</div></div>
		<h1 id="aqua"><a href="' . $scripturl . '">' . $context['forum_name'] . '</a></h1>
		<em id="mysiteslogan">' , !empty($settings['site_slogan']) ? $settings['site_slogan'] : '&nbsp;' ,  '</em>
	</div>';
	template_menu();
	// Show the navigation tree.
	theme_linktree2();
	echo '
	<div id="content_top_start"><div id="content_top_end"><div id="content_top_mid">
	</div></div></div>
	<div id="contento">';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	</div>
	<div id="content_bot_start"><div id="content_bot_end"><div id="content_bot_mid">
	</div></div></div>
	<div id="footer_mid">
	';
	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	theme_copyright();

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';
	
	if(!empty($settings['area1_where']) && $settings['area1_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;">', $settings['area1'], '</div>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area2'], '</div>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area3'], '</div>';

	echo '
		<div class="smalltext"><b>Aqua</b> design by <a href="http://www.blocweb.net">BlocWeb</a></div>
	</div></div>';

}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
	<!-- Load the Mootools Framework -->
	<script src="http://www.google.com/jsapi"></script><script>google.load("mootools", "1.2.1");</script>	
	
	<!-- Load the MenuMatic Class -->
	<script src="'.$settings['theme_url'].'/js/MenuMatic_0.68.3.js" type="text/javascript" charset="utf-8"></script>
	
	<!-- Create a MenuMatic Instance -->
	<script type="text/javascript" >
		window.addEvent(\'domready\', function() {			
			var myMenu = new MenuMatic();
		});
	if(document.getElementById(\'admnav\'))
	{
		window.addEvent(\'domready\', function() {			
			var myMenu = new MenuMatic({
				id:\'admnav\',
				subMenusContainerId:\'admsubMenus\'
				});
		});
	}';

	echo '
	</script>
</body></html>';
}
function theme_linktree() {return;}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree2($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
	{
		echo '<div class="navigate_section"></div>';
		return;
	}
	// Reverse the linktree in right to left mode.
	if ($context['right_to_left'])
		$context['linktree'] = array_reverse($context['linktree'], true);

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Don't show a separator for the last one (RTL mode)
		if ($link_num != count($context['linktree']) - 1 && $context['right_to_left'])
			echo '&#171;&nbsp;';

			// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] .'</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1 && !$context['right_to_left'])
			echo '&nbsp;&#187;';

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

	// add buttons here
	$context['menubox'] = array();

	/*
		'title' - the string the link will have
		'link' - the actual link
		'chosen' - which "current_action" this button belongs to.
		'memberonly' - show it JUST for members
		'guestonly' - show it JUST for guests
		'permission' - any permission you want to check before displaying the button

	*/

	// home button
	$context['menubox']['home']=array(
				'title' => $txt[103],
				'link' => $scripturl,
				'chosen' => '',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);
	if(function_exists('tportal_version'))
		$context['menubox']['forum']=array(
				'title' => $txt['tp-forum'],
				'link' => $scripturl.'?action=forum',
				'chosen' => 'forum',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);

	// help button
	$context['menubox']['help']=array(
				'title' => $txt[119],
				'link' => $scripturl.'?action=help',
				'chosen' => 'help',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);


	// search button
	$context['menubox']['search']=array(
				'title' => $txt[182],
				'link' => $scripturl.'?action=search',
				'chosen' => 'search',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);

	// admin button.This one have permission check for admin as well
	$context['menubox']['admin']=array(
				'title' => $txt[2],
				'link' => $scripturl.'?action=admin',
				'chosen' => 'admin',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_admin',
				);

	// profile button
	$context['menubox']['profile']=array(
				'title' => $txt[79],
				'link' => $scripturl.'?action=profile',
				'chosen' => 'profile',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_edit_profile',
				);

	// PM button
	$context['menubox']['pm']=array(
				'title' => $txt['pm_short'] . ' '. ($context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : ''),
				'link' => $scripturl.'?action=pm',
				'chosen' => 'pm',
				'memberonly' => true,
				'guestonly' => false,
				'permission' => 'allow_pm',
				);

	// calendar button
	$context['menubox']['calendar']=array(
				'title' => $txt['calendar24'],
				'link' => $scripturl.'?action=calendar',
				'chosen' => 'calendar',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_calendar',
				);

	// login button - just for guests
	$context['menubox']['login']=array(
				'title' => $txt[34],
				'link' => $scripturl.'?action=login',
				'chosen' => 'login',
				'memberonly' => false,
				'guestonly' => true,
				'permission' => '',
				);

	// login button - just for guests
	$context['menubox']['member']=array(
				'title' => $txt[331],
				'link' => $scripturl.'?action=mlist',
				'chosen' => 'mlist',
				'memberonly' => false,
				'guestonly' => true,
				'permission' => '',
				);

	// register button - just for guests
	$context['menubox']['register']=array(
				'title' => $txt[97],
				'link' => $scripturl.'?action=register',
				'chosen' => 'register',
				'memberonly' => false,
				'guestonly' => true,
				'permission' => '',
				);

	// logout button - just for members
	$context['menubox']['logout']=array(
				'title' => $txt[108],
				'link' => $scripturl.'?action=logout;sesc='. $context['session_id'],
				'chosen' => 'logout',
				'memberonly' => true,
				'guestonly' => false,
				'permission' => '',
				);
	echo '<div id="menustart"><div id="menuend"><div id="menumid">
			<ul id="nav">';

	foreach ($context['menubox'] as $act => $button)
	{

		
		$show_button=false; $active=false;
		// are we logged in?
		if($context['user']['is_logged'])
			if($button['guestonly'])
				$show_button = false;
			else
				$show_button = true;
		// just a guest then...
		else
		{
			if($button['memberonly'])
				$show_button = false;
			elseif($button['guestonly'] && !$button['memberonly'])
				$show_button = true;
			elseif(!$button['memberonly'])
				$show_button = true;
		}
		// can we show the button?
         if($show_button)
         {
			if(!empty($button['permission']) && $context[$button['permission']])
				$button['active_button']==true;
			elseif(empty($button['permission']))
				$button['active_button']==false;
			else
				$button['active_button']==false;

	
			echo '
					<li id="button_', $act, '">
						<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['link'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
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
								<a href="', $childbutton['link'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
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
										<a', $grandchildbutton['active_button'] ? ' class="active"' : '', ' href="', $grandchildbutton['link'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
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
			</ul></div></div></div>
		';
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
			$buttons[$key] = '<a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a>';

		$button_strip[$key] = $buttons[$key];
	}

	if (empty($button_strip))
		return '<td>&nbsp;</td>';

	echo '<td>
		<div class="buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>
				<li>', implode('</li><li>', $buttons), '</li>
			</ul>
		</div></td>';
}

function topmenu()
{
	global $context, $txt, $scripturl, $settings;

	$current_action='home';
	
	$buts=array();
	$first =true;
	echo '
	<div id="topmenu">';
	foreach($context['sitemenu'] as $menu => $val)
	{
		if($val[2]=='page')
			$buts[] = '<a ' . ($first ? 'class="first" ' : '') . 'href="'.$scripturl.'?action='.$val[0].'">'.$val[1].'</a>';
		elseif($val[2]=='link')
			$buts[] = '<a ' . ($first ? 'class="first" ' : '') . 'href="'.$val[0].'">'.$val[1].'</a>';
		$first=false;
	}
	echo implode("&nbsp;|&nbsp;",$buts) , '</div>';
}
?>