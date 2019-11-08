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
	$settings['use_tabs'] = false;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;

	// extra strings
	loadLanguage('ThemeStrings');

    if(!$context['user']['is_guest'] && isset($_POST['options']['mypic']))
	{
   		include_once($GLOBALS['sourcedir'] . '/Profile.php');
   		makeThemeChanges($context['user']['id'], $settings['theme_id']);
   		$options['mypic'] = $_POST['options']['mypic'];
	}
	elseif ($context['user']['is_guest'])
	{
   		if (isset($_POST['options']['mypic']))
   		{
      		$_SESSION['mypic'] = $_POST['options']['mypic'];
      		$options['mypic'] = $_SESSION['mypic'];
   		}
   		elseif (isset($_SESSION['mypic']))
      		$options['mypic'] = $_SESSION['mypic'];
	}

}

// The main sub template above the content.
function template_main_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;


	// TP test
	if(!isset($context['TPortal']['showtop'])){
		$context['TPortal']['showtop']=true;
		$context['TPortal']['fixed_width']='100%';
	}
	if(!isset($settings['TPortal_front_type'])){
		$settings['TPortal_front_type']='boardindex';
		$context['TPortal']['fixed_width']='100%';
	}

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

	echo '
				document.getElementById("upshrink_ic").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

				document.getElementById("upshrinkHeaderIC").style.display = mode ? "none" : "";

				current_header_ic = mode;
			}
		// ]]></script>
</head>
<body>';

	userbox();
	searchbox();
	newsbox();

	echo '
<div id="maintable">
	<div id="picture_choose">
	<form class="middletext" name="FormName" action="',$scripturl,'" method="post" style="margin: 0; padding: 0;">
<select size="1" name="options[mypic]" onChange="submit()">
  <option value="" ', (!empty($options['mypic']) && $options['mypic']=='') ? 'selected' : '' ,'>Enterprise</option>
  <option value="1"  ', (!empty($options['mypic']) && $options['mypic']=='1') ? 'selected' : '' ,'>Babylon 5</option>
  <option value="2"  ', (!empty($options['mypic']) && $options['mypic']=='2') ? 'selected' : '' ,'>Sunflares</option>
  <option value="3"  ', (!empty($options['mypic']) && $options['mypic']=='3') ? 'selected' : '' ,'>Spaceship</option>
</select>
			</form></div>
	<div id="logo"><span class="logotext"><a href="',$scripturl,'">' , $context['forum_name'], '</a></span>
	<br /><span class="smalltext" style="color: #a0a0a0">' ,$context['current_time'],'</span></div>
	<div id="mainmenu">' , template_menu() , '</div>
	<div id="linktree">' , theme_linktree2() , '</div>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td><img src="',$settings['images_url'],'/img/topl.jpg" alt="" /></td>
			<td width="99%" style="background: url(',$settings['images_url'],'/img/topm.jpg) repeat-x;">&nbsp;</td>
			<td><img src="',$settings['images_url'],'/img/topr', isset($options['mypic']) ? $options['mypic'] : '' , '.jpg" alt="" /></td>
		<tr>
	</table>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td style="background: url(',$settings['images_url'],'/img/left.jpg) repeat-y;"><img src="',$settings['images_url'],'/img/left.jpg" alt="" /></td>
			<td width="100%">';

	// The main content should go here.  A table is used because IE 6 just can't handle a div.
	echo '
	<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';

// TinyPortal integrated bars
          if($context['TPortal']['leftbar'])
          {
              echo '<td width="' ,$context['TPortal']['leftbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex;" valign="top">
                 <div id="leftbarHeader"', empty($options['collapse_leftbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
                 TPortal_sidebar('left');
              echo '</div></td>';

          }

        echo '<td width="100%" align="left" valign="top" style="padding-top: 10px; padding-bottom: 10px;">';
        if($context['TPortal']['centerbar'])
                     echo '<div>' , TPortal_sidebar('center') , '</div>';

}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt , $user_info;


   echo '</td>';

         // TinyPortal integrated bars
          if($context['TPortal']['rightbar']){
              echo '<td style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px; padding-left: 1ex;" valign="top" align="right">
                 <div id="rightbarHeader"', empty($options['collapse_rightbar']) ? '' : ' style="display: none;"', ' style="width: ' ,$context['TPortal']['rightbar_width'], 'px; text-align: left; padding-top: 5px;">';
              TPortal_rightbar();
              echo '</div></td>';
          }

        echo '</tr></table>';
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

	echo '</td>
			<td style="background: url(',$settings['images_url'],'/img/right.jpg) repeat-y;"><img src="',$settings['images_url'],'/img/right.jpg" alt="" /></td>
		</tr>
		<tr>
			<td><img src="',$settings['images_url'],'/img/botleft.jpg" alt="" /></td>
			<td valign="middle" align="center" class="smalltext" style="background: url(',$settings['images_url'],'/img/bottom.jpg) repeat-x;" width="100%"><img src="',$settings['images_url'],'/img/botleft2.jpg" style="margin: 0;" align="left" alt="" />';

	echo '<br /><br /><br />' , theme_copyright(), '<br />' , tportal_version(), '
					<br /> <b>Enterprise</b> design by <a href="http://www.tinyportal.net">Bloc</a>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '<br />
		<span class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</span>';

	echo '</td>
			<td><img src="',$settings['images_url'],'/img/botright.jpg" alt="" /></td>
		</tr>
	</table></div>';
	echo '
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	return;
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree2()
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
		echo '', $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'], '';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;-&nbsp;';
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
	if (in_array($context['current_action'], array('forum','tpadmin', 'search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'search2')
		$current_action = 'search';
	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	if (isset($_GET['dl']))
		$current_action = 'dlmanager';

	if (isset($_GET['board']) || isset($_GET['topic']))
		$current_action = 'forum';

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
					<td class="maintab_' , $first , '">&nbsp;</td>';

	// Show the [home] button.
	echo ($current_action=='home' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td  class="maintab_' , $current_action == 'home' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '">' , $txt[103] , '</a>
				</td>' , $current_action == 'home' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Show the [forum] button.
	echo ($current_action=='forum' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td  class="maintab_' , $current_action == 'forum' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=forum">Forum</a>
				</td>' , $current_action == 'forum' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Show the [help] button.
	echo ($current_action == 'help' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td  class="maintab_' , $current_action == 'help' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=help">' , $txt[119] , '</a>
				</td>' , $current_action == 'help' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// How about the [search] button?
	if ($context['allow_search'])
		echo ($current_action == 'search' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td class="maintab_' , $current_action == 'search' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=search">' , $txt[182] , '</a>
				</td>' , $current_action == 'search' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo ($current_action == 'admin' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td class="maintab_' , $current_action == 'admin' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=admin">' , $txt[2] , '</a>
				</td>' , $current_action == 'admin' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo ($current_action == 'profile' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td class="maintab_' , $current_action == 'profile' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=profile">' , $txt[79] , '</a>
				</td>' , $current_action == 'profile' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo ($current_action == 'pm' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td  class="maintab_' , $current_action == 'pm' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=pm">' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a>
				</td>' , $current_action == 'pm' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// The [calendar]!
	if ($context['allow_calendar'])
		echo ($current_action == 'calendar' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td class="maintab_' , $current_action == 'calendar' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a>
				</td>' , $current_action == 'calendar' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo ($current_action == 'mlist' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td class="maintab_' , $current_action == 'mlist' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=mlist">' , $txt[331] , '</a>
				</td>' , $current_action == 'mlist' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// If the user is a guest, show [login] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'login' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td class="maintab_' , $current_action == 'login' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=login">' , $txt[34] , '</a>
				</td>' , $current_action == 'login' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// If the user is a guest, also show [register] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'register' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td  class="maintab_' , $current_action == 'register' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=register">' , $txt[97] , '</a>
				</td>' , $current_action == 'register' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// Otherwise, they might want to [logout]...
	if ($context['user']['is_logged'])
		echo ($current_action == 'logout' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td class="maintab_' , $current_action == 'logout' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">' , $txt[108] , '</a>
				</td>' , $current_action == 'logout' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// The end of tab section.
	echo '
				<td class="maintab_' , $last , '">&nbsp;</td>
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
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
}

// userbox
function userbox()
{
          global $context, $settings, $options, $scripturl, $txt, $modSettings;


		echo '<div id="pmbox">';
		if($context['user']['is_logged']){
			echo $txt['hello_member'], ' <b>', $context['user']['name'], '</b>';
			// Only tell them about their messages if they can read their messages!
			if ($context['allow_pm'])
				echo ', ', $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
		echo '.<br />';
		}
		else
			echo $txt['welcome_guest'];

		echo '</div>';

         echo'<div id="userbox"><table width="99%" cellpadding="0" cellspacing="5" border="0"><tr>';
        echo '<td width="100%" valign="top" class="smalltext" style="font-family: verdana, arial, sans-serif;">';

		if($context['user']['is_logged']){

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
                echo ' <span class="blockmain">
                                                        <form style="margin-top: 0" class="blockmain" action="', $scripturl, '?action=login2" accept-charset="', $context['character_set'], '" method="post" ', empty($context['disable_login_hashing']) ? '  onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', ' >
                                                                <input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" />
                                                                <select name="cookielength">
                                                                        <option value="60">', $txt['smf53'], '</option>
                                                                        <option value="1440">', $txt['smf47'], '</option>
                                                                        <option value="10080">', $txt['smf48'], '</option>
                                                                        <option value="302400">', $txt['smf49'], '</option>
                                                                        <option value="-1" selected="selected">', $txt['smf50'], '</option>
                                                                </select>
                                                                <input type="submit" value="', $txt[34], '" /><br />
                                                                ', $txt['smf52'], '
                                                        </form>';
        }

        echo '
                                                </td></tr></table></div>';

}

function newsbox()
{
        global $context, $settings, $options, $scripturl, $txt, $modSettings;

        // Show a random news item? (or you could pick one from news_lines...)
        if (!empty($settings['enable_news']))
                echo '<div id="newsbox" style="padding: 5px;" >', $context['random_news_line'], '</div>
                                        ';
}

function statsbox()
{
       global $context, $settings, $options, $scripturl, $txt, $modSettings;
       echo '
                  <div id="statsbox" style="padding: 5px;" class="smalltext">
                                          <b>', $modSettings['totalMessages'], '</b> ', $txt[95], ' ', $txt['smf88'], ' <b>', $modSettings['totalTopics'], '</b> ', $txt[64], ' ', $txt[525], ' <b>', $modSettings['totalMembers'], '</b> ',  $txt[19], '<br />
                                                        ', $txt[656], ': <b> <a href="', $scripturl, '?action=profile;u=', $modSettings['latestMember'], '">', $modSettings['latestRealName'], '</a></b>
                  </div>';
}
function searchbox()
{
    global $context, $settings, $options, $txt , $scripturl;

   echo '<div id="searchbox"><form action="', $scripturl, '?action=search2"  accept-charset="', $context['character_set'], '" method="post" style="margin: 0; margin-bottom: 5px;">
                      <input type="text" name="search" value="">
                      <input type="submit" name="submit" value="', $txt[182], '"  /><span class="smalltext">
                      <a href="', $scripturl, '?action=search;advanced">', $txt['smf298'], '</a></span>
                     <input type="hidden" name="advanced" value="0" />';

        echo '</form></div>';

}


?>
