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
	// make sure undefined actions use their own template
	$settings['catch_action'] = array('layers' => array('main','pages'));
	
	// add custom pages
	$context['sitemenu']=array();
		if(!empty($settings['but1_name']))
	{
		if(!empty($settings['but1_link']) && !empty($settings['but1_type']))
			$context['sitemenu'][1]=array($settings['but1_name'],$settings['but1_link'],$settings['but1_type']);
	}
	if(!empty($settings['but2_name']))
	{
		if(!empty($settings['but2_link']) && !empty($settings['but2_type']))
			$context['sitemenu'][2]=array($settings['but2_name'],$settings['but2_link'],$settings['but2_type']);
	}
	if(!empty($settings['but3_name']))
	{
		if(!empty($settings['but3_link']) && !empty($settings['but3_type']))
			$context['sitemenu'][3]=array($settings['but3_name'],$settings['but3_link'],$settings['but3_type']);
	}
	if(!empty($settings['but4_name']))
	{
		if(!empty($settings['but4_link']) && !empty($settings['but4_type']))
			$context['sitemenu'][4]=array($settings['but4_name'],$settings['but4_link'],$settings['but4_type']);
	}
	if(!empty($settings['but5_name']))
	{
		if(!empty($settings['but5_link']) && !empty($settings['but5_type']))
			$context['sitemenu'][5]=array($settings['but5_name'],$settings['but5_link'],$settings['but5_type']);
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

	/* Internet Explorer 4/5 and Opera 6 just don't do font sizes properly. (they are big...)
		Thus, in Internet Explorer 4, 5, and Opera 6 this will show fonts one size smaller than usual.
		Note that this is affected by whether IE 6 is in standards compliance mode.. if not, it will also be big.
		Standards compliance mode happens when you use xhtml... */
	if ($context['browser']['needs_size_fix'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/fonts-compat.css" />';

	// for ie6!
	if($context['browser']['is_ie6'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/ie6.css?fin1" />';

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

	echo '
				document.getElementById("upshrink_ic").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

				document.getElementById("upshrinkHeaderIC").style.display = mode ? "none" : "";

				current_header_ic = mode;
			}
		// ]]></script>
</head>
<body>';

	echo '
<div id="mainframe">
	<div id="topm"><span>
		<div id="topr">';
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
			', $txt['hello_member'], ' <b>', $context['user']['name'], '</b>';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
			echo ', ', $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
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
			<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a><br />
			', $context['current_time'];
	}
	// Otherwise they're a guest - so politely ask them to register or login.
	else
	{
		echo '
			', $txt['welcome_guest'], '<br />
			', $context['current_time'], '<br />

			<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>
			<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 3px 1ex 1px 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
				<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" />
				<select name="cookielength">
					<option value="60">', $txt['smf53'], '</option>
					<option value="1440">', $txt['smf47'], '</option>
					<option value="10080">', $txt['smf48'], '</option>
					<option value="43200">', $txt['smf49'], '</option>
					<option value="-1" selected="selected">', $txt['smf50'], '</option>
				</select>
				<input type="submit" value="', $txt[34], '" /><br />
				', $txt['smf52'], '
				<input type="hidden" name="hash_passwrd" value="" />
			</form>';
	}
	echo '
		</div>
		<h1><span class="hide">', $context['forum_name'], '</span></h1>		
		</span>
	</div>
	<div id="menuback"><span><span>
		', template_menu(), '
	</span></span>
	</div>
	<div id="frame">
		<div id="frame2">
			', theme_linktree2(), '
			<div id="mainleft"><div id="mainright"><div id="maintop"><div id="mainbot">
			<div id="maintopleft"><div id="maintopright"><div id="mainbotleft"><div id="mainbotright"><div id="innermain">';

	tpcode();
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

 	tpcode2();
	echo '
				</div></div></div></div></div>
			</div></div></div></div>
		</div>
	</div>
	<div id="bottomr"><div class="one"><div class="two">';
	theme_copyright();
	echo '<div  class="smalltext">', function_exists("tportal_version") ? tportal_version().' | ' : '' , '
			<strong>Adagio</strong> design by <a href="http.//www.blocweb.net" target="_blank">BlocWeb</a></div>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';

	echo '
	</div></div></div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
</div>	
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	return;
}
function theme_linktree2()
{
	global $context, $settings, $options;

	echo '<div class="nav" style="font-size: smaller; padding: 0 0 10px 10px;">';

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
			echo '&nbsp;&#0187;&nbsp;';
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

	// Show the start of the tab section.
	echo '
		<div id="container"><div id="inner">
			<ul id="menubox">
				<li class="first' , $current_action=='home' ? ' chosen' : '' , '"><a href="', $scripturl, '"><span>' , $txt[103] , '</span></a></li>';
	// TP
	if (isset($txt['tp-forum']))
		echo '
				<li' , $current_action=='forum' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=forum"><span>' , $txt['tp-forum'] , '</span></a></li>';

	echo '
				<li' , $current_action=='help' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=help"><span>' , $txt[119] , '</span></a></li>';
	if ($context['allow_search'])
		echo '
				<li' , $current_action=='search' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=search"><span>', $txt[182] , '</span></a></li>';
	if ($context['allow_calendar'])
		echo '
				<li' , $current_action=='calendar' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=calendar"><span>' , $txt['calendar24'] , '</span></a></li>';
	if ($context['allow_admin'])
		echo '
				<li' , $current_action=='admin' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=admin"><span>' , $txt[2] , '</span></a></li>';	
	if ($context['allow_edit_profile'])
		echo '
				<li' , $current_action=='profile' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=profile"><span>' , $txt[79] , '</span></a></li>';

	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo '
				<li' , $current_action=='pm' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=pm"><span>' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</span></a></li>';

	// add the buttons
	for($what=0;$what<6;$what++)
	{
		// extra button 1
		if(isset($context['sitemenu'][$what]))
		{
				if($context['sitemenu'][$what][2]=='page')
					echo '
				<li><a href="',$scripturl,'?action='.$context['sitemenu'][$what][1].'">'.$context['sitemenu'][$what][0].'</a></li>';
				elseif($context['sitemenu'][$what][2]=='link')
					echo '
				<li><a href="'.$context['sitemenu'][$what][1].'">'.$context['sitemenu'][$what][0].'</a></li>';
		}
	}	

	if ($context['user']['is_logged'])
		echo '
				<li><a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '"><span>' , $txt[108] , '</span></a></li>';
	if ($context['user']['is_guest'])
	{
		echo '
				<li' , $current_action=='login' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=login"><span>' , $txt[34] , '</span></a></li>';	
		echo '
				<li><a href="', $scripturl, '?action=register"><span>' , $txt[97] , '</span></a></li>';	
	}
	echo '
				<li class="last">&nbsp;</li>
			</ul></div>
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


function tpcode()
{
	global $settings, $context, $options, $txt;

	echo '
	<div id="innerframe2">		
		<table width="100%" cellspacing="0" cellpadding="2">
			<tr>';


// TinyPortal integrated bars
	if(!empty($settings['use_tp']) && $context['TPortal']['leftbar'])
	{
		echo '<td  class="padtop" width="' ,$context['TPortal']['leftbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex; " valign="top">
					<div id="leftbarHeader" style="', empty($options['collapse_leftbar']) ? '' : 'display: none;', 'padding-top: 15px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
        TPortal_sidebar('left');
        echo '	</div>
				</td>';
	}


	echo '
				<td width="100%" align="left" valign="top" style="padding-top: 15px; padding-bottom: 10px;" >';
        if(!empty($settings['use_tp']) && $context['TPortal']['centerbar'])
                     echo '<div>' , TPortal_sidebar('center') , '</div>';

}

function tpcode2()
{
	global $settings,$context, $txt, $options;

	echo '
				</td>';
	// TinyPortal integrated bars
	if(!empty($settings['use_tp']) && $context['TPortal']['rightbar'])
	{
		echo '<td  class="padtop" width="' ,$context['TPortal']['rightbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex;" valign="top">
                 <div id="rightbarHeader" style="', empty($options['collapse_rightbar']) ? '' : 'display: none;', 'padding-top: 15px; width: ' ,$context['TPortal']['rightbar_width'], 'px;">';
        TPortal_sidebar('right');
        echo '</div></td>';
	}

	echo '	
			</tr>
		</table>
	</div>';
}
?>