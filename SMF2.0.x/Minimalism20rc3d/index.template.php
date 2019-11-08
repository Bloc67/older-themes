<?php
// Version: 2.0 RC3; index

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
	$settings['theme_version'] = '2.0 RC3';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = true;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
	loadtemplate('ProteusFunctions');
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	loadlanguage('ThemeStrings');

	if(empty($settings['themealias'])) 
		$settings['themealias'] = 'facebook'; 

	// quickoptions
	if(isset($_GET['blue']))
		$settings['themealias'] = 'chat'; 
	if(isset($_GET['green']))
		$settings['themealias'] = 'facebook'; 

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// The ?rc3 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/grid.css" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?rc3" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/print.css?rc3" media="print" />';

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

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?rc3"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?rc3"></script>
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

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
<script type="text/javascript">
	function showme(searchClass,more)
	{
        var  classElements = new Array();
		node = document;
		tag = \'li\';
        var els = node.getElementsByTagName(tag);
        var elsLen = els.length;
        var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
        for (i = 0, j = 0; i < elsLen; i++) {
                if ( pattern.test(els[i].className) ) {
                        els[i].style.display = \'\';
                }
        }
		document.getElementById(more).style.display= \'none\';
	}
	function togglesub(id)
	{
		if(document.getElementById(\'spp\').style.display == \'none\')
		{
			document.getElementById(\'sub\' + id).style.display= \'\';
			document.getElementById(\'spp\').style.display= \'\';
			document.getElementById(\'a\' + id).setAttribute("class", "active");		
		}
		else
		{
			document.getElementById(\'sub\' + id).style.display= \'none\';
			document.getElementById(\'spp\').style.display= \'none\';
			document.getElementById(\'a\' + id).setAttribute("class", "inactive");		
		}
	}
	function togglesubb(id, mess)
	{
		if(document.getElementById(\'spp2\' + mess).style.display == \'none\')
		{
			document.getElementById(\'sub2\' + mess + \'_\' + id).style.display= \'\';
			document.getElementById(\'spp2\' + mess).style.display= \'\';
			document.getElementById(\'a2\' + mess + \'_\' + id).setAttribute("class", "active");		
			document.getElementById(\'postmodify\').focus();
			document.getElementById(\'postmodify\').scrollTop = document.getElementById(\'postmodify\').scrollHeight;
		}
		else
		{
			document.getElementById(\'sub2\' + mess + \'_\' + id).style.display= \'none\';
			document.getElementById(\'spp2\'+mess).style.display= \'none\';
			document.getElementById(\'a2\' + mess + \'_\' + id).setAttribute("class", "inactive");		
		}
	}
	function toggleb(id)
	{
		if(document.getElementById(\'cb\' + id).style.display == \'none\')
		{
			document.getElementById(\'cb\' + id).style.display= \'\';
			setInnerHTML(document.getElementById(\'a\' + id), \'&nbsp;-\');
		}
		else
		{
			document.getElementById(\'cb\' + id).style.display= \'none\';
			setInnerHTML(document.getElementById(\'a\' + id), \'+\');
		}
	}
</script>
<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/editor.js?rc3"></script>
<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/minimalism_'.$settings['themealias'].'.css?rc3" />';
	
	if(!empty($settings['forum_width']))
		echo '
	<style type="text/css">
		.innerwidth
		{
			width: ' . $settings['forum_width'] . '
		}
	</style>';

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $settings;
	call_user_func('index_'.$settings['themealias']);
}

function index_facebook()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<div id="topframe">
	<div class="innerwidth">';

	$menu = $context['menu_buttons'];
	foreach($menu as $m => $me)
	{
		if(!in_array($m, array('search','mlist','profile','media','pm')) )
			unset($menu[$m]);
		if(in_array($m, array('pm','media')))
			$menu[$m]['title'] = str_replace(array('[',']'), array('<span class="markbubble"><span>','</span></span>'),$me['title']);
	}
	echo '
		<div id="topmenu_container">' , theme_menu($menu, 'topmenu') , '</div>
		<h1 id="proh1">';		

	if (!empty($context['user']['avatar']))
		echo '
			<span class="avatar40h"><img src="', $context['user']['avatar']['href'], '" alt="" /></span>';

	echo '<a href="'.$scripturl.'">' , $context['forum_name'] , '</a>
		</h1>
	</div>
</div>
<div id="naviframe">
	<div class="innerwidth">
		<div id="ltree">' , linktree() , '</div>';
	
	if(!empty($settings['useportal']))
	{
		$menu = $context['menu_buttons'];
		foreach($menu as $m => $me)
		{
			if(in_array($m, array('pm','media')))
				$menu[$m]['title'] = str_replace(array('[',']'), array('<span class="markbubble"><span>','</span></span>'),$me['title']);
		}
		theme_menu($menu, 'portalmenu');		
	}
	echo '
	</div>
</div>
<div id="contentframe">
	<div class="innerwidth">
		<div class="container">';

	if(empty($settings['useportal']))
		cell();

	echo '
			<div class="col' , empty($settings['useportal']) ? '12' : '16' , '"><div style="padding-' , empty($settings['sidebarpos']) ? 'left' : 'right' , ': ' , !empty($settings['useportal']) ? '0' : '3' , 'em;">';

}

function index_chat()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<div id="topframe">
	<div class="innerwidth">
		<div id="topmenu_container">';
	if($context['user']['is_logged'])	
		echo $txt['hello_member']. $context['user']['name']. ' ( '.$context['user']['messages'].' / '.$context['user']['unread_messages'].' ' .$txt['newmessages1']. ' )';
	else
		echo $txt['login_or_register'];

	echo '
		</div>
		<h1 id="proh1"><a href="'.$scripturl.'">' , $context['forum_name'] , '</a></h1>
	</div>
</div>
<div id="naviframe">
	<div class="innerwidth">
		<div style="text-transform: uppercase;">' , theme_menu($context['menu_buttons'], 'portalmenu') , '</div>
	</div>
</div>
<div id="contentframe">
	<div class="innerwidth">
		<div id="whosblock">' , mini_whosblock_chat() , '</div>
		<div id="ltree">' , linktree() , '</div>
		<div style="padding: 1em 0.5em;">';
}


function template_body_below()
{
	global $settings;
	call_user_func('index_below_'.$settings['themealias']);
}

function index_below_facebook()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
			</div>
		</div>		
	</div>
</div>
<div id="footframe">
	<div class="innerwidth">
	' , 	theme_copyright() , '
	<div><b>Minimalism</b>[FB] design by <a href="http://www.blocweb.net">Bloc</a></div>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
	</div>
</div>';
}

function index_below_chat()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div>
</div>
<div id="footframe">
	<div class="innerwidth">
	' , 	theme_copyright() , '
	<div><b>Minimalism</b>[CH] design by <a href="http://www.blocweb.net">Bloc</a></div>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
	</div>
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body>
</html>';
}

function theme_linktree() { return; }

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) )
		return;

	$tre = array(); 
	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		$item = '';
		// Show something before the link?
		if (isset($tree['extra_before']))
			$item .= $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		$item .=  ($settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>');

		// Show something after the link...?
		if (isset($tree['extra_after']))
			$item .= $tree['extra_after'];

		$tre[] = $item;
		$context['mini-last'] = $tree['name']; 
	}
	echo implode(' / ', $tre);
}

function theme_menu($menu, $id)
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
<div style="overflow: hidden;">
	<ul id="'.$id.'">';

	foreach ($menu as $act => $button)
	{
		echo '
		<li>
			<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
				<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
			</a>';
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
							<a', $grandchildbutton['active_button'] ? ' class="active"' : '', ' href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
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
</div>';
}
function cell()
{
	
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
			<div class="col4" >
				<div id="usercell">
					<h2 style="margin-bottom: 6px;">' , $context['user']['is_logged'] ? $txt['hello_member_ndt']. ' <span>'. $context['user']['name'].'</span>' :  $txt['guest'] , '</h2>';
	
	$menu = $context['menu_buttons'];
	foreach($menu as $m => $me)
	{
		if(in_array($m, array('search','home','help','mlist','profile')) )
			unset($menu[$m]);
		if(in_array($m, array('pm','media')))
			$menu[$m]['title'] = str_replace(array('[',']'), array('<span class="markbubble"><span>','</span></span>'),$me['title']);
	}
	if($context['user']['is_logged'])
	{
		$menu['unreadreplies'] = array(
				'title' => $txt['mini-unreadreplies'],
				'href' => $scripturl . '?action=unreadreplies',
				'show' => true,
				'active_button' => $context['current_action'] == 'unreadreplies',
				'sub_buttons' => array(
				),
			);
		$menu['unread'] = array(
				'title' => $txt['mini-unread'],
				'href' => $scripturl . '?action=unread',
				'show' => true,
				'active_button' => $context['current_action'] == 'unread' ,
				'sub_buttons' => array(
				),
			);
	}
	theme_menu($menu, 'usermenu');		
	// if any subtempalte want to add links
	if(function_exists('mini_extramenu'))
		$menu = array_merge($menu, mini_extramenu());

	if(function_exists('mini_sidebar'))
		mini_sidebar();
	
	// whos online
	mini_whosblock();

	echo '	</div>
			</div>';
}
// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . '' . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	if(count($buttons)>0)
		echo '
		<div class="buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>