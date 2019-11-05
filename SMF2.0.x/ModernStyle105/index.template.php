<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	$settings['use_default_images'] = 'never';
	$settings['doctype'] = 'xhtml';
	$settings['theme_version'] = '2.0';
	$settings['use_tabs'] = true;
	$settings['use_buttons'] = true;
	$settings['separate_sticky_lock'] = true;
	$settings['strict_doctype'] = false;
	$settings['message_index_preview'] = true;

	$settings['internal_ver'] = '1.0.5';
	
	$settings['require_theme_strings'] = true;
	$settings['extra_copyrights'] = array('<a title="version v' . $settings['internal_ver'] . '" href="http://demos.bjornhkristiansen.com">ModernStyle theme by Bloc</a>');
	$settings['catch_action'] = array('layers' => array('html','body','pages'));

	
	$settings['theme_variants'] = array('blue','light', 'dark','grey','haze','forest');
	$settings['googleFonts'] = array(
			'_light' => 'Poppins:400,700&subset=latin,latin-ext',
			'_dark' => 'Raleway:400,700&subset=latin,latin-ext',
			'_blue' => 'Arimo:400,700&subset=latin,latin-ext',
			'_grey' => 'Poppins:400,700&subset=latin,latin-ext',
			'_haze' => 'Arimo:400,700&subset=latin,latin-ext',
			'_forest' => 'Raleway:400,700&subset=latin,latin-ext',
	);
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	if(empty($context['theme_variant']))
		$context['theme_variant'] = '_haze';

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link href="https://fonts.googleapis.com/css?family=', !empty($context['theme_variant']) ? $settings['googleFonts'][$context['theme_variant']] : 'Poppins:400,700&subset=latin,latin-ext' , '" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?fin20" />';

	if(!empty($context['theme_variant']))
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index' , $context['theme_variant'] , '.css?fin20" />';

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
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', empty($context['page_title_html_safe']) ? $context['forum_name'] : $context['page_title_html_safe'] , '</title>';

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

	if(!empty($settings['fbodyheight']))
		echo '
	<style>
		#fbody { margin-top: ' , $settings['fbodyheight'] , 'em; }
	</style>';

	echo '
</head>';

	if(!empty($context['page_index']))
	{
		$context['page_index'] = convertpages($context['page_index']);
		$txt['pages'] = '';
	}
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl;

	echo '
<body class="msfullwidth"><iframe id="myhelp" src="#top"></iframe>
	<div id="fwidth">
		<div id="ftop" class="ftop">
			<div id="fheader">
				<div class="themepadding" style="overflow: hidden;">
					<div class="bwgrid">
						<div class="bwcell5">
							<h1><a href="' , $scripturl , '"><img src="' , !empty($settings['header_logo_url']) ?  $settings['header_logo_url'] : $settings['images_url'].'/theme/logo' .$context['theme_variant']. '.png' , '" style="max-width: 100%; " alt="" />&nbsp;</a></h1>
						</div>
						<div class="bwcell11">' , template_menu(), '</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clear" id="fbody">';
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div><br>
		<div id="fbottom">
			<div class="themepadding">
				<div class="bwgrid">
					<div class="bwcell8">
					' , theme_copyright();

		// Show the load time?
	if ($context['show_load_time'])
			echo '
						| ' , $context['load_time'] . ' (sql ' . $context['load_queries'] . ')';
	echo '		
					</div>
					<div class="bwcell8"><div class="bwfloatright">
						' , !empty($settings['extra_copyrights']) ? implode("<br>", $settings['extra_copyrights']) : '' , '
					</div></div>
				</div>
			</div>
		</div>
	</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<script type="text/javascript"><!-- // --><![CDATA[

	var header             = document.getElementById(\'ftop\'),
    header_height      = \'5\',
    fix_class          = \'ftopfixed\';
    split_class          = \'\';

	function stickyScroll(e) {
	  if( window.pageYOffset > header_height ) {
		header.className = fix_class;
	  }
	  if( window.pageYOffset < header_height ) {
		header.className = \'ftop\';
	  }
	}

	// Scroll handler to toggle classes.
	window.addEventListener(\'scroll\', stickyScroll, false);

// ]]></script>
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
		<ul  id="linktree">';

	$first = true;
	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li>';

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"' . (count($context['linktree']) <2 ? ' class="single"' : '') . '><span class="iconright"><span class="icon-' . ($first ? 'home' : 'right') . '"></span></span><span class="linktreename">' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		echo '
			</li>';
		$first = false;
	}
	echo '
		</ul><div style="clear: both;"></div>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	if(!empty($settings['typedivide']))
	{
		template_menu_divide();
		return;
	}
	
	echo '
<div class="flow_hidden">
	<div id="mmenu_desktop">
	<ul class="horiz_list toplist floatright">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
		<li><a href="' . $button['href'] . '">' , $button['active_button'] ? '<b>'.$button['title'].'</b>' : $button['title'] , !empty($button['sub_buttons']) ? ' <span class="arrow_more"></span>' : '' ,'</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
			<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
				<li>
					<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
						<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
					</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
					<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
						<li>
							<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
								<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
							</a>
						</li>';

					echo '
					</ul>';
				}

				echo '
				</li>';
			}
			echo '
			</ul>';
		}
		echo '
		</li>';
	}
	echo '
		</ul>
	</div>
	<ul class="roundlist" id="mmenu_touch">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
		<li><a id="mmtouch_'.$act.'" href="' . $button['href'] . '"' , $button['active_button'] ? ' class="active"' : '' , '><span class="icon-'.$act.'"><span>'. substr($button['title'],0,1).'</span></span></a></li>';
	}

	echo '
	</ul>
</div>';
}

// divided menu..
function template_menu_divide()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
<div class="flow_hidden">
	<div id="mmenu_desktop">
	<ul class="horiz_list toplist floatright">';

	// first, get home/forum buttons
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
		<li><a href="' . $button['href'] . '">' , $button['active_button'] ? '<b>'.$button['title'].'</b>' : $button['title'] , !empty($button['sub_buttons']) ? ' <span class="arrow_more"></span>' : '' ,'</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
			<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
				<li>
					<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
						<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
					</a>
				</li>';
			}
			echo '
			</ul>';
		}
		echo '
		</li>';
	}
	
	echo '
		</ul>
	</div>
	<ul class="roundlist" id="mmenu_touch">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
		<li><a id="mmtouch_'.$act.'" href="' . $button['href'] . '"' , $button['active_button'] ? ' class="active"' : '' , '><span class="icon-'.$act.'"><span>'. substr($button['title'],0,1).'</span></span></a></li>';
	}

	echo '
	</ul>
</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array(), $nodiv = false, $nolist = false)
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
				<a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);
	
	if(!$nodiv)
		echo '
		<div ', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul class="horiz_list toplist greytext"><li>',
				implode('</li><li>', $buttons), '</li>
			</ul>
		</div>';
	else
	{
		if(!$nolist)
			echo '
			<ul class="horiz_list toplist greytext"', (empty($buttons) ? ' style="display: none;"' : ''), '><li>',
				implode('</li><li>', $buttons), '</li>
			</ul>';
		else
			echo '
			<span class="horiz_list toplist greytext"', (empty($buttons) ? ' style="display: none;"' : ''), '>',
				implode('', $buttons), '
			</span>';
	}
}

function convertpages($string)
{
	$string = str_replace(array('[',']'),array('',''), $string);
	return '<span class="pagelinks">'.$string.'</span>';
}

function checkextension($name)
{
	$what = substr($name, strlen($name)-3, 3);
	if(in_array($what, array('txt','doc','rtf')))
		return 'icon-file-word-o';
	elseif($what == 'pdf')
		return 'icon-file-pdf-o';
	elseif(in_array($what, array('xml','xls')))
		return 'icon-file-excel-o';
	else
		return 'icon-paperclip';

}


?>