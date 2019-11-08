<?php

/**
 * @package Blocthemes Admin
 * @version 1.0
 * @theme AlphaCentauri
 * @author Blocthemes - http://www.blocthemes.net
 * Copyright (C) 2014 - Blocthemes
 *
 */

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	$settings['bloctheme'] = 'AlphaCentauri';
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
	<link href="http://fonts.googleapis.com/css?family=Baumans" rel="stylesheet" type="text/css">
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

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	<div id="themeframe">
		<div id="topframe">
			<div class="forumwidth">' , template_menu() , '</div>
		</div>
		<div id="top_content" class="forumwidth">
			', template_titlebox(), template_newsbox(), '
		</div>
		<div id="full_content" class="forumwidth">';
	
	template_userbox();
	template_linktreebox();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

		echo '
		</div>
		<div id="full_footer" class="forumwidth">';
	
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
	<div id="menuHolder">
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
				<li class="first', $childbutton['active_button'] ? ' topactive ' : '', '">
					<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', ' class="', $childbutton['active_button'] ? 'active ' : '', 'firstlevel">
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
					<div id="socialbox" class="floatright">'; 
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['facebook_link']))
		echo '
						<a id="fb" href="' , $settings['facebook_link'] , '" title="' , $settings['facebook_link'] , '"> </a>';
	if (!empty($settings['pinterest_link']))
		echo '
						<a id="pi" href="' , $settings['pinterest_link'] , '" title="' , $settings['pinterest_link'] , '"> </a>';
	if (!empty($settings['twitter_link']))
		echo '
						<a id="tw" href="' , $settings['twitter_link'] , '" title="' , $settings['twitter_link'] , '"> </a>';

	echo '
						<a id="rs" href="' , $scripturl, '?action=.xml;type=rss" title="' , $scripturl, '?action=.xml;type=rss"> </a>';

	echo '		</div>
					<h1 id="maintitle" class="floatleft">
						<a href="', $scripturl, '"><img src="', !empty($settings['headerlogo']) ? $settings['headerlogo'] : $settings['images_url'].'/theme/logo_light.png' , '" alt="' . $context['forum_name'] . '" /></a>
					</h1>
				</div>';
}

function template_userbox()
{
	global $settings, $context, $txt, $scripturl;

	echo '<div class="titlebg" style="overflow: hidden; ">
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

	if ($context['user']['is_logged'])
	{
		if (!empty($context['user']['avatar']))
			echo '
						<a href="', $scripturl, '?action=profile"><img src="', $context['user']['avatar']['href'], '" alt="" id="topavatar"  /></a>';
		echo '
							<strong><a href="', $scripturl, '?action=profile">', $context['user']['name'], '</a></strong><br>
							<a href="', $scripturl, '?action=logout;' . $context['session_var'] . '=' . $context['session_id'] . '">', $txt['logout'], '</a> &nbsp;|&nbsp;
							<a href="', $scripturl, '?action=unread">', $txt['short_unread'], '</a> &nbsp;|&nbsp;
							<a href="', $scripturl, '?action=unreadreplies">', $txt['short_replies'], '</a>';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
							&nbsp;|&nbsp; ', $txt['short_main'];

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
							&nbsp;|&nbsp; <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve"> ' . $txt['short_approve'], '</a>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
							&nbsp;|&nbsp; <a href="', $scripturl, '?action=moderate;area=reports">', $txt['short_approve'], ' </span></a>';

	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	else
		echo '
							<a href="', $scripturl, '?action=login"><strong>', $txt['login'], '</strong></a>
							&nbsp;|&nbsp; <a href="', $scripturl, '?action=register">', $txt['register'], '</a>
						</ul>';
	
	echo '
					</div>';
}

function template_newsbox()
{
	global $settings, $context, $txt, $scripturl;
	
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
			<p id="full_newsbox"><strong>', $txt['news'], ': </strong>', $context['random_news_line'],'</p>';
}

function template_linktreebox() 
{  
	echo '
				<div id="full_linktreebox">',	theme_linktree(), '</div>';
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
	'',
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
	'',
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
	'',
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