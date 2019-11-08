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

	$settings['tp_images_url'] = $settings['images_url'].'/tinyportal';
	// load custom language strings
	loadLanguage('ThemeStrings');

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
	// are we in certain areas?
	if(isset($_GET['topic']) || isset($_GET['board']) || $context['current_action']=='pm')
		$context['template_layers'][] = 'extra';
}
// we need a container
function template_extra_above()
{
	echo '<div id="extra">';
}

function template_extra_below()
{
	echo '</div>';
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

	// decide class
	$class = '';
	
	// set the size intially
	if(!isset($options['myfontsize']))
		$options['myfontsize'] = '90%';
	
	// Work out where we currently are.
	if(function_exists('tportal_version'))
		$current_action = 'home';
	else
		$current_action = 'forum';

	$settings['forumaction']=false;

	if (in_array($context['current_action'], array('tpadmin','admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
	{
		$current_action = 'admin';
		$class = 'admin_class';
	}
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
	{
		$current_action = $context['current_action'];
		$settings['forumaction']=true;
		$class = $current_action . '_class';
	}
	if ($context['current_action'] == 'search2')
	{
		$current_action = 'search';
		$class = 'search_class';
	}
	if ($context['current_action'] == 'themeshop')
	{
		$current_action = 'themeshop';
		$class = 'themeshop_class';
	}
	if (isset($_GET['dl']))
	{
		$current_action = 'downloads';
		$class = 'downloads_class';
	}
	if (isset($_GET['board']) || isset($_GET['topic']))
	{
		$current_action = 'forum';
		$class = 'forum_class';
	}
	if ($context['current_action'] == 'theme')
	{
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';
	}
	if(isset($_GET['page']))
	{
		if(in_array($_GET['page'],array('tinyportal','freethemes','themeclub','tpteam')))
			$current_action = 'about';
		$class = 'page_class';
	}
	if(isset($_GET['board']))
	{
		if($_GET['board']==85)
			$current_action = 'docs';
		$class = 'forum_class';
	}
	if(isset($_GET['topic']))
	{
		$class = 'forum_class';
	}
	if ($context['current_action'] == 'bugtracker')
	{
		$current_action = 'bugtracker';
		$class = 'bugtracker_class';
	}
	if ($context['current_action'] == 'tpadmin')
	{
		$current_action = 'admin';
		$class = 'admin_class';
	}
	
	$context['caction'] = $current_action;

	$myheader = '<span class="myheader ' . $class . '"></span>' . $context['page_title'];
	
	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '><head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title'], '" />', empty($context['robot_no_index']) ? '' : '
	<meta name="robots" content="noindex" />', '
	<meta name="keywords" content="PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum" />
	<script type="text/javascript" src="', $settings['default_theme_url'], '/script.js?fin11"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
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
	if($context['browser']['is_ie6'])
		echo '<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/styleie6.css?fin11" />';

	/* Internet Explorer 4/5 and Opera 6 just don't do font sizes properly. (they are big...)
		Thus, in Internet Explorer 4, 5, and Opera 6 this will show fonts one size smaller than usual.
		Note that this is affected by whether IE 6 is in standards compliance mode.. if not, it will also be big.
		Standards compliance mode happens when you use xhtml... */
	if ($context['browser']['needs_size_fix'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/fonts-compat.css" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help"  />
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
<script type="text/javascript"><!-- // --><![CDATA[
                var myfontsize = ', empty($options['myfontsize']) ? '\'90\'' : '\'' . $options['myfontsize']. '\'' , ';
                function setfontsize(size)
                {';

        // Guests don't have theme options!!
        if ($context['user']['is_guest'])
                echo '
                        document.cookie = "myfontsize=" + size';
        else
                echo '
                        smf_setThemeOption("myfontsize", size , null, "', $context['session_id'], '");';
        echo '
						document.getElementById("mainframe").style.fontSize = size + "%";
						document.getElementById("font80").src = smf_images_url + ( size==\'80\' ? "/font80b.gif" : "/font80.gif");
                        document.getElementById("font90").src = smf_images_url + ( size==\'90\' ? "/font90b.gif" : "/font90.gif");
                        document.getElementById("font120").src = smf_images_url + ( size==\'120\' ? "/font120b.gif" : "/font120.gif");
                        document.getElementById("font140").src = smf_images_url + ( size==\'140\' ? "/font140b.gif" : "/font140.gif");
                        document.getElementById("font160").src = smf_images_url + ( size==\'160\' ? "/font160b.gif" : "/font160.gif");
                        myfontsize = size;
                }
          // ]]></script>
		<script type="text/javascript"><!-- // --><![CDATA[
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
// ]]></script>';


echo '
</head>
<body>
<div id="mainframe">
	<div id="toparea">
		<div id="controls">
			<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" style="padding: 0; text-align: right; margin: 0 0 10px 0;">
				<input type="text" name="search" value="" id="searchbutton" />
				<input type="submit" name="submit" value="', $txt[182], '" style="border: outset 1px #aaa; background: #444; color: #aaa; margin: 10px 0 0 0; width: 100px;"  />
				<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
				<input type="hidden" name="topic" value="', $context['current_topic'], '" />';

		// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
				<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '
			</form>';
	echo '
		</div>
		<h1 id="themelogo"><a href="' , $scripturl , '?action=forum"><img src="' . $settings['images_url'] . '/img3b/toplogo.jpg" alt="' . $context['forum_name'] . '" /></a></h1>
	</div>
	<div id="middleside">
		<div id="leftside">
			<div id="rightside">';

	if($context['user']['is_logged'])
	{
		echo $txt['hello_member_ndt'] , ' <strong>' , $context['user']['name'] , '</strong>';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
			echo '&nbsp;&nbsp;[<a href="', $scripturl, '?action=pm">', $context['user']['messages'], '/<strong>', $context['user']['unread_messages'] , '</strong></a> PM]';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '[<strong>Maintenace</strong>]';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
			[<a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] , ' APPROVE</a> ]';

		echo '
			<a href="', $scripturl, '?action=unread">Unread</a> /
							<a href="', $scripturl, '?action=unreadreplies">Replies</a>';
	
	}
	else
		echo $txt['welcome_guest'];


	echo 	$context['browser']['is_ie6'] ? template_menu_ie6() : template_menu() , '
				<h2>' , $myheader , '</h2>
				' , theme_linktree2();
		tpcode();

}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

		tpcode2();

	echo '	
				<div id="footerarea">
					<div id="copywrite" class="smalltext">', theme_copyright(), ' <br />
						' , function_exists('tportal_version') ? tportal_version() : '' , '
						<strong>BZ14</strong> design by <a href="http.//www.blocweb.net" >BlocWeb</a> |
						<a href="http://validator.w3.org/check/referer" >XHTML</a> |
						<a href="http://jigsaw.w3.org/css-validator/check/referer" >CSS</a>
					</div>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '
					<div class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';

	echo '
				</div>';
//echo '<pre style="text-align: left;">' , print_r($context['TPortal']) , '</pre>';
	// The following will be used to let the user know that some AJAX process is running
	echo '
			</div>
		</div>
	</div>
</div>
<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

function theme_linktree() { return; }

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree2()
{
	global $context, $settings, $options;

	// dont' bother id linktree is just one item :P
	if(sizeof($context['linktree'])>1)
	{
		echo '<ul id="linktree">';

		// Each tree item has a URL and name. Some may have extra_before and extra_after.
		foreach ($context['linktree'] as $link_num => $tree)
		{
			echo '<li>';
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
				echo '&nbsp;&#0187;&nbsp;';
			
			echo '</li>';
		}

		echo '</ul>';
	}
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	$current_action = $context['caction'];


	// Show the start of the tab section.
	echo '
<div class="menuboxframe">
	<ul class="menubox">';

	// Show the [home] button.
	if(function_exists('tportal_version'))
		echo 	'
		<li' , $current_action == 'home' ? ' class="chosen"' : '' , ' id="first"><a id="firstmenu" ' , $current_action == 'home' ? ' class="chosen"' : '' , ' href="', $scripturl, '">' , $txt[103] , '</a></li>';

	// Show the [forum] button.
	echo 	'
		<li' , ($current_action == 'forum' || $settings['forumaction']) ? ' class="chosen"' : '' , '><a' , ($current_action == 'forum' || $settings['forumaction']) ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=forum">' , function_exists('tportal_version') ? $txt['tp-forum'] : $txt[103] ,  '</a>' ;

		echo 	'
			<ul>
				<li><a' , $current_action == 'help' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=help">' , $txt[119] , '</a></li>';

		// How about the [search] button?
		if ($context['allow_search'])
			echo 	'
				<li><a' , $current_action == 'search' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=search">' , $txt[182] , '</a></li>';

		// Is the user allowed to administrate at all? ([admin])
		if ($context['allow_admin'])
			echo 	'
				<li><a' , $current_action == 'admin' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=admin">' , $txt[2] , '</a></li>';

		// Edit Profile... [profile]
		if ($context['allow_edit_profile'])
			echo 	'
				<li><a' , $current_action == 'profile' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=profile">' , $txt[79] , '</a></li>';

		// Go to PM center... [pm]
		if ($context['user']['is_logged'] && $context['allow_pm'])
			echo 	'
				<li><a' , $current_action == 'pm' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=pm">PM ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a></li>';

		// The [calendar]!
		if ($context['allow_calendar'])
			echo 	'
				<li><a' , $current_action == 'calendar' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a></li>';

		// the [member] list button
		if ($context['allow_memberlist'])
			echo 	'
				<li><a' , $current_action == 'mlist' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=mlist">' , $txt[331] , '</a></li>';

		// If the user is a guest, show [login] button.
		if ($context['user']['is_guest'])
			echo 	'
				<li><a' , $current_action == 'login' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=login">' , $txt[34] , '</a></li>';


		// If the user is a guest, also show [register] button.
		if ($context['user']['is_guest'])
			echo 	'
				<li><a' , $current_action == 'register' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=register">' , $txt[97] , '</a></li>';


		// Otherwise, they might want to [logout]...
		if ($context['user']['is_logged'])
			echo 	'
				<li><a' , $current_action == 'logout' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=logout;sesc=',$context['session_id'],'">' , $txt[108] , '</a></li>';

		echo '
			</ul>';

		echo '
		</li>';

	if(sizeof($context['sitemenu'])>0)
	{
		echo '<li><a href="index.php">', $txt['mypages'], '</a><ul>';
		foreach($context['sitemenu'] as $menu => $val)
		{
			if($val[2]=='page')
				echo '
			<li><a href="',$scripturl,'?action='.$val[0].'">'.$val[1].'</a></li>';
			elseif($val[2]=='link')
				echo '
			<li><a href="'.$val[0].'">'.$val[1].'</a></li>';
		}
		echo '</ul></li>';
	}


	for($a=1;$a<6;$a++)
	{
		if(!empty($settings['but'.$a.'_name']))
			echo '
		<li><a href="', $settings['but'.$a.'_link'], '">', $settings['but'.$a.'_name'], '</a></li>';
	}
	echo '
	</ul>
</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu_ie6()
{
	global $context, $settings, $options, $scripturl, $txt;

	$current_action = $context['caction'];

	// for ie6 we need a cheat :P //
	$ie6='<table><tr><td>';
	$ie6b='</td></tr></table></a>';

	// Show the start of the tab section.
	echo '
<ul class="menu5">';

	// Show the [home] button.
	if(function_exists('tportal_version'))
		// Show the [home] button.
		echo 	'
	<li' , $current_action == 'home' ? ' class="chosen"' : '' , ' ><a id="firstmenu" ' , $current_action == 'home' ? ' class="chosen"' : '' , ' href="', $scripturl, '">' , $txt[103] , '</a></li>';

	// Show the [forum] button.
	echo 	'
	<li' , ($current_action == 'forum' || $settings['forumaction']) ? ' class="chosen"' : '' , '><a' , ($current_action == 'forum' || $settings['forumaction']) ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=forum">' , $txt['tp-forum'];

		echo $ie6;
		echo 	'
		<ul>
			<li><a' , $current_action == 'help' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=help">' , $txt[119] , '</a></li>';

		// How about the [search] button?
		if ($context['allow_search'])
			echo 	'
			<li><a' , $current_action == 'search' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=search">' , $txt[182] , '</a></li>';

		// Is the user allowed to administrate at all? ([admin])
		if ($context['allow_admin'])
			echo 	'
			<li><a' , $current_action == 'admin' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=admin">' , $txt[2] , '</a></li>';

		// Edit Profile... [profile]
		if ($context['allow_edit_profile'])
			echo 	'
			<li><a' , $current_action == 'profile' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=profile">' , $txt[79] , '</a></li>';

		// Go to PM center... [pm]
		if ($context['user']['is_logged'] && $context['allow_pm'])
			echo 	'
			<li><a' , $current_action == 'pm' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=pm">PM ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a></li>';

		// The [calendar]!
		if ($context['allow_calendar'])
			echo 	'
			<li><a' , $current_action == 'calendar' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a></li>';

		// the [member] list button
		if ($context['allow_memberlist'])
			echo 	'
			<li><a' , $current_action == 'mlist' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=mlist">' , $txt[331] , '</a></li>';

		// If the user is a guest, show [login] button.
		if ($context['user']['is_guest'])
			echo 	'
			<li><a' , $current_action == 'login' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=login">' , $txt[34] , '</a></li>';


		// If the user is a guest, also show [register] button.
		if ($context['user']['is_guest'])
			echo 	'
			<li><a' , $current_action == 'register' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=register">' , $txt[97] , '</a></li>';


		// Otherwise, they might want to [logout]...
		if ($context['user']['is_logged'])
			echo 	'
			<li><a' , $current_action == 'logout' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=logout;sesc=',$context['session_id'],'">' , $txt[108] , '</a></li>';

		echo '
		</ul>';
		echo $ie6b;

		echo '
	</li>';
	
	if(sizeof($context['sitemenu'])>0)
	{
		echo '<li><a href="index.php">' , $txt['mypages'] , $ie6 , '<ul>';
		foreach($context['sitemenu'] as $menu => $val)
		{
			if($val[2]=='page')
				echo '
			<li><a href="',$scripturl,'?action='.$val[0].'">'.$val[1].'</a></li>';
			elseif($val[2]=='link')
				echo '
			<li><a href="'.$val[0].'">'.$val[1].'</a></li>';
		}
		echo '</ul>',$ie6b,'</a></li>';
	}


	for($a=1;$a<6;$a++)
	{
		if(!empty($settings['but'.$a.'_name']))
			echo '
		<li><a href="', $settings['but'.$a.'_link'], '">', $settings['but'.$a.'_name'], '</a></li>';
	}

	echo '
</ul>';
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
		<td class="maintab_back">', implode('</td><td class="maintab_back">', $button_strip) , '</td>';
}

// Generate a strip of buttons.
function template_button_strip2($button_strip, $direction = 'top', $force_reset = false, $custom_td = '', $header_href = '#', $header_title = 'Tools menu')
{
	global $settings, $buttons, $context, $txt, $scripturl;
	
	if($context['browser']['is_ie6'])
	{
		template_button_strip($button_strip,$direction, $force_rest,$cusom_td);
		return;
	}
	
	echo '<ul class="menubox2">
				<li><a style="width: 10em;" href="' . $header_href . '">' .$header_title . '</a>
					<ul>';
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
		echo '		<li></li>';
	else
		echo '
						<li>', implode('</li><li>', $button_strip) , '</li>';
	echo '
					</ul>
				</li>
			</ul>';
	
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