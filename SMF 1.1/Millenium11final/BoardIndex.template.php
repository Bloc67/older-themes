<?php
// Version: 1.1; BoardIndex

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;


	foreach ($context['categories'] as $category)
	{
		echo '
	<div class="category">
		<h1>';

		if ($category['can_collapse'])
			echo '<a href="', $category['collapse_href'], '">', $category['is_collapsed'] ? '<img src="'.$settings['images_url'].'/expand.gif" alt="" />' : '<img src="'.$settings['images_url'].'/collapse.gif" alt="" />' , '</a>&nbsp;';

		echo $category['link'],'
		</h1>';

		// Assuming the category hasn't been collapsed...
		if (!$category['is_collapsed'])
		{
			foreach ($category['boards'] as $board)
			{
			echo '
		<div class="boards" style="_height: 1%; overflow: auto;">';

			// If the board is new, show a strong indicator.
			if ($board['new'])
				echo '<img align="left" style="margin-right: 2ex;" src="', $settings['images_url'], '/on.gif" alt="', $txt[333], '" title="', $txt[333], '" />';
			// This board doesn't have new posts, but its children do.
			elseif ($board['children_new'])
				echo '<img align="left" style="margin-right: 2ex;" src="', $settings['images_url'], '/on2.gif" alt="', $txt[333], '" title="', $txt[333], '" />';
			// No new posts at all! The agony!!
			else
				echo '<img align="left" style="margin-right: 2ex;"  src="', $settings['images_url'], '/off.gif" alt="', $txt[334], '" title="', $txt[334], '" />';

			echo '
			<span class="bigtitle"><a href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a></span>';
			if(!empty($board['description']))
				echo '<br /><span class="orangetext">',$board['description'],'</span>';
			echo '
			<br />
			<span class="middletext">
					', $board['posts'], ' ', $txt[21], ' ',$txt['smf88'],'
					', $board['topics'],' ', $txt[330];
			if (!empty($board['last_post']['id']))
					echo '<br />
						<b>', $txt[22], '</b>  ', $txt[525], ' ', $board['last_post']['member']['link'] , '
						', $txt['smf88'], ' ', $board['last_post']['link'], '
						', $txt[30], ' ', $board['last_post']['time'];

			// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
			if (!empty($board['moderators']))
					echo count($board['moderators']) == 1 ? $txt[298] : $txt[299], ': ', implode(', ', $board['link_moderators']);

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
							$children[] = $child['new'] ? '<img src="'.$settings['images_url'].'/childon.gif" alt="" /> ' . $child['link'] : '<img src="'.$settings['images_url'].'/childoff.gif" alt="" /> '.$child['link'];
					}

					echo '
					<br /><span class="middletext"><b>', $txt['parent_boards'], '</b>: ', implode(', ', $children), '</span>';
				}
			echo '
			</span>
		</div>';
			}
		}
		echo '</div>';
	}

	if ($context['user']['is_logged'])
	{

		$mark_read_button = array('markread' => array('text' => 452, 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;sesc=' . $context['session_id']));

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
				echo '<div class="modbutton">
							 ', template_button_strip_mill($mark_read_button, 'top'),'</div>';
	}

	// Here's where the "Info Center" starts...
	echo '<br />
<div style="overflow: auto;">
		<div class="category">
			<h1>', $txt[685], '</h1>
		</div>';

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']))
	{
		echo '
		<div class="boards2">
				<span class="bigtitle">', $txt[214], '</span>
				<a href="', $scripturl, '?action=recent"><img src="', $settings['images_url'], '/icons/posts.gif" align="right" alt="', $txt[214], '" /></a>
				<div style="_height: 1%;">';

		// Show lots of posts.
		if (!empty($context['latest_posts']))
		{
			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
							<div style="overflow: hidden;">
								<div class="middletext" style="white-space: nowrap; float: right; text-align: right;">', $post['time'], '</div>
								<div class="middletext" style=""><b>', $post['link'], '</b> ', $txt[525], ' ', $post['poster']['link'], ' (', $post['board']['link'], ')</div>
							</div>';
		}
		echo '<br /></div>

		</div>';
	}

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
		<div class="boards2">
					<span class="bigtitle">', $context['calendar_only_today'] ? $txt['calendar47b'] : $txt['calendar47'], '</span>
					<a href="', $scripturl, '?action=calendar"><img align="right" src="', $settings['images_url'], '/icons/calendar.gif" alt="', $txt['calendar24'], '" /></a>
					<br />
					<span class="windowbg2">
						<span class="smalltext">';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
				echo '
							<span style="color: #', $modSettings['cal_holidaycolor'], ';">', $txt['calendar5'], ' ', implode(', ', $context['calendar_holidays']), '</span><br />';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
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
					</span>';
		echo '</div>';
	}


	// Show YaBB SP1 style information...
	if ($settings['show_sp1_info'])
	{
		echo '
		<div class="boards2">
				<span class="bigtitle">', $txt[645], '</span>
				<a href="', $scripturl, '?action=stats"><img align="right" src="', $settings['images_url'], '/icons/info.gif" alt="', $txt[645], '" /></a>
				<br /><span class="windowbg2">
						<span class="middletext">
							', $context['common_stats']['total_posts'], ' ', $txt[95], ' ', $txt['smf88'], ' ', $context['common_stats']['total_topics'], ' ', $txt[64], ' ', $txt[525], ' ', $context['common_stats']['total_members'], ' ', $txt[19], '. ', $txt[656], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
							<br /> ' . $txt[659] . ': <b>&quot;' . $context['latest_post']['link'] . '&quot;</b>  ( ' . $context['latest_post']['time'] . ' )<br />
							<a href="', $scripturl, '?action=recent">', $txt[234], '</a>', $context['show_stats'] ? '<br />
							<a href="' . $scripturl . '?action=stats">' . $txt['smf223'] . '</a>' : '', '
						</span>
				</span>';
		echo '</div>';
	}

	// "Users online" - in order of activity.
	echo '
		<div class="boards2">
				<span class="bigtitle">', $txt[158], '</span>
				', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<img align="right" src="', $settings['images_url'], '/icons/online.gif" alt="', $txt[158], '" />', $context['show_who'] ? '</a>' : '', '
				<br /><span class="windowbg2">';

	echo '
						', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', $context['num_guests'], ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . $context['num_users_online'], ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

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

	echo $context['show_who'] ? '</a>' : '', '
						<br /><span class="smalltext">';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
		echo '
							', $txt[140], ':<br />', implode(', ', $context['list_users_online']);

	echo '
							<br />
							', $context['show_stats'] && !$settings['show_sp1_info'] ? '<a href="' . $scripturl . '?action=stats">' . $txt['smf223'] . '</a>' : '', '
				</span>
				</span><br />
				<span class="windowbg2">
						<span class="middletext">
							', $txt['most_online_today'], ': <b>', $modSettings['mostOnlineToday'], '</b>.
							', $txt['most_online_ever'], ': ', $modSettings['mostOnline'], ' (' , timeformat($modSettings['mostDate']), ')
						</span>
				</span>';
	echo '</div>';

echo '</div>';
}

?>
