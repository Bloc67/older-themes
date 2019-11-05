<?php

// Lithium 1.0
// a rewrite theme

function template_init()
{
	global $settings, $txt, $modSettings;

	$settings['theme_version'] = '2.1';
	$settings['internal_revision'] = '2';
	$settings['use_buttons'] = true;
	$settings['require_theme_strings'] = true;
	$settings['avatars_on_indexes'] = true;
	$settings['avatars_on_boardIndex'] = true;
	$settings['page_index'] = array(
		'extra_before' => '<span class="current">'.$txt['pages'].'</span>',
		'previous_page' => '<span class="icon-arrow-left current"></span>',
		'current_page' => '<span class="current">%1$d</span>',
		'page' => '<a href="{URL}">%2$s</a>',
		'expand_pages' => '<span class="expand_pages" style="cursor: pointer;" onclick="expandPages(this, {LINK}, {FIRST_PAGE}, {LAST_PAGE}, {PER_PAGE});">...</span>',
		'next_page' => '<span class="icon-arrow-right current"></span>',
		'extra_after' => '',
	);
	$settings['themecopyright'] = '<a href="https://www.bjornhkristiansen.com/smf21/"><b>Lithium</b> theme by Bloc &copy; 2017</a>';

	$settings['catch_action'] = array('layers' => array('html','body','pages'));

	// Allow css/js files to be disable for this specific theme.
	// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
	if (!isset($settings['disable_files']))
		$settings['disable_files'] = array('smf_index','smf_responsive','smf_rtl');

	loadtemplate('Cesium');

	$settings['ces_bbc_codes'] = array('ingress','slogan','status','tag','version','doclink','gallerylink','filelink','function','solution','endversion');
	$settings['ces_boardtypes'] = array('blogs','galleries','files','news','bugs','docs','copys');
	
	// using the pages feature?
	if(!empty($_GET['action']) && in_array($_GET['action'],$settings['ces_boardtypes']))
	{	
		$settings['ces_run_safe'] = true;
		$settings['ces_run'] = 'ces_'.$_GET['action'];
		loadtemplate('Ces'.$_GET['action']);
	}

	// load the bbc tag
	add_integration_function('integrate_bbc_codes', 'ces_addbbc', false);
	//add_integration_function('integrate_bbc_buttons', 'ces_bbcbuttons', false);
} 

function ces_bbcbuttons(&$buttons)
{
	global $settings, $txt;

	foreach($settings['ces_bbc_codes'] as $t)
		$buttons[count($buttons) + 1][] = array(
			'image' => 'image',
			'code' => $t,
			'description' => $txt['ces_'.$t],
			'before' => '['.$t.']',
			'after' => '[/'.$t.']',
		);
}

function ces_addbbc(&$codes, &$no_autolink_tags)
{
	global $settings; 

	foreach($settings['ces_bbc_codes'] as $a => $tag)
		$codes[] = array(
				'tag' => $tag,
				'type' => 'unparsed_content',
				'content' => '<div class="ces_bbc_hide">[ces class='.$tag.']$1[/ces]</div>',
			);
	
	return;
}

function template_pages_above()
{
	global $settings;

	if(!empty($settings['ces_run']) && function_exists($settings['ces_run']))
		$settings['ces_run']();
	if(!function_exists('template_main'))
	{
		function template_main()
		{
			global $settings, $scripturl; 
			
			echo '
			<a href="' , $scripturl , '"><img src="' , $settings['images_url'] , '/404.png" style="margin: 2rem 0; width: 100%;" alt="404" /></a>';
		}
	}
}

function template_pages_below()
{
	return;
}

function template_html_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings, $mbname, $sourcedir;

	// Show right to left, the language code, and the character set for ease of translating.
	echo '
<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', !empty($txt['lang_locale']) ? ' lang="' . str_replace("_", "-", substr($txt['lang_locale'], 0, strcspn($txt['lang_locale'], "."))) . '"' : '', '>
<head>
	<meta charset="', $context['character_set'], '">';

	loadCSSFile('lithium.css', array('minimize' => true), 'bloc_cesium');
	
	// load in any css from mods or themes so they can overwrite if wanted
	template_css();

	// load in any javascript files from mods and themes
	template_javascript();

	// any template/function having something to put in the header?
	if(function_exists('subtemplate_headers'))
		$title_is_set = subtemplate_headers();

	if(empty($title_is_set))
		echo '
	<title>', !empty($context['page_title_html_safe']) ? $context['page_title_html_safe'] : $mbname , '</title>';
	
	echo '
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />	';

	// Content related meta tags, like description, keywords, Open Graph stuff, etc...
	foreach ($context['meta_tags'] as $meta_tag)
	{
		echo '
	<meta';

		foreach ($meta_tag as $meta_key => $meta_value)
			echo ' ', $meta_key, '="', $meta_value, '"';

		echo '>';
	}

	/* What is your Lollipop's color?
	Theme Authors you can change here to make sure your theme's main color got visible on tab */
	echo '
	<meta name="theme-color" content="#557EA0">';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex">';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '">';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help">
	<link rel="contents" href="', $scripturl, '">', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search">' : '');

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate feed" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?action=.xml;type=rss2', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">
	<link rel="alternate feed" type="application/atom+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $scripturl, '?action=.xml;type=atom', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['links']['next']))
		echo '
	<link rel="next" href="', $context['links']['next'], '">';

	if (!empty($context['links']['prev']))
		echo '
	<link rel="prev" href="', $context['links']['prev'], '">';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0">';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
	<link href="https://fonts.googleapis.com/css?family=Poiret+One" rel="stylesheet">

</head>
<body>';
}

/**
 * The upper part of the main template layer. This is the stuff that shows above the main forum content.
 */
function template_body_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings, $maintenance;

	echo '
	<div id="frame">
		<div id="mobile_menu" class="mob">
			<div class="bot_menu_mobile" id="mmenu">
				' , template_menu_mob() , '
			</div>';
	
	// if blog/gallery etc. features are selected, add to the menu
	$context['mob_boardtypes'] = array(); $context['ces_exists'] = false;
	foreach($settings['ces_boardtypes'] as $g)
	{
		if(!empty($settings['use_'.$g]))
		{
			$context['mob_boardtypes'][$g] = array(
				'id' => $g,
				'title' => $txt['ces_'.$g],
				'active_button' => !empty($_GET['action']) && $_GET['action']==$g ? true : false,
				'href' => $scripturl. '?action='.$g,
				'icon' => '',
			);
			$context['ces_exists'] = true;
		}
	}	
	if($context['ces_exists'])
	{
		echo '
			<div class="bot_menu_mobile" id="bmmenu">
				' , template_menu_mob2() , '
			</div>';
	}
	if ($context['allow_search'])
	{
		echo '	
			<div class="bot_menu_mobile" id="qform_search_mob">
				<form id="search_form_mob" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<input type="search" name="search" id="jsearch" value="">
					<input type="submit" name="search2" value="', $txt['search'], '" >
					<input type="hidden" name="advanced" value="0">
				</form>
			</div>';
	}
	
	if (!empty($settings['enable_news']) && !empty($context['random_news_line']))
		echo '
			<div id="fnews_mob" class="bot_menu_mobile">
				<strong>', $txt['news'], ': </strong>', $context['random_news_line'], '
			</div>';
	
	if ($context['user']['is_logged'])
		echo '
			<div class="bot_menu_mobile" id="pmenu">
				<ul>
					<li><a href="', $scripturl, '?action=profile">', $txt['view_profile'], '</a></li>
					<li><a href="' , $scripturl  , '?action=unread">' , $txt['view_unread_category'] , '</a</li>
					<li><a href="' , $scripturl  , '?action=unreadreplies">' , $txt['unread_replies'] , '</a</li>
					<li><a href="' , $scripturl  , '?action=pm">' , $txt['pm_short'] , '</a</li>
					<li><a href="' , $scripturl  , '?action=alerts">' , $txt['alerts'] , '</a</li>
					<li><a href="' , $scripturl  , '?action=logout;'.$context['session_id'].'='.$context['session_var'].'">' , $txt['logout'] , '</a</li>
				</ul>
			</div>';

	if(function_exists('template_f_menu_subs') || function_exists('template_f_menu_subs_twin'))
		if(function_exists('template_f_menu_subs_twin'))
			template_f_menu_subs_twin();
		else
			template_f_menu_subs();

	echo '
			<ul class="nolist" id="mmenu_bot">
				<li onclick="fPop_slide(\'#mmenu\'); return false;"><span class="icon-menu"></span></li>';

	if($context['ces_exists'])
		echo '
				<li onclick="fPop_slide(\'#bmmenu\'); return false;"><span class="icon-menu"></span><span class="amt smaller">B</span></li>';

	if(function_exists('template_f_menu') || function_exists('template_f_menu_twin'))
		if(function_exists('template_f_menu_twin'))
			template_f_menu_twin();
		else
			template_f_menu();

	
	if ($context['allow_search'])
		echo '
				<li><a href="' , $scripturl , '?action=search"><span class="icon-search"></span></a></li>';
	
	if ($context['user']['is_logged'])
		echo '
				<li onclick="fPop_slide(\'#pmenu\'); return false;"><span class="icon-user"></span></li>';
	echo '
			</ul>
		</div>
		<div id="main_content">
			<div class="fwidth">
				<div id="fheader">';

	// If the user is logged in, display some things that might be useful.
	if ($context['user']['is_logged'])
	{
		loadlanguage('Profile');
		loadlanguage('PersonalMessage');
		// Firstly, the user's menu
		echo '
					<ul class="nolist dropmenu des" id="tprofile">
						<li>
							<a href="', $scripturl, '?action=profile"', !empty($context['self_profile']) ? ' class="active"' : '', '><b>', $context['user']['name'], '</b></a>
							<ul class="des">
								<li><a href="', $scripturl, '?action=profile;area=summary;u=' , $context['user']['id'] , '">' , $txt['popup_summary'] , '</a></li>
								<li><a href="', $scripturl, '?action=profile;area=showposts;u=' , $context['user']['id'] , '">' , $txt['popup_showposts'] , '</a></li>
								<li><a href="', $scripturl, '?action=profile;area=notification;u=' , $context['user']['id'] , '">' , $txt['notification'] , '</a></li>
							</ul>
						</li>';

		// Secondly, PMs if we're doing them
		if ($context['allow_pm'])
		{
			echo '
						<li>
							<a href="', $scripturl, '?action=pm"', !empty($context['self_pm']) ? ' class="active"' : '', '>' , $txt['messages'] , '
								', !empty($context['user']['unread_messages']) ? ' <span class="amt">' . $context['user']['unread_messages'] . '</span>' : '', '
							</a>
							<ul class="des">
								<li><a href="', $scripturl, '?action=pm;sa=send">' , $txt['send_message'] , '</a></li>
								<li><a href="', $scripturl, '?action=pm;sa=showpmdrafts">' , $txt['pm_drafts_short'] , '</a></li>
							</ul>
						</li>';
		}

		// Thirdly, alerts
		echo '
						<li>
							<a href="', $scripturl, '?action=profile;area=showalerts;u=', $context['user']['id'], '"', !empty($context['self_alerts']) ? ' class="active"' : '', '>
								' , $txt['alerts'] , ' ', !empty($context['user']['alerts']) ? ' <span class="amt">' . $context['user']['alerts'] . '</span>' : '', '
							</a>
						</li>';

		// And now we're done.
		echo '
						<li><a href="' , $scripturl  , '?action=logout;'.$context['session_var'].'='.$context['session_id'].'">' , $txt['logout'] , '</a</li>
					</ul>';
	}
	// Otherwise they're a guest. Ask them to either register or login.
	else
	{
		if (empty($maintenance))
			echo '
					<ul class="nolist dropmenu mob_clear">
						<li class="centertext">', $txt['hello_guest'], $txt['guest'], ' <span class="single_action floatright darkbg"><a href="', $scripturl. '?action=login">' , $txt['login'] , '</a><a href="', $scripturl. '?action=signup">' , $txt['register'] , '</a></li>
					</ul>';
		else
			echo '
					<ul class="nolist dropmenu mob_clear">
						<li class="centertext">', sprintf($txt['welcome_guest'], $txt['guest_title'], '', $scripturl. '?action=login', 'return true;'), '</b></li>
					</ul>';
	}

	echo '<div class="fheader_inner">
					<div class="floatpop mob">
						<span class="icon-location2 floatright" style="color: #fff;" onclick="fPop_toggle(\'#flinktree_mob\'); return false;"></span>
				   </div>';			
				
	if ($context['allow_search'])
	{
		echo '	
					<div id="fq_search" class="floatpop des">	
						<a href="' , $scripturl , '?action=search"><span class="icon-search floatright" style="margin: ' , $context['user']['is_logged'] ? '1' : '1'  , 'rem 1rem 0 0; color: #fff;"></span></a>
					</div>';
	}
					
	echo '		<div style="oveflow: hidden;">
					<h1 >
						<a id="top" href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name_html_safe'] : '<img style="max-width: 100%;" src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name_html_safe'] . '">', '</a>
					</h1>';
	if (!empty($settings['enable_news']) && !empty($context['random_news_line']))
		echo '
					<div id="fnews">
						<strong>', $txt['news'], ': </strong>', $context['random_news_line'], '
					</div>';

	echo '		</div>';
	if(function_exists('template_generic_menu'))
		echo '	
						<div class="clear_right"><span class="icon-menu mob icon-bigger" onclick="fPop_toggle(\'#admenu\'); return false;"></span></div>';			
	
	if ($context['user']['is_logged'])
		echo '
					<div class="mob" style="opacity: 0.5; margin: 0.5rem 0 0 0;">' , $txt['hello_guest'] , ' ' , $context['user']['name'] , '</div>';

	echo '		<br class="clear">		
					<div id="flinktree" class="des">
						' , theme_linktree() , '	
					</div>				
					<div id="flinktree_mob" style="display: none;">
						<ul class="nolist mobsub">';
	
	$what = array_reverse($context['linktree']);
	foreach ($what as $link_num => $tree)
	{
			echo '
							<li>';
			// Show the link, including a URL if it should have one.
			if (isset($tree['url']))
				echo '
								<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>';
			else
				echo '
								<span>' . $tree['name'] . '</span>';

			echo '
							</li>';
	}
	echo '		
						</ul>
					</div></div>';

	
	echo '
				</div>

					<div class="bwgrid" id="faside_back">
						<div class="bwcell12">
							<div id="fcontent">';
}

/**
 * The stuff shown immediately below the main content, including the footer
 */
function template_body_below()
{
	global $context, $txt, $scripturl, $settings;

	echo '
						</div>
					</div>
					<div class="bwcell4">
						<div id="lith_menu" class="des">', template_menu($context['ces_exists']) ,'</div>
						<div id="faside">
							' , 	function_exists('subtemplate_aside_twin') ? subtemplate_aside_twin() : '' , '
							' , 	function_exists('subtemplate_aside') && !function_exists('subtemplate_aside_twin') ? subtemplate_aside() : '' , '
						</div>
					</div>
				</div>
				<div id="fbottom">';
	
	theme_copyright();

	// Show the load time?
	if ($context['show_load_time'])
		echo '
					<div>', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '</div>';

	echo '
					<div>' , $settings['themecopyright'] , '</div>
				</div>
			</div>
		</div>
	</div>';
}

function template_html_below()
{
	// load in any javascipt that could be deferred to the end of the page
	template_javascript(true);

	echo '
</body>
</html>';
}

function theme_linktree($force_show = false)
{
	global $context, $shown_linktree, $scripturl, $txt;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
				<div class="navigate des">
					<ul class="nolist">';

	if ($context['user']['is_logged'])
		echo '
						<li class="unread_links des">
							<a href="', $scripturl, '?action=unread" title="', $txt['unread_since_visit'], '">', $txt['view_unread_category'], '</a>
							<a href="', $scripturl, '?action=unreadreplies" title="', $txt['show_unread_replies'], '">', $txt['unread_replies'], '</a>
						</li>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
						<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Don't show a separator for the first one.
		// Better here. Always points to the next level when the linktree breaks to a second line.
		// Picked a better looking HTML entity, and added support for RTL plus a span for styling.
		if ($link_num != 0)
			echo '
							<span style="opacity: 0.4; font-weight: bold;"> / </span>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'], ' ';

		// Show the link, including a URL if it should have one.
		if (isset($tree['url']))
			echo '
					<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>';
		else
			echo '
					<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo ' ', $tree['extra_after'];

		echo '
						</li>';
	}

	echo '
					</ul>
				</div>';

	$shown_linktree = true;
}

/**
 * Show the menu up top. Something like [home] [help] [profile] [logout]...
 */
function template_menu($exists = false)
{
	global $context, $settings, $scripturl, $txt;

	if(!empty($context['current_board']) && $exists)
	{
		foreach ($context['mob_boardtypes'] as $act => $button)
		{
			if(in_array($context['current_board'],explode(',',$settings[$act.'_boards'])))
			{
				$context['mob_boardtypes'][$act]['active_button'] = true;
				$context['menu_buttons']['home']['active_button'] = false;
			}
		}
	}	
	
	if(empty($settings['ces_fullmenus']) && $exists)
	{
		if(!empty($settings['ces_fullmenus_keep']))
			$keep = explode(',',$settings['ces_fullmenus_keep']);
		else
			$keep = array();

		$context['menu_buttons']['home']['sub_buttons'] = array();
		foreach ($context['menu_buttons'] as $act => $button)
		{
			if($act != 'home' && !in_array($act,$keep))
			{
				$context['menu_buttons']['home']['sub_buttons'][$act] = $button;
				unset($context['menu_buttons'][$act]);
			}
		}
	}
	
		echo '
				<ul class="menu_nav dropmenu nolist" id="ces_topmenu">';

	// Note: Menu markup has been cleaned up to remove unnecessary spans and classes.
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
					<li class="button_', $act, '', !empty($button['sub_buttons']) ? ' subsections"' : '"', '>
						<a', $button['active_button'] ? ' class="active"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
							', $button['icon'], '<span class="textmenu">', $button['title'], '</span>
						</a>';

		if (!empty($button['sub_buttons']))
		{
			echo '
						<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
							<li', !empty($childbutton['sub_buttons']) ? ' class="subsections"' : '', '>
								<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
									', $childbutton['title'], '
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
												', $grandchildbutton['title'], '
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

	if($exists && count($context['mob_boardtypes'])>0)
	{
		foreach ($context['mob_boardtypes'] as $act => $button)
		{
			echo '
					<li class="button_', $act, '">
						<a', $button['active_button'] ? ' class="active"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
							', $button['title'], '
						</a>
					</li>';
		}
	}
	echo '
				</ul>';
}

function template_menu_mob()
{
	global $context, $settings, $txt, $scripturl;

	echo '
				<ul class="menu_nav dropmenu nolist">';

	// remove the logout-link
	unset($context['menu_buttons']['logout']);
	
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
					<li class="button_', $act, '">
						<a', $button['active_button'] ? ' class="active"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
							', $button['title'], '
						</a>
					</li>';
	}

	echo '
				</ul>';
}

function template_menu_mob2()
{
	global $context, $settings, $txt, $scripturl;

	echo '
				<ul class="menu_nav dropmenu nolist">';
	
	foreach ($context['mob_boardtypes'] as $act => $button)
	{
		echo '
					<li class="button_', $act, '">
						<a', $button['active_button'] ? ' class="active"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
							', $button['title'], '
						</a>
					</li>';
	}

	echo '
				</ul>';
}

/**
 * Generate a strip of buttons.
 *
 * @param array $button_strip An array with info for displaying the strip
 * @param string $direction The direction
 * @param array $strip_options Options for the button strip
 */
function template_button_strip($button_strip, $css = '', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		// As of 2.1, the 'test' for each button happens while the array is being generated. The extra 'test' check here is deprecated but kept for backward compatibility (update your mods, folks!)
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if (!isset($value['id']))
				$value['id'] = $key;

			$button = '
				<li id="blink'.$value['id'].'"><a class="first button button_strip_' . $key . (!empty($value['active']) ? ' active' : '') . '" ' . (!empty($value['url']) ? 'href="' . $value['url'] . '"' : '') . ' ' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '>' . $txt[$value['text']] . '</a>';

			if (!empty($value['sub_buttons']))
			{
				$button .= '
					<ul>';
				foreach ($value['sub_buttons'] as $element)
				{
					if (isset($element['test']) && empty($context[$element['test']]))
						continue;

					$button .= '
						<li><a href="' . $element['url'] . '">' . $txt[$element['text']] . '</a></li>';
				}
				$button .= '
					</ul>';
			}
			$button .= '
					</li>';
			$buttons[] = $button;
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
		<div', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"' : ''), '>
			<ul class="' , $css , '">
				', implode('', $buttons), '
			</ul>
		</div>';
}

function template_mob_button_strip($button_strip)
{
	global $context, $txt;

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if (!isset($value['id']))
				$value['id'] = $key;

			$button = '
				<li><a ' . (!empty($value['url']) ? 'href="' . $value['url'] . '"' : '') . '>' . $txt[$value['text']] . '</a></li>';
			
			$buttons[] = $button;
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
	<ul>
		', implode('', $buttons), '
	</ul>';
}

/**
 * The upper part of the maintenance warning box
 */
function template_maint_warning_above()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="errorbox" id="errors">
		<dl>
			<dt>
				<strong id="error_serious">', $txt['forum_in_maintenance'], '</strong>
			</dt>
			<dd class="error" id="error_list">
				', sprintf($txt['maintenance_page'], $scripturl . '?action=admin;area=serversettings;' . $context['session_var'] . '=' . $context['session_id']), '
			</dd>
		</dl>
	</div>';
}

/**
 * The lower part of the maintenance warning box.
 */
function template_maint_warning_below()
{

}

?>