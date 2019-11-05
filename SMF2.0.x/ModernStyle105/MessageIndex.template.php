<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $topic, $board_info;

	echo '
	<a id="top"></a>';

	if (!empty($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		echo '
	<div class="cat_bar">
			<h3 class="titlebg">', $txt['parent_boards'], '</h3>
	</div>
	<ul class="toplist horiz_list greytext">';

		foreach ($context['boards'] as $board)
		{
			echo '
		<li>
			<a href="', $board['href'], '" name="b', $board['id'], '">';
			// If the board or children is new, show an indicator.
			if (!$board['new'] || $board['children_new'])
				echo '
				<span class="icon-star" title="', $txt['new_posts'], '"></span>';
			echo '
				', $board['name'], '</a>';

			// Has it outstanding posts for approval?
			if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
				echo '
				<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link"> <span class="icon-info"></span></a>';

			echo '
		</li>';
		}
		echo '
	</ul><br><br>';
	}

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
	<div id="forumposts" style="clear: both; ">
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="floatleft" style="margin-right: 1.5em;">', $board_info['name'], '</span>
				<span class="floatleft righttext">',  template_button_strip($normal_buttons, 'left', array(),true,true), '</span>
			</h3>
		</div>';

		if (!empty($options['show_board_desc']) && $context['description'] != '')
			echo '
		<p class="description_board">', $context['description'], '</p><hr>';

		echo '	
		<div class="themepadding greytext">
			<span class="pagelinks">', convertpages($context['page_index']), ' <a href="#lastPost" title="' . $txt['go_down'] . '">' , substr($txt['bottom'],0,1) , '</a></span>
		</div>';


		// If Quick Moderation is enabled start the form.
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] > 0 && !empty($context['topics']))
			echo '
		<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" class="clear" name="quickModForm" id="quickModForm">';

		echo '
			<div class="bwgrid" id="messageindex"><br>';

		// Are there actually any topics to show?
		if (empty($context['topics']))
			echo '
				<div class="bwcell16"><strong>', $txt['msg_alert_none'], '</strong></div>';

		if (!empty($settings['display_who_viewing']))
		{
			echo '
				<p id="whoisviewing" class="themepadding greytext">';

			// Show just numbers...?
			if ($settings['display_who_viewing'] == 1)
					echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
			// Or show the actual people viewing the topic?
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) || $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');

			// Now show how many guests are here too.
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_topic'], '
				</p>';
		}

		// If this person can approve items and we have some awaiting approval tell them.
		if (!empty($context['unapproved_posts_message']))
		{
			echo '
				<p class="themepadding"><hr>
					<span class="alert">!</span> ', $context['unapproved_posts_message'], '
				</p>';
		}
		foreach ($context['topics'] as $topic)
		{
			echo '
				<div class="bwcell10">
					<div><span class="reveal50">';

			if ($context['can_approve_posts'] && $topic['unapproved_posts'])
				echo '<span class="icon-check opac50" title="' , $txt['awaiting_approval'] , '"></span>&nbsp;&nbsp;';
			if ($topic['is_sticky'])
				echo '<span class="icon-info opac50" title="' , $txt['sticky_topic'] , '"></span>&nbsp;&nbsp;';
			if ($topic['is_locked'])
				echo '<span class="icon-lock opac50" title="' , $txt['locked_topic'] , '"></span>&nbsp;&nbsp;';
			if ($topic['new'] && $context['user']['is_logged'])
					echo '
						<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><span title="', $txt['new'], '" class="icon-star opac50"></span></a>&nbsp;&nbsp;';
						
			echo		'</span>', $topic['first_post']['link'],'
						<span class="greytext smalltext">', $txt['started_by'], ' ', $topic['first_post']['member']['link'], '</span>
						<small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>
					</div>
				</div>
				<div class="bwcell6"><div class="bwfloatright greytext smalltext">
					<a href="', $topic['last_post']['href'], '">', $topic['last_post']['time'], '</a> | 
					', $topic['last_post']['member']['link'], '';

			// Show the quick moderation options?
			if (!empty($context['can_quick_mod']))
			{
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

					if ($topic['quick_mod']['sticky'])
						echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions[', $topic['id'], ']=sticky;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['quickmod_confirm'], '\');"><img src="', $settings['images_url'], '/icons/quick_sticky.gif" width="16" alt="', $txt['set_sticky'], '" title="', $txt['set_sticky'], '" /></a>';

					if ($topic['quick_mod']['move'])
						echo '<a href="', $scripturl, '?action=movetopic;board=', $context['current_board'], '.', $context['start'], ';topic=', $topic['id'], '.0"><img src="', $settings['images_url'], '/icons/quick_move.gif" width="16" alt="', $txt['move_topic'], '" title="', $txt['move_topic'], '" /></a>';
				}
			}
			echo '
				</div></div><br style="clear: both;" /><hr  style="opacity: 0.5; margin: 0.75rem 0;" />';
		}
		echo '
				<div class="bwgrid">
					<div class="bwcell8 smalltext greytext">';
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
		{
			echo '
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
					<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" class="button_submit qaction" />';
		}

		echo '
				</div>
				<div class="bwcell16"><div class="bwfloatright smalltext greytext">';
		// Are there actually any topics to show?
		if (!empty($context['topics']))
		{
			echo '
					<div class="themepadding">
						<a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=starter', $context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['started_by'], $context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>
						| <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=views', $context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['views'], $context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>';
			// Show a "select all" box for quick moderation?
			if (empty($context['can_quick_mod']))
				echo '
						| <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>';
			else
				echo '
						| <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a>';

			// Show a "select all" box for quick moderation?
			if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1)
				echo '
						| <input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check" />';

			echo '
					</div>';
		}
		echo '&nbsp;
				</div></div>
			</div></div>';

		// Finish off the form - again.
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] > 0 && !empty($context['topics']))
			echo '
			<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
		</form>';
		
		echo '
	</div>
	<a id="bot"></a>
	<br class="clear" />';
	}
}


?>