<?php
// Version: 2.0 RC3; index

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
	$settings['theme_version'] = '2.0 RC3';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	// make sure undefined actions use their own template
	$settings['catch_action'] = array('layers' => array('html','body','pages'));

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = true;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
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
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl;
	
	if($context['page_title_html_safe']=='' && isset($_GET['action']))
		$context['page_title_html_safe']=$context['forum_name'].' - '.$_GET['action'];
	elseif($context['page_title_html_safe']=='' && !isset($_GET['action']))
		$context['page_title_html_safe']=$context['forum_name'];

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?rc3" />
	<link rel="stylesheet" href="', $settings['theme_url'], '/css/MenuMatic.css?fin11" type="text/css" media="screen" charset="utf-8" />
	<!--[if lt IE 7]>
		<link rel="stylesheet" href="', $settings['theme_url'], '/css/MenuMatic-ie6.css?fin11" type="text/css" media="screen" charset="utf-8" />
	<![endif]-->
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
	<script src="'.$settings['theme_url'].'/js/mootools.1.2.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="'.$settings['theme_url'].'/js/_class.viewer.js"></script>

</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
	
	echo '
<div id="framefull">
	<div id="shadowcontainer">
		<div id="shadow_top">
			<span class="l"></span>
			<span class="r"></span>
		</div>
	</div>
	<div id="shadow_r">
		<div id="shadow_l">';

	// Show the menu here, according to the menu sub template.
	template_menu();
	echo '
			<div style="background: white; position: relative; height: 23px; margin-top: -23px;">&nbsp;</div>
			<div style="background: white; padding: 1em;">
				<form id="search_form" style="float: right;margin: 0;" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<input type="text" name="search" value="" id="searchtext" />	<input type="submit" name="submit" value="', $txt['search'], '" id="searchsubmit" />
					<input type="hidden" name="advanced" value="0" />';

		// Search within current topic?
		if (!empty($context['current_topic']))
			echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
			// If we're on a certain board, limit it to this board ;).
		elseif (!empty($context['current_board']))
			echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

		echo '</form>';
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
				<div id="useri">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span> |
					<a href="', $scripturl, '?action=unread">Unread</a> | 
					<a href="', $scripturl, '?action=unreadreplies">Replies</a> | 
					<a href="', $scripturl, '?action=logout;' . $context['session_var'] . '=' . $context['session_id'] . '">Logout</a> 
				</div>
				';

	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
			echo '
				<div id="useri">', $txt['login_or_register'], ' </div>';
				
	echo '
				<h1 id="forumtitle"><a href="' . $scripturl . '"><img src="' . $settings['images_url'] . '/envision/logo.png" alt="TinyPortal - simple content managment for SMF" /></a></h1>
			</div>';

		// Show a random news item? (or you could pick one from news_lines...)
		if (!empty($settings['enable_news']))
			echo '
	<div class="quicknews"><b>', $txt['news'], ': </b>
				', $context['random_news_line'], '
	</div>';

	// Show the navigation tree.
	theme_linktree();
	echo '
			<div id="content">	';

}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;


	echo '
			</div>
			<div id="foot">
			<div class="tp_container">
				<div class="tp_col8" style="text-align: center;">' , theme_copyright() , ' <br />		<span class="smalltext"><b>Envision</b> design by <a href="http://www.blocweb.net">BlocWeb</a></span>
  ';

	echo '
				</div>
				<div class="tp_col8 smalltext" style="text-align: center;">' , function_exists('tportal_version') ? tportal_version() : '' , '';


	echo '
				</div>
			</div>
		';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div style="text-align: center; color: #888;" class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</div>';
	
	if(!empty($settings['area1_where']) && $settings['area1_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;">', $settings['area1'], '</div>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area2'], '</div>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area3'], '</div>';


}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '

	</div>
		</div>
	</div>
</div>
	<!-- Load the MenuMatic Class -->
	<script src="'.$settings['theme_url'].'/js/MenuMatic_0.68.3.js" type="text/javascript" charset="utf-8"></script>
	
	<!-- Create a MenuMatic Instance -->
	<script type="text/javascript" >
		window.addEvent(\'domready\', function() {			
			var myMenu = new MenuMatic({
				duration:\'150\',
				opacity:\'100\'
				});
		});		
 	</script>
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
	<div id="linktree">
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

	// Work out where we currently are.
	$current_action = 'home';
	$forumaction=false;
	if (in_array($context['current_action'], array('paidsubscribe','admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('unread','unreadreplies','recent','search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
	{
		$current_action = $context['current_action'];
		$forumaction=true;
	}
	if ($context['current_action'] == 'search2')
		$current_action = 'search';

	if ($context['current_action'] == 'themeshop')
		$current_action = 'themeshop';

	if ($context['current_action'] == 'bugtracker')
		$current_action = 'bugtracker';

	if ($context['current_action'] == 'about')
		$current_action = 'about';

	if (isset($_GET['dl']))
		$current_action = 'downloads';
	elseif (isset($_GET['about']))
		$current_action = 'about';
	elseif (isset($_GET['contribute']))
		$current_action = 'bugtracker';
	elseif (isset($_GET['themeclub']))
		$current_action = 'gallery';

	if (isset($_GET['themegallery']))
		$current_action = 'gallery';

	if (isset($_GET['board']) || isset($_GET['topic']))
		$current_action = 'forum';

	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';
	if(isset($_GET['board']))
	{
		if($_GET['board']==85)
			$current_action = 'docs';
	}
	if ($context['current_action'] == 'paidsubsribe')
		$current_action = 'admin';
	if(isset($_GET['page']) && $_GET['page']=='tpthemeclub')
		$current_action='club';

	if(!empty($settings['onemenu']))
	{
		unset($context['menu_buttons']['home']);
		unset($context['menu_buttons']['forum']);
		$menuu = array(
			'home' => array(
				'href' => $scripturl,	
				'title' => 'Home',	
				'active_button' => $current_action=='home' ? true : false,
			),
			'forum' => array(
				'href' => $scripturl . '?action=forum',	
				'title' => 'Forum',	
				'active_button' => ($current_action == 'forum' || $forumaction) ? true : false,
				'sub_buttons' => $context['menu_buttons'],
			),
			'downloads' => array(
				'href' => $scripturl . '?action=tpmod;dl',	
				'title' => 'Downloads',	
				'active_button' => $current_action == 'downloads',
				'sub_buttons' => array(
					'search' => array(
						'href' => $scripturl . '?action=tpmod;dl=search',
						'title' => 'Search',
						'active_button' => isset($_GET['dl']) && $_GET['dl'] == 'search' ? true : false,
						'check' => true,
					),
				),
			),
		);
	}
	else
		$menuu = $context['menu_buttons'];

	echo '
		<div id="menu_container">
			<ul id="nav">';

	foreach ($menuu as $act => $button)
	{
		echo '
				<li id="button_', $act, '" class="topmenu', isset($button['active_button']) && $button['active_button'] == true ? ' liactive ' : '', '">
					<a class="', isset($button['active_button']) && $button['active_button'] == true ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul class="first">';

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
							<ul class="second">';

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
		<br style="clear: both;" />';
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

	echo '
		<div style="margin-right: 8px;" class="buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

function ctheme_tp_getblockstyles()
{
	return array(
				'0' => array(
					'class' => 'titlebg+windowbg',
					'code_title_left' => '<h3 class="mytitlebg">',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="windowbg"><div style="padding: 8px;">',
					'code_bottom' => '</div></div>',
					'background' => 'windowbg',
				),
				'1' => array(
					'class' => 'titlebg+windowbg',
					'code_title_left' => '<h3 class="mytitlebg">',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="windowbg"><div style="padding: 8px;">',
					'code_bottom' => '</div></div>',
					'background' => 'windowbg',
				),
				'2' => array(
					'class' => 'catbg+windowbg',
					'code_title_left' => '<h3 class="mycatbg">',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="windowbg"><div style="padding: 8px;">',
					'code_bottom' => '</div></div>',
					'background' => 'windowbg',
				),
				'3' => array(
					'class' => 'darktitle+darkbg',
					'code_title_left' => '<h3 class="darktitle">',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="darkbg"><div style="padding: 8px;">',
					'code_bottom' => '</div></div>',
					'background' => 'darkbg',
				),
				'4' => array(
					'class' => 'bluetitle+bluebg',
					'code_title_left' => '<h3 class="bluetitle">',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="bluebg"><div style="padding: 8px;">',
					'code_bottom' => '</div></div>',
					'background' => 'bluebg',
				),
			);
}


?>