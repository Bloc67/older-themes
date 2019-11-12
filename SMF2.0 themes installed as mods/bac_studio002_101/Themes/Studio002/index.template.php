<?php

/**
 * @package Blocthemes Admin
 * @version 1.0.2
 * @theme Studio002
 * @author Blocthemes - http://www.blocthemes.net
 * Copyright (C) 2014 - 2016 Blocthemes
 *
 */

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	$settings['bloctheme'] = 'Studio-002';
	$settings['bloctheme_version'] = '1.01';

	$settings['use_default_images'] = 'never';
	$settings['doctype'] = 'xhtml';
	$settings['theme_version'] = '2.0';
	$settings['use_tabs'] = false;
	$settings['use_buttons'] = true;
	$settings['separate_sticky_lock'] = true;
	$settings['strict_doctype'] = false;
	$settings['message_index_preview'] = false;
	$settings['require_theme_strings'] = true;
}

function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '' , '>
<head>
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta id="viewport" name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">

	<title>', $context['page_title_html_safe'], '</title>
	<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->	
	';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	if(!empty($settings['backpicture']))
		echo '
	<style type="text/css">
		body {	background-image: url(' . $settings['backpicture'] . '); }
	</style>';

	if(!empty($settings['newspicture']))
		echo '
	<style type="text/css">
		#full_newsbox { background: url(' . $settings['newspicture'] . ') 50% 50%; border: none; }
	</style>';

	if(!empty($settings['header_bgcolor']))
	{
		if(hexdec($settings['header_bgcolor'])>12303290)
			echo '
	<style type="text/css">
.titlebg, .titlebg2, tr.titlebg th, tr.titlebg td, tr.titlebg2 td,
.catbg, .catbg2, tr.catbg td, tr.catbg2 td, tr.catbg th, tr.catbg2 th, 
.titlebg a, .titlebg2 a, .catbg a, .catbg2 a,
tr.catbg th a:link, tr.catbg th a:visited, tr.catbg2 td a:link, tr.catbg2 td a:visited {	color: #222; }
	</style>';

		echo '
	<style type="text/css">
.titlebg, .titlebg2, tr.titlebg th, tr.titlebg td, tr.titlebg2 td,
.catbg, .catbg2, tr.catbg td, tr.catbg2 td, tr.catbg th, tr.catbg2 th {
	background: ' . $settings['header_bgcolor'] . '; 
}
	</style>';
	}
	
	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<div class="wrapper">
	<div id="themeframe"><div id="socialbox" class="forumwidth">' , template_socialbox() , '</div><div id="topframe" class="forumwidth">
		<div id="top_content">';
	
	if(empty($settings['headerboxes']))
		$header_boxes = array('titlebox','newsbox','menubox','linktreebox');
	else
	{
		$box = array(
			'titlebox,newsbox,menubox,linktreebox',
			'titlebox,menubox,newsbox,linktreebox',
			'titlebox,menubox,linktreebox,newsbox',
			'newsbox,titlebox,menubox,linktreebox',
			'newsbox,titlebox,linktreebox,menubox',
			'newsbox,titlebox,menubox,linktreebox',
			'menubox,titlebox,newsbox,linktreebox',
			'menubox,titlebox,linktreebox,newsbox',
		);
		$header_boxes = explode(",",$box[$settings['headerboxes']]);
	}
	foreach($header_boxes as $module)
		call_user_func('template_'.$module);

	echo '
		</div>
		<div id="full_content">';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

		echo '
		</div>
		<div id="full_footer">';
	
	if(!empty($settings['own_copy_above']) && !empty($settings['own_copy']))
		echo '
			<div>' , $settings['own_copy'] , '</div>';

	theme_copyright();

	// Show the load time?
	if ($context['show_load_time'])
		echo '
			<div>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</div>';

	echo '
			' , !empty($settings['hide_theme_copy']) ? '<!--' : '' , '<div><a href="http://www.blocthemes.net" target="_blank"><b>' , $settings['bloctheme'] , '</b> design by blocthemes</a></div>' , !empty($settings['hide_theme_copy']) ? '-->' : '' ;

	if(empty($settings['own_copy_above']) && !empty($settings['own_copy']))
		echo '
			<div>' , $settings['own_copy'] , '</div>';
	
	echo '	
		</div>
	</div></div>
	</div>';
}


function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &#187;';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div class="menuHolder">
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '" class="first', $button['active_button'] ? ' topactive ' : '', '">
					<a  id="but_', $act, '" class="', $button['active_button'] ? 'active ' : '', 'firstlevel" title="' , $button['title'] , '" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>
				</li>';
	}

	echo '
			</ul>
		</div>
			';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		if(!$button['active_button'])
			continue; 
		
		if (!empty($button['sub_buttons']))
		{
			echo '
		<div id="sub_menu">	
			<ul class="dropmenu" id="menu_nav_subs">';
			$first = true;
			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
				<li class="first', isset($childbutton['active_button']) && $childbutton['active_button'] ? ' topactive ' : '', '">
					<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', ' class="', isset($childbutton['active_button']) && $childbutton['active_button'] ? 'active ' : '', 'firstlevel">
						<span class="firstlevel">', $childbutton['title'], '</span>
					</a>
				</li>';
			}
			echo '
			</ul>
		</div>';
		}
	}

	echo '
	</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	$buttons = array();
	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
		
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

function template_titlebox()
{
	global $settings, $context, $txt, $scripturl;

	echo '
				<div id="full_titlebox">
					<h1 id="maintitle" class="floatleft">
						<a href="', $scripturl, '"><img src="', !empty($settings['headerlogo']) ? $settings['headerlogo'] : $settings['images_url'].'/theme/logo_dark.png' , '" alt="' . $context['forum_name'] . '" /></a>
					</h1>
					<div class="floatright" id="userbox">';

	if ($context['user']['is_logged'])
	{
		if (!empty($context['user']['avatar']))
			echo '
						<a href="', $scripturl, '?action=profile"><img src="', $context['user']['avatar']['href'], '" alt="" id="topavatar" class="floatright" /></a>';
		echo '
						<ul class="reset floatright">
							<li class="greeting"><strong><a href="', $scripturl, '?action=profile">', $context['user']['name'], '</a></strong></li>
							<li><a href="', $scripturl, '?action=logout;' . $context['session_var'] . '=' . $context['session_id'] . '">', $txt['logout'], '</a></li>
							<li><a href="', $scripturl, '?action=unread">', $txt['short_unread'], '</a></li>
							<li><a href="', $scripturl, '?action=unreadreplies">', $txt['short_replies'], '</a></li>';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
							<li class="notice">', $txt['short_main'], '</li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
							<li><a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve"> ' . $txt['short_approve'], '</a></li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
							<li><a href="', $scripturl, '?action=moderate;area=reports">', $txt['short_approve'], ' </span></a></li>';

		echo '
						</ul>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	else
		echo '
						<ul class="reset">
							<li><a href="', $scripturl, '?action=login"><strong>', $txt['login'], '</strong></a></li>
							<li><a href="', $scripturl, '?action=register">', $txt['register'], '</a></li>
						</ul>';
	
	echo '
					</div>
				</div>';
}

function template_newsbox()
{
	global $settings, $context, $txt, $scripturl;
	
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
				<div id="full_newsbox" class="gradient01">
					<strong>', $txt['news'], ': </strong>', $context['random_news_line'], '
				</div>';
}

function template_socialbox()
{
	global $settings, $context, $txt, $scripturl;
	
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['facebook_link']))
		echo '
		<a id="fb" href="' , $settings['facebook_link'] , '"> </a>';
	if (!empty($settings['pinterest_link']))
		echo '
		<a id="pi" href="' , $settings['pinterest_link'] , '"> </a>';
	if (!empty($settings['twitter_link']))
		echo '
		<a id="tw" href="' , $settings['twitter_link'] , '"> </a>';

	echo '
		<a id="rs" href="' , $scripturl, '?action=.xml;type=rss"> </a>';
}

function template_menubox() 
{ 
	echo '
				<div id="full_menubox">', template_menu(), '</div>';
}

function template_linktreebox() 
{  
	global $settings, $context, $txt, $scripturl;
	
	echo '
				<div id="full_linktreebox">
					<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" class="floatright">
						<input type="text" name="search" value="" class="input_text" />
						<input type="submit" name="submit" value=" " class="button_submit" />
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
	
	theme_linktree(); 
	
	echo '
				</div>';
}

function template_btoptions()
{
	global $context, $settings,$txt;

	$opts = array(
		array(
			'id' => 'headerlogo',
			'label' => 'Logo',
			'type' => 'text',
			'image' => 1,
		),
		array(
			'id' => 'headerlogo_upload',
			'label' => 'Upload new logo',
			'type' => 'upload',
			'linked_id' => 'headerlogo',
		),
		array(
			'id' => 'backpicture',
			'label' => 'Background picture',
			'type' => 'text',
			'image' => 150,
		),
		array(
			'id' => 'backpicture_upload',
			'label' => 'Upload new background',
			'type' => 'upload',
			'linked_id' => 'backpicture',
		),
		array(
			'id' => 'newspicture',
			'label' => 'News background picture',
			'type' => 'text',
			'image' => 150,
		),
		array(
			'id' => 'newspicture_upload',
			'label' => 'Upload a news background picture',
			'type' => 'upload',
			'linked_id' => 'newspicture',
			'description' => '&nbsp;- Recomended width/height: 1400x250px',
		),
		array(
			'id' => 'header_bgcolor',
			'label' => 'Titlebars background color',
			'type' => 'colorselect',
		),
		array(
			'id' => 'facebook_link',
			'label' => 'Facebook link',
			'type' => 'text',
		),
		array(
			'id' => 'pinterest_link',
			'label' => 'Pinterest link',
			'type' => 'text',
		),
		array(
			'id' => 'twitter_link',
			'label' => 'Twitter link',
			'type' => 'text',
		),
		array(
			'id' => 'headerboxes',
			'label' => $txt['headerboxes'],
			'options' => array(
				0 => $txt['headerbox1'],
				1 => $txt['headerbox2'],
				2 => $txt['headerbox3'],
				3 => $txt['headerbox4'],
				4 => $txt['headerbox5'],
				5 => $txt['headerbox6'],
				6 => $txt['headerbox7'],
				7 => $txt['headerbox8'],
			),
			'type' => 'list',
		),
		array(
			'id' => 'own_copy',
			'label' => 'Add your own copyright.',
			'description' => '&nbsp;- Use of HTML codes is allowed.',
			'type' => 'text',
		),
		array(
			'id' => 'own_copy_above',
			'label' => 'Place your own copyright above the SMF copyright',
			'description' => '&nbsp;- default is below',
			'type' => 'checkbox',
		),
		array(
			'id' => 'hide_theme_copy',
			'label' => 'Hide theme copyright',
			'description' => '&nbsp;- Its still in the source, commented out.',
			'type' => 'checkbox',
		),
	);
	show_btsettings($opts);
}

?>