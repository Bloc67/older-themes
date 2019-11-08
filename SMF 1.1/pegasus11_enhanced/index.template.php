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
	$settings['use_tabs'] = true;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;
	// load custom language strings
	if(loadLanguage('ThemeStrings') == false)
      loadLanguage('ThemeStrings', 'english');
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
function template_main_above()
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

	// The ?fin11 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css?fin11" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/' , !empty($settings['colorversion']) ? $settings['colorversion'] : 'blue' , '.css?fin11" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/print.css?fin11" media="print" />';

	if($context['browser']['is_ie6'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/ie6.css?fin11" />';
	elseif($context['browser']['is_ie7'])
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
	{
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);
		$options['collapse_header_ic'] = !empty($_COOKIE['upshrinkIC']);
	}
	if(!empty($settings['fullwidth']))
		echo '	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/full.css?fin11" />';

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

			document.getElementById("menubox").style.display = mode ? "none" : "";

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
<body id="mybody">
	<div id="back">
		<div id="right">
			<div id="left">

				<div id="topbar">
					<div id="searcha">';
	

	if($context['user']['is_logged'])
		echo '		<a class="bluebutton" href="' . $scripturl . '?action=unread"><span>' . $txt['peg_unread'] .'</span></a>
						<a class="bluebutton" href="' . $scripturl . '?action=unreadreplies"><span>' . $txt['peg_replies'] .'</span></a>
						<a class="bluebutton" href="' . $scripturl . '?action=profile"><span>' . $txt['hello_member_ndt'] . ' ' . $context['user']['name'] .'</span></a>	';

	echo '
						<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" style="display: inline; margin: 0;padding: 0;">
							<div class="greybutton"><div class="inner"><input type="text" class="nostyle" name="search" value="" style="width: 10em;"   /></div></div>
							<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
							<input type="hidden" name="topic" value="', $context['current_topic'], '" />';

		// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
							<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '<a href="#" onclick="shrinkHeader(!current_header); return false;"><img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" align="bottom" style="margin: 0 1em;" /></a>
						</form>
					</div>';

	echo '</div>';
	template_menu();
	template_linktree();
	echo '
	<div class="content">';
	topmenu();
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '<div id="copyr">' , theme_copyright() , '
									<div class="smalltext"><b>Pegasus</b> theme &copy; <a href="http.//www.blocweb.net">BlocWeb</a></div> ';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
									<div class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';

	echo '</div>';
	
	if(!empty($settings['area1_where']) && $settings['area1_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;">', $settings['area1'], '</div>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area2'], '</div>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area3'], '</div>';
	
	echo '
				</div>
			</div>
		</div>
	</div>
	<div id="footer"><span class="left"></span><span class="right"></span></div>';
	// The following will be used to let the user know that some AJAX process is running
	echo '
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}
function theme_linktree()
{
	return;
}
// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function template_linktree()
{
	global $context, $settings, $options;

	echo '<div id=linktree><div>';

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
			echo '&nbsp;>&nbsp;';
	}

	echo '</div></div>';
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
				'title' => $txt['peg_pm'] . ' '. ($context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : ''),
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

	// now render it
	template_menu_render();
}

// the actual rendering 
function template_menu_render()
{
	global $context, $settings, $options, $scripturl, $txt;

	if(function_exists('tportal_version'))
	{
		if(isset($_GET['topic']) || isset($_GET['board']))
			$context['current_action'] = 'forum';
		if(isset($_GET['action']) && in_array($_GET['action'],array('post','post2')))
			$context['current_action'] = 'forum';
	}
	
	echo '
<div id="menubox"', empty($options['collapse_header']) ? '' : ' style="display: none;"' , '>
	<ul>';
	
	$first=true;
	foreach($context['menubox'] as $but => $button)
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
				echo '<li ', $context['current_action'] == $button['chosen'] ? 'class="chosen"' : '' , '><a id="' , $but , '" href="' , $button['link'] , '"><span>' , $button['title'] , '</span></a></li>';
			elseif(empty($button['permission']))
				echo '<li ', $context['current_action'] == $button['chosen'] ? 'class="chosen"' : '' ,  '><a id="' , $but , '" href="' , $button['link'] , '"><span>' , $button['title'] , '</span></a></li>';
			
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
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
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