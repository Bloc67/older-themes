<?php
/**
 * Simple Machines Forum (SMF)
 *
 *
 * @version 2.0
 */

function template_main()
{
	global $context, $smcFunc, $db_prefix, $settings, $options, $scripturl, $modSettings, $txt, $user_info;

	echo '
	<a id="top"></a>';
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
			{
					$membs[$m['first_post']['member']['id']] = $m['first_post']['member']['id'];
					$membs[$m['last_post']['member']['id']] = $m['last_post']['member']['id'];
			}
		}
		if(count($membs)>0)
		{
			$request =  $smcFunc['db_query']('',"SELECT mem.id_member as ID_MEMBER, mem.real_name, mem.avatar,
				IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType 
				FROM " . $db_prefix . "members AS mem
				LEFT JOIN " . $db_prefix . "attachments AS a ON (a.id_member = mem.id_member)
				WHERE mem.id_member IN (" . implode(",",$membs) . ")",array());
			
			$avvy = array();
			$savvy = array();
			if($smcFunc['db_num_rows']($request)>0)
			{
				while($row = $smcFunc['db_fetch_assoc']($request))
				{
					$avvy[$row['ID_MEMBER']]  = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']));
					$savvy[$row['ID_MEMBER']] = $row['real_name'];
				}
				$smcFunc['db_free_result']($request);
			}
		}
	}
	
	if (!empty($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		echo '
<div class="tborder childboards" id="board_', $context['current_board'], '_childboards">
	<div class="title_bar"><h3 class="titlebg">', $txt['parent_boards'], '</h3></div>
		<div class="table_frame">
			<table class="table_list">
				<tbody id="board_', $context['current_board'], '_children" class="content">';

		foreach ($context['boards'] as $board)
		{
			echo '
				<tr id="board_', $board['id'], '" class="windowbg2">
					<td class="icon windowbg"', !empty($board['children']) ? ' rowspan="2"' : '', '>
						<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';

			// If the board or children is new, show an indicator.
			if ($board['new'] || $board['children_new'])
				echo '
						<img src="', $settings['images_url'], '/on', $board['new'] ? '' : '2', '.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" border="0" />';
			// Is it a redirection board?
			elseif ($board['is_redirect'])
				echo '
						<img src="', $settings['images_url'], '/redirect.png" alt="*" title="*" border="0" />';
			// No new posts at all! The agony!!
			else
				echo '
						<img src="', $settings['images_url'], '/off.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';

			echo '
					</a>
				</td>
				<td class="info">';

			if(!empty($settings['rsslinks']))
				echo '
				<a href="'.$scripturl.'?action=.xml;board=' . $board['id'] . ';type=rss"><img class="floatright" src="' . $settings['images_url'] . '/rss.png" alt="RSS" /></a>';

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
						<p class="moderators">', count($board['moderators']) === 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</p>';

			// Show some basic information about the number of posts, etc.
			echo '
					</td>
					<td class="stats windowbg">
						<p>', comma_format($board['posts']), ' ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], ' <br />
						', $board['is_redirect'] ? '' : comma_format($board['topics']) . ' ' . $txt['board_topics'], '
						</p>
					</td>
					<td class="lastpost">';

			if(!empty($settings['avatarboards']))
				echo '
				<img class="floatright" style="max-height: 20px; margin: 4px;" src="' . (!empty($avvy[$board['last_post']['member']['id']]) ? $avvy[$board['last_post']['member']['id']] : $settings['images_url'].'/none.png') . '" />
					';
			
			/* The board's and children's 'last_post's have:
			time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
			link, href, subject, start (where they should go for the first unread post.),
			and member. (which has id, name, link, href, username in it.) */
			if (!empty($board['last_post']['id']))
				echo '
						<p><strong>', $txt['last_post'], '</strong>  ', $txt['by'], ' ', $board['last_post']['member']['link'], '<br />
						', $txt['in'], ' ', $board['last_post']['link'], '<br />
						', $txt['on'], ' ', $board['last_post']['time'],'
						</p>';

			echo '
					</td>
				</tr>';

			if (!empty($board['children']))
				render_childs($board['children'],3,false, $board['id']);
		}
		echo '
				</tbody>
			</table>
		</div>
	</div>';
	}

	if (!empty($options['show_board_desc']) && $context['description'] != '')
		echo '
	<p class="description_board">', $context['description'], '</p>';

	// Create the button set...
	$normal_buttons = array(
		'new_topic' => array('test' => 'can_post_new', 'text' => 'new_topic', 'image' => 'new_topic.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0', 'active' => true),
		'post_poll' => array('test' => 'can_post_poll', 'text' => 'new_poll', 'image' => 'new_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll'),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : ''). 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'markread' => array('text' => 'mark_read_short', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
	);

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($normal_buttons['markread']);

	// Allow adding new buttons easily.
	call_integration_hook('integrate_messageindex_buttons', array(&$normal_buttons));

	if (!$context['no_topic_listing'])
	{
		echo '
		<div class="pagesection">
			<div class="mypages align_left">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#bot"><strong>' . $txt['go_down'] . '</strong></a>' : '', '</div>
			', template_button_strip($normal_buttons, 'right'), '
		</div>';

		// If Quick Moderation is enabled start the form.
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
			echo '
	<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" class="clear" name="quickModForm" id="quickModForm">';

		echo '
			<div class="tborder topic_table" id="messageindex">
				<table class="table_grid" cellspacing="0">
					<thead>
				<tr class="catbg">';

		// Are there actually any topics to show?
		if (!empty($context['topics']))
		{
			echo '
							<th scope="col" class="smalltext first_th">&nbsp;</th>';
			echo '
							<th scope="col" class="smalltext"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=starter', $context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['started_by'], $context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></th>
							<th scope="col" class="smalltext" width="12%"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=views', $context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['views'], $context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></th>';
			// Show a "select all" box for quick moderation?
			if (empty($context['can_quick_mod']))
				echo '
							<th scope="col" class="smalltext last_th" width="20%"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></th>';
			else
				echo '
							<th scope="col" class="smalltext" width="20%"><a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></th>';

			// Show a "select all" box for quick moderation?
			if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1)
				echo '
							<th scope="col" class="smalltext last_th" width="24"><input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check" /></th>';

			// If it's on in "image" mode, don't show anything but the column.
			elseif (!empty($context['can_quick_mod']))
				echo '
							<th class="smalltext last_th" width="4%">&nbsp;</th>';
		}
		// No topics.... just say, "sorry bub".
		else
			echo '
							<th scope="col" class="smalltext first_th" width="8%">&nbsp;</th>
							<th class="smalltext" colspan="3"><strong>', $txt['msg_alert_none'], '</strong></th>
							<th scope="col" class="smalltext last_th" width="8%">&nbsp;</th>';

		echo '
				</tr>
			</thead>
			<tbody>';

		if (!empty($settings['display_who_viewing']))
		{
			echo '
						<tr class="smalltext whos_viewing">
					<td colspan="', !empty($context['can_quick_mod']) ? '6' : '5', '" class="smalltext">';
			if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) === 1 ? $txt['who_member'] : $txt['members'];
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'], '
					</td>
				</tr>';
		}

		// If this person can approve items and we have some awaiting approval tell them.
		if (!empty($context['unapproved_posts_message']))
		{
			echo '
				<tr class="windowbg2">
					<td colspan="', !empty($context['can_quick_mod']) ? '6' : '5', '">
						<span class="alert">!</span> ', $context['unapproved_posts_message'], '
					</td>
				</tr>';
		}

		$alternate = true;
		foreach ($context['topics'] as $topic)
		{
			$sub_class="";
			$color_class = 'windowbg';
			
			if(empty($settings['donotshowhot']))
			{
				if($topic['is_hot'])
					$color_class = 'windowbg_hot';
				if($topic['is_very_hot'])
					$color_class = 'windowbg_very_hot';
			}

			echo '
						<tr class="' , $color_class , '">';
			
			if(!empty($settings['avatarboards']))
				echo '
						<td class="myicon1" style="width: 1%;">
				<a href="',$scripturl,'?action=miniprofile;u='.$topic['first_post']['member']['id'].'" rel="width:560,height:260" id="mb'.$topic['id'].'" class="mb" >
				<img class="avyframe" src="' . (!empty($avvy[$topic['first_post']['member']['id']]) ? $avvy[$topic['first_post']['member']['id']] : $settings['images_url'].'/TPguest.png') . '" style="max-height: 35px;float: none; margin: 4px auto 0 auto;" alt="*" title="' . $topic['first_post']['member']['name'].'" />
				</a>
				<div class="multiBoxDesc mb'.$topic['id'].' mbHidden" style="display: none;"></div>
						</td>';
			else
				echo '<td class="myicon1" >
							<img src="', $topic['first_post']['icon_url'], '" alt="" /></td>';

			if(!empty($settings['showmootips']))
			{
					$which = !empty($settings['firstpreview']) ? 'first' : 'last';
					$which_post = $which.'_post';
					$mootip = '
			<img src=' . (!empty($avvy[$topic[$which_post ]['member']['id']]) ? $avvy[$topic[$which_post]['member']['id']] : $settings['images_url'].'/TPguest.png') . '  class=mooavy />
				<span class=greysmalltext>'.$which.' post by <b>'.$topic[$which_post]['member']['name'].'</b><br>' . $topic[$which_post]['time'] . '</span> 
				<hr><div> '. $topic[$which_post]['preview'].'</div>';
			}
			else
				$mootip = '';

			echo '
						<td class="subject">';
			if($topic['is_sticky'] && !$topic['is_locked'])
				echo '<img class="floatright" src="', $settings['images_url'], '/icons/quick_sticky.gif" alt="" style="margin: 0px 5px 0 5px;" />';
			elseif(!$topic['is_sticky'] && $topic['is_locked'])
				echo '<img class="floatright" src="', $settings['images_url'], '/icons/quick_lock.gif" alt="" style="margin: 0px 5px 0 5px;" />';
			elseif($topic['is_sticky'] && $topic['is_locked'])
				echo '<img class="floatright" src="', $settings['images_url'], '/icons/quick_lock.gif" alt="" style="margin: 0px 5px 0 0px;" />
			<img class="floatright" src="', $settings['images_url'], '/icons/quick_sticky.gif" alt="" style="margin: 0px 0px 0 5px;" />';
			
			if(!empty($settings['avatarboards']))
				echo '
			<img class="floatleft" src="', $topic['first_post']['icon_url'], '" alt="" style="margin: 8px 8px 1em 0;" />';

					
					echo '			<div style="padding: 4px 0;" ', (!empty($topic['quick_mod']['modify']) ? 'id="topic_' . $topic['first_post']['id'] . '" onmouseout="mouse_on_div = 0;" onmouseover="mouse_on_div = 1;" ondblclick="modify_topic(\'' . $topic['id'] . '\', \'' . $topic['first_post']['id'] . '\', \'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');"' : ''), '>
									<span id="msg_' . $topic['first_post']['id'] . '"><span class="', $sub_class, '"><a href="', $topic['first_post']['href'], '" ' , !empty($settings['showmootips']) ? ' class="tipz"' : '' , ' title="'.$mootip.'"><span',$topic['is_sticky'] ? ' class="subject_sticky"' : '','><span class="bigger">' , $topic['subject'] ,'</span></span></a></span>' . (!$context['can_approve_posts'] && !$topic['approved'] ? '&nbsp;<em>(' . $txt['awaiting_approval'] . ')</em>' : ''), '</span>';

			// Is this topic new? (assuming they are logged in!)
			if ($topic['new'] && $context['user']['is_logged'])
					echo '
									<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><img src="', $settings['lang_images_url'], '/new.gif" alt="', $txt['new'], '" /></a>';

			echo '				<span class="mypages">', $topic['pages'], '</span>
									<p class="mysmalltext">', $txt['started_by'], ' ', $topic['first_post']['member']['link'], '	</p>
								</div>
							</td>
							<td class="stats">
								', $topic['replies'], ' ', $txt['replies'], '
								<br />
								', $topic['views'], ' ', $txt['views'], '
							</td>
							<td class="lastpost" >
								<a href="'.$topic['last_post']['href'].'"><img src="'.$settings['images_url'].'/icons/last_post.gif" alt="last" title="Go to the last post" style="margin: 0 0 0 5px;float: right;" /></a>
								', $topic['last_post']['member']['name'], ' -
								', $topic['last_post']['time'], '
							</td>';

			// Show the quick moderation options?
			if (!empty($context['can_quick_mod']))
			{
				echo '
							<td >';
				if ($options['display_quick_mod'] == 1)
					echo '
								<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="input_check" />';
				else
				{
					// Check permissions on each and show only the ones they are allowed to use.
					if ($topic['quick_mod']['remove'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=remove;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_remove.gif" width="16" alt="', $txt['remove_topic'], '" title="', $txt['remove_topic'], '" /></a>';

					if ($topic['quick_mod']['lock'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=lock;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_lock.gif" width="16" alt="', $txt['set_lock'], '" title="', $txt['set_lock'], '" /></a>';

					if ($topic['quick_mod']['lock'] || $topic['quick_mod']['remove'])
						echo '<br />';

					if ($topic['quick_mod']['sticky'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=sticky;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_sticky.gif" width="16" alt="', $txt['set_sticky'], '" title="', $txt['set_sticky'], '" /></a>';

					if ($topic['quick_mod']['move'])
						echo '<a href="', $scripturl, '?action=movetopic;board=', $context['current_board'], '.', $context['start'], ';topic=', $topic['id'], '.0"><img src="', $settings['images_url'], '/icons/quick_move.gif" width="16" alt="', $txt['move_topic'], '" title="', $txt['move_topic'], '" /></a>';
				}
				echo '
							</td>';
			}
			echo '
						</tr>';
						$alternate = !$alternate;
		}

		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
		{
			echo '
				<tr class="titlebg">
					<td colspan="6" align="right">
						<select class="qaction" name="qaction"', $context['can_move'] ? ' onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');"' : '', '>
							<option value="">--------</option>', $context['can_remove'] ? '
							<option value="remove">' . $txt['quick_mod_remove'] . '</option>' : '', $context['can_lock'] ? '
							<option value="lock">' . $txt['quick_mod_lock'] . '</option>' : '', $context['can_sticky'] ? '
							<option value="sticky">' . $txt['quick_mod_sticky'] . '</option>' : '', $context['can_move'] ? '
							<option value="move">' . $txt['quick_mod_move'] . ': </option>' : '', $context['can_merge'] ? '
							<option value="merge">' . $txt['quick_mod_merge'] . '</option>' : '', $context['can_restore'] ? '
							<option value="restore">' . $txt['quick_mod_restore'] . '</option>' : '', $context['can_approve'] ? '
							<option value="approve">' . $txt['quick_mod_approve'] . '</option>' : '', $context['user']['is_logged'] ? '
							<option value="markread">' . $txt['quick_mod_markread'] . '</option>' : '', '
						</select>';

			// Show a list of boards they can move the topic to.
			if ($context['can_move'])
			{
					echo '
						<select class="qaction" id="moveItTo" name="move_to" disabled="disabled">';

					foreach ($context['move_to_boards'] as $category)
					{
						echo '
							<optgroup label="', $category['name'], '">';
						foreach ($category['boards'] as $board)
								echo '
								<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['child_level'] > 0 ? str_repeat('==', $board['child_level'] - 1) . '=&gt;' : '', ' ', $board['name'], '</option>';
						echo '
							</optgroup>';
					}
					echo '
						</select>';
			}

			echo '
						<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" class="button_submit qaction" />
					</td>
				</tr>';
		}

		echo '
			</tbody>
		</table>
	</div>
	<a id="bot"></a>';

		// Finish off the form - again.
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
			echo '
	<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
	</form>';

		echo '
	<div class="pagesection">
		', template_button_strip($normal_buttons, 'right'), '
		<div class="mypages">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . '&nbsp;&nbsp;<a href="#top"><strong>' . $txt['go_up'] . '</strong></a>' : '', '</div>
	</div>';
	}


	echo '
	<div class="tborder" id="topic_icons">
		<div class="description">
			<p class="floatright" id="message_index_jump_to">&nbsp;</p>';

	if (!$context['no_topic_listing'])
	{
		if(empty($settings['donotshowhot']))
			echo '
			<p class="floatleft smalltext" style="margin-right: 20px;">
				<span class="hotindicator"><span class="windowbg" style="padding:0;"></span>' . $txt['normal_topic'] . '</span>
				<span class="hotindicator"><span class="windowbg_hot" style="padding:0;"></span>' . sprintf($txt['hot_topics'], $modSettings['hotTopicPosts']) . '</span>
				<span class="hotindicator"><span class="windowbg_very_hot" style="padding:0;"></span>' . sprintf($txt['very_hot_topics'], $modSettings['hotTopicVeryPosts']) . '</span>
			</p>';
		
		echo '
			<p class="smalltext">
				<img src="' . $settings['images_url'] . '/icons/quick_lock.gif" alt="" align="middle" /> ' . $txt['locked_topic'] . '<br />' . ($modSettings['enableStickyTopics'] == '1' ? '
				<img src="' . $settings['images_url'] . '/icons/quick_sticky.gif" alt="" align="middle" /> ' . $txt['sticky_topic'] . '<br />' : '') . ($modSettings['pollMode'] == '1' ? '
				<img src="' . $settings['images_url'] . '/topic/normal_poll.gif" alt="" align="middle" /> ' . $txt['poll'] : '') . '
			</p>';
	}
	echo '
			<script type="text/javascript"><!-- // --><![CDATA[
				if (typeof(window.XMLHttpRequest) != "undefined")
					aJumpTo[aJumpTo.length] = new JumpTo({
						sContainerId: "message_index_jump_to",
						sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">', $context['jump_to']['label'], ':<" + "/label> %dropdown_list%",
						iCurBoardId: ', $context['current_board'], ',
						iCurBoardChildLevel: ', $context['jump_to']['child_level'], ',
						sCurBoardName: "', $context['jump_to']['board_name'], '",
						sBoardChildLevelIndicator: "==",
						sBoardPrefix: "=> ",
						sCatSeparator: "-----------------------------",
						sCatPrefix: "",
						sGoButtonLabel: "', $txt['quick_mod_go'], '"
					});
			// ]]></script>
			<br class="clear" />
		</div>
	</div>';

	// Javascript for inline editing.
	echo '
<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/topic.js"></script>
<script type="text/javascript"><!-- // --><![CDATA[

	// Hide certain bits during topic edit.
	hide_prefixes.push("lockicon", "stickyicon", "pages", "newicon");

	// Use it to detect when we\'ve stopped editing.
	document.onclick = modify_topic_click;

	var mouse_on_div;
	function modify_topic_click()
	{
		if (in_edit_mode == 1 && mouse_on_div == 0)
			modify_topic_save("', $context['session_id'], '", "', $context['session_var'], '");
	}

	function modify_topic_keypress(oEvent)
	{
		if (typeof(oEvent.keyCode) != "undefined" && oEvent.keyCode == 13)
		{
			modify_topic_save("', $context['session_id'], '", "', $context['session_var'], '");
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
		setInnerHTML(cur_subject_div, \'<input type="text" name="subject" value="\' + subject + \'" size="60" style="width: 95%;" maxlength="80" onkeypress="modify_topic_keypress(event)" class="input_text" /><input type="hidden" name="topic" value="\' + cur_topic_id + \'" /><input type="hidden" name="msg" value="\' + cur_msg_id.substr(4) + \'" />\');
	}

	// And the reverse for hiding it.
	function modify_topic_hide_edit(subject)
	{
		// Re-template the subject!
		setInnerHTML(cur_subject_div, \'<a href="', $scripturl, '?topic=\' + cur_topic_id + \'.0">\' + subject + \'<\' +\'/a>\');
	}

// ]]></script>';
}

?>