<?php
// Version: 1.1; MessageIndex

function template_main()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	if (isset($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		echo '
	<div class="category">
		<h1>', $context['page_title'], '</h1>';

		// Assuming the category hasn't been collapsed...
			foreach ($context['boards'] as $board)
			{
			echo '
		<div class="boards">';

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
		echo '
	</div>';
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
	<div class="viewbutton" style="background: #f0f5f6; overflow: hidden; margin-top: 0;">
			<div>', $txt[139], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#lastPost"><b>' . $txt['topbottom5'] . '</b></a>' : '', '
			</div>
		</div>
<div class="modbutton" style="text-align: right;">', template_button_strip_mill($normal_buttons, 'bottom'), '</div>';

	if (!empty($options['show_board_desc']) && $context['description'] != '')
	{
		echo '
					<div class="orangetext">', $context['description'], '</div>';
	}

		// If Quick Moderation is enabled start the form.
		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
			echo '
	<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;">';


		if (!empty($settings['display_who_viewing']))
		{
			echo '
<div class="viewbutton"><small>';
			if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt[19];
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt[19] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'], '
</small></div>';
		}

		// Are there actually any topics to show?
		if (!empty($context['topics']))
		{
			echo '
		<div class="bigblue" style="margin-bottom: 1ex;"><span class="smalltext">';

		// Show a "select all" box for quick moderation?
		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1)
				echo ' <input style="float: right;" type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="check" />';

		echo '
			<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[70], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> |
			<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=starter', $context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[109], $context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> |
			<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[110], $context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> |
			<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=views', $context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[301], $context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> |
			<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt[111], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>
		';

			echo '</span>
		</div>';
		}
		// No topics.... just say, "sorry bub".
		else
			echo '
		<div class="catbg3"><b>', $txt[151], '</b></div>';

		foreach ($context['topics'] as $topic)
		{

			echo '
<div style="padding: 4px; margin-bottom: 2px; border-bottom: dotted 1px #d0d0d0; ';
			if($topic['is_sticky'])
				echo 'background: #f0f0f0;';
			if($topic['is_locked'])
				echo 'background: #fff0f0;';
			echo '">';

			// Show the quick moderation options?
			if (!empty($options['display_quick_mod']))
			{
				if ($options['display_quick_mod'] == 1)
					echo '
								<input type="checkbox" name="topics[]" style="margin-right: 5px; float: right" value="', $topic['id'], '" class="check" />';
				else
				{
					// Check permissions on each and show only the ones they are allowed to use.
					if ($topic['quick_mod']['remove'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=remove;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_remove.gif" style="margin-right: 1px; float: right"  alt="', $txt[63], '" title="', $txt[63], '" /></a>';

					if ($topic['quick_mod']['lock'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=lock;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_lock.gif" style="margin-right: 1px; float: right" alt="', $txt['smf279'], '" title="', $txt['smf279'], '" /></a>';


					if ($topic['quick_mod']['sticky'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=sticky;sesc=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_sticky.gif" style="margin-right: 1px; float: right" alt="', $txt['smf277'], '" title="', $txt['smf277'], '" /></a>';

					if ($topic['quick_mod']['move'])
						echo '<a href="', $scripturl, '?action=movetopic;board=', $context['current_board'], '.', $context['start'], ';topic=', $topic['id'], '.0"><img src="', $settings['images_url'], '/icons/quick_move.gif" style="margin-right: 1px; float: right"  alt="', $txt[132], '" title="', $txt[132], '" /></a>';
				}
			}
			echo '
		<img src="', $settings['images_url'], '/topic/', $topic['class'], '.gif" style="margin-right: 5px;" align="right" alt="" />
		<img src="', $topic['first_post']['icon_url'], '" style="margin-right: 5px;" align="right" alt="" />';

			echo $topic['is_sticky'] ? '<b>' : '' , '<span id="msg_' . $topic['first_post']['id'] . '">', $topic['first_post']['link'], '</span>', $topic['is_sticky'] ? '</b>' : '';

			// Is this topic new? (assuming they are logged in!)
			if ($topic['new'] && $context['user']['is_logged'])
					echo '
							<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><img src="', $settings['images_url'], '/', $context['user']['language'], '/new.gif" alt="', $txt[302], '" /></a>';

			echo '
							<small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>
						',$txt[525],' ', $topic['first_post']['member']['link'], '<br />
							<span class="smalltext">', $topic['replies'], ' ' ,$txt[110], ' |	', $topic['views'], ' ',$txt[301],' |
							<a href="', $topic['last_post']['href'], '">', $txt[111], '</a>

								', $topic['last_post']['time'], '
								', $txt[525], ' ', $topic['last_post']['member']['link'], '
							</span>';

			echo '
</div>
	';
		}

		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
		{
			echo '
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
							<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" />';
		}

		echo '
			<a name="bot"></a>';

			// Finish off the form - again.
		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
				echo '
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
	</form>';

		echo '
	<div class="viewbutton" style="background: #f0f5f6; overflow: hidden; margin-top: 1ex;">
			<div>', $txt[139], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#lastPost"><b>' . $txt['topbottom5'] . '</b></a>' : '', '
			</div>
		</div>
					<div class="modbutton" style="text-align: right;">
						', template_button_strip_mill($normal_buttons, 'top'), '
					</div>';
	}



	echo '
					<form action="', $scripturl, '" method="get" name="jumptoForm">
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
					</form>';
 echo '<p></p>';
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
