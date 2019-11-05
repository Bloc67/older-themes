<?php

/**
 * @package Blocthemes Admin
 * @version 1.2
 * @theme ShelfLife
 * @author Blocthemes - http://demos.bjornhkristiansen.com
 * Copyright (C) 2014-2016 - Blocthemes
 *
 */

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt, $modSettings;
	$settings['bloctheme'] = 'ShelfLife';
	$settings['bloctheme_version'] = '1.2';

	$settings['use_default_images'] = 'never';
	$settings['doctype'] = 'html';
	$settings['theme_version'] = '2.0';
	$settings['use_tabs'] = false;
	$settings['use_buttons'] = true;
	$settings['separate_sticky_lock'] = true;
	$settings['strict_doctype'] = false;
	$settings['message_index_preview'] = true;
	$settings['require_theme_strings'] = true;
	$settings['themecopyright'] = '<b>ShelfLife</b> &copy; <a href="https://www.bjornhkristiansen.com/smf21/index.php?board=6">2015-2017, Bloc</a>';
}

function template_html_above() 
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?fin20" />
	' , ($context['right_to_left']) ? '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/css/rtl.css" />' : '' , '';
	
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var alpha_themeId = "', $settings['theme_id'], '";
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
	// ]]></script>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="apple-touch-icon" sizes="72x72" href="touch-icon-ipad.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="touch-icon-iphone4.png" />
	<link rel="apple-touch-startup-image" href="startup.png" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />	
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>' , !empty($context['robot_no_index']) ?
	'<meta name="robots" content="noindex" />' : '' , !empty($context['canonical_url']) ?
	'<link rel="canonical" href="'. $context['canonical_url']. '" />' : '' , '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />', (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged'])) ?
	'<link rel="alternate" type="application/rss+xml" title="'. $context['forum_name_html_safe']. ' - '. $txt['rss']. '" href="'. $scripturl . '?type=rss;action=.xml" />' : '' , '
	' , (!empty($context['current_topic'])) ? '
	<link rel="prev" href="'. $scripturl. '?topic='. $context['current_topic']. '.0;prev_next=prev" />
	<link rel="next" href="'. $scripturl. '?topic='. $context['current_topic']. '.0;prev_next=next" />' : '';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	if(isset($settings['csstop']) && !empty($settings['csstop']))
		echo '
	<style type="text/css">
		#ftop { background: url(' , $settings['csstop'] , ') #fff no-repeat top left; }
	</style>';
	elseif(isset($settings['csstop']) && empty($settings['csstop']))
		echo '
	<style type="text/css">
		#ftop { background: #fff; padding-top: 8px; }
	</style>';
	
	if(!empty($settings['cssbg']))
		echo '
	<style type="text/css">
		html { background-image: url(' , $settings['cssbg'] , '); }
	</style>';

	if(!empty($settings['boardicon']))
		echo '
	<style type="text/css">
.board_icon {
	background-image: url(' , $settings['boardicon'], ');
}
</style>';

	if(!empty($settings['portalfullwidth']))
		echo '
	<style type="text/css">
		body { width: 99%;  }
		#fwidth { width: 99%; }
	</style>';

	echo '
</head>
<body>';
}

function template_body_above() 
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	<div id="fwidth">';

	if ($context['user']['is_logged'] && $context['in_maintenance'] && $context['user']['is_admin'])
		echo '
		<div id="fnotice" class="themepadding">' , $txt['maintain_mode_on'] , '</div>';

	echo '
		<div id="ftop">
			<div id="fheader"><div class="bwgrid">
				<div class="bwcell6"><div class="themepadding">
					<a href="' , $scripturl , '"><img src="' , !empty($settings['header_logo_url']) ? $settings['header_logo_url'] : $settings['images_url'].'/theme/logo.png' , '" style="max-width: 100%;" alt="*" /></a>
					' , !empty($settings['site_slogan']) ? '<div class="subtitle">' . $settings['site_slogan'] . '</div>' : '' , '
				</div></div>
				<div class="bwcell10"><div class="themepadding">
					<div id="qsearch" class="bwfloatright">
						<form id="search_form" class="floatright" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
							<input type="text" name="search" value="" id="qsearch_search" class="input_text" />
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
					</div>';

	if(!empty($settings['enable_news']))
		echo '<div id="qnews" class="floatright">' , $txt['news'], ': ', $context['random_news_line'], '</div>';

	echo '	<br><br>
				</div></div>
			</div></div>
		</div>
		<div id="fcontent">
			<div id="fmenu">';			
	
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
				<div class="greeting">';

		if(!empty($context['user']['avatar']))
			echo '
					<div class="roundavatar" style="float: left; vertical-align: 50%; ">', $context['user']['avatar']['image'], '</div>';

		echo '
					<div class="floatleft">
						<ul class="horiz_list floatleft">
							<li><a class="', $context['current_action']=='profile' ? 'active ' : '', '" href="', $scripturl, '?action=profile"><strong style="text-transform: uppercase;">', $context['user']['name'], '</strong></a></li>
							<li><a class="', $context['current_action']=='unread' ? 'active ' : '', '" href="', $scripturl, '?action=unread">', $txt['o_unread'], '</a></li>
							<li><a class="', $context['current_action']=='unreadreplies' ? 'active ' : '', '" href="', $scripturl, '?action=unreadreplies">', $txt['o_replies'], '</a></li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
							<li><a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $txt['o_approve'] . '</a><em class="dark_notice">' . $context['unapproved_members'], '</em></li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
							<li><a href="', $scripturl, '?action=moderate;area=reports">', $txt['o_reports'] , '</a><em class="dark_notice">' , $context['open_mod_reports'], '</em></li>';

		// finally, add in the ones we left out
		foreach ($context['menu_buttons'] as $act => $button)
		{
			if(!in_array($act, array('profile','pm')))
				continue;
			
			echo '
							<li><a class="', $context['current_action']==$act ? 'active ' : '', '" href="', $button['href'], '">', $button['title'], '</a></li>';
		}
		echo '
						</ul>
					</div>
				</div>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" class="themepadding" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div class="info">', sprintf($txt['welcome_guest'], $txt['guest_title']), '</div>
					<input type="text" name="user" size="10" class="input_text" />
					<input type="password" name="passwrd" size="10" class="input_password" />
					<select name="cookielength">
						<option value="60">', $txt['one_hour'], '</option>
						<option value="1440">', $txt['one_day'], '</option>
						<option value="10080">', $txt['one_week'], '</option>
						<option value="43200">', $txt['one_month'], '</option>
						<option value="-1" selected="selected">', $txt['forever'], '</option>
					</select>
					<input type="submit" value="', $txt['login'], '" class="button_submit" /><br />
					<div class="info">', $txt['quick_login_dec'], '</div>';

		if (!empty($modSettings['enableOpenID']))
			echo '
					<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</form>';
	}
	echo '	<div style="clear: both;" id="fmainmenu">' , template_menu() , '</div>
			</div>
			<div id="fbody">
				<div class="themepadding">' , theme_linktree() , '</div>';
}


function template_body_below() 
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
			</div>
		</div>
		<div id="fbottom" class="themepadding">
			<div class="bwgrid">
				<div class="bwcell8">
					' , theme_copyright() , '
					' , $context['show_load_time'] ? '<br><span style="font-size: 85%;">'. $txt['page_created']. $context['load_time']. $txt['seconds_with']. $context['load_queries']. $txt['queries']. '</span>' : '' , '
				</div>
				<div class="bwcell8"><div class="bwfloatright">
					' , $settings['themecopyright'] , '
				</div></div>
			</div>
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
		<ul class="horiz_list wbutton">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo '<span class="whide">'.$tree['extra_before'].'</span>';

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo '<span class="whide">'.$tree['extra_after'].'</span>';

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' <span class="whide">&#187;</span>';

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
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', !empty($button['active_button']) ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						', $button['title'], '
					</a>';

		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';
				
			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a class="', !empty($childbutton['active_button']) ? 'active ' : '', 'firstlevel" href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								', $childbutton['title'], '
							</a>
						</li>';
			}
			echo '
					</ul>';
		}
		echo '
				</li>';
	}
	echo '</ul>
		</div>
';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array(), $hit_only = false)
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

function my_toggle($catid, $area)
{
	global $settings, $options, $context;
	
	echo '
	<img src="' , $settings['images_url'] , '/' , !empty($options['togglecat' . $catid]) ? 'expand' : 'collapse' , '.gif" alt="toggle" class="toggleme" id="toggleme_' , $catid , '" style="vertical-align: middle;" onclick="togglecategory(this.id, \'' . $area . '\', \'togglecat' . $catid . '\', \'' . $context['session_id'] . '\',\'' . $context['session_var']. '\');" 	/>';
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