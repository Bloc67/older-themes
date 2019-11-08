<?php
// Version: 1.1; BoardIndex

function template_main()
{
        global $context, $settings, $options, $txt, $scripturl, $modSettings;


        $topbox='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 16px; width: 22px;"><img src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topleft.gif" style="height: 16px; width: 22px; border: 0px; padding: 0px; margin: 0px;" alt="gfx" /></td><td style="height: 16px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topmid.gif); "></td><td style="height: 16px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topright.gif" style="height: 16px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr><tr><td style="width: 22px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midleft.gif); "></td><td valign="top">';
        $botbox='</td><td style="width: 27px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midright.gif); "></td></tr><tr><td style="height: 14px; width: 22px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botleft.gif" style="height: 14px; width: 22px; border: 0px; padding: 0px; margin: 0px;" /></td><td style="height: 14px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botmid.gif); "></td><td style="height: 14px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botright.gif" style="height: 14px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        $leftbox='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 54px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-lefttop.gif" style="height: 54px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td nowrap="nowrap" style="text-align: center; height: 54px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-midtop.gif); ">';
        $rightbox='</td><td style="height: 54px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-topright.gif" style="height: 54px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';


        $leftboxbot='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-left: 5px; height: 78px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-botleft.gif" style="height: 78px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td valign="top" nowrap="nowrap" style="text-align: center; height: 78px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-botmid.gif); ">';
        $rightboxbot='</td><td style="height: 78px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-botright.gif" style="height: 78px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        $leftboxbot2='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-left: 4px; height: 36px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topleft-40.gif" style="height: 36px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td valign="top" nowrap="nowrap" style="text-align: center; height: 36px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topmid-41.gif); ">';
        $rightboxbot2='</td><td style="padding-right: 0px; height: 36px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topright-42.gif" style="height: 36px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';


        // Show some statistics next to the link tree if SP1 info is off.
        echo $leftbox.'
<table width="100%" cellpadding="0" cellspacing="0">
        <tr>
                <td style="text-align: left; padding-left: 0px;" valign="bottom">', theme_linktree(), '</td>
        </tr>
</table>'.$rightbox;

        // Show the news fader?  (assuming there are things to show...)
        if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
        {
                echo $topbox;
                echo '
<div style="padding: 0px; margin-bottom: 2px;">
        <table border="0" width="100%" cellspacing="1" cellpadding="4">
                <tr>
                        <td align="left" style="font-weight: bold;">&nbsp;&nbsp;', $txt[102], '</td>
                </tr>
                <tr>
                        <td valign="middle" align="center" height="60">';

                // Prepare all the javascript settings.
		echo '
				<div id="smfFadeScroller" style="width: 90%; padding: 2px;"><b>', $context['news_lines'][0], '</b></div>
				<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
					// The fading delay (in ms.)
					var smfFadeDelay = ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], ';
					// Fade from... what text color? To which background color?
                                        var smfFadeFrom = {"r": 255, "g": 255, "b": 255}, smfFadeTo = {"r": 0, "g": 0, "b": 0};
                                        // Surround each item with... anything special?
                                        var smfFadeBefore = "<b>", smfFadeAfter = "</b>";

                                        var foreColor, backEl, backColor;

                                        if (typeof(document.getElementById(\'smfFadeScroller\').currentStyle) != "undefined")
                                        {
                                                foreColor = document.getElementById(\'smfFadeScroller\').currentStyle.color.match(/#([\da-f][\da-f])([\da-f][\da-f])([\da-f][\da-f])/);
                                                smfFadeFrom = {"r": parseInt(foreColor[1]), "g": parseInt(foreColor[2]), "b": parseInt(foreColor[3])};

                                                backEl = document.getElementById(\'smfFadeScroller\');
                                                while (backEl.currentStyle.backgroundColor == "transparent" && typeof(backEl.parentNode) != "undefined")
                                                        backEl = backEl.parentNode;

                                                backColor = backEl.currentStyle.backgroundColor.match(/#([\da-f][\da-f])([\da-f][\da-f])([\da-f][\da-f])/);
                                                smfFadeTo = {"r": eval("0x" + backColor[1]), "g": eval("0x" + backColor[2]), "b": eval("0x" + backColor[3])};
                                        }
                                        else if (typeof(window.opera) == "undefined" && typeof(document.defaultView) != "undefined")
                                        {
                                                foreColor = document.defaultView.getComputedStyle(document.getElementById(\'smfFadeScroller\'), null).color.match(/rgb\((\d+), (\d+), (\d+)\)/);
                                                smfFadeFrom = {"r": parseInt(foreColor[1]), "g": parseInt(foreColor[2]), "b": parseInt(foreColor[3])};

                                                backEl = document.getElementById(\'smfFadeScroller\');
                                                while (document.defaultView.getComputedStyle(backEl, null).backgroundColor == "transparent" && typeof(backEl.parentNode) != "undefined" && typeof(backEl.parentNode.tagName) != "undefined")
                                                        backEl = backEl.parentNode;

                                                backColor = document.defaultView.getComputedStyle(backEl, null).backgroundColor.match(/rgb\((\d+), (\d+), (\d+)\)/);
                                                smfFadeTo = {"r": parseInt(backColor[1]), "g": parseInt(backColor[2]), "b": parseInt(backColor[3])};
                                        }
                                        // List all the lines of the news for display.
                                        var smfFadeContent = new Array(
                                                "', implode('",
                                                "', $context['fader_news_lines']), '"
                                        );
                                // ]]></script>
                                <script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/fader.js"></script>
                        </td>
                </tr>
        </table>
</div>';
echo $botbox;
        }

        /* Each category in categories is made up of:
                id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
                new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down iamge),
                and boards. (see below.) */
        echo $topbox;
        foreach ($context['categories'] as $category)
        {
                echo '
<div class="tborder"><table border="0" width="100%" cellspacing="1" cellpadding="6">
        <tr>
                <td colspan="2" class="catbg', $category['new'] ? '2' : '', '" height="18">';

                // If this category even can collapse, show a link to collapse it.
                if ($category['can_collapse'])
                        echo '
                        <a href="', $category['collapse_href'], '">', $category['collapse_image'], '</a>';

                echo '
                        ', $category['link'], '
                </td>
                <td width="4%" class="catbg', $category['new'] ? '2' : '', '"><b>' ,$txt[21], '</b></td>
                <td width="4%" class="catbg', $category['new'] ? '2' : '', '"><b>' ,$txt[330], '</b></td>
                <td width="22%" align="center" class="catbg', $category['new'] ? '2' : '', '"> ', $txt[22], '</td>
        </tr>';

                // Assuming the category hasn't been collapsed...
                if (!$category['is_collapsed'])
                {
                        /* Each board in each category's boards has:
                                new (is it new?), id, name, description, moderators (see below), link_moderators (just a list.),
                                children (see below.), link_children (easier to use.), children_new (are they new?),
                                topics (# of), posts (# of), link, href, and last_post. (see below.) */
                        foreach ($category['boards'] as $board)
                        {
                                echo '
        <tr class="windowbg2">
                <td class="windowbg" width="6%" align="center" valign="top"><a href="', $scripturl, '?action=unread;board=', $board['id'], '.0">';

                                // If the board is new, show a strong indicator.
                                if ($board['new'])
                                        echo '<img src="', $settings['images_url'], '/on.gif" alt="', $txt[333], '" title="', $txt[333], '" border="0" />';
                                // This board doesn't have new posts, but its children do.
                                elseif ($board['children_new'])
                                        echo '<img src="', $settings['images_url'], '/on2.gif" alt="', $txt[333], '" title="', $txt[333], '" border="0" />';
                                // No new posts at all!  The agony!!
                                else
                                        echo '<img src="', $settings['images_url'], '/off.gif" alt="', $txt[334], '" title="', $txt[334], '" border="0" />';

                                echo '</a></td>
                <td>
                        <b><a href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a></b><br />
                        ', $board['description'];

                                // Show the "Moderators: ".  Each has name, href, link, and id. (but we're gonna use link_moderators.)
                                if (!empty($board['moderators']))
                                        echo '
                        <div style="padding-top: 1px;" class="smalltext"><i>', count($board['moderators']) == 1 ? $txt[298] : $txt[299], ': ', implode(', ', $board['link_moderators']), '</i></div>';

                                // Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
                                if (!empty($board['children']))
                                {
                                        // Sort the links into an array with new boards bold so it can be imploded.
                                        $children = array();
                                        /* Each child in each board's children has:
                                                id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
                                        foreach ($board['children'] as $child)
                                        {
                                                $child['link'] = '<a href="' . $child['href'] . '" title="' . ($child['new'] ? $txt[333] : $txt[334]) . ' (' . $txt[330] . ': ' . $child['topics'] . ', ' . $txt[21] . ': ' . $child['posts'] . ')">' . $child['name'] . '</a>';
                                                $children[] = $child['new'] ? '<b>' . $child['link'] . '</b>' : $child['link'];
                                        }

                                        echo '
                        <div style="padding-top: 1px;" class="smalltext"><i>', $txt['parent_boards'], ': ', implode(', ', $children), '</i></div>';
                                }

                                // Show some basic information about the number of posts, etc.
                                echo '
                </td>
                <td class="windowbg" valign="middle" align="center" style="width: 8ex;"><span class="smalltext">
                        ', $board['posts'], '</span></td>
                <td class="windowbg" valign="middle" align="center" style="width: 8ex;"><span class="smalltext">
                        ', $board['topics'],'
                </span></td>
                <td class="smalltext" valign="middle" width="25%" style="padding-left: 12px;">';

                                /* The board's and children's last_post's have:
                                        time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
                                        link, href, subject, start (where they should go for the first unread post.),
                                        and member. (which has id, name, link, href, username in it.) */
                                if (!empty($board['last_post']['id']))
                                        echo $board['last_post']['time'], '<br />
                       <b>', $board['last_post']['link'], '</b> ', $txt[525], '
                       ', $board['last_post']['member']['link'];

                                echo '
                </td>
        </tr>';
                        }
                }

                echo '
</table></div>';
        }

        echo $botbox;

        echo $leftboxbot;
        if ($context['user']['is_logged'])
        {
                echo '
<div style="padding-top: 16px;"><table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top">
        <tr>
                <td align="left" class="smalltext" valign="top">
                        <img src="' . $settings['images_url'] . '/new_some.gif" alt="" align="absmiddle" border="0" /> ', $txt[333], '
                        <img src="' . $settings['images_url'] . '/new_none.gif" alt=""  align="absmiddle" border="0" style="margin-left: 4ex;" /> ', $txt[334], '
                </td>
                <td align="right" class="smalltext">';
                // Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
                        echo '
                        <a href="', $scripturl, '?action=markasread;sa=all;sesc=' . $context['session_id'] . '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/markread.gif" alt="' . $txt[452] . '" border="0" />' : $txt[452]), '</a>';
                echo '
                </td>
        </tr>
</table></div>';
        }
        else
           echo '<div style="padding: 0px;"><table border="0" width="100%" cellspacing="0" cellpadding="5">
        <tr>
                <td align="left" class="smalltext">

                </td>
                <td align="right" class="smalltext">
                </td>
        </tr>
</table></div>';
echo $rightboxbot;

        // Here's where the "Info Center" starts...

       echo $leftboxbot2.'<div style="font-weight: bold; text-align: center;">'.$txt[685].'</div>'.$rightboxbot2;

       echo '<div style="padding-left: 0px; padding-right: 0px;">'.$topbox;
       echo '

<div class="tborder"><table border="0" width="100%" cellspacing="1" cellpadding="4"> ';

        // This is the "Recent Posts" bar.
        if (!empty($settings['number_recent_posts']))
        {
                echo '
        <tr>
                <td class="catbg" colspan="2">', $txt[214], '</td>
        </tr>
        <tr>
                <td class="windowbg" width="20" valign="middle" align="center">
                        <a href="', $scripturl, '?action=recent">
                                <img src="', $settings['images_url'], '/post/xx.gif" alt="', $txt[214], '" border="0" /></a>
                </td>
                <td class="windowbg2">';

                // Only show one post.
                if ($settings['number_recent_posts'] == 1)
                {
                        // latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (it's id.)
                        echo '
                        <b><a href="', $scripturl, '?action=recent">', $txt[214], '</a></b>
                        <div class="smalltext">
                                ', $txt[234], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt[235], ' (', $context['latest_post']['time'], ')<br />
                        </div>';
                }
                // Show lots of posts.
                elseif (!empty($context['latest_posts']))
                {
                        echo '
                        <table width="100%" border="0">';
                        /* Each post in latest_posts has:
                                board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
                                subject, short_subject (shortened with...), time, link, and href. */
                        foreach ($context['latest_posts'] as $post)
                                echo '
                                <tr>
                                        <td class="smalltext" align="right" valign="top" nowrap="nowrap">[', $post['board']['link'], ']</td>
                                        <td class="smalltext" valign="top">', $post['link'], ' ', $txt[525], ' ', $post['poster']['link'], '</td>
                                        <td class="smalltext" align="right" valign="top" nowrap="nowrap">', $post['time'], '</td>
                                </tr>';
                        echo '
                        </table>';
                }
                echo '
                </td>
        </tr>';
        }

        // Show information about events, birthdays, and holidays on the calendar.
        if ($context['show_calendar'])
        {
                echo '
        <tr>
                <td class="catbg" colspan="2">', $context['calendar_only_today'] ? $txt['calendar47b'] : $txt['calendar47'], '</td>
        </tr><tr>
                <td class="windowbg" width="20" valign="middle" align="center">
                        <a href="', $scripturl, '?action=calendar">
                                <img src="', $settings['images_url'], '/icons/calendar.gif" alt="', $txt['calendar24'], '" border="0" /></a>
                </td>
                <td class="windowbg2" width="100%">
                        <span class="smalltext">';

                // Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
                if (!empty($context['calendar_holidays']))
                        echo '
                                <span style="color: #', $modSettings['cal_holidaycolor'], ';">', $txt['calendar5'], ' ', implode(', ', $context['calendar_holidays']), '</span><br />';

                // People's birthdays.  Like mine.  And yours, I guess.  Kidding.
                if (!empty($context['calendar_birthdays']))
                {
                        echo '
                                <span style="color: #', $modSettings['cal_bdaycolor'], ';">', $context['calendar_only_today'] ? $txt['calendar3'] : $txt['calendar3b'], '</span> ';
                        /* Each member in calendar_birthdays has:
                                id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?) */
                        foreach ($context['calendar_birthdays'] as $member)
                                echo '
                                <a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<b>' : '', $member['name'], $member['is_today'] ? '</b>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '<br />' : ', ';
                }
                // Events like community get-togethers.
                if (!empty($context['calendar_events']))
                {
                        echo '
                                <span style="color: #', $modSettings['cal_eventcolor'], ';">', $context['calendar_only_today'] ? $txt['calendar4'] : $txt['calendar4b'], '</span> ';
                        /* Each event in calendar_events should have:
                                title, href, is_last, can_edit (are they allowed?), modify_href, and is_today. */
                        foreach ($context['calendar_events'] as $event)
                                echo '
				', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" style="color: #FF0000;">*</a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<b>' . $event['title'] . '</b>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br />' : ', ';

                        // Show a little help text to help them along ;).
                        if ($context['calendar_can_edit'])
                                echo '
                                (<a href="', $scripturl, '?action=helpadmin;help=calendar_how_edit" onclick="return reqWin(this.href);">', $txt['calendar_how_edit'], '</a>)';
                }
                echo '
                        </span>
                </td>
        </tr>';
        }

        // Show a member bar.  Not heavily ornate, but functional at least.
        if ($settings['show_member_bar'])
        {
                echo '
        <tr>
                <td class="catbg" colspan="2">', $txt[331], '</td>
        </tr>
        <tr>
                <td class="windowbg" width="20" valign="middle" align="center">
                        ', $context['show_member_list'] ? '<a href="' . $scripturl . '?action=mlist">' : '', '<img src="', $settings['images_url'], '/icons/members.gif" alt="', $txt[332], '" border="0" />', $context['show_member_list'] ? '</a>' : '', '
                </td>
                <td class="windowbg2" width="100%">
                        <b>', $context['show_member_list'] ? '<a href="' . $scripturl . '?action=mlist">' . $txt[332] . '</a>' : $txt[332], '</b>
                        <div class="smalltext">', $txt[200], '</div>
                </td>
        </tr>';
        }

        // Show YaBB SP1 style information...
        if ($settings['show_sp1_info'])
        {
                echo '
        <tr>
                <td class="catbg" colspan="2">', $txt[645], '</td>
        </tr>
        <tr>
                <td class="windowbg" width="20" valign="middle" align="center">
                        <a href="', $scripturl, '?action=stats">
                                <img src="', $settings['images_url'], '/icons/info.gif" alt="', $txt[645], '" border="0" /></a>
                </td>
                <td class="windowbg2" width="100%">
                        <table border="0" width="90%"><tr>
                                <td class="smalltext">
                                        <div style="float: ', !$context['right_to_left'] ? 'left' : 'right', '; width: 50%;">', $txt[490], ': <b>', $context['common_stats']['total_topics'], '</b></div>', $txt[489], ': <b>', $context['common_stats']['total_posts'], '</b><br />', !empty($context['latest_post']) ? '
                                        ' . $txt[659] . ': &quot;' . $context['latest_post']['link'] . '&quot;  (' . $context['latest_post']['time'] . ')<br />' : '', '
                                        <a href="', $scripturl, '?action=recent">', $txt[234], '</a>', $context['show_stats'] ? '<br />
                                        <a href="' . $scripturl . '?action=stats">' . $txt['smf223'] . '</a>' : '', '
                                </td>
                                <td width="32%" class="smalltext" valign="top">
                                        ', $txt[488], ': <b><a href="', $scripturl, '?action=mlist">', $context['common_stats']['total_members'], '</a></b><br />
                                        ', $txt[656], ': <b>', $context['common_stats']['latest_member']['link'], '</b><br />';
                // If they are logged in, show their unread message count, etc..
                if ($context['user']['is_logged'])
                        echo '
                                        ', $txt['smf199'], ': <b><a href="', $scripturl, '?action=pm">', $context['user']['messages'], '</a></b> ', $txt['newmessages3'], ': <b><a href="', $scripturl, '?action=pm">', $context['user']['unread_messages'], '</a></b>';
                echo '
                                </td>
                        </tr></table>
                </td>
        </tr>';
        }

        // "Users online" - in order of activity.
        echo '
        <tr>
                <td class="catbg" colspan="2">', $txt[158], '</td>
        </tr><tr>
                <td class="windowbg" width="20" valign="middle" align="center">
                        ', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<img src="', $settings['images_url'], '/icons/online.gif" alt="', $txt[158], '" border="0" />', $context['show_who'] ? '</a>' : '', '
                </td>
                <td class="windowbg2" width="100%">';

        if ($context['show_who'])
                echo '
                        <a href="', $scripturl, '?action=who">';

        echo $context['num_guests'], ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . $context['num_users_online'], ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

        // Handle hidden users and buddies.
        if (!empty($context['num_users_hidden']) || ($context['show_buddies'] && !empty($context['show_buddies'])))
        {
                echo ' (';

                // Show the number of buddies online?
                if ($context['show_buddies'])
                        echo $context['num_buddies'], ' ', $context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies'];

                // How about hidden users?
                if (!empty($context['num_users_hidden']))
                        echo $context['show_buddies'] ? ', ' : '', $context['num_users_hidden'] . ' ' . $txt['hidden'];

                echo ')';
        }

        if ($context['show_who'])
                echo '</a>';

        echo '
                        <span class="smalltext">';

        // Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
        if (!empty($context['users_online']))
                echo '
                                ', $txt[140], ':<br />', implode(', ', $context['list_users_online']);

        echo '
                                <br />', $context['show_stats'] && !$settings['show_sp1_info'] ? '
                                <a href="' . $scripturl . '?action=stats">' . $txt['smf223'] . '</a>' : '', '
                        </span>
                        <hr /><span class="smalltext">
                        Most users online today: '.$modSettings['mostOnlineToday'].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Most users online ever: <b>'.$modSettings['mostOnline'].'</b> ( '.timeformat($modSettings['mostDate']).'  )

                        </span>


                </td>
        </tr>';

        // If they are logged in, but SP1 style information is off... show a personal message bar.
        if ($context['user']['is_logged'] && !$settings['show_sp1_info'])
        {
                echo '
        <tr>
                <td class="catbg" colspan="2">', $txt[159], '</td>
        </tr><tr>
                <td class="windowbg" width="20" valign="middle" align="center">
			', $context['allow_pm'] ? '<a href="' . $scripturl . '?action=pm">' : '', '<img src="', $settings['images_url'], '/message_sm.gif" alt="', $txt[159], '" border="0" />', $context['allow_pm'] ? '</a>' : '', '
                </td>
                <td class="windowbg2" valign="top">
                        <b><a href="', $scripturl, '?action=pm">', $txt[159], '</a></b>
                        <div class="smalltext">
                                ', $txt[660], ' ', $context['user']['messages'], ' ', $context['user']['messages'] == 1 ? $txt[471] : $txt[153], '.... ', $txt[661], ' <a href="', $scripturl, '?action=pm">', $txt[662], '</a> ', $txt[663], '
                        </div>
                </td>
        </tr>';
        }

        // Show the login bar. (it's only true if they are logged out anyway.)
        if ($context['show_login_bar'])
        {
                echo '
        <tr>
                <td class="catbg" colspan="2">', $txt[34], ' <a href="', $scripturl, '?action=reminder" class="smalltext">(' . $txt[315] . ')</a></td>
        </tr>
        <tr>
                <td class="windowbg" width="20" align="center">
                        <a href="', $scripturl, '?action=login">
                                <img src="', $settings['images_url'], '/icons/login.gif" alt="', $txt[34], '" border="0" /></a>
                </td>
                <td class="windowbg2" valign="middle">
						<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;">
                                <table border="0" cellpadding="2" cellspacing="0" align="center" width="100%"><tr>
                                        <td valign="middle" align="left">
                                                <label for="user"><b>', $txt[35], ':</b><br /><input type="text" name="user" id="user" size="15" /></label>
                                        </td>
                                        <td valign="middle" align="left">
                                                <label for="passwrd"><b>', $txt[36], ':</b><br /><input type="password" name="passwrd" id="passwrd" size="15" /></label>
                                        </td>
                                        <td valign="middle" align="left">
                                                <label for="cookielength"><b>', $txt[497], ':</b><br /><input type="text" name="cookielength" id="cookielength" size="4" maxlength="4" value="', $modSettings['cookieTime'], '" /></label>
                                        </td>
                                        <td valign="middle" align="left">
                                                <label for="cookieneverexp"><b>', $txt[508], ':</b><br /><input type="checkbox" name="cookieneverexp" id="cookieneverexp" checked="checked" class="check" /></label>
                                        </td>
                                        <td valign="middle" align="left">
                                                <input type="submit" value="', $txt[34], '" />
                                        </td>
                                </tr></table>
                        </form>
                </td>
        </tr>';
        }

        echo '
</table></div>';
echo $botbox.'</div>';

}

?>