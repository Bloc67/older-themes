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
	$settings['theme_version'] = '1.1.4';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = true;
	
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

	// not assigned? assign a value
	if(empty($options['myfontsize']))
		$options['myfontsize'] = '90';
	
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
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/print.css?fin11" media="print" />
	';

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

	// and the fontsise
	if ($context['user']['is_guest'])
		$options['myfontsize'] = !empty($_COOKIE['myfontsize']) ? $_COOKIE['myfontsize'] : '90';

	// set fontsize from user choice
	echo '
	<style type="text/css">
		#mainframe
		{
			width: ' , !empty($settings['forumwidth']) ? $settings['forumwidth'] : '950px', ';
			font-size: ' , !empty($options['myfontsize']) ? $options['myfontsize'] : '90', '%;
		}
		#topframe, #footer
		{
			background: url(' , $settings['images_url'] , '/img/' , !empty($settings['invazionlogo']) ? $settings['invazionlogo'] : 'logo1.jpg' , ') repeat-x;
		}
	</style>';

// *******************
// if we are in boardindex...
	if(empty($options['showcat']) && !empty($context['categories']))
	{
		// fetch the first category
			$first=true;
			foreach ($context['categories'] as $category)
			{
				if($first)
					$options['showcat']=$category['id'];
				$first=false;
			}
	}

// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'], '
        <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
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
	  <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                var current_cat = ', empty($options['showcat']) ? '1' : $options['showcat'] , ';

                function showcat(mode)
                {';

        // Guests don't have theme options!!
        if ($context['user']['is_guest'])
                echo '
                        document.cookie = "showcat=" + mode;';
        else
                echo '
                        smf_setThemeOption("showcat", mode, null, "', $context['session_id'], '");';

        echo '
                        document.getElementById("cat" + mode).style.display = "";
						document.getElementById("cattab" + mode).className =  \'chosen\';

					   if (current_cat != mode)
						{
							document.getElementById("cat" + current_cat).style.display = "none";
							document.getElementById("cattab" + current_cat).className = \'\';
						}
						current_cat = mode;
                }
                
        // ]]></script>
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
			document.getElementById("upshrinkHeader").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");
			document.getElementById("myuser").style.display = mode ? "none" : "";
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
	// check for some mods if they are indeed loaded :P
	checkmods();
	
	echo '
<div id="mainframe">
	<div id="topframe">
	<h1><a href="' , $scripturl , '">' , $context['forum_name'] , '</a></h1>
		<div id="fontsize-controls">
			<a href="javascript:void(0);" onclick="setfontsize(\'80\'); return false;"><img id="font80" src="', $settings['images_url'], '/', $options['myfontsize']=='80' ? 'font80b.gif' : 'font80.gif', '" alt=""  /></a>
			<a href="javascript:void(0);" onclick="setfontsize(\'90\'); return false;"><img id="font90" src="', $settings['images_url'], '/', $options['myfontsize']=='90' ? 'font90b.gif' : 'font90.gif', '" alt=""  /></a>
			<a href="javascript:void(0);" onclick="setfontsize(\'120\'); return false;"><img id="font120" src="', $settings['images_url'], '/', $options['myfontsize']=='120' ? 'font120b.gif' : 'font120.gif', '" alt=""  /></a>
			<a href="javascript:void(0);" onclick="setfontsize(\'140\'); return false;"><img id="font140" src="', $settings['images_url'], '/', $options['myfontsize']=='140' ? 'font140b.gif' : 'font140.gif', '" alt=""  /></a>
			<a href="javascript:void(0);" onclick="setfontsize(\'160\'); return false;"><img id="font160" src="', $settings['images_url'], '/', $options['myfontsize']=='160' ? 'font160b.gif' : 'font160.gif', '" alt=""  /></a>&nbsp;&nbsp;';
	if(!empty($settings['use_tp']))
	{
	// TinyPortal
		if($context['TPortal']['leftbar'] || (isset($context['TPortal']['leftpanel']) && $context['TPortal']['leftpanel']==1))
			echo '
				<a href="javascript:void(0);" onclick="shrinkHeaderLeftbar(!current_leftbar); return false;"><img id="upshrinkLeftbar" src="', $settings['images_url'], '/', empty($options['collapse_leftbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '"  border="0" /></a><img id="upshrinkTempLeftbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
		if($context['TPortal']['rightbar'] || (isset($context['TPortal']['rightpanel']) && $context['TPortal']['rightpanel'])==1)
			echo '
				<a href="javascript:void(0);" onclick="shrinkHeaderRightbar(!current_rightbar); return false;"><img id="upshrinkRightbar" src="', $settings['images_url'], '/', empty($options['collapse_rightbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" border="0" /></a><img id="upshrinkTempRightbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
	}
	
	
	echo '
			<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;padding: 0;">
				<input type="text" name="search"  value=""  />
				<input type="submit" name="submit" value="', $txt[182], '"  />
				<input type="hidden" name="advanced" value="0" />
				<a href="', $scripturl, '?action=search;advanced"><img src="'.$settings['images_url'].'/filter.gif" align="middle" alt="" /></a>';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
				<input type="hidden" name="topic" value="', $context['current_topic'], '" />';

	// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
				<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '
			</form>
		</div>
	<div id="greeting">';
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

	echo '
	</div> 
	</div>
	' , template_menu() , ' 
	' , theme_linktree2() , '
	<div id="content">';
	
	ads_topcode();
	ads_towercode();
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

 	ads_towercode2();
	
	echo '
	</div>
	<div id="footer">';
	
	ads_botcode();

	echo '
		<div id="copywrite" class="smalltext">', theme_copyright(), ' |
			' , function_exists("tportal_version") ? tportal_version().' | ' : '' , '
			<strong>Invazion</strong> design by <a href="http://www.tinyportal.net" target="_blank">Bloc</a> |
			<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
			<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
			<div class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';

	if (!empty($settings['use_gb']) && isset($context['ob_googlebot_stats']))
		echo '
			<div class="smalltext">', $txt['ob_googlebot_stats_lastvisit'], timeformat($context['ob_googlebot_stats']['Googlebot']['lastvisit']), '<div>';

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
	// The following will be used to let the user know that some AJAX process is running
	echo '
		</div>
	</div>
</div>
<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

function theme_linktree()
{
	return;
}
// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree2()
{
	global $context, $settings, $options;

	echo '<div id="linktree">';
	topmenu();

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
	if (in_array($context['current_action'], array('admin', 'managegames', 'arcadesettings', 'arcadecategory', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('gallery','search', 'arcade', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'globalAnnouncementsAdmin')
		$current_action = 'admin';

	if ($context['current_action'] == 'search2')
		$current_action = 'search';
	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	if (isset($_GET['dl']))
		$current_action = 'dlmanager';

	if ((isset($_GET['board']) || isset($_GET['topic'])) && empty($settings['use_tp']))
		$current_action = 'home';
	elseif ((isset($_GET['board']) || isset($_GET['topic'])) && !empty($settings['use_tp']))
		$current_action = 'forum';

	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	// Begin SMFShop code
	if ($context['current_action'] == 'shop')
		$current_action = 'shop';
	if (in_array($context['current_action'], array('shop_general', 'shop_items_add', 'shop_items_edit', 'shop_cat', 'shop_inventory', 'shop_restock', 'shop_usergroup')))
		$current_action = 'admin';
	// End SMFShop code


	// Show the start of the tab section.
	echo '
	<div id="menudiv">
		<div id="menuinner">
			<ul id="menubox">
				<li' , $current_action=='home' ? ' class="chosen"' : '' , '><a href="', $scripturl, '"><span>' , $txt[103] , '</span></a></li>';
	// TP
	if (!empty($settings['use_tp']))
		echo '
				<li' , $current_action=='forum' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=forum"><span>' , $txt['tp-forum'] , '</span></a></li>';

	echo '
				<li' , $current_action=='help' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=help"><span>' , $txt[119] , '</span></a></li>';
	if ($context['allow_search'])
		echo '
				<li' , $current_action=='search' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=search"><span>Search</span></a></li>';
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

	// SMF Arcade
	if (!empty($settings['use_arcade']))
		echo '
				<li' , $current_action=='arcade' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=arcade"><span>' , $txt['arcade'] , '</span></a></li>';
	// SMF Gallery
	if (!empty($settings['use_smfgallery']))
		echo '
				<li' , $current_action=='gallery' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=gallery"><span>' , $txt['smfgallery_menu'] , '</span></a></li>';
	// SMFshop 
	if (!empty($settings['use_shop']))
		echo '
				<li' , $current_action=='shop' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=shop"><span>Shop</span></a></li>';

	if ($context['user']['is_logged'])
		echo '
				<li class="last"><a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '"><span>' , $txt[108] , '</span></a></li>';
	if ($context['user']['is_guest'])
	{
		echo '
				<li' , $current_action=='login' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=login"><span>' , $txt[34] , '</span></a></li>';	
		echo '
				<li' , $current_action=='register' ? ' class="chosen"' : '' , '><a href="', $scripturl, '?action=register"><span>' , $txt[97] , '</span></a></li>';	
	}
	// extra button 1
	if(!empty($settings['but1_name']))
		echo '
				<li><a href="', $settings['but1_link'] , '"><span>' , $settings['but1_name'] , '</span></a></li>';	
	// extra button 2
	if(!empty($settings['but2_name']))
		echo '
				<li><a href="', $settings['but2_link'] , '"><span>' , $settings['but2_name'] , '</span></a></li>';	
	// extra button 3
	if(!empty($settings['but3_name']))
		echo '
				<li><a href="', $settings['but3_link'] , '"><span>' , $settings['but3_name'] , '</span></a></li>';	
	// extra button 4
	if(!empty($settings['but4_name']))
		echo '
				<li><a href="', $settings['but4_link'] , '"><span>' , $settings['but4_name'] , '</span></a></li>';	
	// extra button 5
	if(!empty($settings['but5_name']))
		echo '
				<li><a href="', $settings['but5_link'] , '"><span>' , $settings['but5_name'] , '</span></a></li>';	
	
	echo '
			</ul>
		</div>
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
	global $options, $context, $txt, $scripturl, $settings;

	if(sizeof($context['sitemenu'])>0)
	{
		echo '
	<div id="topmenu">';
		$current_action='forum';
		if (!empty($settings['use_tp']) && (isset($_GET['board']) || isset($_GET['topic'])))
			$current_action = 'forum';
		
		echo '
		<ul>';
		foreach($context['sitemenu'] as $menu => $val)
		{
			if($val[2]=='page')
				echo '
			<li><a href="',$scripturl,'?action='.$val[0].'">'.$val[1].'</a></li>';
			elseif($val[2]=='link')
				echo '
			<li><a href="'.$val[0].'">'.$val[1].'</a></li>';
		}

			echo '
			</ul>
		</div>';
	}
}

function template_content_above()
{
	global $context, $settings, $options, $scripturl, $txt;

	if(isset($_GET['action']))
		echo '
	<div id="content2">
		<div id="content2-l">
			<div id="content2-r">
				<div class="mpad">';
	else
		echo '
	<div class="content3">';

}
function template_content_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	if(isset($_GET['action']))
		echo '
				</div>
			</div>
		</div>
	</div>';
	else
		echo '
	</div>';
}
function checkmods()
{
	global $context, $settings, $options, $scripturl, $txt;

	if($context['user']['is_admin'])
	{
		$error='';
		// TP 
		if(!empty($settings['use_tp']) && !isset($context['TPortal']['showtop']))
			$error .= '<div class="errorbar">You have turned on theme support for <b>TinyPortal</b>, but the mod itself is NOT installed!</div>';
		// Shoutbox
		if(!empty($settings['use_shoutbox']) && !function_exists('smfshout'))
			$error .= '<div class="errorbar">You have turned on theme support for <b>Ultimate Shoutbox</b>, but the mod itself is NOT installed!</div>';
		// render it
		if(!empty($error))
			echo '<div id="errorpanel">'.$error.'</div>';
	}
}

function ads_topcode()
{
	global $settings;

	//Display ads on the top of the page
	if (!empty($settings['use_ads']) && function_exists("show_topofpageAds"))
	{
		$ads = show_topofpageAds();	
		if(!empty($ads))
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);	
			unset($ads);
	}
}
function ads_botcode()
{
	global $settings;

	//Display ads on the bottom of the page
	if (!empty($settings['use_ads']) && function_exists("show_bottomofpageAds"))
	{
		$ads = show_bottomofpageAds();	
		if(!empty($ads))
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);	
			unset($ads);
	}
}

function ads_towercode()
{
	global $settings, $context, $options, $txt;

	echo '
	<div id="innerframe2">		
		<table width="100%" cellspacing="0" cellpadding="2">
			<tr>';

	if(!empty($settings['use_ads']) && function_exists("show_towerleftAds"))
	{
		$ads = show_towerleftAds();	
		if(!empty($ads))
		{
			echo '		
				<td valign="top" class="padtop">';
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);
			
			echo '		
				</td>';
		}
		unset($ads);
	}

// TinyPortal integrated bars
	if(!empty($settings['use_tp']) && $context['TPortal']['leftbar'])
	{
		echo '<td  class="padtop" width="' ,$context['TPortal']['leftbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex; " valign="top">
					<div id="leftbarHeader"', empty($options['collapse_leftbar']) ? '' : ' style="display: none;"', ' style="padding-top: 15px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
        TPortal_sidebar('left');
        echo '	</div>
				</td>';
	}


	echo '	<td class="padtop" valign="top" width="100%" id="bodyarea">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>';

	// shoutbox mod
	if(!empty($settings['use_shoutbox']) && function_exists("smfshout"))
	{
		echo '			<td width="20%" valign="top"style="padding: 10px 3px 10px 0; ">
													<div style="padding: 3px;" class="titlebg">Shoutbox</div><div id="smfshout" class="windowbg2">' , smfshout() , '</div>
												</td>';
		$sidebar=true;
	}
	else
		$sidebar=false;

	echo '
							<td width="', $sidebar ? '80%' : '100%' , '" align="left" valign="top" style="padding-top: 15px; padding-bottom: 10px;" >';
        if(!empty($settings['use_tp']) && $context['TPortal']['centerbar'])
                     echo '<div>' , TPortal_sidebar('center') , '</div>';

}
function ads_towercode2()
{
	global $settings,$context, $txt, $options;

	echo '
							</td>
						</tr>
					</table>
				</td>';
	// TinyPortal integrated bars
	if(!empty($settings['use_tp']) && $context['TPortal']['rightbar'])
	{
		echo '<td  class="padtop" width="' ,$context['TPortal']['rightbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex;" valign="top">
                 <div id="rightbarHeader"', empty($options['collapse_rightbar']) ? '' : ' style="display: none;"', ' style="padding-top: 15px; width: ' ,$context['TPortal']['rightbar_width'], 'px;">';
        TPortal_sidebar('right');
        echo '</div></td>';
	}

	if(!empty($settings['use_ads']) && function_exists("show_towerrightAds"))
	{
		$ads = show_towerrightAds();	
		if(!empty($ads))
		{
			echo '				
					<td valign="top">';
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);
			echo '</td>';
		}
		unset($ads);
	}
	echo '	
			</tr>
		</table>
	</div>';
}
function user_info()
{
	global $settings,$context, $txt, $options;

}

?>