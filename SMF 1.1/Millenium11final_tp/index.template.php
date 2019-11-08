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
    $wback='home';
	$smartword= 'home - ground zero station';
	if(isset($_GET['action'])){
		if($_GET['action']=='forum'){
			$wback='forum';
			$smartword= 'forum - get all the action';
			}
		elseif($_GET['action']=='admin' || $_GET['action']=='tpadmin' ){
			$wback='admin';
			$smartword= 'admin - HQ central';
			}
		elseif($_GET['action']=='help' ){
			$wback='help';
			$smartword= 'help - enter the help pages';
			}
		elseif($_GET['action']=='calendar' ){
			$wback='calendar';
			$smartword= 'calendar - what happens today?';
			}
		elseif($_GET['action']=='search' ){
			$wback='search';
			$smartword= 'search - whats been said before';
			}
		elseif($_GET['action']=='mlist' ){
			$wback='members';
			$smartword= 'members - join the forces';
			}
		elseif($_GET['action']=='profile' || $_GET['action']=='pm'){
			$wback=$_GET['action'];
			if($wback=='profile')
			 $smartword= 'profile - upfront and personal';
			if($wback=='pm')
			 $smartword= 'pm - any message for me?';
			}
		elseif($_GET['action']=='recent' || $_GET['action']=='unread' || $_GET['action']=='unreadreplies'){
			$wback='recent';
			$smartword= 'recent - they\'re waiting in line';
			}
		elseif($_GET['action']=='login' || $_GET['action']=='register' ){
			$wback=$_GET['action'];
			if($wback=='login')
			 $smartword= 'login - get connected';
			if($wback=='register')
			 $smartword= 'register - join the community';
			}
		elseif($_GET['action']=='tpmod' && isset($_GET['sa']) && $_GET['sa']=='shoutbox'){
			$wback='shoutbox';
			$smartword= 'shoutbox - where shouts roam';
			}
		elseif($_GET['action']=='tpmod' && isset($_GET['dl'])){
			$wback='downloads';
			$smartword= 'downloads - check out the files';
			}
		else{
			$wback='home';
			$smartword= 'home - ground zero station';
			}
	}
		if(isset($_GET['board']) || isset($_GET['topic'])){
			$wback='forum';
			$smartword= 'forum - get all the action';
		}
		if(!isset($_GET['action']) && (isset($_GET['page']) || isset($_GET['cat']))){
			$wback='article';
			$smartword= 'article - for your reading pleasure';
		}
	// the styles for divs here..so we can manupulate leftbar
	echo '
	<style type="text/css"><!--
		#rightcorner
		{
			background: url(',$settings['images_url'],'/img/topr_', $wback ,'.jpg) no-repeat top right;
		}
		#maincontent{
			padding: 0px ' ,$context['TPortal']['rightbar'] ? '12' : '0' , 'px 0 ' , $context['TPortal']['leftbar'] ? '12' : '0' , 'px;
		}
	 --></style>';

	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'], '
        <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                var current_leftbar = ', empty($options['collapse_leftbar']) ? 'false' : 'true', ';

                function shrinkHeaderLeftbar(mode)
                {';

        // Guests don't have theme options!!
        if ($context['user']['is_guest'])
                echo '
                        document.cookie = "upshrink=" + (mode ? 1 : 0);';
        else
                echo '
                        smf_setThemeOption("collapse_leftbar", mode ? 1 : 0, null, "', $context['session_id'], '");';
        echo '
                        document.getElementById("upshrinkLeftbar").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

                        document.getElementById("leftbarHeader").style.display = mode ? "none" : "";

                        current_leftbar = mode;
                }
          // ]]></script>
       <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                var current_rightbar = ', empty($options['collapse_rightbar']) ? 'false' : 'true', ';

                function shrinkHeaderRightbar(mode)
                {';

        // Guests don't have theme options!!
        if ($context['user']['is_guest'])
                echo '
                        document.cookie = "upshrink=" + (mode ? 1 : 0);';
        else
                echo '
                        smf_setThemeOption("collapse_rightbar", mode ? 1 : 0, null, "', $context['session_id'], '");';

        echo '
                        document.getElementById("upshrinkRightbar").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

                        document.getElementById("rightbarHeader").style.display = mode ? "none" : "";

                        current_rightbar = mode;
                }
        // ]]></script>

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

// Millenium start
	echo '
	<div id="slogan">"',$txt['mill-bblocks'],'"</div>
	<div id="greeting">' , $context['user']['is_logged'] ? $txt['hello_member_ndt'].' '.$context['user']['name'] : $txt['welcome_guest'] , '</div>
	<div id="mainmenu">',template_menu(),'</div>
	<div id="navbar">',theme_linktree2(),'</div>
	<div id="sizechanger"></div>
	<div id="smartword">'.$smartword.'</div>
	<div id="top"></div>
	<div id="leftcorner"></div>
	<div id="rightcorner"></div>';

	if($context['TPortal']['leftbar'])
		echo '<div id="leftshrink"><a href="javascript:void(0);" onclick="shrinkHeaderLeftbar(!current_leftbar); return false;"><img id="upshrinkLeftbar" src="', $settings['images_url'], '/', empty($options['collapse_leftbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 2px 0;" border="0" /></a><img id="upshrinkTempLeftbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" /></div>';
	if($context['TPortal']['rightbar'])
    	echo '<div id="rightshrink"><a href="javascript:void(0);" onclick="shrinkHeaderRightbar(!current_rightbar); return false;"><img id="upshrinkRightbar" src="', $settings['images_url'], '/', empty($options['collapse_rightbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 2px 0;" border="0" /></a><img id="upshrinkTempRightbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" /></div>';

	// The main content should go here.  A table is used because IE 6 just can't handle a div.
	echo '
   <div id="container">
   	<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';

// TinyPortal integrated bars
          if($context['TPortal']['leftbar'])
          {
              echo '<td width="' ,$context['TPortal']['leftbar_width'], '" id="leftbar" valign="top">
                 <div id="leftbarHeader"', empty($options['collapse_leftbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
                 TPortal_sidebar('left');
              echo '</div></td>';

          }

        echo '<td id="maincontent" valign="top" style="width: 100%">';
        if($context['TPortal']['centerbar'])
                     echo '<div>' , TPortal_sidebar('center') , '</div>';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt, $user_info;

   echo '</td>';

         // TinyPortal integrated bars
          if($context['TPortal']['rightbar']){
              echo '<td id="rightbar" valign="top" width="' ,$context['TPortal']['rightbar_width'], '">
                 <div id="rightbarHeader"', empty($options['collapse_rightbar']) ? '' : ' style="display: none;"', ' style="width: ' ,$context['TPortal']['rightbar_width'], 'px; text-align: left; padding-top: 5px;">';
              TPortal_rightbar();
              echo '</div></td>';
          }

        echo '</tr></table></div>

	<div id="footer">
          ', theme_copyright(), '
          |	<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
		<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a>
          <br />', tportal_version() , ' |
          <b>Millenium</b> design by Bloc';

		// Show the load time?
	if ($context['show_load_time'])
		echo ' | '. $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'];

	echo '
	</div>';

     echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree2()
{
	global $context, $settings, $options;

	echo '<div>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo ($link_num != count($context['linktree']) - 1) ? '' : '<b>' ,' ', $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'], ' ', ($link_num != count($context['linktree']) - 1) ? '' : '</b>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;/&nbsp;';
	}

	echo '</div>';
}
// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
}
// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = 'home';
	if (in_array($context['current_action'], array('admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'search2')
		$current_action = 'search';

	if (isset($_GET['dl']))
		$current_action = 'dlmanager';

	if (isset($_GET['board']) || isset($_GET['topic']))
		$current_action = 'forum';

	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

         $ca=$current_action;
	$tab1 = '<span class="tabs';

	// Show the [home] and [help] buttons.
	echo $tab1, $ca== 'home' ? '1' : '2' , '"><a href="', $scripturl, '">'.$txt[103]. '</a></span>&nbsp;|&nbsp;';

	if($settings['TPortal_front_type']!='boardindex')
		echo $tab1, $ca== 'forum' ? '1' : '2' , '"><a href="', $scripturl, '?action=forum">Forum</a></span>&nbsp;|&nbsp;';

	echo $tab1, $ca== 'help' ? '1' : '2' , '"><a href="', $scripturl, '?action=help">'.$txt[119]. '</a></span>&nbsp;|&nbsp;';

	// How about the [search] button?
	if ($context['allow_search'])
		echo $tab1, $ca== 'search' ? '1' : '2' , '"><a href="', $scripturl, '?action=search">'.$txt[182]. '</a></span>&nbsp;|&nbsp;';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo $tab1, $ca== 'admin' ? '1' : '2' , '"><a href="', $scripturl, '?action=admin">'.$txt[2]. '</a></span>&nbsp;|&nbsp;';


	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo $tab1, $ca== 'profile' ? '1' : '2' , '"><a href="', $scripturl, '?action=profile">'.$txt[467]. '</a></span>&nbsp;|&nbsp;';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo $tab1, $ca== 'pm' ? '1' : '2' , '"><a href="', $scripturl, '?action=pm">'.$txt['pm_short']. '', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a></span>&nbsp;|&nbsp;';


	// The [calendar]!
	if ($context['allow_calendar'])
		echo $tab1, $ca== 'calendar' ? '1' : '2' , '"><a href="', $scripturl, '?action=calendar">'.$txt['calendar24']. '</a></span>&nbsp;|&nbsp;';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo $tab1, $ca== 'mlist' ? '1' : '2' , '"><a href="', $scripturl, '?action=mlist">'.$txt[331]. '</a></span>&nbsp;|&nbsp;';

	// If the user is a guest, show [login] and [register] buttons.
	if ($context['user']['is_guest'])
	{
		echo $tab1, $ca== 'login' ? '1' : '2' , '"><a href="', $scripturl, '?action=login">'.$txt[34]. '</a></span>&nbsp;|&nbsp;';
		echo $tab1, $ca== 'register' ? '1' : '2' , '"><a href="', $scripturl, '?action=register">'.$txt[97]. '</a></span>';
	}
	// Otherwise, they might want to [logout]...
	else
		echo $tab1, $ca== 'logout' ? '1' : '2' , '"><a href="', $scripturl, '?action=logout;sesc='.$context['session_id'].'">'.$txt[108]. '</a></span>';


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

	echo'
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
}
// Generate a strip of buttons.
function template_button_strip_mill($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
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
			$buttons[$key] = '<a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '>'. ($custom_td!='' ? '<'.$custom_td.'>' : '') . $txt[$value['text']] . ($custom_td!='' ? '</'.$custom_td.'>' : '') .'</a>';

		$button_strip[$key] = $buttons[$key];
	}

	if (empty($button_strip))
		return '&nbsp;';

	echo implode(' &nbsp;|&nbsp; ', $button_strip);
}


?>
