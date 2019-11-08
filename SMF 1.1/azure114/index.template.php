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
	// the width
    if($context['user']['is_logged']){
		echo '<style type="text/css"><!--
		#maintable{
			width: ' , !empty($options['mywidth']) ? $options['mywidth'] : '90%' , ';
			margin: auto;
			min-width: 750px;
		}
	 --></style>';
	}
	else{
		if(isset($_COOKIE['mywidth']))
			$gwidth=$_COOKIE['mywidth'];
		else
			$gwidth='90%';

		echo '<style type="text/css"><!--
		#maintable{
			width: ' , $gwidth , ';
			margin: auto;
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
			document.getElementById("upshrinkHeader").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");
			document.getElementById("user").style.display = mode ? "none" : "";
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

			var mysize = "', !empty($options['mysize']) ? $options['mysize'] : 'small', '";

			function setmysize(size)
			{';

	if ($context['user']['is_guest'])
		echo '
				document.cookie = "size=" + size;';
	else
		echo '
				smf_setThemeOption("mysize", size , null, "', $context['session_id'], '");';

	echo '
			}

             /*------------------------------------------------------------
	Document Text Sizer- Copyright 2003 - Taewook Kang.  All rights reserved.
	Coded by: Taewook Kang (txkang.REMOVETHIS@hotmail.com)
	Web Site: http://txkang.com
	Script featured on Dynamic Drive (http://www.dynamicdrive.com)

	Please retain this copyright notice in the script.
	License is granted to user to reuse this code on
	their own website if, and only if,
	this entire copyright notice is included.
--------------------------------------------------------------*/

//Specify affected tags. Add or remove from list:
var tgs = new Array( \'div\',\'td\',\'tr\');

//Specify spectrum of different font sizes:
var szs = new Array( \'xx-small\',\'x-small\',\'small\',\'medium\',\'large\',\'x-large\' );
';

// setup the array to start with
$fsize=array('xx-small' => 0 , 'x-small' => 1, 'small' => 2, 'medium' => 3, 'large' => 4, 'x-large' => 5);
if(!empty($options['mysize']))
	$what=$fsize[$options['mysize']];
else
	$what=2;

echo '
var startSz = '.$what.';

function ts( trgt,inc ) {
	if (!document.getElementById) return
	var d = document,cEl = null,sz = startSz,i,j,cTags;

	sz += inc;
	if ( sz < 0 ) sz = 0;
	if ( sz > 5 ) sz = 5;
	startSz = sz;


	if ( !( cEl = d.getElementById( trgt ) ) ) cEl = d.getElementsByTagName( trgt )[ 0 ];

	cEl.style.fontSize = szs[ sz ];

	for ( i = 0 ; i < tgs.length ; i++ ) {
		cTags = cEl.getElementsByTagName( tgs[ i ] );
		for ( j = 0 ; j < cTags.length ; j++ ) cTags[ j ].style.fontSize = szs[ sz ];
	}
    setmysize(szs[ sz ]);
 }

 function tsreset( trgt ) {
	if (!document.getElementById) return
	var d = document,cEl = null,sz = startSz,i,j,cTags;

	sz = 2;
	startSz = sz;


	if ( !( cEl = d.getElementById( trgt ) ) ) cEl = d.getElementsByTagName( trgt )[ 0 ];

	cEl.style.fontSize = szs[ sz ];

	for ( i = 0 ; i < tgs.length ; i++ ) {
		cTags = cEl.getElementsByTagName( tgs[ i ] );
		for ( j = 0 ; j < cTags.length ; j++ ) cTags[ j ].style.fontSize = szs[ sz ];
	}
    setmysize(szs[ sz ]);
 }
		// ]]></script>
	<script type="text/javascript" src="'.$settings['theme_url'].'/styleswitch.js"></script>
</head>
<body><div id="backdrop">';

	echo	'<div id="maintable">';

	// check for some mods if they are indeed loaded :P
	checkmods();


	if($show_widths)
		echo '
<form action="', $scripturl,  !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ,'" style="color: #ddd; margin: 0;padding: 0; text-align: right;" method="post">
<input style="vertical-align: top; padding: 0; margin: 0; background: #000; border: 0; color: #eee; cursor: pointer; cursor: hand;" type="submit" value="800PX" name="options[mywidth]" />|
<input style="vertical-align: top; padding: 0; margin: 0; background: #000;  border: 0; color: #eee;cursor: pointer; cursor: hand; " type="submit" value="96%" name="options[mywidth]" />|
<input style="vertical-align: top; padding: 0; margin: 0;  background: #000; border: 0; color: #eee;cursor: pointer; cursor: hand; " type="submit" value="90%" name="options[mywidth]" />
</form><br />';
	
	topmenu();

	echo '
	<table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 1em;">
		<tr>
			<td><img src="'.$settings['images_url'].'/img/topleft.jpg" alt="" /></td>
			<td style="background: url('.$settings['images_url'].'/img/topmid.jpg);" width="100%">
			</td>
			<td><img src="'.$settings['images_url'].'/img/topright.jpg" alt="" /></td>
		</tr>
	</table>
	<div id="user"' , empty($options['collapse_header']) ? ' style: display: none;' : '' , '>
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="'.$settings['images_url'].'/img/midleft.jpg" alt="" /></td>
			<td valign="top" class="user" style="padding-top: 6px; background: url('.$settings['images_url'].'/img/midmid1.jpg);" width="60%">';
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '<div class="greeting">', $txt['hello_member_ndt'], ' <b>', $context['user']['name'], '</b></div>
					<div>';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
			echo $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
		echo '.<br />';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
							<b>', $txt[616], '</b><br />';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
							', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

		// Show the total time logged in?
		if (!empty($context['user']['total_time_logged_in']))
		{
			echo '
							', $txt['totalTimeLogged1'];

			// If days is just zero, don't bother to show it.
			if ($context['user']['total_time_logged_in']['days'] > 0)
				echo $context['user']['total_time_logged_in']['days'] . $txt['totalTimeLogged2'];

			// Same with hours - only show it if it's above zero.
			if ($context['user']['total_time_logged_in']['hours'] > 0)
				echo $context['user']['total_time_logged_in']['hours'] . $txt['totalTimeLogged3'];

			// But, let's always show minutes - Time wasted here: 0 minutes ;).
			echo $context['user']['total_time_logged_in']['minutes'], $txt['totalTimeLogged4'], '<br />';
		}

		echo '
							<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br />
							<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a>
		</div>';
	}
	// Otherwise they're a guest - so politely ask them to register or login.
	else
	{
		echo '<div style="margin-top: 4px;">
							', $txt['welcome_guest'], '</div>

							<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>

							<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 35px 1ex 1px 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
								<div>
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
								</div>
							</form>';
	}

	echo '
			</td>
			<td><img src="'.$settings['images_url'].'/img/midborder.jpg" alt="" /></td>
			<td class="user" valign="top" style="background: url('.$settings['images_url'].'/img/midmid2.jpg);" width="40%">
<div class="greeting2" style="font-size: 10px; text-transform: lowercase; color: #ddd;">
'.$context['current_time'].'
</div>
';

	if (!empty($settings['enable_news']))
		echo '<div style="height: 50px; overflow: auto; padding: 5px;" class="smalltext">', $context['random_news_line'], '</div>';

	echo '
			</td>
			<td><img src="'.$settings['images_url'].'/img/midright.jpg" alt="" /></td>
		</tr>
	</table></div>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="'.$settings['images_url'].'/img/maintopleft.jpg" alt="" /></td>
			<td valign="top" style="background: url('.$settings['images_url'].'/img/maintopback.jpg);" width="100%">
			' , template_menu() ,'<br />
			</td>
			<td><img src="'.$settings['images_url'].'/img/maintopright.jpg" alt="" /></td>
		</tr>
		<tr>
			<td style="background: url('.$settings['images_url'].'/img/mainmidleft.gif) repeat-y ");"><img src="'.$settings['images_url'].'/img/mainmidleft.gif" alt="" /></td>
			<td valign="top" style="background: #cdcdcd" width="100%">';
	
	ads_topcode();
	ads_towercode();


}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;
 	
	ads_towercode2();

	echo '</td>
			<td style="background: url('.$settings['images_url'].'/img/mainmidright.gif) repeat-y;"><img src="'.$settings['images_url'].'/img/mainmidright.gif" alt="" /></td>
		</tr>
		<tr>
			<td><img src="'.$settings['images_url'].'/img/mainbotleft.gif" alt="" /></td>
			<td style="background: url('.$settings['images_url'].'/img/mainbotmid.gif);" width="100%">
			</td>
			<td><img src="'.$settings['images_url'].'/img/mainbotright.gif" alt="" /></td>
		</tr>
	</table>';
	ads_botcode();
	echo '<table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 4px;">
		<tr>
			<td><img src="'.$settings['images_url'].'/img/botleft.gif" alt="" /></td>
			<td style="background: url('.$settings['images_url'].'/img/botmid.gif);" align="center" width="100%">
				<div id="copyright">';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '

					', theme_copyright(), '<br />
								' , !empty($settings['use_tp']) ? tportal_version().' | ' : '' , '
				<strong>Azure</strong> design by <a href="http.//www.blocweb.net">BlocWeb</a> |
					<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
					<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a>
			';

		// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';

	if (!empty($settings['use_gb']) && isset($context['ob_googlebot_stats']))
		echo '
								<br /><br /><span class="smalltext">', $txt['ob_googlebot_stats_lastvisit'], timeformat($context['ob_googlebot_stats']['Googlebot']['lastvisit']), '</span>';
	echo '</div></td>
			<td><img src="'.$settings['images_url'].'/img/botright.gif" alt="" /></td>
		</tr>
	</table></div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</div></body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
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

	// Show the start of the tab section.
	echo '
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="main2tab_' , $first , '">&nbsp;</td>';

	// Show the [home] button.
	echo ($current_action=='home' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'home' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '">' , $txt[103] , '</a>
				</td>' , $current_action == 'home' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';
	
	// Show the [forum] button.
	if (!empty($settings['use_tp']))
	   echo ($current_action=='forum' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'forum' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=forum">' , $txt['tp-forum'] , '</a>
				</td>' , $current_action == 'forum' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// Show the [help] button.
	echo ($current_action == 'help' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'help' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=help">' , $txt[119] , '</a>
				</td>' , $current_action == 'help' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// How about the [search] button?
	if ($context['allow_search'])
		echo ($current_action == 'search' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'search' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=search">' , $txt[182] , '</a>
				</td>' , $current_action == 'search' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo ($current_action == 'admin' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'admin' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=admin">' , $txt[2] , '</a>
				</td>' , $current_action == 'admin' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo ($current_action == 'profile' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'profile' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=profile">' , $txt[79] , '</a>
				</td>' , $current_action == 'profile' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo ($current_action == 'pm' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'pm' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=pm">' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a>
				</td>' , $current_action == 'pm' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// The [calendar]!
	if ($context['allow_calendar'])
		echo ($current_action == 'calendar' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'calendar' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a>
				</td>' , $current_action == 'calendar' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo ($current_action == 'mlist' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'mlist' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=mlist">' , $txt[331] , '</a>
				</td>' , $current_action == 'mlist' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';


	// SMF Arcade
	if (!empty($settings['use_arcade']))
		echo ($current_action == 'arcade' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'arcade' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=arcade">' , $txt['arcade'] , '</a>
				</td>' , $current_action == 'arcade' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';
	// SMF Gallery
	if (!empty($settings['use_smfgallery']))
		echo ($current_action == 'gallery' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'gallery' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=gallery">' , $txt['smfgallery_menu'] , '</a>
				</td>' , $current_action == 'gallery' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';
	// SMFshop 
	if (!empty($settings['use_shop']))
		echo ($current_action == 'shop' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'shop' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=shop">Shop</a>
				</td>' , $current_action == 'shop' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';
	
	
	// If the user is a guest, show [login] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'login' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'login' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=login">' , $txt[34] , '</a>
				</td>' , $current_action == 'login' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';


	// If the user is a guest, also show [register] button.
	if ($context['user']['is_guest'])
		echo ($current_action == 'register' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'register' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=register">' , $txt[97] , '</a>
				</td>' , $current_action == 'register' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';


	// Otherwise, they might want to [logout]...
	if ($context['user']['is_logged'])
		echo ($current_action == 'logout' || $context['browser']['is_ie4']) ? '<td class="main2tab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="main2tab_' , $current_action == 'logout' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">' , $txt[108] , '</a>
				</td>' , $current_action == 'logout' ? '<td class="main2tab_active_' . $last . '">&nbsp;</td>' : '';

	// The end of tab section.
	echo '
				<td class="main2tab_' , $last , '">&nbsp;</td>
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
		<td class="maintab_back">', implode(' &nbsp;|&nbsp; ', $button_strip) , '</td>';
}
function topmenu()
{
	global $context, $txt, $scripturl, $settings;

	echo '
	<div id="topmenu">';
	$current_action='forum';
	if (!empty($settings['use_tp']) && (isset($_GET['board']) || isset($_GET['topic'])))
		$current_action = 'forum';

	echo '
		<ul>';


	if(sizeof($context['sitemenu'])>0)
	{
		foreach($context['sitemenu'] as $menu => $val)
		{
			if($val[2]=='page')
				echo '
			<li><a href="',$scripturl,'?action='.$val[0].'">'.$val[1].'</a></li>';
			elseif($val[2]=='link')
				echo '
			<li><a href="'.$val[0].'">'.$val[1].'</a></li>';
		}

	}

	if(!empty($settings['use_tp']))
	{
	// TinyPortal
		if($context['TPortal']['showtop'])
			echo '
			<li><a href="javascript:void(0);" onclick="shrinkHeader(!current_header); return false;">&nbsp;H&nbsp;<img id="upshrinkHeader" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '"  border="0" /></a><img id="upshrinkTempHeader" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" /></li>';
		if($context['TPortal']['leftbar'] || $context['TPortal']['leftpanel'])
			echo '
			<li><a href="javascript:void(0);" onclick="shrinkHeaderLeftbar(!current_leftbar); return false;">&nbsp;L&nbsp;<img id="upshrinkLeftbar" src="', $settings['images_url'], '/', empty($options['collapse_leftbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '"  border="0" /></a><img id="upshrinkTempLeftbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" /></li>';
		if($context['TPortal']['rightbar'] || $context['TPortal']['rightpanel'])
			echo '
			<li><a href="javascript:void(0);" onclick="shrinkHeaderRightbar(!current_rightbar); return false;">&nbsp;R&nbsp;<img id="upshrinkRightbar" src="', $settings['images_url'], '/', empty($options['collapse_rightbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" border="0" /></a><img id="upshrinkTempRightbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" /></li>';
	}
	else
			echo '
			<li><a href="javascript:void(0);" onclick="shrinkHeader(!current_header); return false;">&nbsp;H&nbsp;<img id="upshrinkHeader" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '"  border="0" /></a><img id="upshrinkTempHeader" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" /></li>';

	echo '
		</ul>
	</div>';
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
				<td valign="top">';
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
					<div id="leftbarHeader"', empty($options['collapse_leftbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
        TPortal_sidebar('left');
        echo '	</div>
				</td>';
	}


	echo '	<td  class="padtop" valign="top" width="100%">
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
		echo '<td  class="padtop" width="' ,$context['TPortal']['rightbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 4px;padding-right: 1ex;" valign="top">
                 <div id="rightbarHeader"', empty($options['collapse_rightbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['rightbar_width'], 'px;">';
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
?>
