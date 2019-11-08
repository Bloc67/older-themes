<?php
// Version: 1.1; MessageIndex

function template_main()
{
	global $context, $db_prefix, $settings, $options, $scripturl, $modSettings, $txt;


	$membs = array();
	// if avatars are to be fetched
	if(!empty($settings['avatarboards']))
	{
		// fetch last posters
		if(isset($context['boards']))
		{
			foreach($context['boards'] as $b)
					$membs[$b['last_post']['member']['id']] = $b['last_post']['member']['id'];
		}
		if(isset($context['topics']))
		{
			foreach($context['topics'] as $m)
					$membs[$m['first_post']['member']['id']] = $m['last_post']['member']['id'];
		}
		if(count($membs)>0)
		{
			$request =  db_query("SELECT mem.ID_MEMBER, mem.avatar,
				IFNULL(a.ID_ATTACH, 0) AS ID_ATTACH, a.filename, a.attachmentType 
				FROM " . $db_prefix . "members AS mem
				LEFT JOIN " . $db_prefix . "attachments AS a ON (a.ID_MEMBER = mem.ID_MEMBER)
				WHERE mem.ID_MEMBER IN (" . implode(",",$membs) . ")", __FILE__, __LINE__);
			
			$avvy = array();
			if(mysql_num_rows($request)>0)
			{
				while($row = mysql_fetch_assoc($request))
					$avvy[$row['ID_MEMBER']] = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']));

				mysql_free_result($request);
			}
		}
	}
	if (!empty($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		echo '
<div class="tborder" id="childboards">
	<h3 class="catbg" style="padding: 0;"><span class="left"></span>', $txt['parent_boards'], '</h3>
	<div class="table_frame">
		<table class="table_list">
			<tbody class="content">	';

		foreach ($context['boards'] as $board)
		{
			echo '
			<tr class="windowbg2">
				<td class="icon windowbg"', !empty($board['children']) ? ' rowspan="2"' : '', '>
					<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';

			// If the board or children is new, show an indicator.
			if ($board['new'] || $board['children_new'])
				echo '
						<img src="', $settings['images_url'], '/' .$context['theme_variant'], '/on', $board['new'] ? '' : '2', '.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" border="0" />';
			// No new posts at all! The agony!!
			else
				echo '
						<img src="', $settings['images_url'], '/' .$context['theme_variant'], '/off.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';

			echo '
					</a>
				</td>
				<td class="info">
					<a class="subject" href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a>';


			echo '

					<p>', $board['description'] , '</p>';

			// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
			if (!empty($board['moderators']))
				echo '
					<p class="moderators">', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</p>';

			// Show some basic information about the number of posts, etc.
				echo '
				</td>
				<td class="windowbg" valign="middle" align="center" style="width: 8ex;">
					', $board['posts'],'</td>
				<td class="windowbg" valign="middle" align="center" style="width: 8ex;">
					', $board['topics'],' 
				</td>';

			if(!empty($settings['avatarboards']))
			{
				echo '
			<td class="windowbg2" valign="top" width="2%" style="padding: 2px;"><div style="height: ' , !empty($settings['avatarboards_height']) ? $settings['avatarboards_height'] : '60' , 'px;  width: ' , !empty($settings['avatarboards_width']) ? $settings['avatarboards_width'] : '60' , 'px; background: url(' . (!empty($avvy[$board['last_post']['member']['id']]) ? $avvy[$board['last_post']['member']['id']] : $settings['images_url'].'/none.png') . ') 50% 50% no-repeat;"></div></td>';
			}
			echo '	<td class="lastpost">';

			/* The board's and children's 'last_post's have:
			time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
			link, href, subject, start (where they should go for the first unread post.),
			and member. (which has id, name, link, href, username in it.) */
			if (!empty($board['last_post']['id']))
				echo '
					<b>', $txt[22], '</b> ', $txt[525], ' ', $board['last_post']['member']['link'] , '<br />
					', $txt['smf88'], ' ', $board['last_post']['link'], '<br />
					', $txt[30], ' ', $board['last_post']['time'];

			echo '
				</td>';
				if(!empty($settings['rsslinks']))
				{
					echo '
				<td class="windowbg2" width="1%"><a href="'.$scripturl.'?action=.xml;board=' . $board['id'] . ';type=rss"><img src="' . $settings['images_url'] . '/rss.png" alt="RSS" /></a></td>';
				}
				echo '
			</tr>';

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
			<tr><td colspan="3" class="children windowbg"><strong>', $txt['parent_boards'], '</strong>: ', implode(', ', $children), '</td></tr>';
			}
		}

		echo '
			</tbody>
		</table>
	</div>
</div>';
	}

	if (!empty($options['show_board_desc']) && $context['description'] != '')
	{
		echo '
		<table width="100%" cellpadding="6" cellspacing="1" border="0" class="tborder" style="padding: 0; margin-bottom: 2ex;">
			<tr>
				<td class="titlebg2" width="100%" height="24" style="border-top: 0;">
					<small>', $context['description'], '</small>
				</td>
			</tr>
		</table>';
	}

	// Create the button set...
	$normal_buttons = array(
		'markread' => array('text' => 'mark_read_short', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;sesc=' . $context['session_id']),
		'notify' => array('test' => 'can_mark_notify', 'text' => 125, 'image' => 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';sesc=' . $context['session_id']),
		'new_topic' => array('test' => 'can_post_new', 'text' => 'smf258', 'image' => 'new_topic.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0'),
		'post_poll' => array('test' => 'can_post_poll', 'text' => 'smf20', 'image' => 'new_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll'),
	);

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($normal_buttons['markread']);

	if (!$context['no_topic_listing'])
	{
		echo '
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="middletext">', $txt[139], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#bot"><b>' . $txt['topbottom5'] . '</b></a>' : '', '</td>
				<td align="right" style="padding-right: 1ex;">
					<table cellpadding="0" cellspacing="0">
						<tr>
							', template_button_strip($normal_buttons, 'bottom'), '
						</tr>
					</table>
				</td>
			</tr>
		</table>';

		// If Quick Moderation is enabled start the form.
		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
			echo '
	<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;">';

		echo '
			<div class="tborder" ', $context['browser']['needs_size_fix'] && !$context['browser']['is_ie6'] ? 'style="width: 100%;"' : '', '>
				<table border="0" width="100%" cellspacing="1" cellpadding="4" class="bordercolor">
					<tr>';

		// Are there actually any topics to show?
		if (!empty($context['topics']))
		{
			echo '
						<td width="6%" colspan="2" class="catbg3"></td>

						<td class="catbg3"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[70], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td' , !empty($settings['avatarboards']) ? ' colspan="2"' : '' , ' class="catbg3" width="11%"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=starter', $context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[109], $context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td class="catbg3" width="4%" align="center"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[110], $context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td class="catbg3" width="4%" align="center"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=views', $context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[301], $context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>

						<td class="catbg3" width="14%"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[111], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>';

			// Show a "select all" box for quick moderation?
			if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1)
				echo '
						<td class="catbg3" width="24" valign="middle" align="center">
							<input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="check" />
						</td>';
			// If it's on in "image" mode, don't show anything but the column.
			elseif (!empty($options['display_quick_mod']))
				echo '
						<td class="catbg3" width="4%" valign="middle" align="center"></td>';
		}
		// No topics.... just say, "sorry bub".
		else
			echo '
						<td class="catbg3" width="100%" colspan="7"><b>', $txt[151], '</b></td>';

		echo '
					</tr>';

		if (!empty($settings['display_who_viewing']))
		{
			echo '
					<tr class="windowbg2">
						<td colspan="' , !empty($options['display_quick_mod']) ? '8' : '7' , '"><small>';
			if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt[19];
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt[19] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'], '
						</small></td>
					</tr>';
		}

		foreach ($context['topics'] as $topic)
		{
			// Do we want to seperate the sticky and lock status out?
			if (!empty($settings['seperate_sticky_lock']) && strpos($topic['class'], 'sticky') !== false)
				$topic['class'] = substr($topic['class'], 0, strrpos($topic['class'], '_sticky'));
			if (!empty($settings['seperate_sticky_lock']) && strpos($topic['class'], 'locked') !== false)
				$topic['class'] = substr($topic['class'], 0, strrpos($topic['class'], '_locked'));
	
			echo '
					<tr>
						<td class="windowbg2" valign="middle" align="center" width="3%">
							<img src="', $settings['images_url'], '/topic/', $topic['class'], '.gif" alt="" />
						</td>
						<td class="windowbg2" valign="middle" align="center" width="3%">
							<img src="', $topic['first_post']['icon_url'], '" alt="" />
						</td>
						<td class="windowbg' , !empty($settings['seperate_sticky_lock']) && $topic['is_sticky'] ? '3' : '' , '" valign="middle" ', (!empty($topic['quick_mod']['remove']) ? 'id="topic_' . $topic['first_post']['id'] . '" onmouseout="mouse_on_div = 0;" onmouseover="mouse_on_div = 1;" ondblclick="modify_topic(\'' . $topic['id'] . '\', \'' . $topic['first_post']['id'] . '\', \'' . $context['session_id'] . '\');"' : ''), '>';

			if (!empty($settings['seperate_sticky_lock']))
				echo '
							' , $topic['is_locked'] ? '<img src="' . $settings['images_url'] . '/icons/quick_lock.gif" align="right" alt="" id="lockicon' . $topic['first_post']['id'] . '" style="margin: 0;" />' : '' , '
							' , $topic['is_sticky'] ? '<img src="' . $settings['images_url'] . '/icons/show_sticky.gif" align="right" alt="" id="stickyicon' . $topic['first_post']['id'] . '" style="margin: 0;" />' : '';

			echo '
							', $topic['is_sticky'] ? '<b>' : '' , '<span id="msg_' . $topic['first_post']['id'] . '">', $topic['first_post']['link'], '</span>', $topic['is_sticky'] ? '</b>' : '';

			// Is this topic new? (assuming they are logged in!)
			if ($topic['new'] && $context['user']['is_logged'])
					echo '
							<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><img src="', $settings['images_url'], '/', $context['user']['language'], '/new.gif" alt="', $txt[302], '" /></a>';

			echo '
							<small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>';

			if(!empty($settings['messagepreview']) && empty($settings['messagepreview_last']))
				echo '
						<div class="middletext" style="background: url('.$settings['images_url'].'/bubble.png) 3px 10px no-repeat; padding: 4px 0 0 25px; font-size: 85%;">', $topic['first_post']['preview'] , '</div>';
			elseif(!empty($settings['messagepreview']) && !empty($settings['messagepreview_last']))
				echo '
						<div class="middletext" style="background: url('.$settings['images_url'].'/bubble.png) 3px 10px no-repeat; padding: 4px 0 0 25px; font-size: 85%;">', $topic['last_post']['preview'] , '</div>';

			echo '
						</td>';
			if(!empty($settings['avatarboards']))
				echo '
						<td class="windowbg" width="30" valign="middle" style="padding: 0 5px;"><img src="' . (!empty($avvy[$topic['last_post']['member']['id']]) ? $avvy[$topic['last_post']['member']['id']] : $settings['images_url'].'/none.png') . '" style="width: 35px; height: 35px;" alt="" /></td>';
			echo '
						<td class="windowbg2" valign="middle" width="14%">
							', $topic['first_post']['member']['link'], '
						</td>
						<td class="windowbg' , $topic['is_sticky'] ? '3' : '' , '" valign="middle" width="4%" align="center">
							', $topic['replies'], '
						</td>
						<td class="windowbg' , $topic['is_sticky'] ? '3' : '' , '" valign="middle" width="4%" align="center">
							', $topic['views'], '
						</td>';
			echo '
						<td class="windowbg2" valign="middle" width="14%">
							<a href="', $topic['last_post']['href'], '"><img src="', $settings['images_url'], '/icons/last_post.gif" alt="', $txt[111], '" title="', $txt[111], '" style="float: right;" /></a>
							<span class="smalltext">
								', $topic['last_post']['time'], '<br />
								', $txt[525], ' ', $topic['last_post']['member']['link'], '
							</span>
						</td>';

			// Show the quick moderation options?
			if (!empty($options['display_quick_mod']))
			{
				echo '
						<td class="windowbg' , $topic['is_sticky'] ? '3' : '' , '" valign="middle" align="center" width="4%">';
				if ($options['display_quick_mod'] == 1)
					echo '
								<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="check" />';
				else
				{
					// Check permissions on each and show only the ones they are allowed to use.
					if ($topic['quick_mod']['remove'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=remove;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_remove.gif" width="16" alt="', $txt[63], '" title="', $txt[63], '" /></a>';

					if ($topic['quick_mod']['lock'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=lock;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_lock.gif" width="16" alt="', $txt['smf279'], '" title="', $txt['smf279'], '" /></a>';

					if ($topic['quick_mod']['lock'] || $topic['quick_mod']['remove'])
						echo '<br />';

					if ($topic['quick_mod']['sticky'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=sticky;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_sticky.gif" width="16" alt="', $txt['smf277'], '" title="', $txt['smf277'], '" /></a>';
						
					if ($topic['quick_mod']['move'])
						echo '<a href="', $scripturl, '?action=movetopic;board=', $context['current_board'], '.', $context['start'], ';topic=', $topic['id'], '.0"><img src="', $settings['images_url'], '/icons/quick_move.gif" width="16" alt="', $txt[132], '" title="', $txt[132], '" /></a>';
				}
				echo '</td>';
			}
			echo '
					</tr>';
		}

		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
		{
			echo '
					<tr class="catbg">
						<td colspan="8" align="right">
					<select name="qaction"', $context['can_move'] ? ' onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');"' : '', '>
								<option value="">--------</option>
								', $context['can_remove'] ? '<option value="remove">' . $txt['quick_mod_remove'] . '</option>' : '', '
								', $context['can_lock'] ? '<option value="lock">' . $txt['quick_mod_lock'] . '</option>' : '', '
								', $context['can_sticky'] ? '<option value="sticky">' . $txt['quick_mod_sticky'] . '</option>' : '', '
								', $context['can_move'] ? '<option value="move">' . $txt['quick_mod_move'] . ': </option>' : '', '
								', $context['can_merge'] ? '<option value="merge">' . $txt['quick_mod_merge'] . '</option>' : '', '
								<option value="markread">', $txt['quick_mod_markread'], '</option>
							</select>';

			if ($context['can_move'])
			{
					echo '
							<select id="moveItTo" name="move_to" disabled="disabled">';

					foreach ($context['jump_to'] as $category)
							foreach ($category['boards'] as $board)
							{
								if (!$board['is_current'])
									echo '
												<option value="', $board['id'], '"', !empty($board['selected']) ? ' selected="selected"' : '', '>', str_repeat('-', $board['child_level'] + 1), ' ', $board['name'], '</option>';
							}
					echo '
							</select>';
			}
			echo '
							<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" />
						</td>
					</tr>';
		}

		echo '
				</table>
			</div>
			<a name="bot"></a>';

			// Finish off the form - again.
		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
				echo '
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
	</form>';

		echo '
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="middletext">', $txt[139], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#top"><b>' . $txt['topbottom4'] . '</b></a>' : '', '</td>
			<td align="right" style="padding-right: 1ex;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						', template_button_strip($normal_buttons, 'top'), '
					</tr>
				</table>
			</td>
		</tr>
	</table>';
	}


	// Show breadcrumbs at the bottom too?
	echo '
	<div>', theme_linktree(), '<br /></div>';

	echo '
	<div class="tborder">
		<table cellpadding="8" cellspacing="0" width="100%" class="windowbg2">
			<tr>';

	if (!$context['no_topic_listing'])
			echo '
				<td style="padding-top: 2ex;" class="smalltext">', !empty($modSettings['enableParticipation']) ? '
					<img src="' . $settings['images_url'] . '/topic/my_normal_post.gif" alt="" align="middle" /> ' . $txt['participation_caption'] . '<br />' : '', '
					<img src="' . $settings['images_url'] . '/topic/normal_post.gif" alt="" align="middle" /> ' . $txt[457] . '<br />
					<img src="' . $settings['images_url'] . '/topic/hot_post.gif" alt="" align="middle" /> ' . $txt[454] . '<br />
					<img src="' . $settings['images_url'] . '/topic/veryhot_post.gif" alt="" align="middle" /> ' . $txt[455] . '
				</td>
				<td valign="top" style="padding-top: 2ex;" class="smalltext">
					<img src="' . $settings['images_url'] . '/icons/quick_lock.gif" alt="" align="middle" /> ' . $txt[456] . '<br />' . ($modSettings['enableStickyTopics'] == '1' ? '
					<img src="' . $settings['images_url'] . '/icons/quick_sticky.gif" alt="" align="middle" /> ' . $txt['smf96'] . '<br />' : '') . ($modSettings['pollMode'] == '1' ? '
					<img src="' . $settings['images_url'] . '/topic/normal_poll.gif" alt="" align="middle" /> ' . $txt['smf43'] : '') . '
				</td>';

	echo '
				<td align="', !$context['right_to_left'] ? 'right' : 'left', '" valign="middle">
					<form action="', $scripturl, '" method="get" accept-charset="', $context['character_set'], '" name="jumptoForm">
						<span class="smalltext"><label for="jumpto">' . $txt[160] . '</label>:</span>
					<select name="jumpto" id="jumpto" onchange="if (this.selectedIndex > 0 &amp;&amp; this.options[this.selectedIndex].value) window.location.href = smf_scripturl + this.options[this.selectedIndex].value.substr(smf_scripturl.indexOf(\'?\') == -1 || this.options[this.selectedIndex].value.substr(0, 1) != \'?\' ? 0 : 1);">
								<option value="">' . $txt[251] . ':</option>';

	// Show each category - they all have an id, name, and the boards in them.
	foreach ($context['jump_to'] as $category)
	{
		// Show the category name with a link to the category. (index.php#id)
		echo '
								<option value="" disabled="disabled">-----------------------------</option>
								<option value="#', $category['id'], '">', $category['name'], '</option>
								<option value="" disabled="disabled">-----------------------------</option>';

		/* Now go through each board - they all have:
				id, name, child_level (how many parents they have, basically...), and is_current. (is this the current board?) */
		foreach ($category['boards'] as $board)
		{
			// Show some more =='s if this is a child, so as to make it look nice.
			echo '
								<option value="?board=', $board['id'], '.0"', $board['is_current'] ? ' selected="selected"' : '', '> ', str_repeat('==', $board['child_level']), '=> ', $board['name'], '</option>';
		}
	}

	echo '
						</select>&nbsp;
					<input type="button" value="', $txt[161], '" onclick="if (this.form.jumpto.options[this.form.jumpto.selectedIndex].value) window.location.href = \'', $scripturl, '\' + this.form.jumpto.options[this.form.jumpto.selectedIndex].value;" />
					</form>
				</td>
			</tr>
		</table>
	</div>';

	// Javascript for inline editing.
	echo '
<script language="JavaScript" type="text/javascript" src="' . $settings['default_theme_url'] . '/xml_board.js"></script>
<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[

	// Hide certain bits during topic edit.
	hide_prefixes.push("lockicon", "stickyicon", "pages", "newicon");

	// Use it to detect when we\'ve stopped editing.
	document.onclick = modify_topic_click;

	var mouse_on_div;
	function modify_topic_click()
	{
		if (in_edit_mode == 1 && mouse_on_div == 0)
			modify_topic_save("', $context['session_id'], '");
	}

	function modify_topic_keypress(oEvent)
	{
		if (typeof(oEvent.keyCode) != "undefined" && oEvent.keyCode == 13)
		{
			modify_topic_save("', $context['session_id'], '");
			if (typeof(oEvent.preventDefault) == "undefined")
				oEvent.returnValue = false;
			else
				oEvent.preventDefault();
		}
	}

	// For templating, shown when an inline edit is made.
	function modify_topic_show_edit(subject)
	{
		// Just template the subject.
		setInnerHTML(cur_subject_div, \'<input type="text" name="subject" value="\' + subject + \'" size="60" style="width: 99%;"  maxlength="80" onkeypress="modify_topic_keypress(event)" /><input type="hidden" name="topic" value="\' + cur_topic_id + \'" /><input type="hidden" name="msg" value="\' + cur_msg_id.substr(4) + \'" />\');
	}

	// And the reverse for hiding it.
	function modify_topic_hide_edit(subject)
	{
		// Re-template the subject!
		setInnerHTML(cur_subject_div, \'<a href="', $scripturl, '?topic=\' + cur_topic_id + \'.0">\' + subject + \'</a>\');
	}

// ]]></script>';
}

function theme_show_buttons()
{
	global $context, $settings, $options, $txt, $scripturl;

	$buttonArray = array();

	// If they are logged in, and the mark read buttons are enabled..
	if ($context['user']['is_logged'] && $settings['show_mark_read'])
		$buttonArray[] = '<a href="' . $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;sesc=' . $context['session_id'] . '">' . $txt['mark_read_short'] . '</a>';

	// If the user has permission to show the notification button... ask them if they're sure, though.
	if ($context['can_mark_notify'])
		$buttonArray[] = '<a href="' . $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';sesc=' . $context['session_id'] . '" onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');">' . $txt[125] . '</a>';

	// Are they allowed to post new topics?
	if ($context['can_post_new'])
		$buttonArray[] = '<a href="' . $scripturl . '?action=post;board=' . $context['current_board'] . '.0">' . $txt['smf258'] . '</a>';

	// How about new polls, can the user post those?
	if ($context['can_post_poll'])
		$buttonArray[] = '<a href="' . $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll">' . $txt['smf20'] . '</a>';

	return implode(' &nbsp;|&nbsp; ', $buttonArray);
}

?>