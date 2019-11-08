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
	$settings['theme_version'] = '1.1.2';

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
	// load custom language strings
	loadLanguage('ThemeStrings');
	// set the layers to use
	$context['template_layers']=array('main','menu','content');

	// make sure undefined actions use their own template
	$settings['catch_action'] = array('layers' => array('main','nomenu','pages'));
	// split up the links if any
	$context['sitemenu']=array();
		
	// **************** 
	if(!empty($settings['custom_pages']))
	{
		$pag=explode('|',$settings['custom_pages']);
		foreach($pag as $menu => $value)
		{
			$what=explode(',',$value);
			$context['sitemenu'][]=array($what[0],$what[1],$what[2]);
		}
	}
	// ******************* 
}

// any special pages?
function template_pages_above()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	echo '<div id="pages">';
	if(isset($_GET['action']))
		$what=$_GET['action'];
	// check that indeed it exists
	if(file_exists('pages/'.$what))
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

	// likewise, any admin settings for color?
	if(!empty($settings['site_color']))
		$options['mycolor']=$settings['site_color'];
	else
		$options['mycolor']='';

	if($context['page_title']=='' && isset($_GET['action']))
		$context['page_title']=$context['forum_name'].' - '.$_GET['action'];
	if($context['page_title']=='' && !isset($_GET['action']))
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
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style'.$options['mycolor'].'.css?fin11" />
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
		#frame
		{
			margin: auto;
			width: ' , !empty($options['mywidth']) ? $options['mywidth'] : '80%' , ';
		}
	 --></style>';
	}
	else{
		if(isset($options['mywidth']))
			$gwidth=$options['mywidth'];
		else
			$gwidth='80%';

		if(isset($options['mycolor']))
			$gcolor=$options['mycolor'];
		else
			$gcolor='beige';

		echo '<style type="text/css"><!--
		#frame
		{
			margin: auto;
			width: ' , $gwidth , ';
		}
	 --></style>';
	}

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
			document.getElementById("upshrinkHeader").style.display = mode ? "none" : "";
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

		// ]]></script>
		<script language="JavaScript" type="text/javascript">
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

		</script>
</head>
<body>';

echo '
<div id="frame">
	<div id="top">
		<ul id="topmenu">
			<li class="first"></li>';

topmenu();

echo '	
			<li class="last"></li>
		</ul>
	<div style="padding: 4px 0 0 10px;" class="smalltext"><a href="#" onclick="shrinkHeader(!current_header); return false;">Hide/Show</a></div>
	</div>
	<div id="usersection">
		<div id="upshrinkHeader"', empty($options['collapse_header']) ? '' : ' style="display: none;"', '>
		<div id="avatar_nov">';

usersection();

echo '
		</div>
		<img src="' , $settings['images_url'] , '/png/'.$options['mycolor'].'/logo.jpg" alt="" /></div>';
}

function template_menu_above()
{
	global $context, $settings, $options, $scripturl, $txt;

	template_menu();

	echo '
	</div>';
	theme_linktree2();

	echo '
	<div id="bodyarea">';
}

function template_menu_below()
{
	global $context, $settings, $options, $scripturl, $txt;

echo '
	</div>
</div>';
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

function template_nomenu_above()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	</div>';

	echo '
	<div id="bodyarea">';
}

function template_nomenu_below()
{
	global $context, $settings, $options, $scripturl, $txt;

echo '
	</div>
</div>';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '

	<div id="footerarea">
		<div style="text-align: center;">
			<span class="smalltext">', theme_copyright(), ' <br />
				<strong>Novelty</strong> design by <a href="http.//www.blocweb.net">BlocWeb</a> |
					<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
					<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a>
			</span>
		</div>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div style="text-align: center; padding-top: 12px;" class="smalltext">', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</div>';


	echo '
</div>';


	// The following will be used to let the user know that some AJAX process is running
	echo '
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

	echo '<div class="nav" id="linktree">';

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
			echo '&nbsp;>&nbsp;';
	}

	echo '</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// add buttons here
	$context['menubox'] = array();

	/*
		'title' - the string the link will have
		'link' - the actual link
		'chosen' - which "current_action" this button belongs to.
		'memberonly' - show it JUST for members
		'guestonly' - show it JUST for guests
		'permission' - any permission you want to check before displaying the button

	*/

	// home button
	$context['menubox'][]=array(
				'title' => $txt[103],
				'link' => $scripturl,
				'chosen' => '',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);

	// help button
	$context['menubox'][]=array(
				'title' => $txt[119],
				'link' => $scripturl.'?action=help',
				'chosen' => 'help',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);


	// search button
	$context['menubox'][]=array(
				'title' => $txt[182],
				'link' => $scripturl.'?action=search',
				'chosen' => 'search',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => '',
				);

	// admin button.This one have permission check for admin as well
	$context['menubox'][]=array(
				'title' => $txt[2],
				'link' => $scripturl.'?action=admin',
				'chosen' => 'admin',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_admin',
				);

	// profile button
	$context['menubox'][]=array(
				'title' => $txt[79],
				'link' => $scripturl.'?action=profile',
				'chosen' => 'profile',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_edit_profile',
				);

	// PM button
	$context['menubox'][]=array(
				'title' => $txt['pm_short'] . ' '. ($context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : ''),
				'link' => $scripturl.'?action=pm',
				'chosen' => 'pm',
				'memberonly' => true,
				'guestonly' => false,
				'permission' => 'allow_pm',
				);

	// calendar button
	$context['menubox'][]=array(
				'title' => $txt['calendar24'],
				'link' => $scripturl.'?action=calendar',
				'chosen' => 'calendar',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_calendar',
				);

	// members
	$context['menubox'][]=array(
				'title' => $txt[331],
				'link' => $scripturl.'?action=mlist',
				'chosen' => 'mlist',
				'memberonly' => false,
				'guestonly' => false,
				'permission' => 'allow_memberlist',
				);

	// login button - just for guests
	$context['menubox'][]=array(
				'title' => $txt[34],
				'link' => $scripturl.'?action=login',
				'chosen' => 'login',
				'memberonly' => false,
				'guestonly' => true,
				'permission' => '',
				);

	// register button - just for guests
	$context['menubox'][]=array(
				'title' => $txt[97],
				'link' => $scripturl.'?action=register',
				'chosen' => 'register',
				'memberonly' => false,
				'guestonly' => true,
				'permission' => '',
				);

	// logout button - just for members
	$context['menubox'][]=array(
				'title' => $txt[108],
				'link' => $scripturl.'?action=logout;sesc='. $context['session_id'],
				'chosen' => 'logout',
				'memberonly' => true,
				'guestonly' => false,
				'permission' => '',
				);

	// now render it
	template_menu_render();
}

// the actual rendering "machine" of the menu
function template_menu_render()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '<div class="clearfix">
<ul id="menubox">';

	foreach($context['menubox'] as $button){
		$show_button=false;
		// are we logged in?
		if($context['user']['is_logged'])
			if($button['guestonly'])
				$show_button = false;
			else
				$show_button = true;
		// just a guest then...
		else
		{
			if($button['memberonly'])
				$show_button = false;
			elseif($button['guestonly'] && !$button['memberonly'])
				$show_button = true;
			elseif(!$button['memberonly'])
				$show_button = true;
		}
		// can we show the button?
         if($show_button)
         {
			if(!empty($button['permission']) && $context[$button['permission']])
				echo '<li ', $context['current_action'] == $button['chosen'] ? 'class="chosen"' : '' , '><a href="' , $button['link'] , '">' , $button['title'] , '</a></li>';
			elseif(empty($button['permission']))
				echo '<li ', $context['current_action'] == $button['chosen'] ? 'class="chosen"' : '' , '><a href="' , $button['link'] , '">' , $button['title'] , '</a></li>';
		}
	}

	echo '
	</ul></div>';

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
	global $context, $txt, $scripturl;

	echo '<li><a href="',$scripturl,'">Forum</a></li>';
	foreach($context['sitemenu'] as $menu => $val)
	{
		if($val[2]=='page')
			echo '<li><a href="',$scripturl,'?action='.$val[0].'">'.$val[1].'</a></li>';
		elseif($val[2]=='link')
			echo '<li><a href="'.$val[0].'">'.$val[1].'</a></li>';
	}
}

function usersection()
{
	global $settings, $buttons, $context, $txt, $scripturl;

echo '			
			<form action="', $scripturl,  !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ,'" style="margin: 0; padding: 0;" class="smalltext" method="post">
				<a href="javascript:ts(\'body\',1)">A+</a> 
				<a href="javascript:ts(\'body\',-1)">A-</a> 
				<a href="javascript:tsreset(\'body\')">[A]</a>';

if($context['show_widths'])
	echo '
				<input class="widthbuttons" type="submit" onmouseover="pointer: hand;" value="800PX" name="options[mywidth]" />
				<input class="widthbuttons" type="submit" value="96%" name="options[mywidth]" />
				<input class="widthbuttons" type="submit" value="80%" name="options[mywidth]" />';

echo '	</form>';

echo '<div class="smalltext" style="float: left; padding-right: 1em;">';

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
								<div style="text-align: right;">
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
	echo '</div>';

}
?>