<?php
/**
 * Helios theme for SMF
 *
 * @theme SMF
 * @author Blocweb
 * @copyright 2011 Blocweb
 * @license http://www.blocweb.net/license.txt BSD
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

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;

	if(!$context['user']['is_guest'] && isset($_POST['options']['theme_color']))
	{
	   include_once($GLOBALS['sourcedir'] . '/Profile-Modify.php');
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
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?rc3 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?rc3" />';

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
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?rc5"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?rc5"></script>
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
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css?fin11" />
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

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body><div><table cellpadding="0" align="center" cellspacing="0" style="width: ' , (isset($settings['forum_width']) && !empty($settings['forum_width'])) ? $settings['forum_width'] : '100%' ,  ';"><tr><td>';
   
		
		$topbox='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 16px; width: 22px;"><img src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topleft.gif" style="height: 16px; width: 22px; border: 0px; padding: 0px; margin: 0px;" alt="gfx" /></td><td style="height: 16px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topmid.gif); "></td><td style="height: 16px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topright.gif" style="height: 16px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr><tr><td style="width: 22px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midleft.gif); ">&nbsp;</td><td valign="top">';
        $botbox='</td><td style="width: 27px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midright.gif); ">&nbsp;</td></tr><tr><td valign="top" style="height: 14px; width: 22px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botleft.gif" style="height: 14px; width: 22px; border: 0px; padding: 0px; margin: 0px;" /></td><td style="height: 14px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botmid.gif); ">&nbsp;</td><td valign="top" style="height: 14px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botright.gif" style="height: 14px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        $leftbox='<table cellpadding="0" width="100%" cellspacing="0" border="0"><tr><td valign="top" style="height: 44px; width: 124px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-stat-left.gif" style="height: 44px; width: 124px; border: 0px; padding: 0px; margin: 0px;" /></td><td nowrap="nowrap" style="text-align: center; height: 44px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-stat-mid.gif); ">';
        $rightbox='</td><td valign="top" style="height: 44px; width: 135px;"><img alt="*" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-stat-right.gif" style="height: 44px; width: 135px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

		$leftboxm='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 54px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-lefttop.gif" style="height: 54px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td nowrap="nowrap" style="text-align: center; height: 54px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-midtop.gif); ">';
		$rightboxm='</td><td style="height: 54px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-topright.gif" style="height: 54px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

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
                        echo ', ', $txt['msg_alert_you_have'], ' <a href="', $scripturl, '?action=pm">', $context['user']['messages'], ' ', $context['user']['messages'] != 1 ? $txt['msg_alert_messages'] : $txt['message_lowercase'], '</a>', $txt['newmessages4'], ' ', $context['user']['unread_messages'], ' ', $context['user']['unread_messages'] == 1 ? $txt['newmessages0'] : $txt['newmessages1'];
                echo '.';

                // Is the forum in maintenance mode?
                if ($context['in_maintenance'] && $context['user']['is_admin'])
                        echo '<br />
                                                        <b>', $txt['maintain_mode_on'], '</b>';

                // Are there any members waiting for approval?
                if (!empty($context['unapproved_members']))
                        echo '<br />
							', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '<br />';


                echo '<br />
                                                        <a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br />
                                                        <a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a>';

        }
        // Otherwise they're a guest - so politely ask them to register or login.
	elseif (!empty($context['show_login_bar']))
        {
			echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div class="info">', $txt['login_or_register'], '</div>
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
				</form>';
		}

        echo '
                                                </td></tr></table>';

        echo '</div></td><td style="padding-right: 10px;" width="50%" align="right" >';
        if(!empty($settings['header_logo_url']))
               echo '<a href="index.php"><img src="'.$settings['header_logo_url'].'" border="0" alt="logo" /></a>';

        echo ' </td></tr></table>'.$botbox.'</div>';

        if (!empty($settings['enable_news']))
               echo '<div style="padding-right: 10px;">'. $topbox.'<div style="padding: 6px; text-align: center;">'.$context['random_news_line'].'</div>'.$botbox.'</div>';

        // news
            echo '
                   </div>';
        // stats
            echo '<div style="padding-left: 0px;">'.$leftbox.'<table width="99%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" style="color: #88908a; font-size: 8pt; font-family: tahoma, helvetica, serif;"><b>', $modSettings['totalMessages'], '</b> ', $txt['posts_made'], ' ', $txt['in'], ' <b>
                      ', $modSettings['totalTopics'], '</b> ', $txt['topics'], ' ', $txt['by'], ' <b>', $modSettings['totalMembers'], '</b>
                      ',  $txt['members'], '
                      - ', $txt['latest_member'], ': <b> <a style="color: #b0b0b0" href="', $scripturl , '?action=profile;u=' , $modSettings['latestMember'] , '">' , $modSettings['latestRealName'] , '</a></b>
                      </td><td align="right" style="color: #b0b0b0; font-size: 8pt; font-family: tahoma, helvetica, serif;">'.$context['current_time'].'</td></tr></table>'.$rightbox.'</div>';


                // Show the menu here, according to the menu sub template.
 echo '<table cellpadding="0" cellspacing="0" border="0" style="text-align: left; margin-left: 20px; background: url(' . $settings['images_url'] . '/pod/'.$options['theme_color'].'/pod-midbar.gif) repeat-x black;" ><tr>';
 echo '<td style="width: 39%; background-color: black;"> </td><td valign="top"><img src="' . $settings['images_url'] . '/pod/'.$options['theme_color'].'/pod-leftbar.gif" alt="gfx" style="margin: 0px 0;" border="0" /></td>';
 echo '<td valign="bottom" ><img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin:  0;" border="0" /></td><td><img id="upshrinkTemp" src="', $settings['images_url'], '/blank.gif" alt="" style="width: 2px; height: 20px;margin: 0px 0;" /></td>';
                template_menu_smf();
echo '<td style="background-color: black;width: 59%;"> </td></tr></table>';

       // Show some statistics next to the link tree if SP1 info is off.
        echo $leftboxm.'
<table width="100%" cellpadding="0" cellspacing="0">
        <tr>
                <td style="text-align: left; padding-left: 0px;" valign="bottom">', theme_linktree(), '</td>
        </tr>
</table>'.$rightboxm;
		
		if(!in_array($context['current_action'],array('','forum')) || isset($_GET['board']) || isset($_GET['topic']))
			echo $topbox;
		// The main content should go here.  A table is used because IE 6 just can't handle a div.
        echo '
        <table style="table-layout: fixed;" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
         echo '<td align="left" id="bodyarea" style="padding-left: 3px; padding-right: 8px; padding-top: 0px; padding-bottom: 10px;">';
}

function template_main_below()
{
        global $context, $settings, $options, $scripturl, $txt;

       $topbox='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 16px; width: 22px;"><img src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topleft.gif" style="height: 16px; width: 22px; border: 0px; padding: 0px; margin: 0px;" alt="gfx" /></td><td style="height: 16px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topmid.gif); "></td><td style="height: 16px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topright.gif" style="height: 16px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr><tr><td style="width: 22px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midleft.gif); "></td><td valign="top">';
        $botbox='</td><td style="width: 27px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midright.gif); "></td></tr><tr><td style="height: 14px; width: 22px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botleft.gif" style="height: 14px; width: 22px; border: 0px; padding: 0px; margin: 0px;" /></td><td style="height: 14px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botmid.gif); "></td><td style="height: 14px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botright.gif" style="height: 14px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';
        $leftboxbot3='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-left: 8px; height: 58px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-maxbotleft.gif" style="height: 58px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td valign="top" nowrap="nowrap" style="text-align: center; height: 58px; background-repeat: repeat-x;background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-maxbotmid.gif); ">';
        $rightboxbot3='</td><td valign="top" style="padding-right: 5px; height: 58px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-maxbotright.gif" style="height: 58px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

		echo '</td>
        </tr></table>';
		if(!in_array($context['current_action'],array('','forum')) || isset($_GET['board']) || isset($_GET['topic']))
			echo $botbox;

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
                $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'];

        echo '<br /><a href="' , $settings['theme_url'] , '/licence.txt"><b>Helios Multi</b></a> &copy; <a href="http://www.blocweb.net">Bloc</a></span></td>
             </tr>
          </table></div>'.$rightboxbot3;
        echo '<div style="text-align: center;">
                             <a href="http://www.mysql.com/" target="_blank"><img id="powered-mysql" src="', $settings['images_url'], '/powered-mysql.gif" alt="', $txt['powered_by_mysql'], '" border="0"  /></a>
                             <a href="http://www.php.net/" target="_blank"><img id="powered-php" src="', $settings['images_url'], '/powered-php.gif" alt="', $txt['powered_by_php'], '" border="0"  /></a>
                                       <a href="http://validator.w3.org/check/referer" target="_blank"><img id="valid-xhtml10" src="', $settings['images_url'], '/valid-xhtml10.gif" alt="', $txt['valid_xhtml'], '" border="0" /></a>
                                        <a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank"><img id="valid-css" src="', $settings['images_url'], '/valid-css.gif" alt="', $txt['valid_css'], '" border="0" /></a>

        </div>';

	// Define the upper_section toggle in JavaScript.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var oMainHeaderToggle = new smc_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
				aSwappableContainers: [
					\'upshrinkHeader\'
				],
				aSwapImages: [
					{
						sId: \'upshrink\',
						srcExpanded: smf_images_url + \'/upshrink.gif\',
						altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
						srcCollapsed: smf_images_url + \'/upshrink2.gif\',
						altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
					}
				],
				oThemeOptions: {
					bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
					sOptionName: \'collapse_header\',
					sSessionVar: ', JavaScriptEscape($context['session_var']), ',
					sSessionId: ', JavaScriptEscape($context['session_id']), '
				},
				oCookieOptions: {
					bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
					sCookieName: \'upshrink\'
				}
			});
		// ]]></script>';


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
function template_menu_smf()
{
        global $context, $settings, $options, $scripturl, $txt;

 

	   // first button...is it on?
	for($a = 1 ; $a<4 ; $a++)
	{
	   if(isset($settings['custombutton'.$a.'_use'])  && $settings['custombutton'.$a.'_use']==true){
          // is it memberonly?
            if(isset($settings['custombutton'.$a.'_member']) && $settings['custombutton'.$a.'_member']==true){
                // it is, check if guest
                if($context['user']['is_logged'])
				{
                   // member, check if its not empty...
                   if(!empty($settings['custombutton'.$a.'_name']) && !empty($settings['custombutton'.$a.'_link']))
						$context['menu_buttons']['cus'.$a] = array(
							'title' => $settings['custombutton'.$a.'_name'],
							'href' => $settings['custombutton'.$a.'_link'],
							'show' => true,
							'sub_buttons' => array(
								),
							'is_last' => $context['right_to_left'],
							);	   
                }
            }
            else{
                   // guests too, check if its not empty...
                   if(!empty($settings['custombutton'.$a.'_name']) && !empty($settings['custombutton'.$a.'_link']))
						$context['menu_buttons']['cus'.$a] = array(
							'title' => $settings['custombutton'.$a.'_name'],
							'href' => $settings['custombutton'.$a.'_link'],
							'show' => true,
							'sub_buttons' => array(
								),
							'is_last' => $context['right_to_left'],
							);	   
            }
       }
	}
	template_menu();
    echo '<td valign="top"><img src="' . $settings['images_url'] . '/pod/'.$options['theme_color'].'/pod-rightbar.gif" alt="gfx" style="margin: 0px 0;" border="0" />';

}
// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '<td style="font-family: tahoma, sans-serif; white-space: nowrap; padding: 4px 10px 0 10px; font-size: 10px; text-transform: uppercase; background: url('.$settings['images_url'].'/menuback.gif) 0 0px no-repeat;">';
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
					<a href="', $button['href'], '">	', $button['title'], '</a>&nbsp;|';
	}

	echo '
		</td>';
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

?>