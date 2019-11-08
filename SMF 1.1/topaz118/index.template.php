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
	$settings['use_tabs'] = true;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;

	// override the preset images
	// make sure undefined actions use their own template
	$settings['catch_action'] = array('layers' => array('main','pages'));
	if(empty($settings['globe']))
		$settings['globe'] = $settings['images_url'] . '/theme/globe.png';
	else
		$settings['globe'] = $settings['images_url'] . '/theme/'.$settings['globe'].'.png';

	if(!empty($settings['own_globe']))
		$settings['globe'] = $settings['own_globe'];

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
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
	if($context['page_title']=='' && isset($_GET['action']))
		$context['page_title']=$_GET['action'];
	elseif($context['page_title']=='' && !isset($_GET['action']))
		$context['page_title']=$context['forum_name'];

	// Show right to left and the character set for ease of translating.
	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '><head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title'], '" />', empty($context['robot_no_index']) ? '' : '
	<meta name="robots" content="noindex" />', '
	<meta name="keywords" content="PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum" />
	<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/script.js?fin11"></script>
	<script language="JavaScript" type="text/javascript" src="', $settings['theme_url'], '/theme.js?fin11"></script>
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

	if ($context['browser']['is_ie7'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/ie7.css" />';

	if ($context['browser']['is_ie6'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/ie6.css" />
	<script defer type="text/javascript" src="', $settings['theme_url'], '/pngfix.js"></script>';

	// any color versions?
	if (!empty($settings['site_color']))
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/' . $settings['site_color'] . '.css" />';

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
<style type="text/css"><!--
		.outermargins
		{
			max-width: ' , !empty($settings['site_width']) ? $settings['site_width'] : '1024px' , ';
			margin: 0 auto 0 auto;
		}
	 --></style>

</head>
<body>
	<div id="mainframe">
		<div id="topframe">
			<div class="outermargins">';

	if(!empty($settings['top_slogan']))
		echo '<em class="slogan2 times">' . $settings['top_slogan'] . '</em>';
	
	template_menu();

	echo '
				<h1><img src="' . $settings['images_url'] . '/theme/logo.png" alt="'.$context['forum_name'].'" /></h1>';

	if(isset($context['TPortal']['toppanel']))
	{
		if($context['TPortal']['toppanel']==1)
			echo '
	<div class="slogan" id="tptopbarHeader">' , TPortal_panel('top') , '</div>';	
	}
	else
	{
		if(!empty($settings['slogan_title']))
		{
			echo '
					<div class="slogan times">
						<h2 class="slogan2">' . $settings['slogan_title'] . '</h2>
						<p>' , !empty($settings['slogan_text']) ? $settings['slogan_text'] : ' '  , '</p>
					</div>';
		}
	}
	echo '
				<img id="globus" src="' , $settings['globe'] , '" alt="" />
			</div>
		</div>
		<div id="contentframe">
			<div class="outermargins">';
	

}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	echo '
			</div>
		</div>
		<div id="botframe">
			<div class="outermargins">';

	theme_copyright();

	echo '	<div class="smalltext">' , function_exists("tportal_version") ? tportal_version(). ' | '  : '' ,  '<strong>Topaz</strong> design by <a href="http.//www.blocweb.net" target="_blank">BlocWeb</a></div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
			</div>
		</div>
	</div>
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	global $context, $settings, $options;

	echo '
	<div id="linktree">';

	$lin=array();
	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		$what='';
		// Show something before the link?
		if (isset($tree['extra_before']))
			$what = $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		$what .= $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '">' . $tree['name'] . '</a>' : $tree['name'];

		// Show something after the link...?
		if (isset($tree['extra_after']))
			$what .= $tree['extra_after'];

		$lin[]=$what;
	}

	echo implode("&nbsp;&#187;&nbsp;",$lin) , '
	</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = 'home';
	$forumaction=false;
	if (in_array($context['current_action'], array('paidsubscribe','admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
	{
		$current_action = $context['current_action'];
		$forumaction=true;
	}
	if ($context['current_action'] == 'search2')
		$current_action = 'search';


	if ($context['current_action'] == 'about')
		$current_action = 'about';

	if (isset($_GET['dl']))
		$current_action = 'downloads';


	if (isset($_GET['board']) || isset($_GET['topic']))
		$current_action = 'forum';

	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';
	if ($context['current_action'] == 'paidsubsribe')
		$current_action = 'admin';

	// IE6 meeds some fixes :P
	if($context['browser']['is_ie6'] && !$context['browser']['is_ie7'])
	{
		$ie6fix1='<table cellpadding="0" cellspacing="0"><tr><td>';
		$ie6fix2='</td></tr></table></a>';
	}
	else
	{
		$ie6fix1='</a>';
		$ie6fix2='';
	}

	// Show the start of the tab section.
	echo '
									<div id="outer"><div id="container">
										<ul id="topnav">';

	// Show the [home] button.
	if(isset($txt['tp-forum']))
		echo 	'
	<li' , $current_action == 'home' ? ' class="chosen"' : '' , ' ><a id="firstmenu" ' , $current_action == 'home' ? ' class="chosen"' : '' , ' href="', $scripturl, '">' , $txt[103] , '</a></li>';
	// Show the [home] button.
	else
		echo 	'
	<li' , $current_action == 'home' ? ' class="chosen"' : '' , ' ><a id="firstmenu" ' , $current_action == 'home' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=homepage">' , $txt[103] , '</a></li>';

	// Show the [forum] button.
	echo 	'
											<li' , ($current_action == 'forum' || $forumaction) ? ' class="chosen"' : '' , ' id="forum"><a' , ($current_action == 'forum' || $forumaction) ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=forum">Forum
												'.$ie6fix1.'
												<ul>
													<li><a' , $current_action == 'help' ? ' class="chosen"' : '' , ' id="help" href="', $scripturl, '?action=help">' , $txt[119] , '</a></li>';

		// How about the [search] button?
		if ($context['allow_search'])
			echo 	'
													<li><a' , $current_action == 'search' ? ' class="chosen"' : '' , ' id="search" href="', $scripturl, '?action=search">' , $txt[182] , '</a></li>';

		// Is the user allowed to administrate at all? ([admin])
		if ($context['allow_admin'])
			echo 	'
													<li><a' , $current_action == 'admin' ? ' class="chosen"' : '' , ' id="admin" href="', $scripturl, '?action=admin">' , $txt[2] , '</a></li>';

		// Edit Profile... [profile]
		if ($context['allow_edit_profile'])
			echo 	'
													<li><a' , $current_action == 'profile' ? ' class="chosen"' : '' , ' id="profile" href="', $scripturl, '?action=profile">' , $txt[79] , '</a></li>';

		// Go to PM center... [pm]
		if ($context['user']['is_logged'] && $context['allow_pm'])
			echo 	'
													<li><a' , $current_action == 'pm' ? ' class="chosen"' : '' , ' id="pm" href="', $scripturl, '?action=pm">PM ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a></li>';

		// The [calendar]!
		if ($context['allow_calendar'])
			echo 	'
													<li><a' , $current_action == 'calendar' ? ' class="chosen"' : '' , ' id="calendar" href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a></li>';

		// the [member] list button
		if ($context['allow_memberlist'])
			echo 	'
													<li><a' , $current_action == 'mlist' ? ' class="chosen"' : '' , ' id="members" href="', $scripturl, '?action=mlist">' , $txt[331] , '</a></li>';

		echo '
												</ul>
												'.$ie6fix2.'
											</li>';

for($a=1 ; $a<6 ; $a++)
{
	// if TP, render one menu here
	if(!empty($settings['use_menu'.$a]))
	{
		// TP or regular?
		if(!empty($settings['tpmenu'.$a]) && function_exists('TP_getmenu'))
		{
			echo '
			<li><a href="index.php">',!empty($settings['tpmenu'.$a.'_title']) ? $settings['tpmenu'.$a.'_title'] : 'menu'.$a ,'
				'.$ie6fix1.'
				<ul>';
			
			$mymenu = TP_getmenu($settings['tpmenu'.$a]);
			foreach($mymenu as $menu)
				echo '<li>'.$menu['link'].'</li>';
			
			echo '
				</ul>	
				'.$ie6fix2.'
			</li>';
		}
		// TP or regular?
		if(!empty($settings['tpmenu'.$a]) && !function_exists('TP_getmenu'))
		{
			echo '
			<li><a href="index.php">',!empty($settings['tpmenu'.$a.'_title']) ? $settings['tpmenu'.$a.'_title'] : 'menu'.$a ,'
				'.$ie6fix1.'
				<ul>';
			
			$mymenu= explode(",",$settings['tpmenu'.$a]);
			foreach($mymenu as $menu => $link)
			{
				$sub=explode("|",$link);
				echo '<li><a href="'.$sub[0].'">'.$sub[1].'</a></li>';
			}
			echo '
				</ul>	
				'.$ie6fix2.'
			</li>';
		}
	}
}	
	echo '
	<li' , $current_action == 'about' ? ' class="chosen"' : '' , ' ><a ' , $current_action == 'about' ? ' class="chosen"' : '' , ' href="index.php?action=about">About</a></li>';

		if($context['user']['is_logged'])
			echo '
			<li><a href="index.php?action=logout;sesc='.$context['session_id'].'">Log Out</a></li>';
		else
			echo '
			<li><a href="index.php?action=login">Login
				'.$ie6fix1.'
				<ul>
					<li><a id="register" href="' , $scripturl , '?action=register">Register</a></li>
				</ul>		
				'.$ie6fix2.'
			</li>';

		echo '
		</ul>
	</div></div>
	<br style="clear: both;" />';

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