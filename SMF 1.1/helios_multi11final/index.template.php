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
	$settings['theme_version'] = '1.1';
	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = false;

	/* Use plain buttons - as oppossed to text buttons? */
	$settings['use_buttons'] = false;

	/* Show sticky and lock status seperate from topic icons? */
	$settings['seperate_sticky_lock'] = false;




if(!$context['user']['is_guest'] && isset($_POST['options']['theme_color']))
{
   include_once($GLOBALS['sourcedir'] . '/Profile.php');
   makeThemeChanges($context['user']['id'], $settings['theme_id']);
   $options['theme_color'] = $_POST['options']['theme_color'];
}
elseif ($context['user']['is_guest'])
{
   if (isset($_POST['options']['theme_color']))
   {
      $_SESSION['theme_color'] = $_POST['options']['theme_color'];
      $options['theme_color'] = $_SESSION['theme_color'];
   }
   elseif (isset($_SESSION['theme_color']))
      $options['theme_color'] = $_SESSION['theme_color'];
}


}

// The main sub template above the content.
function template_main_above()
{
        global $context, $settings, $options, $scripturl, $txt, $modSettings;

if (isset($options['theme_color']))
   $mycolor = $options['theme_color'];
else{
   // Defaults.
   $options['theme_color'] = isset($settings['default_theme_color']) ? $settings['default_theme_color'] : 'brown';
   $mycolor=$options['theme_color'];
}

if(isset($settings['color_change_off']) && $settings['color_change_off']==1)
  $options['theme_color'] = isset($settings['default_theme_color']) ? $settings['default_theme_color'] : 'brown';

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '><head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title'], '" />', empty($context['robot_no_index']) ? '' : '
	<meta name="robots" content="noindex" />', '
	<meta name="keywords" content="PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum" />
	<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/script.js?rc3"></script>
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
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/print.css?fin11" media="print" />';

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
                        document.getElementById("upshrink").src = smf_images_url + (mode ? "/upshrink2.gif" : "/upshrink.gif");

                        document.getElementById("upshrinkHeader").style.display = mode ? "none" : "";

                        current_header = mode;
                }
        // ]]></script>
</head>
<body><div><table cellpadding="0" align="center" cellspacing="0" style="width: ' , (isset($settings['forum_width']) && !empty($settings['forum_width'])) ? $settings['forum_width'] : '100%' ,  ';"><tr><td>';
        $topbox='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 16px; width: 22px;"><img src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topleft.gif" style="height: 16px; width: 22px; border: 0px; padding: 0px; margin: 0px;" alt="gfx" /></td><td style="height: 16px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topmid.gif); "></td><td style="height: 16px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topright.gif" style="height: 16px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr><tr><td style="width: 22px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midleft.gif); ">&nbsp;</td><td valign="top">';
        $botbox='</td><td style="width: 27px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midright.gif); ">&nbsp;</td></tr><tr><td valign="top" style="height: 14px; width: 22px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botleft.gif" style="height: 14px; width: 22px; border: 0px; padding: 0px; margin: 0px;" /></td><td style="height: 14px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botmid.gif); ">&nbsp;</td><td valign="top" style="height: 14px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botright.gif" style="height: 14px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        $leftbox='<table cellpadding="0" width="100%" cellspacing="0" border="0"><tr><td valign="top" style="height: 44px; width: 124px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-stat-left.gif" style="height: 44px; width: 124px; border: 0px; padding: 0px; margin: 0px;" /></td><td nowrap="nowrap" style="text-align: center; height: 44px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-stat-mid.gif); ">';
        $rightbox='</td><td valign="top" style="height: 44px; width: 135px;"><img alt="*" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-stat-right.gif" style="height: 44px; width: 135px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        if(isset($settings['topbar']) && !empty($settings['topbar']))
               echo '<div style="text-align: center; width: 99%">'.$settings['topbar'].'</div>';


    if(!isset($settings['color_change_off']) || (isset($settings['color_change_off']) && $settings['color_change_off']==0))
      echo '
      <form action="', $scripturl, '" style="text-align: right; margin-right: 30px; margin-bottom: 2px; margin-top: 2px;" method="post" class="smalltext">
         <input style="border: solid 1px #808080; background-color: #503000; color: #503000; font-size: 6px;" type="submit" value="brown" name="options[theme_color]">
         <input style="border: solid 1px #808080; background-color: #600000; color: #600000; font-size: 6px;" type="submit" value="red" name="options[theme_color]">
         <input style="border: solid 1px #808080; background-color: #908000; color: #908000; font-size: 6px;" type="submit" value="golden" name="options[theme_color]">
         <input style="border: solid 1px #808080; background-color: #005000; color: #005000; font-size: 6px;" type="submit" value="green" name="options[theme_color]">
         <input style="border: solid 1px #808080; background-color: #000040; color: #000040; font-size: 6px;" type="submit" value="blue" name="options[theme_color]">
         <input style="border: solid 1px #808080; background-color: #606060; color: #606060; font-size: 6px;" type="submit" value="silver" name="options[theme_color]">
       </form>';

        echo '<div id="upshrinkHeader"', empty($options['collapse_header']) ? '' : ' style="display: none;"', '><div style="padding-right: 10px;">'.$topbox.'<table width="100%" cellpadding="0" cellspacing="0" border="0">';
         echo '<tr><td width="45%" valign="bottom" style="padding-right: 10px;"><div style="padding-right: 0px;">';

        echo '<table width="99%" cellpadding="4" cellspacing="5" border="0" style="margin-left: 2px;"><tr>';

        if (!empty($context['user']['avatar']))
                echo '<td valign="top">', $context['user']['avatar']['image'], '</td>';

        echo '<td width="100%" valign="top" class="smalltext" style="font-family: verdana, arial, sans-serif;">
        ';

        // If the user is logged in, display stuff like their name, new messages, etc.
        if ($context['user']['is_logged'])
        {
                echo '
                                                        ', $txt['hello_member'], ' <b>', $context['user']['name'], '</b>';

                // Only tell them about their messages if they can read their messages!
                if ($context['allow_pm'])
                        echo ', ', $txt[152], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt[153] : $txt[471], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
                echo '.';

                // Is the forum in maintenance mode?
                if ($context['in_maintenance'] && $context['user']['is_admin'])
                        echo '<br />
                                                        <b>', $txt[616], '</b>';

                // Are there any members waiting for approval?
                if (!empty($context['unapproved_members']))
                        echo '<br />
							', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';

                // Show the total time logged in?
                if (!empty($context['user']['total_time_logged_in']))
                {
                        echo '
                                                        <br />', $txt['totalTimeLogged1'];

                        // If days is just zero, don't bother to show it.
                        if ($context['user']['total_time_logged_in']['days'] > 0)
                                echo $context['user']['total_time_logged_in']['days'] . $txt['totalTimeLogged2'];

                        // Same with hours - only show it if it's above zero.
                        if ($context['user']['total_time_logged_in']['hours'] > 0)
                                echo $context['user']['total_time_logged_in']['hours'] . $txt['totalTimeLogged3'];

                        // But, let's always show minutes - Time wasted here: 0 minutes ;).
                        echo $context['user']['total_time_logged_in']['minutes'], $txt['totalTimeLogged4'];
                }

                echo '<br />
                                                        <a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br />
                                                        <a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a>';

        }
        // Otherwise they're a guest - so politely ask them to register or login.
        else
        {
                echo '
                                                        ', $txt['welcome_guest'], '<br />

							<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/sha1.js"></script>

								<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" class="middletext" style="margin: 3px 1ex 1px 0;"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
								<div style="text-align: right;">
									<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" />
									<select name="cookielength">
										<option value="60">', $txt['smf53'], '</option>
										<option value="1440">', $txt['smf47'], '</option>
										<option value="10080">', $txt['smf48'], '</option>
										<option value="43200">', $txt['smf49'], '</option>
										<option value="-1" selected="selected">', $txt['smf50'], '</option>
									</select>
									<input type="submit" value="', $txt[34], '" /><br />
									<span class="middletext">', $txt['smf52'], '</span>
									<input type="hidden" name="hash_passwrd" value="" />
								</div>
							</form>';
	}

        echo '
                                                </td></tr></table>';

        echo '</div></td><td style="padding-right: 10px;" width="50%" align="right" >';
        if(isset($settings['userlogo']) && !empty($settings['userlogo']))
               echo '<a href="index.php"><img src="'.$settings['userlogo'].'" border="0" alt="logo" /></a>';

        echo ' </td></tr></table>'.$botbox.'</div>';

        if (!empty($settings['enable_news']))
               echo '<div style="padding-right: 10px;">'. $topbox.'<div style="padding: 6px; text-align: center;">'.$context['random_news_line'].'</div>'.$botbox.'</div>';

        // news
            echo '
                   </div>';
        // stats
            echo '<div style="padding-left: 0px;">'.$leftbox.'<table width="99%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" style="color: #88908a; font-size: 8pt; font-family: tahoma, helvetica, serif;"><b>', $modSettings['totalMessages'], '</b> ', $txt[95], ' ', $txt['smf88'], ' <b>
                      ', $modSettings['totalTopics'], '</b> ', $txt[64], ' ', $txt[525], ' <b>', $modSettings['totalMembers'], '</b>
                      ',  $txt[19], '
                      - ', $txt[656], ': <b> <a style="color: #b0b0b0" href="', $scripturl , '?action=profile;u=' , $modSettings['latestMember'] , '">' , $modSettings['latestRealName'] , '</a></b>
                      </td><td align="right" style="color: #b0b0b0; font-size: 8pt; font-family: tahoma, helvetica, serif;">'.$context['current_time'].'</td></tr></table>'.$rightbox.'</div>';


                // Show the menu here, according to the menu sub template.
 echo '<table cellpadding="0" cellspacing="0" border="0" style="text-align: left; margin-left: 20px; background-image: url(' . $settings['images_url'] . '/pod/'.$options['theme_color'].'/pod-midbar.gif);" ><tr>';
 echo '<td style="width: 39%; background-color: black;"> </td><td><img src="' . $settings['images_url'] . '/pod/'.$options['theme_color'].'/pod-leftbar.gif" alt="gfx" style="margin: 0px 0;" border="0" /></td>';
 echo '<td><a href="#" onclick="shrinkHeader(!current_header); return false;"><img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 0px 0;" border="0" /></a></td><td><img id="upshrinkTemp" src="', $settings['images_url'], '/blank.gif" alt="" style="width: 2px; height: 20px;margin: 0px 0;" /></td>';
                template_menu();
echo '</td><td style="background-color: black;width: 59%;"> </td></tr></table>';
        // The main content should go here.  A table is used because IE 6 just can't handle a div.
        echo '
        <table style="margin-top: 4px;" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
         echo '<td align="left" id="bodyarea" style="padding-left: 3px; padding-right: 8px; padding-top: 0px; padding-bottom: 10px;">';
}

function template_main_below()
{
        global $context, $settings, $options, $scripturl, $txt;

        $leftboxbot3='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-left: 8px; height: 58px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-maxbotleft.gif" style="height: 58px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td valign="top" nowrap="nowrap" style="text-align: center; height: 58px; background-repeat: repeat-x;background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-maxbotmid.gif); ">';
        $rightboxbot3='</td><td valign="top" style="padding-right: 5px; height: 58px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-maxbotright.gif" style="height: 58px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        echo '</td>
        </tr></table>';

        // Show the "Powered by" and "Valid" logos, as well as the copyright.  Remember, the copyright must be somewhere!
        echo $leftboxbot3.'
<div style="padding-top: 15px;">
                <table cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
                        <tr>
                                <td valign="middle" align="center" style="white-space: nowrap;">
                                        ', theme_copyright(), '
                                </td><td align="right"><span class="smalltext">';
        // Show the load time?
        if ($context['show_load_time'])
                echo
                $txt['smf301'], $context['load_time'], $txt['smf302'], $context['load_queries'], $txt['smf302b'];

        echo '<br /><b>Helios Multi</b> design by <a target="_blank" href="http://www.bloczone.net/smf">BlocWeb</a></span></td>
             </tr>
          </table></div>'.$rightboxbot3;
        echo '<div style="text-align: center;">
                             <a href="http://www.mysql.com/" target="_blank"><img id="powered-mysql" src="', $settings['images_url'], '/powered-mysql.gif" alt="', $txt['powered_by_mysql'], '" border="0"  /></a>
                             <a href="http://www.php.net/" target="_blank"><img id="powered-php" src="', $settings['images_url'], '/powered-php.gif" alt="', $txt['powered_by_php'], '" border="0"  /></a>
                                       <a href="http://validator.w3.org/check/referer" target="_blank"><img id="valid-xhtml10" src="', $settings['images_url'], '/valid-xhtml10.gif" alt="', $txt['valid_xhtml'], '" border="0" /></a>
                                        <a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank"><img id="valid-css" src="', $settings['images_url'], '/valid-css.gif" alt="', $txt['valid_css'], '" border="0" /></a>

        </div>';


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
		{
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
		}

		echo '
		// ]]></script>';
	}

        echo '</td></tr></table></div>
	<div id="ajax_in_progress" style="display: none;', $context['browser']['is_ie'] && !$context['browser']['is_ie7'] ? 'position: absolute;' : '', '">', $txt['ajax_in_progress'], '</div>
        </body>
</html>';
}


// Show a linktree.  This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree()
{
        global $context, $settings, $options;

        // Folder style or inline?  Inline has a smaller font.
        echo '<span class="nav">';

        // Each tree item has a URL and name.  Some may have extra_before and extra_after.
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
                        echo ' &nbsp;|&nbsp; ';
        }

        echo '</span>';
}

// Show the menu up top.  Something like [home] [help] [profile] [logout]...
function template_menu()
{
        global $context, $settings, $options, $scripturl, $txt;

        // Show the [home] and [help] buttons.

        echo '<td><a href="', $scripturl, '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/home.gif" alt="' . $txt[103] . '" style="margin: 0px 0;" border="0" />' : $txt[103]), '</a></td>';
        echo '<td><a href="', $scripturl, '?action=help" >', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/help.gif" alt="' . $txt[119] . '" style="margin: 0px 0;" border="0" />' : $txt[119]), '</a></td>';

        // How about the [search] button?
        if ($context['allow_search'])
                echo '<td><a href="', $scripturl, '?action=search">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/search.gif" alt="' . $txt[182] . '" style="margin: 0px 0;" border="0" />' : $txt[182]), '</a></td>';

        // Is the user allowed to administrate at all? ([admin])
        if ($context['allow_admin'])
                echo '<td><a href="', $scripturl, '?action=admin">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/admin.gif" alt="' . $txt[2] . '" style="margin: 0px 0;" border="0" />' : $txt[2]), '</a></td>';

        // Edit Profile... [profile]
        if ($context['allow_edit_profile'])
                echo '<td><a href="', $scripturl, '?action=profile">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/profile.gif" alt="' . $txt[79] . '" style="margin: 0px 0;" border="0" />' : $txt[467]), '</a></td>';

        // The [calendar]!
        if ($context['allow_calendar'])
                echo '<td><a href="', $scripturl, '?action=calendar">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/calendar.gif" alt="' . $txt['calendar24'] . '" style="margin: 0px 0;" border="0" />' : $txt['calendar24']), '</a></td>';

        // If the user is a guest, show [login] and [register] buttons.
        if ($context['user']['is_guest'])
        {
                echo '<td><a href="', $scripturl, '?action=login">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/login.gif" alt="' . $txt[34] . '" style="margin: 0px 0;" border="0" />' : $txt[34]), '</a></td>';
                echo '<td><a href="', $scripturl, '?action=register">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/register.gif" alt="' . $txt[97] . '" style="margin: 0px 0;" border="0" />' : $txt[97]), '</a></td>';
        }
        // Otherwise, they might want to [logout]...
        else{
              echo '<td><a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/logout.gif" alt="' . $txt[108] . '" style="margin: 0px 0;" border="0" />' : $txt[108]), '</a></td>';
              }

       // first button...is it on?
       if(isset($settings['custombutton1_use'])  && $settings['custombutton1_use']==true){
          // is it memberonly?
            if(isset($settings['custombutton1_member']) && $settings['custombutton1_member']==true){
                // it is, check if guest
                if($context['user']['is_logged']){
                   // member, check if its not empty...
                   if(isset($settings['custombutton1']) && !empty($settings['custombutton1']) && isset($settings['custombutton1_link']) && !empty($settings['custombutton1_link']))
                      echo '<td><a href="'. $settings['custombutton1_link'].'"><img src="' . $settings['images_url'] . '/' . $settings['custombutton1'] . '" alt="" style="margin: 0px 0;" border="0" /></a></td>';
                }
            }
            else{
                   // guests too, check if its not empty...
                   if(isset($settings['custombutton1']) && !empty($settings['custombutton1']) && isset($settings['custombutton1_link']) && !empty($settings['custombutton1_link']))
                      echo '<td><a href="'. $settings['custombutton1_link'].'"><img src="' . $settings['images_url'] . '/' . $settings['custombutton1'] . '" alt="" style="margin: 0px 0;" border="0" /></a></td>';
            }
       }
       // second button...is it on?
       if(isset($settings['custombutton2_use'])  && $settings['custombutton2_use']==true){
          // is it memberonly?
            if(isset($settings['custombutton2_member']) && $settings['custombutton2_member']==true){
                // it is, check if guest
                if($context['user']['is_logged']){
                   // member, check if its not empty...
                   if(isset($settings['custombutton2']) && !empty($settings['custombutton2']) && isset($settings['custombutton2_link']) && !empty($settings['custombutton2_link']))
                      echo '<td><a href="'. $settings['custombutton2_link'].'"><img src="' . $settings['images_url'] . '/' . $settings['custombutton2'] . '" alt="" style="margin: 0px 0;" border="0" /></a></td>';
                }
            }
            else{
                   // guests too, check if its not empty...
                   if(isset($settings['custombutton2']) && !empty($settings['custombutton2']) && isset($settings['custombutton2_link']) && !empty($settings['custombutton2_link']))
                      echo '<td><a href="'. $settings['custombutton2_link'].'"><img src="' . $settings['images_url'] . '/' . $settings['custombutton2'] . '" alt="" style="margin: 0px 0;" border="0" /></a></td>';
            }
       }
       // third button...is it on?
       if(isset($settings['custombutton3_use'])  && $settings['custombutton3_use']==true){
          // is it memberonly?
            if(isset($settings['custombutton3_member']) && $settings['custombutton3_member']==true){
                // it is, check if guest
                if($context['user']['is_logged']){
                   // member, check if its not empty...
                   if(isset($settings['custombutton3']) && !empty($settings['custombutton3']) && isset($settings['custombutton3_link']) && !empty($settings['custombutton3_link']))
                      echo '<td><a href="'. $settings['custombutton3_link'].'"><img src="' . $settings['images_url'] . '/' . $settings['custombutton3'] . '" alt="" style="margin: 0px 0;" border="0" /></a></td>';
                }
            }
            else{
                   // guests too, check if its not empty...
                   if(isset($settings['custombutton3']) && !empty($settings['custombutton3']) && isset($settings['custombutton3_link']) && !empty($settings['custombutton3_link']))
                      echo '<td><a href="'. $settings['custombutton3_link'].'"><img src="' . $settings['images_url'] . '/' . $settings['custombutton3'] . '" alt="" style="margin: 0px 0;" border="0" /></a></td>';
            }
       }


       echo '<td><img src="' . $settings['images_url'] . '/pod/'.$options['theme_color'].'/pod-rightbar.gif" alt="gfx" style="margin: 0px 0;" border="0" />';

}
// Generate a strip of buttons, out of buttons.
function template_button_strip($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl;

	if (empty($button_strip))
		return '';

	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if (isset($value['test']) && empty($context[$value['test']]))
		{
			unset($button_strip[$key]);
			continue;
		}
		elseif (!isset($buttons[$key]) || $force_reset)
			$buttons[$key] = '<a href="' . $value['url'] . '" ' .( isset($value['custom']) ? $value['custom'] : '') . '>' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . ($value['lang'] ? $context['user']['language'] . '/' : '') . $value['image'] . '" alt="' . $txt[$value['text']] . '" border="0" />' : $txt[$value['text']]) . '</a>';

		$button_strip[$key] = $buttons[$key];
	}

	echo '
		<td ', $custom_td, '>', implode($context['menu_separator'], $button_strip) , '</td>';
}

?>