<?php
// Version: 1.1; BoardIndex

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	
	echo '<div class="center3">';
	
	// Show some statistics next to the link tree if SP1 info is off.
	echo theme_linktree();


	foreach ($context['categories'] as $category)
	{
		echo '
		<div class="catbg" style="padding: 5px 0 5px 0;">';
		$first = false;

		// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
				<a style="display: block; float: right; font-size: smaller;" href="', $category['collapse_href'], '">' , !$category['is_collapsed'] ? 'collapse' : 'expand' ,'</a> ';

		echo '
				', $category['link'], '
		</div>';

		// Assuming the category hasn't been collapsed...
		if (!$category['is_collapsed'])
		{
			foreach ($category['boards'] as $board)
			{
				echo '
			<div class="board">
				<div class="boardicon">';
				// If the board is new, show a strong indicator.
				if ($board['new'])
					echo '<img src="', $settings['images_url'], '/png24/on' , $context['browser']['is_ie6'] ? '_ie' : '' , '.png" alt="', $txt[333], '" title="', $txt[333], '" />';
				// This board doesn't have new posts, but its children do.
				elseif ($board['children_new'])
					echo '<img width="44" height="44" src="', $settings['images_url'], '/png24/on2' , $context['browser']['is_ie6'] ? '_ie' : '' , '.png" alt="', $txt[333], '" title="', $txt[333], '" />';
				// No new posts at all! The agony!!
				else
					echo '<img width="44" height="44" src="', $settings['images_url'], '/png24/off' , $context['browser']['is_ie6'] ? '.gif' : '.png' , '" alt="', $txt[334], '" title="', $txt[334], '" />';
				
				echo '
				</div>
			<h3><a href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a></h3>
			<ul>';

				if(!empty($board['description']))
					echo '<li class="descript">', $board['description'] , '</li>';

				if (!empty($board['moderators']))
					echo '
				<li class="moderators">', count($board['moderators']) == 1 ? $txt[298] : $txt[299], ': ', implode(', ', $board['link_moderators']), '</li>';

				echo '
				<li class="stats">', $board['posts'], ' ', $txt[21], ' in ', $board['topics'],' ', $txt[330], '</li>';
				
				if (!empty($board['last_post']['id']))
					echo '
				<li class="lastpost"><b>', $txt[22], '</b>  ', $txt[525], ' ', $board['last_post']['member']['link'] , '
						', $txt['smf88'], ' ', $board['last_post']['link'], ' 
						', $txt[30], ' ', $board['last_post']['time'], '
				<li>';
				echo '
			</ul>';

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
			<p class="childs"><b>', $txt['parent_boards'], '</b>: ', implode(', ', $children), '</p>';
				}
			echo '
			</div>';
			}
		}
	}

	if ($context['user']['is_logged'])
	{
		// Mark read button.
		$mark_read_button = array('markread' => array('text' => 452, 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;sesc=' . $context['session_id']));

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
				echo '
				<table cellpadding="0" cellspacing="0" border="0" style="position: relative; top: -5px;">
					<tr>
							 ', template_button_strip($mark_read_button, 'top'), '
					</tr>
				</table>';
	}
	echo '</div><div class="center_dark">';


	
	// Here's where the "Info Center" starts...
	echo '<br />
		<h2 class="catbg" style="border: none; color: #FFC591">
			<a href="#" onclick="shrinkHeaderIC(!current_header_ic); return false;"><img id="upshrink_ic" src="', $settings['images_url'], '/', empty($options['collapse_header_ic']) ? 'collapse.gif' : 'expand.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin-right: 2ex;" align="right" /></a>
			', $txt[685], '
		</h2>
		<div id="upshrinkHeaderIC"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>';

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']))
	{
		echo '
			<h3 class="infocenter"><a href="', $scripturl, '?action=recent"><img src="', $settings['images_url'], '/post/xx.gif" alt="', $txt[214], '" /></a> ', $txt[214], '</h3>
			<div class="windowbg2 infocenter">';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
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
						<table cellpadding="0" cellspacing="0" width="100%" border="0">';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
							<tr>
								<td class="middletext" valign="top"><b>', $post['link'], '</b> ', $txt[525], ' ', $post['poster']['link'], ' (', $post['board']['link'], ')</td>
								<td class="middletext" align="right" valign="top" nowrap="nowrap">', $post['time'], '</td>
							</tr>';
			echo '
						</table>';
		}
		echo '
			</div>';
	}

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
			<h3 class="infocenter"><a href="', $scripturl, '?action=calendar"><img src="', $settings['images_url'], '/icons/calendar.gif" alt="', $txt['calendar24'], '" /></a> ', $context['calendar_only_today'] ? $txt['calendar47b'] : $txt['calendar47'], '</h3>
			<div class="windowbg2 infocenter smalltext">';

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
				</div>';
	}


	// Show YaBB SP1 style information...
	if ($settings['show_sp1_info'])
	{
		echo '
			<h3 class="infocenter"><a href="', $scripturl, '?action=stats"><img src="', $settings['images_url'], '/icons/info.gif" alt="', $txt[645], '" /></a> ', $txt[645], '</h3>
			<div class="windowbg2 infocenter middletext">
							', $context['common_stats']['total_posts'], ' ', $txt[95], ' ', $txt['smf88'], ' ', $context['common_stats']['total_topics'], ' ', $txt[64], ' ', $txt[525], ' ', $context['common_stats']['total_members'], ' ', $txt[19], '. ', $txt[656], ': <b> ', $context['common_stats']['latest_member']['link'], '</b>
							<br /> ' . $txt[659] . ': <b>&quot;' . $context['latest_post']['link'] . '&quot;</b>  ( ' . $context['latest_post']['time'] . ' )<br />
							<a href="', $scripturl, '?action=recent">', $txt[234], '</a>', $context['show_stats'] ? '<br />
							<a href="' . $scripturl . '?action=stats">' . $txt['smf223'] . '</a>' : '', '
			</div>';
	}

	// "Users online" - in order of activity.
	echo '
			<h3 class="infocenter">', $txt[158], '</h3>
			<div class="windowbg2 infocenter">';

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
						<div class="smalltext">';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
		echo '
							', $txt[140], ':<br />', implode(', ', $context['list_users_online']);

	echo '
							<br />
							', $context['show_stats'] && !$settings['show_sp1_info'] ? '<a href="' . $scripturl . '?action=stats">' . $txt['smf223'] . '</a>' : '', '
						</div>
				</div>
				<div class="windowbg2 middletext">
							', $txt['most_online_today'], ': <b>', $modSettings['mostOnlineToday'], '</b>.
							', $txt['most_online_ever'], ': ', $modSettings['mostOnline'], ' (' , timeformat($modSettings['mostDate']), ')
				</div>';

	// If they are logged in, but SP1 style information is off... show a personal message bar.
	if ($context['user']['is_logged'] && !$settings['show_sp1_info'])
	{
		 echo '
				<h3 class="infocenter">', $context['allow_pm'] ? '<a href="' . $scripturl . '?action=pm">' : '', '<img src="', $settings['images_url'], '/message_sm.gif" alt="', $txt[159], '" />', $context['allow_pm'] ? '</a>' : '', ' ', $txt[159], '</h3>
				<div class="infocenter windowbg2">
						<b><a href="', $scripturl, '?action=pm">', $txt[159], '</a></b>
						<div class="smalltext">
							', $txt[660], ' ', $context['user']['messages'], ' ', $context['user']['messages'] == 1 ? $txt[471] : $txt[153], '.... ', $txt[661], ' <a href="', $scripturl, '?action=pm">', $txt[662], '</a> ', $txt[663], '
						</div>
				</div>';
	}

	// Show the login bar. (it's only true if they are logged out anyway.)
	if ($context['show_login_bar'])
	{
		echo '
				<h3 class="infocenter"><a href="', $scripturl, '?action=login"><img src="', $settings['images_url'], '/icons/login.gif" alt="', $txt[34], '" /></a> ', $txt[34], ' <a href="', $scripturl, '?action=reminder" class="smalltext">(' . $txt[315] . ')</a></h3>
				<div class="infocenter windowbg2">
						<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;">
							<table border="0" cellpadding="2" cellspacing="0" align="center" width="100%"><tr>
								<td valign="middle" align="left">
									<label for="user"><b>', $txt[35], ':</b><br />
									<input type="text" name="user" id="user" size="15" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="passwrd"><b>', $txt[36], ':</b><br />
									<input type="password" name="passwrd" id="passwrd" size="15" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="cookielength"><b>', $txt[497], ':</b><br />
									<input type="text" name="cookielength" id="cookielength" size="4" maxlength="4" value="', $modSettings['cookieTime'], '" /></label>
								</td>
								<td valign="middle" align="left">
									<label for="cookieneverexp"><b>', $txt[508], ':</b><br />
									<input type="checkbox" name="cookieneverexp" id="cookieneverexp" checked="checked" class="check" /></label>
								</td>
								<td valign="middle" align="left">
									<input type="submit" value="', $txt[34], '" />
								</td>
							</tr></table>
						</form>
					</div>';
	}

	echo '</div>
	</div>';

}

?>