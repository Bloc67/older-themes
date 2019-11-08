<?php
// Version: 1.1.8; index

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
	$settings['catch_action'] = array('layers' => array('main','pages'));
	// split up the links if any
	$context['sitemenu']=array();
	if(!empty($settings['custom_pages']))
	{
		$pag=explode('|',$settings['custom_pages']);

		foreach($pag as $menu => $value)
		{
			$what=explode(',',$value);
			$context['sitemenu'][]=array($what[0],$what[1],$what[2]);
		}
	}
}

// any special pages?
function template_pages_above()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	echo '<div id="pages">';
	if(isset($_GET['action']))
		$what=$_GET['action'];
	loadtemplate('pages/'.$what);
}

function template_pages_below()
{
	echo '</div>';
}

// The main sub template above the content.
function template_main_above()
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
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/styles/' , !empty($settings['themestyle']) ? $settings['themestyle'] : '1' , '.css?fin11" />
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
<body>
	<div id="mainframe">
		' , template_menu() , '
		<h1><a href="' , $scripturl , '">' , $context['forum_name'] , '</a></h1>
		' , template_linktree() , '
		<div id="upshrinkHeader"', !empty($options['collapse_header']) ? '' : ' style="display: none;"', '>';

	if (!empty($context['user']['avatar']))
		echo '
			<div id="avatar">', $context['user']['avatar']['image'], '</div>';

	if($context['user']['is_logged'])
	{
		echo '
			<div><strong> ', $txt['hello_member_ndt'], ' ', $context['user']['name'] , '</strong></div>
					<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a> <br />
					<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a><br />';

	}
	// Otherwise they're a guest - send them a lovely greeting...
	else
		echo $txt['welcome_guest'];

	// Now, onto our second set of info, are they logged in again?
	if ($context['user']['is_logged'])
	{
		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
								<b>', $txt[616], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
								', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

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

	echo '
			</div>
		</div>
		<div id="content">';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		</div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '

		<div id="footerarea">
			', theme_copyright(), '
			<div class="smalltext"><strong>liquidWeb</strong> by <a href="http.//www.blocweb.net">BlocWeb</a></div>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '
			<div class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';


	echo '
		</div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
	</div>
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

function theme_linktree() {	return; }

function template_linktree()
{
	global $context, $settings, $options;

	echo '<div id="linktree">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '">' . $tree['name'] . '</a>' : $tree['name'];

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;/&nbsp;';
	}

	echo '</div>';
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
	$context['menubox'][]=array(
				'title' => $txt[103],
				'link' => $scripturl,
				'chosen' => '',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);

	// help button
	$context['menubox'][]=array(
				'title' => $txt[119],
				'link' => $scripturl.'?action=help',
				'chosen' => 'help',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);


	// search button
	$context['menubox'][]=array(
				'title' => $txt[182],
				'link' => $scripturl.'?action=search',
				'chosen' => 'search',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);

	// admin button.This one have permission check for admin as well
	$context['menubox'][]=array(
				'title' => $txt[2],
				'link' => $scripturl.'?action=admin',
				'chosen' => 'admin',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_admin',
				);

	// profile button
	$context['menubox'][]=array(
				'title' => $txt[79],
				'link' => $scripturl.'?action=profile',
				'chosen' => 'profile',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_edit_profile',
				);

	// PM button
	$context['menubox'][]=array(
				'title' => $txt['pm_short'] . ' '. ($context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : ''),
				'link' => $scripturl.'?action=pm',
				'chosen' => 'pm',
				'memberonly' => true,
				'guestonly' => false,
				'permission' => 'allow_pm',
				);

	// calendar button
	$context['menubox'][]=array(
				'title' => $txt['calendar24'],
				'link' => $scripturl.'?action=calendar',
				'chosen' => 'calendar',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_calendar',
				);

	// login button - just for guests
	$context['menubox'][]=array(
				'title' => $txt[34],
				'link' => $scripturl.'?action=login',
				'chosen' => 'login',
				'memberonly' => false,
				'guestonly' => true,
				'permission' => '',
				);

	// register button - just for guests
	$context['menubox'][]=array(
				'title' => $txt[97],
				'link' => $scripturl.'?action=register',
				'chosen' => 'register',
				'memberonly' => false,
				'guestonly' => true,
				'permission' => '',
				);

	// logout button - just for members
	$context['menubox'][]=array(
				'title' => $txt[108],
				'link' => $scripturl.'?action=logout;sesc='. $context['session_id'],
				'chosen' => 'logout',
				'memberonly' => true,
				'guestonly' => false,
				'permission' => '',
				);

	// now render it
	template_menu_render();
}

// the actual rendering 
function template_menu_render()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
<div id="menubox">' , topmenu() , '
	<ul>';
	
	$first=true;
	foreach($context['menubox'] as $button)
	{
		$show_button=false;
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
				echo '<li ', $context['current_action'] == $button['chosen'] ? 'id="chosen"' : '' , $first ? ' class="first"' : '' , '><a href="' , $button['link'] , '">' , $button['title'] , '</a></li>';
			elseif(empty($button['permission']))
				echo '<li ', $context['current_action'] == $button['chosen'] ? 'id="chosen"' : '' , $first ? ' class="first"' : '' , '><a href="' , $button['link'] , '">' , $button['title'] , '</a></li>';
			
			$first=false;
		}
	}

	echo '
	</ul>
</div>';

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
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>
		';
}

function topmenu()
{
	global $context, $txt, $scripturl, $settings;

	$current_action='home';
	
	$buts=array();
	$first =true;
	foreach($context['sitemenu'] as $menu => $val)
	{
		if($val[2]=='page')
			$buts[] = '<a ' . ($first ? 'class="first" ' : '') . 'href="'.$scripturl.'?action='.$val[0].'">'.$val[1].'</a>';
		elseif($val[2]=='link')
			$buts[] = '<a ' . ($first ? 'class="first" ' : '') . 'href="'.$val[0].'">'.$val[1].'</a>';
		$first=false;
	}
	echo implode("",$buts) , '<br />';
}

?>