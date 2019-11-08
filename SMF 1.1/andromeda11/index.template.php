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
	loadLanguage('ThemeStrings');

	// make sure undefined actions use their own template
	$settings['catch_action'] = array('layers' => array('main','pages'));

	if($context['browser']['is_ie6'])
		loadtemplate('ie6');
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

	if($context['browser']['is_ie6'])
		template_ie6_above();
	else
		template_default_above();
}

// The main sub template above the content.
function template_default_above()
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

	if($context['browser']['is_ie6'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/ie6.css?fin11" />';

	if($context['browser']['is_ie7'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/ie7.css?fin11" />';

	// theme css
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/andromeda.css?fin11" />
	<link rel="alternate stylesheet" media="screen" title="stripes" type="text/css" href="', $settings['theme_url'], '/stripes.css?fin11" />
	<link rel="alternate stylesheet" media="screen" title="sun" type="text/css" href="', $settings['theme_url'], '/sun.css?fin11" />
	<link rel="alternate stylesheet" media="screen" title="blue" type="text/css" href="', $settings['theme_url'], '/blue.css?fin11" />
	<link rel="alternate stylesheet" media="screen" title="andro" type="text/css" href="', $settings['theme_url'], '/andro.css?fin11" />
	
	<link rel="font stylesheet" media="screen" title="small" type="text/css" href="', $settings['theme_url'], '/small.css?fin11" />
	<link rel="font stylesheet" media="screen" title="normal" type="text/css" href="', $settings['theme_url'], '/normal.css?fin11" />
	<link rel="font stylesheet" media="screen" title="big" type="text/css" href="', $settings['theme_url'], '/big.css?fin11" />

	<link rel="left stylesheet" media="screen" title="hideleft" type="text/css" href="', $settings['theme_url'], '/hideleft.css?fin11" />
	';

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
			document.getElementById("upshrink").src = "', $settings['theme_url'],'/theme" + (mode ? "/user2.png" : "/user.png");

			document.getElementById("userbox").style.display = mode ? "none" : "";
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
	<script src="', $settings['theme_url'] , '/javascript/styleswitch.js" type="text/javascript">
		/***********************************************
		* Style Sheet Switcher v1.1- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
		* This notice MUST stay intact for legal use
		* Visit Dynamic Drive at http://www.dynamicdrive.com/ for this script and 100s more
		***********************************************/
	</script>
	<script src="', $settings['theme_url'] , '/javascript/mootools.1.2.js" type="text/javascript"></script>
<script language="javascript" type="text/ecmascript" src="' . $settings['theme_url'] . '/javascript/mooSlide2-moo12.js"></script>
<script language="javascript" type="text/ecmascript">
	window.addEvent(\'domready\',function(){
	var p = new mooSlide2({ slideSpeed: 1100, fadeSpeed: 300,  toggler:\'toggle\', content:\'dropdiv1\', height:200, removeOnClick: true, opacity:\'1\', effects:Fx.Transitions.Bounce.easeOut, from:\'top\' });';
	if(!empty($settings['dropdiv2']))
		echo '
	var px = new mooSlide2({ slideSpeed: 1100, fadeSpeed: 300,  toggler:\'toggle2\', content:\'ddiv2\', height:200, removeOnClick: true, opacity:\'1\', effects:Fx.Transitions.Bounce.easeOut, from:\'top\' });';
	if(!empty($settings['dropdiv3']))
		echo '
	var py = new mooSlide2({ slideSpeed: 1100, fadeSpeed: 300,  toggler:\'toggle3\', content:\'ddiv3\', height:200, removeOnClick: true, opacity:\'1\', effects:Fx.Transitions.Bounce.easeOut, from:\'top\' });';
	
	echo '
		})
</script>
</head>
<body>';

	echo '
<div id="mainarea">
	<div id="topsection">
		<div id="themelogo">
			<a href="javascript:chooseStyle(\'none\', 60)" checked="checked"><img class="themecolor" src="' . $settings['images_url'] . '/backgrounds/thumbs/default.jpg" alt="default" /></a>
			<a href="javascript:chooseStyle(\'andro\', 60)" checked="checked"><img class="themecolor" src="' . $settings['images_url'] . '/backgrounds/thumbs/andromeda.jpg" alt="default" /></a>
			<a href="javascript:chooseStyle(\'stripes\', 60)" checked="checked"><img class="themecolor" src="' . $settings['images_url'] . '/backgrounds/thumbs/stripes.jpg" alt="default" /></a>
			<a href="javascript:chooseStyle(\'sun\', 60)" checked="checked"><img class="themecolor" src="' . $settings['images_url'] . '/backgrounds/thumbs/sun.jpg" alt="default" /></a>
			<a href="javascript:chooseStyle(\'blue\', 60)" checked="checked"><img class="themecolor" src="' . $settings['images_url'] . '/backgrounds/thumbs/blue.jpg" alt="default" /></a>
			<div class="controls">
				<a href="javascript:chooseStyle3(\'hideleft\', 60)" checked="checked"><img style="padding-top: 4px;" src="' , $settings['images_url'] , '/TPleft.gif" alt="" /></a> 
				<a href="javascript:chooseStyle3(\'none\', 60)" checked="checked"><img style="padding-top: 4px;" src="' , $settings['images_url'] , '/expand.gif" alt="" /></a> 
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="javascript:chooseStyle2(\'small\', 60)" checked="checked">Small</a> | 
				<a href="javascript:chooseStyle2(\'none\', 60)" checked="checked">Normal</a> |
				<a href="javascript:chooseStyle2(\'big\', 60)" checked="checked">Big</a>
			</div>
			<img src="' . $settings['images_url'] . '/theme/logo.png" alt="Andromeda" />
		</div>';

	if (empty($settings['header_logo_url']))
		echo '
		<h1 id="mainheader"><a href="' , $scripturl , '">', $context['forum_name'], '</a></h1>';
	else
		echo '
		<a href="' , $scripturl , '"><img id="mainheader" src="', $settings['header_logo_url'], '" alt="', $context['forum_name'], '" /></a>';

	template_menu();
	
	echo '
	</div>
	<div id="content">
		<div id="leftsection">
			<div class="top">
				<img src="' , $settings['images_url'] , '/theme/left1.png" alt="" />
				<div class="content smalltext">';

	if($context['user']['is_logged'])
	{
		echo '<b>', $txt['hello_member_ndt'], ' ', $context['user']['name'] , '</b>
				<br />';
		if ($context['allow_pm'])
			echo $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
		echo '.<br />';
		
		echo '
				<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a> <br />
				<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a><br />';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
				<b>', $txt[616], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
				', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

	}
	else
		echo '	
				<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>
				<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="margin: 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
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
				
	echo '	</div>
			</div>
			<div class="middle">
				<img src="' , $settings['images_url'] , '/theme/left3.png" alt="" />
				<div class="content">'; 

	if (!empty($context['user']['avatar']))
		echo '
				<div id="myavatar">', $context['user']['avatar']['image'], '</div>';
				
	echo '
				</div>
			</div>
			<div class="bottom">
				<img src="' , $settings['images_url'] , '/theme/left5.png" alt="" />
				<div class="content">
				<br />';
	
	if(!empty($context['TPortal']['version']) && $context['TPortal']['version']=='098')
	{
		
		// TinyPortal v.0.98
		if($context['TPortal']['leftbar'])
		{
			echo '<div id="leftbarHeader" style="', empty($options['collapse_leftbar']) ? '' : 'display: none;', 'padding-top: 15px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
			TPortal_sidebar('left');
			echo '</div>';
		}
		
		if($context['TPortal']['rightbar'])
		{
			echo '<div id="rightbarHeader" style="', empty($options['collapse_rightbar']) ? '' : 'display: none;', 'padding-top: 15px; width: ' ,$context['TPortal']['rightbar_width'], 'px;">';
			TPortal_sidebar('right');
			echo '</div>';
		}
	}
	elseif(!empty($context['TPortal']['version']) && $context['TPortal']['version']!='098')
	{
		
		// TinyPortal v1.0.x
		if($context['TPortal']['leftpanel']==1)
			echo '
				<div id="tpleftbarHeader" style="padding-top: 5px; width: ' ,$context['TPortal']['leftbar_width'], 'px;', (!empty($options['tpcollapse_leftbar']) && $context['TPortal']['showcollapse']==1) ? ' display: none;' : '' , '">
					' , TPortal_panel('left') , '		
				</div>';
		
		// TinyPortal integrated bars
		if($context['TPortal']['rightpanel']==1)
			echo '
				<div id="tprightbarHeader" style="padding-top: 5px; width: ' ,$context['TPortal']['rightbar_width'], 'px;', (!empty($options['tpcollapse_rightbar']) && $context['TPortal']['showcollapse']==1) ? ' display: none;' : '' , '">
					' , TPortal_panel('right') , '		
				</div>';
	}
	else
	{
		echo '<div id="news">' , $context['random_news_line'] . '</div>
				<div id="stats">
					<b>', $context['common_stats']['total_posts'], '</b> ', $txt[95], ' ', $txt['smf88'], ' <b>', $context['common_stats']['total_topics'], '</b> ', $txt[64], ' ', $txt[525], ' <span style="white-space: nowrap;"><b>', $context['common_stats']['total_members'], '</b> ', $txt[19], '</span><br />
					', $txt[656], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
				</div>';
	}
	
	echo '
				</div>
			</div>
		</div>
		<div id="rightsection">
			<div class="top"><span class="left"></span><span class="right"></span><span class="mid"></span></div>
			<div id="top2">
				<div class="l"><div class="r"><div class="text">' , theme_linktree(true) , '</div></div></div>
			</div>
			<div id="search"><div><div>
				<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" id="searchform">
						<input type="text" name="search" value="" id="searchtext" />
						<input type="submit" name="submit" value="', $txt[182], '" id="submittext" />
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
					</form>
			</div></div></div>
			<div id="midsection"><div class="rs"><div class="ms">';	

	//for TP 0.9.8
	if(!empty($context['TPortal']['version']) && $context['TPortal']['version']=='098')
	{	
		if($context['TPortal']['centerbar']==1)
			echo '<div>' , TPortal_sidebar('center') , '</div>';
	}
	echo '	<div style="overflow: auto;">';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	if($context['browser']['is_ie6'])
		template_ie6_below();
	else
		template_default_below();
}

function template_default_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '	</div>
				<div id="copywrite">' , theme_copyright();
	if(function_exists('tportal_version')) 
		echo '
					<div class="smalltext"><strong>Andromeda</strong> theme | ' , tportal_version() , '</div>';
	else
		echo '	<div class="smalltext"><strong>Andromeda</strong> theme by <a href="http.//www.blocweb.net">BlocWeb</a></div>';
		
	 echo '	</div>';
	
	// Show the load time?
	if ($context['show_load_time'])
		echo '
				<div id="rendertime">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';

	echo '
			</div></div></div>
			<div class="bot"><span class="left"></span><span class="right"></span><span class="mid"></span></div>
		</div>
		
	</div>';
	// The following will be used to let the user know that some AJAX process is running
	echo '
	<br style="clear: both;" />	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</div>
<div id="dropdiv1" class="mooSlide">
	<div class="content">
		<div style="color: #ccc; height: 80px; overflow: auto;">' , $context['random_news_line'] , '</div>
		<b>', $context['common_stats']['total_posts'], '</b> ', $txt[95], ' ', $txt['smf88'], ' <b>', $context['common_stats']['total_topics'], '</b> ', $txt[64], ' ', $txt[525], ' <span style="white-space: nowrap;"><b>', $context['common_stats']['total_members'], '</b> ', $txt[19], '</span>
		', $txt[656], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
		<hr /><em style="color: #d84;" class="smalltext">Click to close the panel.</em>
	</div>
</div>';
	if(!empty($settings['dropdiv2']))
		echo '
<div id="ddiv2" class="mooSlide"><div class="content">' , $settings['dropdiv2'] , '</div></div>';
	if(!empty($settings['dropdiv3']))
		echo '
<div id="ddiv3" class="mooSlide"><div class="content">' , $settings['dropdiv3'] , '</div></div>';

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($render=false)
{
	global $context, $settings, $options;

	if(!$render)
		return;

	echo '
	<div id="linktree">';

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
			echo '&nbsp;»&nbsp;';
	}

	echo '
	</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = '';
	if (in_array($context['current_action'], array('admin','tpadmin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('forum','staff','gallery','contact','search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'search2')
		$current_action = 'search';
	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	// add buttons here
	$context['menubox'] = array();

	/*
		'title' - the string the link will have
		'link' - the actual link
		'chosen' - which "current_action" this button belongs to.
	*/

	// home button
	$context['menubox'][]=array(
				'title' => $txt[103],
				'link' => $scripturl,
				'chosen' => '',
				);

	// TP onboard?
	if (function_exists('tportal_version'))
		$context['menubox'][]=array(
				'title' => $txt['tp-forum'],
				'link' => $scripturl.'?action=forum',
				'chosen' => 'forum',
				);
	
	// help button
	$context['menubox'][]=array(
				'title' => $txt[119],
				'link' => $scripturl.'?action=help',
				'chosen' => 'help',
				);


	// search button
	$context['menubox'][]=array(
				'title' => $txt[182],
				'link' => $scripturl.'?action=search',
				'chosen' => 'search',
				);

	// admin button.This one have permission check for admin as well
	if($context['allow_admin'])
		$context['menubox'][]=array(
				'title' => $txt[2],
				'link' => $scripturl.'?action=admin',
				'chosen' => 'admin',
				);

	// profile button
	if($context['allow_edit_profile'])
		$context['menubox'][]=array(
				'title' => $txt[79],
				'link' => $scripturl.'?action=profile',
				'chosen' => 'profile',
				);

	// PM button
	if($context['allow_pm'])
		$context['menubox'][]=array(
				'title' => $txt['pm_short'] . ' '. ($context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : ''),
				'link' => $scripturl.'?action=pm',
				'chosen' => 'pm',
				);

	// calendar button
	if($context['allow_calendar'])
		$context['menubox'][]=array(
				'title' => $txt['calendar24'],
				'link' => $scripturl.'?action=calendar',
				'chosen' => 'calendar',
				);

	// members
	if($context['allow_memberlist'])
		$context['menubox'][]=array(
				'title' => $txt[331],
				'link' => $scripturl.'?action=mlist',
				'chosen' => 'mlist',
				);

	// news
	$context['menubox'][]=array(
				'title' => $txt[102],
				'link' => '#',
				'chosen' => 'news',
				'extra' => ' id="toggle"'
				);

	// extra1
	if(!empty($settings['dropdiv2-title']))
		$context['menubox'][]=array(
				'title' => $settings['dropdiv2-title'],
				'link' => '#',
				'chosen' => '',
				'extra' => ' id="toggle2"'
				);

	// extra2
	if(!empty($settings['dropdiv3-title']))
		$context['menubox'][]=array(
				'title' => $settings['dropdiv3-title'],
				'link' => '#',
				'chosen' => '',
				'extra' => ' id="toggle3"'
				);
	// login button - just for guests
	if(!$context['user']['is_logged'])
		$context['menubox'][]=array(
				'title' => $txt[34],
				'link' => $scripturl.'?action=login',
				'chosen' => 'login',
				);

	// register button - just for guests
	if(!$context['user']['is_logged'])
		$context['menubox'][]=array(
				'title' => $txt[97],
				'link' => $scripturl.'?action=register',
				'chosen' => 'register',
				);

	// logout button - just for members
	if($context['user']['is_logged'])
		$context['menubox'][]=array(
				'title' => $txt[108],
				'link' => $scripturl.'?action=logout;sesc='. $context['session_id'],
				'chosen' => 'logout',
				);
	
	template_menu2();
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu2()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = '';
	if (in_array($context['current_action'], array('admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('forum','tpadmin','search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'search2')
		$current_action = 'search';
	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	// Show the start of the tab section.
	echo '
	<div id="mainmenu">
		<ul>';

	foreach ($context['menubox'] as $act => $button)
		echo '
			<li' , $button['chosen']==$current_action ? ' class="chosen"' : '' , '><a href="', $button['link'], '"' , isset($button['extra']) ? $button['extra'] : '' , '>' , $button['title'] , '</a></li>';
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
		<td class="oldmenu">', implode('', $button_strip) , '</td>';
}

?>