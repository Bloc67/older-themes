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
	
	// catch any user widths and font-sizes
	if(!$context['user']['is_guest'] && (isset($_POST['options']['mycolor']) || isset($_POST['options']['mywidth'])))
	{
   		include_once($GLOBALS['sourcedir'] . '/Profile.php');
   		makeThemeChanges($context['user']['id'], $settings['theme_id']);
		if(isset($_POST['options']['mycolor']))
   			$options['mycolor'] = $_POST['options']['mycolor'];
		if(isset($_POST['options']['mywidth']))
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
      		$options['mywidth'] = $_SESSION['mywidth'];

   		if (isset($_POST['options']['mycolor']))
   		{
      		$_SESSION['mycolor'] = $_POST['options']['mycolor'];
      		$options['mycolor'] = $_SESSION['mycolor'];
   		}
   		elseif (isset($_SESSION['mycolor']))
      		$options['mycolor'] = $_SESSION['mycolor'];
	}
}

// The main sub template above the content.
function template_main_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;


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


	// the fontsize
    if($context['user']['is_logged'])
		echo '<style type="text/css"><!--
	body
	{
		font-size: ' , !empty($options['mysize']) ? $options['mysize'] : 'small' , ';
		margin: auto;
	}
	 --></style>';
	else{
		if(isset($_COOKIE['size']))
			$gsize=$_COOKIE['size'];
		else
			$gsize='small';

		echo '<style type="text/css"><!--
	body
	{
		font-size: ' , $gsize , ';
	}
	 --></style>';
	}
	// the width
    if($context['user']['is_logged']){
		echo '<style type="text/css"><!--
		#mainframe
		{
			width: ' , !empty($options['mywidth']) ? $options['mywidth'] : '85%' , ';
		}
	 --></style>';
	}
	else{
		if(isset($options['mywidth']))
			$gwidth=$options['mywidth'];
		else
			$gwidth='85%';


		echo '<style type="text/css"><!--
		#mainframe
		{
			width: ' , $gwidth , ';
		}
	 --></style>';
	}

echo '
	<script type="text/javascript" src="', $settings['theme_url'], '/jquery.js"></script>
	<script type="text/javascript">
	  $(document).ready(function(){
			$("#nav-one li").hover(
				function(){ $("ul", this).fadeIn("fast"); }, 
				function() { } 
			);
	  	if (document.all) {
				$("#nav-one li").hoverClass ("sfHover");
			}
	  });
	  
		$.fn.hoverClass = function(c) {
			return this.each(function(){
				$(this).hover( 
					function() { $(this).addClass(c);  },
					function() { $(this).removeClass(c); }
				);
			});
		};	  
	</script>';
	
	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);

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

		// ]]></script>
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
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
</head>
<body>';

	echo '
<div id="mainframe">
	<div id="header">
	<div id="controls">
			<form action="', $scripturl,  !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ,'" style="margin: 0; padding: 0;" class="smalltext" method="post">
				<a href="javascript:ts(\'body\',1)">A+</a> 
				<a href="javascript:ts(\'body\',-1)">A-</a> 
				<a href="javascript:tsreset(\'body\')">[A]</a>';

	echo '
				<input class="widthbuttons" type="submit" onmouseover="pointer: hand;" value="800PX" name="options[mywidth]" />
				<input class="widthbuttons" type="submit" value="100%" name="options[mywidth]" />
				<input class="widthbuttons" type="submit" value="88%" name="options[mywidth]" />';


echo '	</form>
	</div>
		<a href="index.php?action=forum"><img id="logo" src="',$settings['images_url'],'/logo.jpg" alt="TinyPortal" /></a>
	' , template_menu() , '
	<br style="clear: both" />
	</div>
	<table cellspacing="0" cellpadding="0" width="100%">
		<tr>';

	echo '
		<td id="centerframe" width="100%" valign="top">';
	
	// if in certain places, leave out the padding
	$fullwidth=false;
	if (in_array($context['current_action'], array('forum')))
		$fullwidth=true;

	if($fullwidth)
		echo '<div id="center2" style="overflow: auto;">';
	else
		echo '<div id="center" style="overflow: auto;">';

}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		</div></td>';

	echo '
	</tr>
</table>

<div id="footer" class="smalltext">';


	echo '
		', theme_copyright(), ' <br /><b>Emerald</b> design by <a href="http.//www.blocweb.net">BlocWeb</a>';
	if ($context['show_load_time'])
		echo '
		<p class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'],'</p>';

	echo '
</div>
</div>';
   
   

	// The following will be used to let the user know that some AJAX process is running
	echo '
<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>';

// end
echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	global $context, $settings, $options;

	echo '<div id="linktree">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		if ($link_num > count($context['linktree']) - 2)
			echo '<b>', $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '">' . $tree['name'] . '</a>' : $tree['name'], '</b>';
		else
			echo $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '">' . $tree['name'] . '</a>' : $tree['name'];

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;/&nbsp;';
	}

	echo '</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = '';
	$forumcation=false;
	if (in_array($context['current_action'], array('admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
	{
		$current_action = $context['current_action'];
		$forumaction=true;
	}
	if ($context['current_action'] == 'search2')
		$current_action = 'search';

	if ($context['current_action'] == 'themeshop')
		$current_action = 'themeshop';

	if (isset($_GET['dl']))
		$current_action = 'downloads';

	if (isset($_GET['board']) || isset($_GET['topic']))
		$forumaction=true;

	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	// Show the start of the tab section.
	echo '
	<div id="menucontainer">
		<ul id="nav-one" class="nav">';

	// Show the [forum] button.
	echo 	'<li><a' , ($current_action == '' | $forumaction) ? ' class="chosen"' : '' , ' href="', $scripturl, '">' , $txt[103] , '</a>
				<ul>';

	// Show the [help] button.
	echo 	'<li><a' , $current_action == 'help' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=help">' , $txt[119] , '</a></li>';

	// How about the [search] button?
	if ($context['allow_search'])
		echo 	'<li><a' , $current_action == 'search' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=search">' , $txt[182] , '</a></li>';

	// Is the user allowed to administrate at all? ([admin])
	if ($context['allow_admin'])
		echo 	'<li><a' , $current_action == 'admin' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=admin">' , $txt[2] , '</a></li>';

	// Edit Profile... [profile]
	if ($context['allow_edit_profile'])
		echo 	'<li><a' , $current_action == 'profile' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=profile">' , $txt[79] , '</a></li>';

	// Go to PM center... [pm]
	if ($context['user']['is_logged'] && $context['allow_pm'])
		echo 	'<li><a' , $current_action == 'pm' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=pm">', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a></li>';

	// The [calendar]!
	if ($context['allow_calendar'])
		echo 	'<li><a' , $current_action == 'calendar' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a></li>';

	// the [member] list button
	if ($context['allow_memberlist'])
		echo 	'<li><a' , $current_action == 'mlist' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=mlist">' , $txt[331] , '</a></li>';

	// If the user is a guest, show [login] button.
	if ($context['user']['is_guest'])
		echo 	'<li><a' , $current_action == 'login' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=login">' , $txt[34] , '</a></li>';


	// If the user is a guest, also show [register] button.
	if ($context['user']['is_guest'])
		echo 	'<li><a' , $current_action == 'register' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=register">' , $txt[97] , '</a></li>';


	// Otherwise, they might want to [logout]...
	if ($context['user']['is_logged'])
		echo 	'<li><a' , $current_action == 'logout' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=logout;sec=',$context['session_id'],'">' , $txt[108] , '</a></li>';

echo '
				</ul>
			</li>
			<li>
				<a ' , $current_action == 'myaction' ? ' class="chosen"' : '' , ' href="',$scripturl,'?action=myaction">Menu2</a>
				<ul>
					<li><a ' , $current_action == 'mysubaction' ? ' class="chosen"' : '' , ' href="',$scripturl,'?action=mysubaction">Submenu1</a></li>
					<li><a ' , $current_action == 'mysubaction2' ? ' class="chosen"' : '' , ' href="',$scripturl,'?action=mysubaction2">Submenu2</a></li>
				</ul>
			</li>
		</ul>
	</div>';

}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl;

	
	echo '<td><div class="mbuttons_container"><ul class="mbuttons">';
	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (isset($value['test']) && empty($context[$value['test']]))
		{
			unset($button_strip[$key]);
			continue;
		}
		elseif (!isset($buttons[$key]) || $force_reset)
			$buttons[$key] = '<li><a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '>' . $txt[$value['text']] . '</a></li>';

		echo $buttons[$key];
	}

	return '</ul></div></td>';
}

// new routine
function template_button_strip_tp($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl;

	
	echo '<div class="mbuttons_container"><ul class="mbuttons">';
	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (isset($value['test']) && empty($context[$value['test']]))
		{
			unset($button_strip[$key]);
			continue;
		}
		elseif (!isset($buttons[$key]) || $force_reset)
			$buttons[$key] = '<li><a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '>' . $txt[$value['text']] . '</a></li>';

		echo $buttons[$key];
	}

	return '</ul></div>';
}
?>