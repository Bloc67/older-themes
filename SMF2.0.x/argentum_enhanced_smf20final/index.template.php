<?php
/**
 * Simple Machines Forum (SMF)
 *
 *
 * @version 2.0
 */

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
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	$settings['catch_action'] = array('layers' => array('html','body','pages'));

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = true;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
}

// any special pages?
function template_pages_above()
{
	global $context, $settings, $options, $scripturl, $txt;
	
	echo '<div id="pages">';
	if(isset($_GET['action']))
		$what=$_GET['action'];

	if(file_exists($settings['theme_dir'] . '/pages/' . $what. '.template.php'))
	{
		// any preset action? Limited use.
		if(file_exists($settings['theme_dir'] . '/pages/' . $what. '.include.php'))
			require_once($settings['theme_dir'] . '/pages/' . $what. '.include.php');

		loadtemplate('pages/'.$what);
	}
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
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

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
		var quick_theme_id = "', $settings['theme_id'], '";
		var quick_session_id = "', $context['session_id'], '";
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
	<title>', $context['page_title_html_safe'], '</title>';

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

	echo '
	<script src="'.$settings['theme_url'].'/js/mootools13b_min.js" charset="ISO-8859-1"  type="text/javascript"></script>';
	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	$settings['qr_width'] = 'auto';
	$settings['forum_width'] = '100%';
	$settings['qr_width'] = '93%';
	echo '
		<style type="text/css">
			.sreply
			{
				width: '.$settings['qr_width'].';
			}
		</style> ';

	echo '
</head>
<body><div id="midframe"><div id="rightbg">
	<div id="leftbg">
		<div id="rightframe">
			<div id="leftframe">
				<div id="bot">
					<div id="botright">';
	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
		echo '
				<div id="useri"><div>', $txt['hello_member_ndt'], ' <b>', $context['user']['name'], '</b>
				&nbsp;&nbsp; <a href="'.$scripturl.'?action=unread">Unread</a>
				| <a href="'.$scripturl.'?action=unreadreplies">Replies</a>
				</div></div>	';
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
			echo '
				<div id="useri"><div>
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<span class="smalltext">', $txt['login_or_register'], '</span>
					<input type="text" name="user" size="10" class="input_text" />
					<input type="password" name="passwrd" size="10" class="input_password" />
					<select name="cookielength">
						<option value="60">', $txt['one_hour'], '</option>
						<option value="1440">', $txt['one_day'], '</option>
						<option value="10080">', $txt['one_week'], '</option>
						<option value="43200">', $txt['one_month'], '</option>
						<option value="-1" selected="selected">', $txt['forever'], '</option>
					</select>
					<input type="submit" value="', $txt['login'], '" class="button_submit" />
					<div class="info">', $txt['quick_login_dec'], '</div>';

		if (!empty($modSettings['enableOpenID']))
			echo '
					<input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
				</form>
					</div></div>';
	}			
	echo '<h1 id="forumname"><a href="'.$scripturl.'"><img src="'.$settings['images_url'].'/theme/logo.png" alt="'.$context['forum_name'].'" /></a></h1>
	', template_menu();
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	<div style="float: right;"><form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
		<input type="text" name="search" value="" id="searchtext" style="width: 145px;  " />
		<input type="submit" name="search_submit" value="'.$txt['search'].'" class="mysearchsubmit" />
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
	<div style="color: #aaa; text-align: right;" class="smalltext">' . $context['current_time'] . '</div>
	</form>
	</div>
	<div id="mycontent">';
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
	<div id="qnews"><b>', $txt['news'], ': </b>', $context['random_news_line'], '</div>';

	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	</div>
	<div id="foot">';
	if(!empty($settings['area1_where']) && $settings['area1_where']==1)
		echo '<hr><div style="text-align: center; margin-bottom: 7px;">', $settings['area1'], '</div>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==1)
		echo '<hr><div style="text-align: center; margin-bottom: 7px;">', $settings['area2'], '</div>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==1)
		echo '<hr><div style="text-align: center; margin-bottom: 7px;">', $settings['area3'], '</div>';
	theme_copyright();
	
	echo '
	<div class="middletext"><a href="' , $settings['theme_url'] , '/licence.txt"><b>Argentum2</b></a> &copy; <a href="http://www.blocweb.net">Bloc</a></div>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p class="smalltext">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';
	echo '
	</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
					</div>
				</div>
			</div>
		</div>
	</div></div></div>
' , !empty($settings['google']) ? $settings['google'] : '' , '
	<!-- Create a MenuMatic Instance -->
	<script type="text/javascript" >
		window.addEvent(\'domready\', function() {			
			var myMenu = new MenuMatic({
				duration:\'100\',
				opacity:\'100\'
				});
		});		
	if(document.getElementById(\'admnav\'))
	{
		window.addEvent(\'domready\', function() {			
			var myMenu = new MenuMatic({
				id:\'admnav\',
				subMenusContainerId:\'admsubMenus\',
				duration:\'100\'
				});
		});
	}
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
	global $context, $settings, $options, $scripturl, $txt, $boardurl, $modSettings;
	echo '
		<div id="menu_container"><div><div>
			<ul id="nav">';

	// add freinds button
	if($context['user']['is_logged'] && !empty($settings['showfriendsbutton']))
		$context['menu_buttons']['friends'] = array(
			'href' => $scripturl.'?action=friends',	
			'title' => $txt['buddies'],	
			'active_button' => $context['current_action']=='friends',	
		);

	if(!empty($modSettings['custmenu']) && !empty($settings['usermenu']))
	{	
		$actives=0;
		$menuu = unserialize($modSettings['custmenu']);
		if(!empty($modSettings['custmenutitles']))
			$titles = unserialize($modSettings['custmenutitles']);
		
		if(!empty($modSettings['custlinks']))
			$clinks = unserialize($modSettings['custlinks']);

		if(!empty($modSettings['custnames']))
			$names = unserialize($modSettings['custnames']);
		else
			$names=array(
			'sub1' => 'sub1',
			'sub2' => 'sub2',
			'sub3' => 'sub3',
			'sub4' => 'sub4',
			'sub5' => 'sub5',
			'sub6' => 'sub6',
			);

		if(!empty($modSettings['custnamesurl']))
			$namesurl = unserialize($modSettings['custnamesurl']);

		foreach ($menuu as $menu => $menubuttons)
		{
			if(!in_array($menu,array('single','sub1','sub2','sub3','sub4','sub5','sub6')))
				continue;
			
			if($menu!='single')
			{
				if (count($menubuttons)>0)
				{
					$liactive=false;
					$button=$menubuttons;
					$render='';
					foreach ($menubuttons as $c)
					{
						if(isset($context['menu_buttons'][$c]) || isset($clinks[$c]))
						{
							if(isset($context['menu_buttons'][$c]))
								$childbutton = $context['menu_buttons'][$c];
							else
								$childbutton=array(
									'href' => $clinks[$c],
									'title' => !empty($titles[$c]) ? $titles[$c] : $c,
								);
							
							$render .= '
								<li>
									<a href="'. $childbutton['href']. '"'. (isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : ''). '>
										<span' . (isset($childbutton['is_last']) ? ' class="last"' : '') . '>' . $childbutton['title'] . (!empty($childbutton['sub_buttons']) ? '...' : '') . '</span>
									</a>';
							if(!empty($childbutton['sub_buttons']))
							{
								$render .= '
									<ul class="second">';
								foreach($childbutton['sub_buttons'] as $g => $grand)
								{
									$render .= '
										<li>
											<a href="'. $grand['href']. '"'. (isset($grand['target']) ? ' target="' . $grand['target'] . '"' : ''). '>
												<span' . (isset($grand['is_last']) ? ' class="last"' : '') . '>' . $grand['title'] . '</span>
											</a>
										</li>';
								}
								$render .= '
									</ul>';
							}
							$render .= '
								</li>';
							
							if(isset($childbutton['active_button']) && $childbutton['active_button']==true)
								$liactive=true;
						}
					}

					echo '
						<li id="button_', $menu, '" class="topmenu', $liactive ? ' liactive ' : '', '">
							<a href="' , !empty($namesurl[$menu]) ? $namesurl[$menu] : '#' , '" class="', $liactive ? 'active ' : '', 'firstlevel">
								<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $names[$menu], '</span>
							</a>';
					echo '
							<ul class="first">';
					
					echo $render;

					echo '
							</ul>';
				}
				echo '
						</li>';
			}
			else
			{
				if (count($menubuttons)>0)
				{
					foreach ($menubuttons as $c)
					{
						if(isset($context['menu_buttons'][$c]) || isset($clinks[$c]))
						{
							if(isset($context['menu_buttons'][$c]))
								$button = $context['menu_buttons'][$c];
							else
								$button=array(
									'href' => $clinks[$c],
									'title' => !empty($titles[$c]) ? $titles[$c] : $c,
									'active_button' => false,
								);

							echo '
									<li id="button_', $c, '" class="topmenu', isset($button['active_button']) && $button['active_button'] == true ? ' liactive ' : '', '">
										<a class="', isset($button['active_button']) && $button['active_button'] == true ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
											<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', !empty($titles[$c]) ? $titles[$c] : $button['title'], '</span>
										</a>';
						
							if(!empty($button['sub_buttons']))
							{
								echo '
										<ul class="first">';
								foreach($button['sub_buttons'] as $g => $grand)
								{
									echo '
											<li>
												<a href="'. $grand['href']. '"'. (isset($grand['target']) ? ' target="' . $grand['target'] . '"' : ''). '>
													<span' . (isset($grand['is_last']) ? ' class="last"' : '') . '>' . $grand['title'] . '</span>
												</a>
											</li>';
								}
								echo '
										</ul>';
							}
							echo '
								</li>';
						}
					}
				}
			}

		}
	}
	else
	{
		foreach ($context['menu_buttons'] as $act => $button)
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
	}
	echo '
			</ul>
		</div></div></div>';
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


function call_twitter()
{
	global $settings, $txt;

	if(empty($settings['twitter']) || empty($settings['twittercount']))
		return;

	echo '
			<div class="title_bar">
				<h4 class="titlebg">
					'.$txt['bloc_fromtwitter'].'
				</h4>
			</div>
			<div class="widgetbox">
				<div id="twitter_div">
					<ul id="twitter_update_list" class="mytwitter"></ul>
					<hr style="clear: both;" />
					<p><a href="http://twitter.com/'.$settings['twitter'].'" id="twitter-link" >'.$txt['bloc_followus'].'</a></p>
				</div>
			</div>
	<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
	<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/'.$settings['twitter'].'.json?callback=twitterCallback2&amp;count=',!empty($settings['twittercount']) ? $settings['twittercount'] : '6' ,'"></script>';

}

function render_childs($cb, $colspan=0, $alt=false, $board_id)
{
	global $settings, $context, $txt, $scripturl;

	if(empty($settings['childrender']))
	{
				if (!empty($cb))
				{
					$children = array();
					foreach ($cb as $child)
					{
						if (!$child['is_redirect'])
							$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><img src="' . $settings['lang_images_url'] . '/new.gif" class="new_posts" alt="" />' : '') . '</a>';
						else
							$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '">' . $child['name'] . '</a>';

						if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
							$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

						$children[] = $child['new'] ? '<strong>' . $child['link'] . '</strong>' : $child['link'];
					}
					echo '
					<tr  id="board_', $board_id, '_children" class="windowbg' , $alt ? '' : '2' , '">
						<td colspan="'.($colspan-1).'" class="children">
							<strong>', $txt['parent_boards'], '</strong>: ', implode(', ', $children), '
						</td>
					</tr>';
				}
	}
	// render them in cells
	else
	{
		if($settings['childrender']==1)
		{
				if (!empty($cb))
				{
					$children = array();
					foreach ($cb as $child)
					{
						if (!$child['is_redirect'])
							$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><img src="' . $settings['lang_images_url'] . '/new.gif" class="new_posts" alt="" />' : '') . '</a>';
						else
							$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '">' . $child['name'] . '</a>';

						if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
							$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

						$children[] = '<div class="childb1">' . $child['link'] . '</div>';
					}
					echo '
					<tr  id="board_', $board_id, '_children" class="windowbg' , $alt ? '' : '2' , '">
						<td colspan="'.($colspan-1).'" class="children">
							<h4>&nbsp;', $txt['parent_boards'], '</h4><div class="container">', implode('', $children), '</div>
						</td>
					</tr>';
				}
		}
		// render them as new lines
		elseif($settings['childrender']==2)
		{
				if (!empty($cb))
				{
					$children = array();
					foreach ($cb as $child)
					{
						if (!$child['is_redirect'])
							$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' : '') . '</a>';
						else
							$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '">' . $child['name'] . '</a>';

						if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
							$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

						$children[] = '<tr class="windowbg"><td align="center" width="4%"><a href="' . $scripturl . '?board='.$child['id'].'.0;unread"><img src="'.$settings['images_url'].'/' . ($child['new'] ? 'on' : 'off') . '.png" style="width: 30px; height: 30px;" alt="" /></a></td><td>' . $child['link']  . ' ' . (!empty($child['description']) ? '<br>'.$child['description'] : '') . '</td><td align="center">' . comma_format($child['topics']) . ' ' . $txt['board_topics'] . '<br>' . comma_format($child['posts']) . ' ' . $txt['posts'] . '</td></tr>';
					}
					echo '
					<tr  id="board_', $board_id, '_children" class="windowbg' , $alt ? '' : '2' , '">
						<td colspan="'.($colspan-1).'">
							<h5>&nbsp;', $txt['parent_boards'], '</h5><table class="table_list">', implode('', $children), '</table>
						</td>
					</tr>';
				}
		}
		// do not show them
		elseif($settings['childrender']==3)
			return;

	}
}
?>