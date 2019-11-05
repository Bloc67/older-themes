<?php

/**
 * @package Blocthemes Admin
 * @version 1.2
 * @theme ShelfLife
 * @author Blocthemes - http://demos.bjornhkristiansen.com
 * Copyright (C) 2014-2016 - Blocthemes
 *
 */

function template_main()
{
	global $settings;

	// modulebased?
	if(!empty($settings['module_boards']))
	{
		loadtemplate('/modules/boardindex/'.$settings['module_boards']);
		call_user_func($settings['module_boards']);
	}
	else
		my_boards(); 
}

function my_boards()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	loadtemplate('Common');

	// Show the news fader?  (assuming there are things to show...)
	if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
	{
		echo '
	<div class="themepadding catbg">
		<h3>
			<img id="newsupshrink" src="', $settings['images_url'], '/collapse.gif" alt="*" title="', $txt['upshrink_description'], '" class="floatright" style="display: none;" />
			' , $txt['news'] , '
		</h3>
		<p class="subtitle">ttt</p>
	</div>
	<div id="newsfader"><div class="themepadding windowbg">
		<ul class="reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>';

			foreach ($context['news_lines'] as $news)
				echo '
			<li>', $news, '</li>';

	echo '
		</ul>
	</div></div>
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
				\'newsfader\'
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

	echo '
	<div id="boardindex_table">';
	$first= true;
	
	foreach ($context['categories'] as $category)
	{
		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

		echo '
		<div class="catbg' , !$first ? ' catbg2' : '' , '">';
		
		if(!$category['is_collapsed'])
			echo '
			<span class="floatright circleborder">' , my_toggle($category['id'], 'category_boards_'.$category['id']) , '</span>';			

		echo '
			<h3>', $category['link'], '</h3>
			<p class="subtitle">';

		if (!$context['user']['is_guest'] && !empty($category['show_unread']))
			echo '
				<a class="unreadlink" href="', $scripturl, '?action=unread;c=', $category['id'], '">', $txt['view_unread_category'], '</a>';

		// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
				<a href="', $category['collapse_href'], '"> | ', $category['is_collapsed'] ? $txt['o_toggle2'] : $txt['o_toggle'] , '</a>';

		echo '
			</p>
		</div>';

		$alt = true;
		// Assuming the category hasn't been collapsed...
		if (!$category['is_collapsed'])
		{
			echo '
		<div id="category_boards_', $category['id'], '" class="' , !empty($options['togglecat' . $category['id']]) ? '' : 'no' , 'togglecat">';
			
			template_board($category['boards']);
			
			echo '
		</div>';
		}
	}
	echo '
	</div>
	<div class="themepadding">';

	if ($context['user']['is_logged'])
	{
		// Mark read button.
		$mark_read_button = array(
			'markread' => array('text' => 'mark_as_read', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;' . $context['session_var'] . '=' . $context['session_id']),
		);

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
			echo '<div class="mark_read">', template_button_strip($mark_read_button, 'right'), '</div>';
	}
	echo '
	</div>';
	template_info_center();
}


function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Here's where the "Info Center" starts...
	echo '
	<br class="clear" />
	<div><div class="innerframe">
		<div class="catbg">
			<span class="floatright circleborder">' , my_toggle('ic', 'upshrinkHeaderIC') , '</span>			
			<h3>', sprintf($txt['info_center_title'], $context['forum_name_html_safe']), '</h3>
			<p class="subtitle">' , $context['current_time'] , '</p>
		</div>
		<div id="upshrinkHeaderIC" class="', empty($options['togglecatic']) ? 'no' : '', 'togglecat">';

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']) && (!empty($context['latest_posts']) || !empty($context['latest_post'])))
	{
		echo '
		<div class="fmodule">
			<h4 class="titlebg">
				<a href="', $scripturl, '?action=recent"><span class="icon-file-text"></span> ', $txt['recent_posts'], '</a>
			</h4>
			<div class="hslice" id="recent_posts_content">
				<div class="entry-title" style="display: none;">', $context['forum_name_html_safe'], ' - ', $txt['recent_posts'], '</div>
				<div class="entry-content" style="display: none;">
					<a rel="feedurl" href="', $scripturl, '?action=.xml;type=webslice">', $txt['subscribe_webslice'], '</a>
				</div>
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
			<div class="windowbg" style="overflow: hidden;">';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
				<div class="clear aposts bwgrid">
					<div class="bwcell1">
						<div class="bdate floatleft" style="margin-right: 2rem;">
							<span class="ftime">' , date("H:i",$post['timestamp']) , ' 
								<span><span class="fdate"><span class="fday">' , date("d",$post['timestamp']) , '</span>
								<span class="fmonth">' , date("M",$post['timestamp']) , '</span><span class="fyear">' , date("Y",$post['timestamp']) , '</span></span>
						</div>
					</div>
					<div class="bwcell15">
						<div><strong>' , $post['link'], '</strong></div>
						<span class="fpreview">&quot;' , $post['preview'] , '&quot;</span>
						<hr class="fclose"><span class="fpreview">', $txt['by'] , ' ', $post['poster']['link'], ' ' , $txt['in'] , ' ', $post['board']['link'], ' </span>
					</div>
				</div>';
			echo '
			</div>';
		}
		echo '
		</div>';
	}

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
			<div class="title_barIC">
				<h4 class="titlebg">
					<span class="ie6_header floatleft">
						<a href="', $scripturl, '?action=calendar' . '"><img class="icon" src="', $settings['images_url'], '/icons/calendar.gif', '" alt="', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '" /></a>
						', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '
					</span>
				</h4>
			</div>
			<div class="windowbg">';

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
			</div>';
	}

	// Show statistical style information...
	if ($settings['show_stats_index'])
	{
		echo '
			<div class="fmodule">
				<h4 class="titlebg">
					<a href="', $scripturl, '?action=stats"><span class="icon-stats-bars"></span> ', $txt['forum_stats'], '</a>
				</h4>
				<div class="windowbg">
					', $context['common_stats']['total_posts'], ' ', $txt['posts_made'], ' ', $txt['in'], ' ', $context['common_stats']['total_topics'], ' ', $txt['topics'], ' ', $txt['by'], ' ', $context['common_stats']['total_members'], ' ', $txt['members'], '. ', !empty($settings['show_latest_member']) ? $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : '', '<br />
					', (!empty($context['latest_post']) ? $txt['latest_post'] . ': <strong>&quot;' . $context['latest_post']['link'] . '&quot;</strong>  ( ' . $context['latest_post']['time'] . ' )<br />' : ''), '
					<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>', $context['show_stats'] ? '<br />
					<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
				</div>
			</div>';
	}

	// "Users online" - in order of activity.
	echo '
			<div class="fmodule">
				<h4 class="titlebg">
					', $context['show_who'] ? '<a href="' . $scripturl . '?action=who' . '">' : '', '<span class="icon-eye"></span> ', $txt['online_users'] , $context['show_who'] ? '</a>' : '', '
				</h4>
				<div class="inline stats windowbg">
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
				</div>
				<div class="windowbg3">';

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
				</div>
				<div class="last smalltext windowbg">
					', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>.
					', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')
				</div>';

	echo '
			</div>
		</div>
	</div></div>';

}


?>