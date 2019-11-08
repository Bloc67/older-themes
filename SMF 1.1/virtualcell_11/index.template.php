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
	
	// **************** 
	// load custom language strings
	loadLanguage('ThemeStrings');

	// make sure undefined actions use their own template
	$settings['catch_action'] = array('layers' => array('main','pages'));
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
	// ******************* 
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
function template_main_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	if($context['page_title']=='' && isset($_GET['action']))
		$context['page_title']=$context['forum_name'].' - '.$_GET['action'];
	elseif($context['page_title']=='' && !isset($_GET['action']))
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
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css?fin11" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/print.css?fin11" media="print" />
	<link rel="alternate stylesheet" type="text/css" media="screen" title="small" href="', $settings['theme_url'], '/small.css?fin11" />
	<link rel="alternate stylesheet" type="text/css" media="screen" title="normal" href="', $settings['theme_url'], '/normal.css?fin11" />
	<link rel="alternate stylesheet" type="text/css" media="screen" title="big" href="', $settings['theme_url'], '/big.css?fin11" />
	<link rel="alternate stylesheet" type="text/css" media="screen" title="big2" href="', $settings['theme_url'], '/big2.css?fin11" />

	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/',$settings['width'],'.css?fin11" />
	';

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

	// We'll have to use the cookie to remember the header...
	if ($context['user']['is_guest'])
		$options['collapse_header'] = !empty($_COOKIE['upshrink']);

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'], '
        <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                var current_leftbar = ', empty($options['collapse_leftbar']) ? 'false' : 'true', ';

                function shrinkHeaderLeftbar(mode)
                {';

        // Guests don't have theme options!!
        if ($context['user']['is_guest'])
                echo '
                        document.cookie = "upshrink=" + (mode ? 1 : 0);';
        else
                echo '
                        smf_setThemeOption("collapse_leftbar", mode ? 1 : 0, null, "', $context['session_id'], '");';
        echo '
                        document.getElementById("upshrinkLeftbar").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

                        document.getElementById("leftbarHeader").style.display = mode ? "none" : "";

                        current_leftbar = mode;
                }
          // ]]></script>
       <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                var current_rightbar = ', empty($options['collapse_rightbar']) ? 'false' : 'true', ';

                function shrinkHeaderRightbar(mode)
                {';

        // Guests don't have theme options!!
        if ($context['user']['is_guest'])
                echo '
                        document.cookie = "upshrink=" + (mode ? 1 : 0);';
        else
                echo '
                        smf_setThemeOption("collapse_rightbar", mode ? 1 : 0, null, "', $context['session_id'], '");';

        echo '
                        document.getElementById("upshrinkRightbar").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

                        document.getElementById("rightbarHeader").style.display = mode ? "none" : "";

                        current_rightbar = mode;
                }
        // ]]></script>

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
			document.getElementById("upshrinkHeader").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");
			document.getElementById("user").style.display = mode ? "none" : "";
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
		// ]]></script>
	<script type="text/javascript" src="'.$settings['theme_url'].'/styleswitch.js"></script>
	<script type="text/javascript">
window.onload=function(){
var formref=document.getElementById("switchform")
indicateSelected(formref.choice)
}
</script>
</head>
<body>';

// check for some mods if they are indeed loaded :P
checkmods();
	
	echo '
<div id="mainframe">
	<div id="innerframe">';

	echo '
		<div id="userframe">
			<form id="switchform" style="padding: 5px;">
Choose fontsize: 
<select name="choice" size="1" onChange="chooseStyle(this.options[this.selectedIndex].value, 60)">
<option value="small">small</option>
<option value="normal" selected="selected">normal</option>
<option value="big">big</option>
<option value="big2">large</option>
</select>';

if(!empty($settings['use_tp'])){
// TinyPortal
	 if($context['TPortal']['leftbar'])
        echo '&nbsp;<a href="javascript:void(0);" onclick="shrinkHeaderLeftbar(!current_leftbar); return false;">&nbsp;L&nbsp;<img id="upshrinkLeftbar" src="', $settings['images_url'], '/', empty($options['collapse_leftbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '"  border="0" /></a><img id="upshrinkTempLeftbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
     if($context['TPortal']['rightbar'])
		echo '<a href="javascript:void(0);" onclick="shrinkHeaderRightbar(!current_rightbar); return false;">&nbsp;R&nbsp;<img id="upshrinkRightbar" src="', $settings['images_url'], '/', empty($options['collapse_rightbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" border="0" /></a><img id="upshrinkTempRightbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
}
echo '
			</form>
			<h1><a href="'.$scripturl.'">',$context['forum_name'],'</a></h1>
		</div>
		<h2>';
if($context['user']['is_logged'])
	echo '<b>',$txt['hello_member_ndt'],' ' , $context['user']['name'], '</b>&nbsp;&nbsp;<a href="' , $scripturl , '?action=unread">[unread posts]</a><a href="' , $scripturl , '?action=unreadreplies">[new replies]</a>';
else
	echo $txt['welcome_guest'];

echo '
		</h2>';

template_menu();
echo '
<div id="content">';
//Display ads on the top of the page
if (!empty($settings['use_ads']) && function_exists("show_topofpageAds"))
{
	$ads = show_topofpageAds();	
	if(!empty($ads))
		if($ads['type']==0)
			echo $ads['content'];
		else
			eval($ads['content']);	
		unset($ads);
}

	echo '		
							<table width="100%" cellspacing="0" cellpadding="2">
								<tr>';
	if(!empty($settings['use_ads']) && function_exists("show_towerleftAds"))
	{
		$ads = show_towerleftAds();	
		if(!empty($ads))
		{
			echo '		<td valign="top" class="leftpadding">';
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);
			echo '		</td>';
		}
		unset($ads);
	}

// TinyPortal integrated bars
	if(!empty($settings['use_tp']) && $context['TPortal']['leftbar'])
	{
		echo '<td width="' ,$context['TPortal']['leftbar_width'], '" style="padding-right: 1ex;" class="leftpadding"  valign="top">
                 <div id="leftbarHeader"', empty($options['collapse_leftbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['leftbar_width'], 'px;">';
        TPortal_sidebar('left');
        echo '</div></td>';
	}


	echo '					<td valign="top" width="100%">
										<table width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr>';

	// shoutbox mod
	if(!empty($settings['use_shoutbox']))
	{
		echo '								<td width="20%" valign="top"style="padding: 10px 3px 10px 0; ">
													<div style="padding: 3px;" class="titlebg">Shoutbox</div><div id="smfshout" class="windowbg2">' , smfshout() , '</div>
												</td>';
		$sidebar=true;
	}
	else
		$sidebar=false;

	echo '
												<td width="', $sidebar ? '80%' : '100%' , '" align="left" valign="top" style="padding-top: 10px; padding-bottom: 10px;">';
        if(!empty($settings['use_tp']) && $context['TPortal']['centerbar'])
                     echo '<div>' , TPortal_sidebar('center') , '</div>';
}

function template_main_below()
{
	global $context, $settings, $options, $scripturl, $txt;

   echo '
												</td>
											</tr>
										</table>
									</td>';
// TinyPortal integrated bars
	if(!empty($settings['use_tp']) && $context['TPortal']['rightbar'])
	{
		echo '<td width="' ,$context['TPortal']['rightbar_width'], '" style="padding: ' , isset($context['TPortal']['padding']) ? $context['TPortal']['padding'] : '4' , 'px; padding-top: 14px;padding-right: 1ex;" valign="top">
                 <div id="rightbarHeader"', empty($options['collapse_rightbar']) ? '' : ' style="display: none;"', ' style="padding-top: 5px; width: ' ,$context['TPortal']['rightbar_width'], 'px;">';
        TPortal_sidebar('right');
        echo '</div></td>';
	}

	if(!empty($settings['use_ads']) && function_exists("show_towerrightAds"))
	{
		$ads = show_towerrightAds();	
		if(!empty($ads))
		{
			echo '				<td valign="top">';
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);
			echo '				</td>';
		}
		unset($ads);
	}
	echo '					</tr>
							</table>
</td></tr></table>';
	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
		<div id="footerarea" style="clear: both; text-align: center; padding-bottom: 2em;">';

	if(!empty($settings['use_ads']) && function_exists("show_bottomAds"))
	{
		$ads = show_bottomAds();	
		if(!empty($ads))
		{
			echo '
								<div>';
			if($ads['type']==0)
				echo $ads['content'];
			else
				eval($ads['content']);
			echo '
								</div>';
		}
		unset($ads);
	}

	echo '
									<span class="smalltext">', theme_copyright(), ' <br />
									' , $settings['use_tp'] ? tportal_version().'<br />' : '' , '
									<strong>VirtualCell</strong> design by <a href="http.//www.blocweb.net" target="_blank">BlocWeb</a> |
									<a href="http://validator.w3.org/check/referer" target="_blank">XHTML</a> |
									<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS</a></span>';

		// Show the load time?
	if ($context['show_load_time'])
		echo '
		<span class="smalltext"><br />', $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'], '</span>';

	if (!empty($settings['use_gb']) && isset($context['ob_googlebot_stats']))
		echo '
		<br /><br /><span class="smalltext">', $txt['ob_googlebot_stats_lastvisit'], timeformat($context['ob_googlebot_stats']['Googlebot']['lastvisit']), '</span>';

	// This is an interesting bug in Internet Explorer AND Safari. Rather annoying, it makes overflows just not tall enough.
	if (($context['browser']['is_ie'] && !$context['browser']['is_ie4']) || $context['browser']['is_mac_ie'] || $context['browser']['is_safari'] || $context['browser']['is_firefox'])
	{
		// The purpose of this code is to fix the height of overflow: auto div blocks, because IE can't figure it out for itself.
		echo '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[';

		// Unfortunately, Safari does not have a "getComputedStyle" implementation yet, so we have to just do it to code...
		if ($context['browser']['is_safari'])
			echo '
			window.addEventListener("load", smf_codeFix, false);

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if ((codeFix[i].className == "code" || codeFix[i].className == "post" || codeFix[i].className == "signature") && codeFix[i].offsetHeight < 20)
						codeFix[i].style.height = (codeFix[i].offsetHeight + 20) + "px";
				}
			}';
		elseif ($context['browser']['is_firefox'])
			echo '
			window.addEventListener("load", smf_codeFix, false);
			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = 0; i < codeFix.length; i++)
				{
					if (codeFix[i].className == "code" && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0))
						codeFix[i].style.overflow = "scroll";
				}
			}';			
		else
			echo '
			var window_oldOnload = window.onload;
			window.onload = smf_codeFix;

			function smf_codeFix()
			{
				var codeFix = document.getElementsByTagName ? document.getElementsByTagName("div") : document.all.tags("div");

				for (var i = codeFix.length - 1; i > 0; i--)
				{
					if (codeFix[i].currentStyle.overflow == "auto" && (codeFix[i].currentStyle.height == "" || codeFix[i].currentStyle.height == "auto") && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0) && (codeFix[i].offsetHeight != 0 || codeFix[i].className == "code"))
						codeFix[i].style.height = (codeFix[i].offsetHeight + 36) + "px";
				}

				if (window_oldOnload)
				{
					window_oldOnload();
					window_oldOnload = null;
				}
			}';

		echo '
		// ]]></script>';
	}

	echo '
	</div>
	</div>
	</div>
</div>';

	// The following will be used to let the user know that some AJAX process is running
	echo '
<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
	global $context, $settings, $options;

	echo '<div id="linktree">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '" class="nav">' . $tree['name'] . '</a>' : $tree['name'];

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo '&nbsp;&#187;&nbsp;';
	}

	echo '</div>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Work out where we currently are.
	$current_action = 'home';
	if (in_array($context['current_action'], array('admin', 'managegames', 'arcadesettings', 'arcadecategory', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$current_action = 'admin';
	if (in_array($context['current_action'], array('gallery','search', 'arcade', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
		$current_action = $context['current_action'];
	if ($context['current_action'] == 'globalAnnouncementsAdmin')
		$current_action = 'admin';

	if ($context['current_action'] == 'search2')
		$current_action = 'search';

	if (isset($_GET['dl']))
		$current_action = 'dlmanager';

	if ((isset($_GET['board']) || isset($_GET['topic'])) && empty($settings['use_tp']))
		$current_action = 'home';
	elseif ((isset($_GET['board']) || isset($_GET['topic'])) && !empty($settings['use_tp']))
		$current_action = 'forum';

	if ($context['current_action'] == 'theme')
		$current_action = isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'pick' ? 'profile' : 'admin';

	// Begin SMFShop code
	if ($context['current_action'] == 'shop')
		$current_action = 'shop';
	if (in_array($context['current_action'], array('shop_general', 'shop_items_add', 'shop_items_edit', 'shop_cat', 'shop_inventory', 'shop_restock', 'shop_usergroup')))
		$current_action = 'admin';
	// End SMFShop code


	// Show the start of the tab section.
	echo '<ul id="menubox">
					<li><a' , $current_action=='home' ? ' class="chosen"' : '' , ' href="', $scripturl, '">' , $txt[103] , '</a></li>';
// TP
if (!empty($settings['use_tp']))
	echo '<li ><a' , $current_action=='forum' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=forum">' , $txt['tp-forum'] , '</a></li>';

echo '
					<li><a' , $current_action=='help' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=help">' , $txt[119] , '</a></li>';
					if ($context['allow_search'])
						echo '<li><a' , $current_action=='search' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=search">Search</a></li>';
					if ($context['allow_calendar'])
						echo '<li><a' , $current_action=='calendar' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=calendar">' , $txt['calendar24'] , '</a></li>';
					if ($context['allow_admin'])
						echo '<li><a' , $current_action=='admin' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=admin">' , $txt[2] , '</a></li>';	
					if ($context['allow_edit_profile'])
						echo '<li><a' , $current_action=='profile' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=profile">' , $txt[79] , '</a></li>';

					if ($context['user']['is_logged'] && $context['allow_pm'])
						echo '<li><a' , $current_action=='pm' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=pm">' , $txt['pm_short'] , ' ', $context['user']['unread_messages'] > 0 ? '[<strong>'. $context['user']['unread_messages'] . '</strong>]' : '' , '</a></li>';

					// SMF Arcade
					if (!empty($settings['use_arcade']))
						echo '<li><a' , $current_action=='arcade' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=arcade">' , $txt['arcade'] , '</a></li>';
					// SMF Gallery
					if (!empty($settings['use_smfgallery']))
						echo '<li><a' , $current_action=='gallery' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=gallery">' , $txt['smfgallery_menu'] , '</a></li>';
					// SMFshop 
					if (!empty($settings['use_shop']))
						echo '<li><a' , $current_action=='shop' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=shop">Shop</a></li>';

					if ($context['user']['is_logged'])
						echo '<li class="last"><a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">' , $txt[108] , '</a></li>';
					if ($context['user']['is_guest'])
					{
						echo '<li><a' , $current_action=='login' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=login">' , $txt[34] , '</a></li>';	
						echo '<li class="last"><a' , $current_action=='register' ? ' class="chosen"' : '' , ' href="', $scripturl, '?action=register">' , $txt[97] , '</a></li>';	
					}
					echo '
				</ul>';

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
	$mid='';
	echo '
		<td valign="top" class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>
		<td valign="top" class="', $direction == 'top' ? 'main' : 'mirror', 'tab_back">', implode($mid, $button_strip) , '</td>
		<td valign="top" class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
}
function topmenu()
{
	global $context, $txt, $scripturl, $settings;

	$current_action='forum';
	if (!empty($settings['use_tp']) && (isset($_GET['board']) || isset($_GET['topic'])))
		$current_action = 'forum';

	echo '<div id="topmenu"><ul><li><a ' , $current_action=='forum' ? 'class="chosen" ' : '' , 'href="',$scripturl,'?action=forum">Forum</a></li>';
	foreach($context['sitemenu'] as $menu => $val)
	{
		if($val[2]=='page')
			echo '<li><a href="',$scripturl,'?action='.$val[0].'">'.$val[1].'</a></li>';
		elseif($val[2]=='link')
			echo '<li><a href="'.$val[0].'">'.$val[1].'</a></li>';
	}
	echo '</ul></div>';
}


function checkmods()
{
	global $context, $settings, $options, $scripturl, $txt;

	if($context['user']['is_admin'])
	{
		$error='';
		// TP 
		if(!empty($settings['use_tp']) && !isset($context['TPortal']['showtop']))
			$error .= '<div class="errorbar" style="background: #f00;">You have turned on support for <b>TinyPortal</b>, but it doesn\'t seem the mod is actually installed!</div>';
		// Shoutbox
		if(!empty($settings['use_shoutbox']) && !function_exists('smfshout'))
			$error .= '<div class="errorbar" style="background: #f80;">You have turned on support for <b>Ultimate Shoutbox</b>, but it doesn\'t seem the mod is actually installed!</div>';

		// render it
		if(!empty($error))
			echo '<div id="errorpanel">'.$error.'</div>';
	}
}

?>