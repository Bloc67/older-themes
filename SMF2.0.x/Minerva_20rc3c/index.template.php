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

	// The ?rc2 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?rc2" />';
	if(!empty($settings['colorversion']))
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/'.$settings['colorversion'].'.css?rc2" />';

	if(empty($settings['nomootools']))
		echo '
	<link rel="stylesheet" href="', $settings['theme_url'], '/css/MenuMatic.css?fin11" type="text/css" media="screen" charset="utf-8" />
	<!--[if lt IE 7]>
		<link rel="stylesheet" href="', $settings['theme_url'], '/css/MenuMatic-ie6.css?fin11" type="text/css" media="screen" charset="utf-8" />
	<![endif]-->';
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/print.css?rc2" media="print" />';

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

	if(empty($settings['nomootools']))
		echo '
	<script src="'.$settings['theme_url'].'/js/mootools.1.2.js" type="text/javascript" charset="utf-8"></script>';
	
	// check if we want to close off right apnel
	$turnoff_r = false;
	// do we have TP?
	if(isset($context['TPortal']))
	{
		if(in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 && $context['TPortal']['leftpanel']==0)
			$turnoff_r = true;
	}
	else
	{
		if(!empty($settings['area1_where']) && $settings['area1_where']==4 && empty($settings['area1']))
			$turnoff_r = true;
		if(!empty($settings['noleftpanel']))
			$turnoff_r = true;
	 }


	echo '
</head>
<body>
	<table cellspacing="0" cellpadding="0" style="width: ' , !empty($settings['forum_width']) ? $settings['forum_width'] : '100%' , '; margin: 0 auto;">
		<tr>
			<td valign="top" id="leftie"' , $turnoff_r ? ' style="display: none;"' : '' , '>
				<div class="wbot"><div class="wtop"><div class="wright"><div class="wleft"><div class="wbotleft"><div class="wbotright"><div class="wtopleft"><div class="wtopright">
				<div' , !empty($settings['leftpanel_width']) && empty($context['TPortal']['leftpanel']) ? ' style="width: ' . $settings['leftpanel_width'].'px;"' : '' , '>';

	// the left side
	if(!empty($settings['area1_where']) && $settings['area1_where']==4)
		echo '<div style="margin-bottom: 7px;">', $settings['area1'], '</div>';

	// TinyPortal integrated bars
	if(!empty($context['TPortal']['leftpanel']))
		echo '
				<div id="tpleftbarHeader">' , TPortal_panel('left') , '</div>';

	echo '
				</div>
				</div></div></div></div></div></div></div></div>
			</td>
			<td valign="top" id="midside" width="100%">
				<div class="wbot"><div class="wtop"><div class="wright"><div class="wleft"><div class="wbotleft"><div class="wbotright"><div class="wtopleft"><div class="wtopright">';
	

}
function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// check if we want to close off right apnel
	$turnoff_r = false;
	// do we have TP?
	if(isset($context['TPortal']))
	{
		if(in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 && $context['TPortal']['rightpanel']==0)
			$turnoff_r = true;
	}
	else
	{
		if(!empty($settings['area2_where']) && $settings['area2_where']==4 && empty($settings['area2']))
			$turnoff_r = true;
		if(!empty($settings['norightpanel']))
			$turnoff_r = true;
	}
	echo '
				</div></div></div></div></div></div></div></div>
			</td>
			<td valign="top" id="rightie"' , $turnoff_r ? ' style="display: none;"' : '' , '>
				<div class="wbot"><div class="wtop"><div class="wright"><div class="wleft"><div class="wbotleft"><div class="wbotright"><div class="wtopleft"><div class="wtopright">
				<div' , !empty($settings['rightpanel_width']) && !isset($context['TPortal']) && empty($context['TPortal']['rightpanel']) ? ' style="width: ' . $settings['rightpanel_width'].'px;"' : '' , '>';

	// the right side
	if(!empty($settings['area2_where']) && $settings['area2_where']==4)
		echo '<div style="margin-bottom: 7px;">', $settings['area2'], '</div>';

	// TinyPortal integrated bars
	if(!empty($context['TPortal']['rightpanel']))
		echo '
				<div id="tprightbarHeader">' , TPortal_panel('right') , '</div>';

	echo '	</div>
				</div></div></div></div></div></div></div></div>
			</td>
		</tr>
	</table>';
	
	if(empty($settings['nomootools']))
		echo '<!-- Load the MenuMatic Class -->
	<script src="'.$settings['theme_url'].'/js/MenuMatic_0.68.3.js" type="text/javascript" charset="utf-8"></script>
	
	<!-- Create a MenuMatic Instance -->
	<script type="text/javascript" >
		window.addEvent(\'domready\', function() {			
			var myMenu = new MenuMatic({
				duration:\'150\'
				});
		});		
	if(document.getElementById(\'admnav\'))
	{
		window.addEvent(\'domready\', function() {			
			var myMenu = new MenuMatic({
				id:\'admnav\',
				subMenusContainerId:\'admsubMenus\',
				duration:\'150\'
				});
		});
	}
 	</script>';

	echo '
	</body></html>';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div id="wfooter">', theme_copyright(), '<br />
		<span class="smalltext"><b>Minerva</b> design by <a href="http://www.blocweb.net">BlocWeb</a></span>
		<ul class="smalltext reset">
			<li><a id="button_xhtml" href="http://validator.w3.org/check/referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a></li>
			', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<li><a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a></li>' : '', '
			<li class="last"><a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a></li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<div class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</div>';
	
	if(!empty($settings['area1_where']) && $settings['area1_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;">', $settings['area1'], '</div>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area2'], '</div>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==1)
		echo '<div style="text-align: center; margin-bottom: 7px;"">', $settings['area3'], '</div>';
	echo '
	</div>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo toplinks();

	echo '<h1 class="forumtitle"><a href="', $scripturl, '">', empty($settings['header_logo_url']) ? $context['forum_name'] : '<img src="' . $settings['header_logo_url'] . '" alt="' . $context['forum_name'] . '" />', '</a></h1>
	' , template_menu();

	// Show the navigation tree.
	theme_linktree2();

}

function toplinks()
{
	global $context, $scripturl, $txt;


	echo '
	<div style="position: absolute; top: 5px; color: white;">';
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		echo '
		' , $txt['hello_member_ndt'] , ' ', $context['user']['name'], '';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	else
		echo $txt['hello_guest'], ' ', $txt['guest'];

	echo '
	</div>';
	echo '
	<div id="toplinks">&nbsp;';

	if(sizeof($context['sitemenu'])>0)
	{
		$links=array();
		foreach($context['sitemenu'] as $menu => $val)
		{
			if($val[2]=='page')
				$links[] = '
			<a href="' . $scripturl . '?action='.$val[0].'">'.$val[1].'</a>';
			elseif($val[2]=='link')
				$links[] = '
			<a href="'.$val[0].'">'.$val[1].'</a>';
		}
		echo implode("&nbsp;|&nbsp;",$links);
	}
	echo '</div>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree2($force_show = false)
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
function theme_linktree() { return; }

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	if(empty($settings['nomootools']))
		echo '
		<div id="menu_container">
			<ul id="nav">';
	else
		echo '
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
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
			$buttons[] = '<a ' . (isset($value['active']) ? 'class="active" ' : '') . 'href="' . $value['url'] . '" ' . (isset($value['custom']) ? $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>
				<li>', implode('</li><li>', $buttons), '</li>
			</ul>
		</div>';
}

?>