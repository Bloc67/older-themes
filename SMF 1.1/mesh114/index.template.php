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

// the user values
	if(!$context['user']['is_guest'] && isset($_POST['options']['mywidth']))
	{
   		include_once($GLOBALS['sourcedir'] . '/Profile.php');
   		makeThemeChanges($context['user']['id'], $settings['theme_id']);
		$options['mywidth'] = $_POST['options']['mywidth'];
	}
	elseif ($context['user']['is_guest'])
	{
   		if (isset($_POST['options']['mywidth']))
   		{
      		$_SESSION['mywidth'] = $_POST['options']['mywidth'];
      		$options['mywidth'] = $_SESSION['mywidth'];
   		}
   		elseif (isset($_SESSION['mywidth']))
      		$options['mycolor'] = $_SESSION['mycolor'];
	}
// end

	$settings['myframe1'] = '
	<div style="background: url('.$settings['images_url'].'/box_midmid.jpg) bottom left;">
	<div style="background: url('.$settings['images_url'].'/box_midbot.gif) repeat-x bottom left;">
	<div style="background: url('.$settings['images_url'].'/box_midtop.jpg) repeat-x top left;">
	<div style="background: url('.$settings['images_url'].'/box_leftmid.jpg) repeat-y bottom left;">
	<div style="background: url('.$settings['images_url'].'/box_rightmid.jpg) repeat-y bottom right;">

	<div style="background: url('.$settings['images_url'].'/box_leftbot.jpg) no-repeat bottom left;">
	<div style="background: url('.$settings['images_url'].'/box_rightbot.jpg) no-repeat bottom right;">

	<div style="background: url('.$settings['images_url'].'/box_lefttop.jpg) no-repeat top left;">
	<div style="padding: 20px; background: url('.$settings['images_url'].'/box_righttop.jpg) no-repeat top right;">';

	$settings['myframe2'] = '</div></div></div></div></div></div></div></div></div>';
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

	// any admin settings for width?
	if(!empty($settings['disallow_width'])){
		$show_widths=false;
		if(!empty($settings['site_width']))
			$options['mywidth']=$settings['site_width'];
		else
			$options['mywidth']='90%';
	}
	else{
		$show_widths=true;
	}

	$context['show_widths']=$show_widths;

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
	<link rel="alternate stylesheet" type="text/css" media="screen" title="small" href="', $settings['theme_url'], '/small.css?fin11" />
	<link rel="alternate stylesheet" type="text/css" media="screen" title="normal" href="', $settings['theme_url'], '/normal.css?fin11" />
	<link rel="alternate stylesheet" type="text/css" media="screen" title="big" href="', $settings['theme_url'], '/big.css?fin11" />
	<link rel="alternate stylesheet" type="text/css" media="screen" title="big2" href="', $settings['theme_url'], '/big2.css?fin11" />
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

	// the width
    if($context['user']['is_logged']){
		echo '<style type="text/css"><!--
		#maintable{
			width: ' , !empty($options['mywidth']) ? $options['mywidth'] : '80%' , ';
			min-width: 750px;
		}
	 --></style>';
	}
	else{
		if(isset($_COOKIE['mywidth']))
			$gwidth=$_COOKIE['mywidth'];
		else
			$gwidth='80%';

		echo '<style type="text/css"><!--
		#maintable{
			width: ' , $gwidth , ';
			min-width: 750px;
		}
	 --></style>';
	}

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
			document.getElementById("upshrink").src = smf_images_url + (mode ? "/expand.gif" : "/collapse.gif");

			document.getElementById("topsection").style.display = mode ? "none" : "";

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
	<script type="text/javascript" src="'.$settings['theme_url'].'/styleswitch.js"></script>
	<script type="text/javascript">
window.onload=function(){
var formref=document.getElementById("switchform")
indicateSelected(formref.choice)
}
</script>
</head>
<body>';
// check for some mods if they are indeed loaded :P
checkmods();

echo '
<div id="maintable">';

if(!empty($settings['mytopbox']))
	echo '<div style="background: #414141;">',$settings['mytopbox'],'</div>';

echo '
	<div id="topsection" ', !empty($options['collapse_header']) ? 'style="display: none;"' : '' ,'>
		<div id="topright"><img src="' .$settings['images_url']. '/topr.jpg" alt="" /></div>
		<div id="topleft">';
	
if (empty($settings['header_logo_url']))
	echo '
			<h1>', $context['forum_name'], '</h1>';
else
	echo '
			<img src="', $settings['header_logo_url'], '" style="margin: 4px;" alt="', $context['forum_name'], '" />';

echo '
		</div>
		<div>';

if($context['user']['is_logged'])
{
	echo '
			', $txt['hello_member_ndt'], ' <b>', $context['user']['name'] , '</b><br />
			<span class="middletext">
			<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a> <br />
			<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a>';

	// Is the forum in maintenance mode?
	if ($context['in_maintenance'] && $context['user']['is_admin'])
		echo '
			<br /><b>', $txt[616], '</b>';

	// Are there any members waiting for approval?
	if (!empty($context['unapproved_members']))
		echo '
			<br />', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'];
}
else
{
		echo $txt['welcome_guest'] , '
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

echo '</span>
		</div>
	</div>
	<div id="mainmenu">' , template_menu() , '</div>
	<div id="bodyarea">';

if(!empty($settings['mybannerbox']))
	echo '<div style="padding-top: 5px;">',$settings['mybannerbox'],'</div>';
	ads_topcode();
	ads_towercode();

	theme_linktree2();
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

 	ads_towercode2();
	echo '
							</div>
							<div id="footerarea" style="clear: both; text-align: center;">';

	ads_botcode();

	echo '
								<span class="smalltext">', theme_copyright(), ' <br />
								' , function_exists("tportal_version") ? tportal_version().'<br />' : '' , '
								<strong>Mesh</strong> design by <a href="http.//www.blocweb.net" target="_blank">BlocWeb</a> |
					<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
					<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a></span>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '<br />
		<small>', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</small>';
	if (!empty($settings['use_gb']) && isset($context['ob_googlebot_stats']))
		echo '
								<br /><br /><span class="smalltext">', $txt['ob_googlebot_stats_lastvisit'], timeformat($context['ob_googlebot_stats']['Googlebot']['lastvisit']), '</span>';

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

	echo '
	</div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
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

	echo '<div class="nav" style="font-size: smaller; padding: 20px 0 20px 0;">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'];

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;>&nbsp;';
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

echo '<div style="float: right;">';

if($context['show_widths'] && $context['user']['is_logged'])
		echo '
<form action="', $scripturl,  !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ,'" style="margin: 0;padding: 0; text-align: right;" method="post">
<input style="vertical-align: top; background: #6B6B6B; padding: 0; margin: 0;  border: 0; cursor: pointer; cursor: hand;" type="submit" value="800PX" name="options[mywidth]" />|
<input style="vertical-align: top; background: #6B6B6B; padding: 0; margin: 0; border: 0; cursor: pointer; cursor: hand; " type="submit" value="95%" name="options[mywidth]" />|
<input style="vertical-align: top; background: #6B6B6B; padding: 0; margin: 0;  border: 0; cursor: pointer; cursor: hand; " type="submit" value="80%" name="options[mywidth]" />
</form>';
echo '
<a href="#" onclick="shrinkHeader(!current_header); return false;"><img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'collapse.gif' : 'expand.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin-right: 2ex;" align="right" /></a></div>';
// Show the start of the tab section.
	echo '
			<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 10px;">
				<tr>
					<td nowrap="nowrap" class="maintab_' , $first , '">&nbsp;</td>';

	// Show the [home] button.
	echo ($current_action=='home' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'home' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '">' , $txt[103] , '</a>
				</td>' , $current_action == 'home' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	if (!empty($settings['use_tp']))
	echo ($current_action=='forum' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'forum' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=forum">' , $txt['tp-forum'] , '</a>
				</td>' , $current_action == 'forum' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Show the [help] button.
	echo ($current_action == 'help' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'help' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=help">' , $txt[119] , '</a>
				</td>' , $current_action == 'help' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// How about the [search] button?
	if ($context['allow_search'])
		echo ($current_action == 'search' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'search' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=search">' , $txt[182] , '</a>
				</td>' , $current_action == 'search' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo ($current_action == 'admin' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'admin' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=admin">' , $txt[2] , '</a>
				</td>' , $current_action == 'admin' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo ($current_action == 'profile' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'profile' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=profile">' , $txt[79] , '</a>
				</td>' , $current_action == 'profile' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo ($current_action == 'pm' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'pm' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=pm">' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a>
				</td>' , $current_action == 'pm' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// The [calendar]!
	if ($context['allow_calendar'])
		echo ($current_action == 'calendar' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'calendar' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a>
				</td>' , $current_action == 'calendar' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo ($current_action == 'mlist' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'mlist' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=mlist">' , $txt[331] , '</a>
				</td>' , $current_action == 'mlist' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// the [arcade] list button
	if (!empty($settings['use_arcade']))
		echo ($current_action == 'arcade' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'arcade' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=arcade">' , $txt['arcade'] , '</a>
				</td>' , $current_action == 'mlist' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// the [gallery] list button
	if (!empty($settings['use_smfgallery']))
		echo ($current_action == 'gallery' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'gallery' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=gallery">' , $txt['smfgallery_menu'] , '</a>
				</td>' , $current_action == 'gallery' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';

	// the shop list button
	if (!empty($settings['use_shop']))
		echo ($current_action == 'shop' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'shop' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=shop">SHOP</a>
				</td>' , $current_action == 'shop' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// If the user is a guest, show [login] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'login' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'login' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=login">' , $txt[34] , '</a>
				</td>' , $current_action == 'login' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// If the user is a guest, also show [register] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'register' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'register' ? 'active_back' : 'back2' , '">
					<a href="', $scripturl, '?action=register">' , $txt[97] , '</a>
				</td>' , $current_action == 'register' ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';


	// Otherwise, they might want to [logout]...
	if ($context['user']['is_logged'])
		echo ($current_action == 'logout' || $context['browser']['is_ie4']) ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td nowrap="nowrap" valign="top" class="maintab_' , $current_action == 'logout' ? 'active_back' : 'back2' , '">
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
		<td class="maintab_back smalltext">', implode('</td><td class="maintab_back smalltext">', $button_strip) , '</td>';
}

function template_content_above()
{
	global $context, $settings, $options, $scripturl, $txt;

	if(isset($_GET['action']))
		echo '
	<div id="content2"><div id="content2-l"><div id="content2-r"><div class="mpad">';
	else
		echo '<div class="content3">';

}
function template_content_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	if(isset($_GET['action']))
		echo '
	</div></div></div></div>';
	else
		echo '</div>';
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
function userarea()
{
	global $context, $settings, $options, $scripturl, $txt;


	echo '<div id="userarea">
				<div id="usertop">';
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
				<ul><li>', $txt['hello_member'], ' ', $context['user']['name'],'</li>';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
			echo '<li><a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'],'</li>';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '<li><b>', $txt[616], '</b></li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '<li>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

		echo '
					<li><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
					<li><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>
				</ul>';
	}
	// Otherwise they're a guest - so politely ask them to register or login.
	else
	{
		echo '
							', $txt['welcome_guest'], '<br />
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
									<input type="submit" value="', $txt[34], '" />
									<input type="hidden" name="hash_passwrd" value="" />
							</form>';
	}

	echo '	</div><form id="sform" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;">
						<div style="margin-top: 7px;">
						<a href="', $scripturl, '?action=search;advanced"><img src="'.$settings['images_url'].'/filter.gif" align="middle" style="margin: 0 1ex;" alt="" /></a>
						<input type="text" name="search" value="" style="width: 190px;" />&nbsp;
						<input type="submit" name="submit" value="', $txt[182], '" style="width: 11ex;" />
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
						</div>
					</form>
					<div id="userbot">

					</div>
				</div>';
}
function width_changer()
{
	global $settings, $options, $txt;

	echo '
<form id="switchform" style="padding: 5px;">
Choose fontsize: 
<select name="choice" size="1" onChange="chooseStyle(this.options[this.selectedIndex].value, 60)">
<option value="small">small</option>
<option value="normal" selected="selected">normal</option>
<option value="big">big</option>
<option value="big2">large</option>
</select>';

if(!empty($settings['use_tp'])){
// TinyPortal
	 if($context['TPortal']['showtop'])
        echo '&nbsp;<a href="javascript:void(0);" onclick="shrinkHeader(!current_header); return false;">&nbsp;H&nbsp;<img id="upshrinkHeader" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '"  border="0" /></a><img id="upshrinkTempHeader" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
	 if($context['TPortal']['leftbar'])
        echo '&nbsp;<a href="javascript:void(0);" onclick="shrinkHeaderLeftbar(!current_leftbar); return false;">&nbsp;L&nbsp;<img id="upshrinkLeftbar" src="', $settings['images_url'], '/', empty($options['collapse_leftbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '"  border="0" /></a><img id="upshrinkTempLeftbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
     if($context['TPortal']['rightbar'])
		echo '<a href="javascript:void(0);" onclick="shrinkHeaderRightbar(!current_rightbar); return false;">&nbsp;R&nbsp;<img id="upshrinkRightbar" src="', $settings['images_url'], '/', empty($options['collapse_rightbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" border="0" /></a><img id="upshrinkTempRightbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
}

echo '
</form>';
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

	echo '<div id="innerframe2">		
							<table width="100%" cellspacing="0" cellpadding="2">
								<tr>';

	if(!empty($settings['use_ads']) && function_exists("show_towerleftAds"))
	{
		$ads = show_towerleftAds();	
		if(!empty($ads))
		{
			echo '		<td valign="top">';
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);
			echo '		</td>';
		}
		unset($ads);
	}

// TinyPortal integrated bars
	if(!empty($settings['use_tp']) && $context['TPortal']['leftbar'])
	{
		echo '<td width="' ,$context['TPortal']['leftbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex;" valign="top">
                 <div id="leftbarHeader"', empty($options['collapse_leftbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
        TPortal_sidebar('left');
        echo '</div></td>';
	}


	echo '						<td valign="top" width="100%">
										<table width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr>';

	// shoutbox mod
	if(!empty($settings['use_shoutbox']) && function_exists("smfshout"))
	{
		echo '								<td width="20%" valign="top"style="padding: 10px 3px 10px 0; ">
													<div style="padding: 3px;" class="titlebg">Shoutbox</div><div id="smfshout" class="windowbg2">' , smfshout() , '</div>
												</td>';
		$sidebar=true;
	}
	else
		$sidebar=false;

	echo '
												<td width="', $sidebar ? '80%' : '100%' , '" align="left" valign="top" style="padding-top: 10px; padding-bottom: 10px;">';
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
		echo '<td width="' ,$context['TPortal']['rightbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex;" valign="top">
                 <div id="rightbarHeader"', empty($options['collapse_rightbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['rightbar_width'], 'px;">';
        TPortal_sidebar('right');
        echo '</div></td>';
	}

	if(!empty($settings['use_ads']) && function_exists("show_towerrightAds"))
	{
		$ads = show_towerrightAds();	
		if(!empty($ads))
		{
			echo '				<td valign="top">';
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);
			echo '				</td>';
		}
		unset($ads);
	}
	echo '					</tr>
							</table>
</td></tr></table></div>';

}


?>