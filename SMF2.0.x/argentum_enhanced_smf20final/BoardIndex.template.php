<?php
/**
 * Simple Machines Forum (SMF)
 *
 *
 * @version 2.0
 */

function template_main()
{
	global $smcFunc,$context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix;
	
	// Show the news fader?  (assuming there are things to show...)
	if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
	{
		echo '
	<div id="newsfader">
		<div class="cat_bar">
			<h3 class="catbg">
				<img id="newsupshrink" src="', $settings['images_url'], '/collapse.gif" alt="*" title="', $txt['upshrink_description'], '" align="bottom" style="display: none;" />
				', $txt['news'], '
			</h3>
		</div>
		<ul class="reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>';

			foreach ($context['news_lines'] as $news)
				echo '
			<li>', $news, '</li>';

	echo '
		</ul>
	</div>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[

		// Create a news fader object.
		var oNewsFader = new smf_NewsFader({
			sSelf: \'oNewsFader\',
			sFaderControlId: \'smfFadeScroller\',
			sItemTemplate: ', JavaScriptEscape('<strong>%1$s</strong>'), ',
			iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
		});

		// Create the news fader toggle.
		var smfNewsFadeToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_news_fader']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'smfFadeScroller\'
			],
			aSwapImages: [
				{
					sId: \'newsupshrink\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_news_fader\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'newsupshrink\'
			}
		});
	// ]]></script>';
	}
	// if avatars are to be fetched
	if(!empty($settings['avatarboards']))
	{
		// fetch last posters
		$membs = array();
		foreach($context['categories'] as $category)
		{
			if(!empty($category['boards']))
			{
				foreach($category['boards'] as $b)
					$membs[$b['last_post']['member']['id']] = $b['last_post']['member']['id'];
			}
		}
		if(count($membs)>0)
		{
			$request =  $smcFunc['db_query']('','SELECT mem.id_member as ID_MEMBER, mem.avatar,
					IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType 
					FROM ' . $db_prefix . 'members AS mem
					LEFT JOIN ' . $db_prefix . 'attachments AS a ON (a.id_member = mem.id_member)
					WHERE mem.id_member IN (' . implode(",",$membs) . ')',array());
			
			$avvy = array();
			if($smcFunc['db_num_rows']($request)>0)
			{
				while($row = $smcFunc['db_fetch_assoc']($request))
					$avvy[$row['ID_MEMBER']] = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']));

				$smcFunc['db_free_result']($request);
			}
		}
	}

	echo '
<div class="container" style="overflow: hidden;">';
	
	$sbar = !empty($settings['boardindex_layoutwidth']) ? $settings['boardindex_layoutwidth'] : 4;
	$mbar = 16 - $sbar;

	if(!empty($settings['boardindex_layout']) && $settings['boardindex_layout']==2)
		$mbar = $sbar = 16;

	if(!empty($settings['boardindex_layout']) && $settings['boardindex_layout']==1)
		echo '
	<div class="col'.$sbar.'"><div style="padding-right: 0.5em;">', boardindex_widgets() , '</div></div>';
	
	echo '
	<div class="col'.$mbar.'">
	<div id="boardindex_table">
		<table class="table_list">';

	/* Each category in categories is made up of:
	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image),
	and boards. (see below.) */
	foreach ($context['categories'] as $category)
	{
		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

		// calcualte
		$colspan=4;
		echo '
			<tbody class="header" id="category_', $category['id'], '">
				<tr>
					<td colspan="'.$colspan.'" class="catbg"><span class="left"></span>';

		// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
						<a class="collapse" href="', $category['collapse_href'], '">', $category['collapse_image'], '</a>';

		if (!$context['user']['is_guest'] && !empty($category['show_unread']))
			echo '
						<a class="unreadlink" href="', $scripturl, '?action=unread;c=', $category['id'], '">', $txt['view_unread_category'], '</a>';

		echo '
						', $category['link'], '
					</td>
				</tr>
			</tbody>
			<tbody class="content" id="category_', $category['id'], '_boards">';

		// Assuming the category hasn't been collapsed...
		if (!$category['is_collapsed'])
		{
			/* Each board in each category's boards has:
			new (is it new?), id, name, description, moderators (see below), link_moderators (just a list.),
			children (see below.), link_children (easier to use.), children_new (are they new?),
			topics (# of), posts (# of), link, href, and last_post. (see below.) */
			
			$alt = true;
			foreach ($category['boards'] as $board)
			{
				echo '
				<tr  id="board_', $board['id'], '" class="windowbg' , $alt ? '' : '2' , '">
					<td class="icon"', !empty($board['children']) ? ' rowspan="2"' : '', ' width="4%" valign="top">
						<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';

				// If the board or children is new, show an indicator.
				if ($board['new'] || $board['children_new'])
					echo '
							<img src="', $settings['images_url'], '/on', $board['new'] ? '' : '2', '.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
				// Is it a redirection board?
				elseif ($board['is_redirect'])
					echo '
							<img src="', $settings['images_url'], '/redirect.png" alt="*" title="*" />';
				// No new posts at all! The agony!!
				else
					echo '
							<img src="', $settings['images_url'], '/off.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';

				echo '
						</a>
					</td>
					<td class="info">';

				if(!empty($settings['rsslinks']) && !$board['is_redirect'])
					echo '
				<a title="Subscribe to this board" href="'.$scripturl.'?action=.xml;board=' . $board['id'] . ';type=rss"><img class="floatright" style="margin: 0;" src="' . $settings['images_url'] . '/rss.png" alt="RSS" /></a>';

				echo '
						<a class="subject" href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a>';

				// Has it outstanding posts for approval?
				if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
					echo '
						<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';

				echo '

						<p>', $board['description'] , '</p>';

				// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
				if (!empty($board['moderators']))
					echo '
						<p class="moderators">', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</p>';

				// Show some basic information about the number of posts, etc.
					echo '
					</td>
					<td class="stats" ' , $board['is_redirect'] ? ' colspan="2"' : '' , '>
						<p>', comma_format($board['posts']), ' ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], ' <br />
						', $board['is_redirect'] ? '' : comma_format($board['topics']) . ' ' . $txt['board_topics'], '
						</p>
					</td>';
					
					if(!$board['is_redirect'])
						echo '<td class="lastpost">';

				if(!empty($settings['avatarboards']) && !$board['is_redirect'])
				{
					echo '
				<a href="',$scripturl,'?action=miniprofile;u='.$board['last_post']['member']['id'].'" rel="width:560,height:260" id="mb'.$board['id'].'" class="mb" style="float: right;">
				<img alt=""  class="avyframe" style="max-height: 25px;" src="' . (!empty($avvy[$board['last_post']['member']['id']]) ? $avvy[$board['last_post']['member']['id']] : $settings['images_url'].'/TPguest.png') . '" />
				</a>
				<div class="multiBoxDesc mb'.$board['id'].'">&nbsp;</div>

				';
				}
				/* The board's and children's 'last_post's have:
				time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
				link, href, subject, start (where they should go for the first unread post.),
				and member. (which has id, name, link, href, username in it.) */
				if (!empty($board['last_post']['id']))
					echo '
						<p><strong>', $txt['last_post'], '</strong>  ', $txt['by'], ' ', $board['last_post']['member']['link'] , '<br />
						', $txt['in'], ' ', $board['last_post']['link'], '<br />
						', $txt['on'], ' ', $board['last_post']['time'],'
						</p>';
				echo '
				</td>';
				
				echo '
			</tr>';
				if (!empty($board['children']))
					render_childs($board['children'], $colspan,$alt, $board['id']);
				
				$alt= !$alt;
			}
		}
		echo '
			</tbody>
			<tbody class="divider">
				<tr>
					<td colspan="'.$colspan.'"></td>
				</tr>
			</tbody>';
	}
	echo '
		</table>
	</div>';

	if ($context['user']['is_logged'])
	{
		echo '
	<div id="posting_icons" class="floatleft">';

		// Mark read button.
		$mark_read_button = array(
			'markread' => array('text' => 'mark_as_read', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;' . $context['session_var'] . '=' . $context['session_id']),
		);

		echo '
		<ul class="reset" style="white-space: nowrap;">
			<li class="floatleft"><img src="', $settings['images_url'], '/on.png" style="width: 20px; max-width: 100%;" alt="" /> ', $txt['new_posts'], '</li>
			<li class="floatleft"><img src="', $settings['images_url'], '/off.png"  style="width: 20px; max-width: 100%;" alt="" /> ', $txt['old_posts'], '</li>
			<li class="floatleft"><img src="', $settings['images_url'], '/redirect.png"  style="width: 20px; max-width: 100%;" alt="" /> ', $txt['redirect_board'], '</li>
		</ul>
	</div>';

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
			echo '<div class="mark_read">', template_button_strip($mark_read_button, 'right'), '</div>';
	}
	else
	{
		echo '
	<div id="posting_icons" class="flow_hidden" style="padding-left: 0; padding-right: 0;">
		<div class="plainbox">To be able to see boards with unread posts, please <a href="index.php?action=login">login</a> or <a href="index.php?action=register">register</a>.</div>
	</div>';
	}
	echo '
</div>';
	
	if(empty($settings['boardindex_layout']) || (isset($settings['boardindex_layout']) && $settings['boardindex_layout']==2))
		echo '
	<div class="col'.$sbar.'"><div style="padding-left: ' , $sbar == 16 ? '0' : '0.5em' , ';">', boardindex_widgets() , '</div></div>';
	
	echo '
	</div>';
}

function boardindex_widgets()
{
	global $settings;

	// HTML boxes?
	if(!empty($settings['area1_where']) && $settings['area1_where']==4)
		echo '<div class="widgetbox">', $settings['area1'], '</div>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==4)
		echo '<div class="widgetbox">', $settings['area2'], '</div>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==4)
		echo '<div class="widgetbox">', $settings['area3'], '</div>';

	template_info_center();

}

function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if(isset($settings['boardindex_layout']) && $settings['boardindex_layout']==2)
		echo '<br style="clear: both;" />';

	// Here's where the "Info Center" starts...
	echo '
		<div class="cat_bar">	
			<h3 class="catbg">
				<img  id="upshrink_ic" src="', $settings['images_url'], '/collapse.gif" alt="*" title="', $txt['upshrink_description'], '" style="display: none;" />
				', substr($txt['info_center_title'], 7), '
			</h3>
		</div>
		<div id="infoc"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>';

	// make sidebar better
	if(isset($settings['boardindex_layout']) && $settings['boardindex_layout']<2)
		$clear=true;
	else
		$clear=false;
	
	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']))
	{
		echo '
			<div class="title_bar">
				<h4 class="titlebg">
					<span class="ie6_header floatleft">
						<a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a>
					</span>
				</h4>
			</div>
	<div class="widgetbox">
			<div class="hslice" id="recent_posts_content">
				<div class="entry-title" style="display: none;">', $context['forum_name_html_safe'], ' - ', $txt['recent_posts'], '</div>
				<div class="entry-content" style="display: none;">
					<a rel="feedurl" href="', $scripturl, '?action=.xml;type=webslice">', $txt['subscribe_webslice'], '</a>
				</div>';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
				<strong><a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a></strong>
				<p id="infocenter_onepost" class="middletext">
					', $txt['recent_view'], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt['recent_updated'], ' (', $context['latest_post']['time'], ')<br />
				</p>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
				<dl id="ic_recentposts" class="middletext">';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
					<dt' , $clear ? ' style="padding: 0; display: block;text-align: left; margin: 4px 0 0 0; float: none;"' : '' , '><strong>', $post['link'], '</strong> ', $txt['by'], ' ', $post['poster']['link'], ' (', $post['board']['link'], ')</dt>
					<dd' , $clear ? ' style="color: #666; font-size: 0.8em; display: block; margin: 0 0 0.5em 0;text-align: left; float: none;"' : '' , '>', $post['time'], '</dd>';
			echo '
				</dl>';
		}
		echo '
			</div>
		</div>';
	}

	if(!empty($settings['twitter']))
	{	
		call_twitter();
	}
	
	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
			<div class="title_bar">
				<h4 class="titlebg">
						', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '
				</h4>
			</div>
		<div class="widgetbox">
			<p class="smalltext">';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
				echo '
				<span class="holiday">', $txt['calendar_prompt'], ' ', implode(', ', $context['calendar_holidays']), '</span><br />';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))
		{
				echo '
				<span class="birthday">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], '</span> ';
		/* Each member in calendar_birthdays has:
				id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?) */
		foreach ($context['calendar_birthdays'] as $member)
				echo '
				<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<strong>' : '', $member['name'], $member['is_today'] ? '</strong>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '<br />' : ', ';
		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			echo '
				<span class="event">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], '</span> ';
			/* Each event in calendar_events should have:
					title, href, is_last, can_edit (are they allowed?), modify_href, and is_today. */
			foreach ($context['calendar_events'] as $event)
				echo '
					', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" title="' . $txt['calendar_edit'] . '"><img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="*" /></a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br />' : ', ';
		}
		echo '
			</p>
		</div>';
	}

	// Show statistical style information...
	if ($settings['show_stats_index'])
	{
		echo '
			<div class="title_bar">
				<h4 class="titlebg">
						', $txt['forum_stats'], '
				</h4>
			</div>
	<div class="widgetbox">
			<p>
				', $context['common_stats']['total_posts'], ' ', $txt['posts_made'], ' ', $txt['in'], ' ', $context['common_stats']['total_topics'], ' ', $txt['topics'], ' ', $txt['by'], ' ', $context['common_stats']['total_members'], ' ', $txt['members'], '. ', !empty($settings['show_latest_member']) ? $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : '', '<br />
				', (!empty($context['latest_post']) ? $txt['latest_post'] . ': <strong>&quot;' . $context['latest_post']['link'] . '&quot;</strong>  ( ' . $context['latest_post']['time'] . ' )<br />' : ''), '
				<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>', $context['show_stats'] ? '<br />
				<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
			</p>
		</div>';
	}

	// "Users online" - in order of activity.
	echo '
			<div class="title_bar">
				<h4 class="titlebg">
						', $txt['online_users'], '
				</h4>
			</div>
	<div class="widgetbox">
			<p class="inline stats">
				', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', comma_format($context['num_guests']), ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . comma_format($context['num_users_online']), ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	$bracketList = array();
	if ($context['show_buddies'])
		$bracketList[] = comma_format($context['num_buddies']) . ' ' . ($context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies']);
	if (!empty($context['num_spiders']))
		$bracketList[] = comma_format($context['num_spiders']) . ' ' . ($context['num_spiders'] == 1 ? $txt['spider'] : $txt['spiders']);
	if (!empty($context['num_users_hidden']))
		$bracketList[] = comma_format($context['num_users_hidden']) . ' ' . $txt['hidden'];

	if (!empty($bracketList))
		echo ' (' . implode(', ', $bracketList) . ')';

	echo $context['show_who'] ? '</a>' : '', '
			</p>
			<p class="inline smalltext">';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
	{
		echo '
				', sprintf($txt['users_active'], $modSettings['lastActive']), ':<br />', implode(', ', $context['list_users_online']);

		// Showing membergroups?
		if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
			echo '
				<br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
	}

	echo '
			</p>
			<p class="last smalltext">
				', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>.
				', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')
			</p>';


	echo '
		</div>
	</div>
';

	// Info center collapse object.
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var oInfoCenterToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_header_ic']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'infoc\'
			],
			aSwapImages: [
				{
					sId: \'upshrink_ic\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_header_ic\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'upshrinkIC\'
			}
		});
	// ]]></script>';
}
?>