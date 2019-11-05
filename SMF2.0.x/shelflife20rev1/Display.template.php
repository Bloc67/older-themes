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
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $sourcedir;
	
	// Let them know, if their report was a success!
	if ($context['report_sent'])
	{
		echo '
	<div class="information" id="profile_success">
		<strong>', $txt['report_sent'], '</strong>
	</div>';
	}

	// Show the anchor for the top and for the first message. If the first message is new, say so.
	echo '
	<a id="top"></a>
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '';

	// Is this topic also a poll?
	if ($context['is_poll'])
	{
		echo '
	<div id="poll">
		<div class="catbg">
			<h3>', $txt['poll'], '</h3>
			<p class="subtitle">', $context['poll']['question'], '</p>
		</div>
		<div class="windowbg">	';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
			<div class="bwgrid">
				<div class="bwcell4 right_align">
					<span class="smalltext">' , $option['option'] , '</span>
				</div>
				<div class="bwcell8">';

				if ($context['allow_poll_view'])
					echo '
					<div class="statsbar' , $option['voted_this'] ? ' myvote' : '' , '"><div style="width: ', $option['percent'], '%;"></div></div>';
				
				echo '
				</div>
				<div class="bwcell4">';
				
				if ($context['allow_poll_view'])
					echo '
					<span class="smalltext">', $option['votes'], ' (', $option['percent'], '%)</span>';

				echo '
				</div>
			</div>';
			}
			if ($context['allow_poll_view'])
				echo '<hr>
			<p>', $txt['poll_total_voters'], ' <span class="dark_notice wide_notice" style="margin-top: -5px;">', $context['poll']['total_votes'], '</span></p>';
		}
		// They are allowed to vote! Go to it!
		else
		{
			echo '
			<form action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';

			// Show a warning if they are allowed more than one option.
			if ($context['poll']['allowed_warning'])
				echo '
				<p class="smallpadding">', $context['poll']['allowed_warning'], '</p>';

			echo '
				<ul class="nolist">';

			// Show each option with its button - a radio likely.
			foreach ($context['poll']['options'] as $option)
				echo '
					<li>', $option['vote_button'], ' <label for="', $option['id'], '">', $option['option'], '</label></li>';

			echo '
				</ul>
				<hr>
				<input type="submit" value="', $txt['poll_vote'], '" class="button_submit" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			</form>';
		}

		// Is the clock ticking?
		if (!empty($context['poll']['expire_time']))
			echo '
			<p><strong>', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ':</strong> ', $context['poll']['expire_time'], '</p>';

		echo '
		</div>
	</div>
	<div id="pollmoderation" class="themepadding">';

		// Build the poll moderation button array.
		$poll_buttons = array(
			'vote' => array('test' => 'allow_return_vote', 'text' => 'poll_return_vote', 'image' => 'poll_options.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start']),
			'results' => array('test' => 'show_view_results_button', 'text' => 'poll_results', 'image' => 'poll_results.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start'] . ';viewresults'),
			'change_vote' => array('test' => 'allow_change_vote', 'text' => 'poll_change_vote', 'image' => 'poll_change_vote.gif', 'lang' => true, 'url' => $scripturl . '?action=vote;topic=' . $context['current_topic'] . '.' . $context['start'] . ';poll=' . $context['poll']['id'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'lock' => array('test' => 'allow_lock_poll', 'text' => (!$context['poll']['is_locked'] ? 'poll_lock' : 'poll_unlock'), 'image' => 'poll_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lockvoting;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'edit' => array('test' => 'allow_edit_poll', 'text' => 'poll_edit', 'image' => 'poll_edit.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;topic=' . $context['current_topic'] . '.' . $context['start']),
			'remove_poll' => array('test' => 'can_remove_poll', 'text' => 'poll_remove', 'image' => 'admin_remove_poll.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['poll_remove_warn'] . '\');"', 'url' => $scripturl . '?action=removepoll;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		);

		template_button_strip($poll_buttons);

		echo '
	</div>';
	}

	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
	<div class="linked_events">
		<div class="title_bar">
			<h3 class="titlebg">', $txt['calendar_linked_events'], '</h3>
		</div>
		<div class="windowbg">
			<ul class="nolist">';

		foreach ($context['linked_calendar_events'] as $event)
			echo '
				<li>
					', ($event['can_edit'] ? '<a href="' . $event['modify_href'] . '"> <span class="icon-calendar" title="' . $txt['modify'] . '"></span></a> ' : ''), '<strong>', $event['title'], '</strong>: ', $event['start_date'], ($event['start_date'] != $event['end_date'] ? ' - ' . $event['end_date'] : ''), '
				</li>';

		echo '
			</ul>
		</div>
	</div>';
	}

	// Build the normal button array.
	$normal_buttons = array(
		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';last_msg=' . $context['topic_last_message'], 'active' => true),
		'add_poll' => array('test' => 'can_add_poll', 'text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start']),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : '') . 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_topic'] : $txt['notification_enable_topic']) . '\');"', 'url' => $scripturl . '?action=notify;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'mark_unread' => array('test' => 'can_mark_unread', 'text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'send' => array('test' => 'can_send_topic', 'text' => 'send_topic', 'image' => 'sendtopic.gif', 'lang' => true, 'url' => $scripturl . '?action=emailuser;sa=sendtopic;topic=' . $context['current_topic'] . '.0'),
		'print' => array('text' => 'print', 'image' => 'print.gif', 'lang' => true, 'custom' => 'rel="new_win nofollow"', 'url' => $scripturl . '?action=printpage;topic=' . $context['current_topic'] . '.0'),
	);

	// Allow adding new buttons easily.
	call_integration_hook('integrate_display_buttons', array(&$normal_buttons));

	// Show the page index... "Pages: [1]".
	echo '
	<div class="pagesection themepadding">
		<div class="bwgrid">
			<div class="bwcell4">
				<div class="pagelinks">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#lastPost"><strong>' . $txt['go_down'] . '</strong></a>' : '', '</div>
				<div class="clear floatleft">', $context['previous_next'], '</div>
			</div>
			<div class="bwcell12">
				', template_button_strip($normal_buttons,'right'), '
			</div>
		</div>
	</div>';

	// Show the topic information - icon, subject, etc.
	echo '
	<div class="catbg">
		<h3 id="top_subject">', $context['subject'], ' <span class="dark_notice wide_notice">', $context['num_views'], '</span></h3>
	</div>';

	if (!empty($settings['display_who_viewing']))
	{
		echo '
	<div id="whoisviewing" class="themepadding">';

		// Show just numbers...?
		if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
		// Or show the actual people viewing the topic?
		else
			echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) || $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');

		// Now show how many guests are here too.
		echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_topic'], '
	</div><hr>';
	}

	echo '
	<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" class="themepadding" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	$ignoredMsgs = array();
	$removableMessageIDs = array();
	$alternate = false;

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		$ignoring = false;
		$alternate = !$alternate;
		if ($message['can_remove'])
			$removableMessageIDs[] = $message['id'];

		// Are we ignoring this message?
		if (!empty($message['is_ignored']))
		{
			$ignoring = true;
			$ignoredMsgs[] = $message['id'];
		}

		// Show the message anchor and a "new" anchor if this message is new.
		if ($message['id'] != $context['first_message'])
			echo '
		<a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '';

		echo '
		<div id="avvy_extra' . $message['id'] . '" class="avvy_closed" style="overflow: hidden; clear: both;">
		
	<div> 
		<div class="arrow_style">
			<div style="padding: 1rem 2rem;">';
	
	if (!empty($message['member']['title']))
		echo '
			<span class="bigbutton">', $message['member']['title'], '</span>';

	if (!empty($message['member']['group']))
		echo '
			<span class="bigbutton"><strong>', $message['member']['group'], '</strong></span>';

	if (!$message['member']['is_guest'])
	{
		if ((empty($settings['hide_post_group']) || $message['member']['group'] == '') && $message['member']['post_group'] != '')
			echo '
			<span class="bigbutton">', $message['member']['post_group'], '</span>';
		

		if (!isset($context['disabled_fields']['posts']))
			echo '
			<span class="bigbutton">', $txt['member_postcount'], ': ', $message['member']['posts'], '</span>';

		if ($modSettings['karmaMode'] == '1')
			echo '
			<span class="bigbutton">', $modSettings['karmaLabel'], ' ', $message['member']['karma']['good'] - $message['member']['karma']['bad'], '</span>';
		elseif ($modSettings['karmaMode'] == '2')
			echo '
			<span class="bigbutton">', $modSettings['karmaLabel'], ' +', $message['member']['karma']['good'], '/-', $message['member']['karma']['bad'], '</span>';

		if ($message['member']['karma']['allow'])
			echo '
			<span class="bigbutton">
				<a href="', $scripturl, '?action=modifykarma;sa=applaud;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.' . $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $modSettings['karmaApplaudLabel'], '</a>
				<a href="', $scripturl, '?action=modifykarma;sa=smite;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $modSettings['karmaSmiteLabel'], '</a>
			</span>';

		if (!empty($settings['show_gender']) && $message['member']['gender']['image'] != '' && !isset($context['disabled_fields']['gender']))
			echo '
			<span class="bigbutton" title="', $txt['gender'], '">', $message['member']['gender']['image'], '</span>';

		if (!empty($settings['show_blurb']) && $message['member']['blurb'] != '')
			echo '
			<span class="bigbutton">', $message['member']['blurb'], '</span>';

		if (!empty($message['member']['custom_fields']))
		{
			$shown = false;
			foreach ($message['member']['custom_fields'] as $custom)
			{
				if ($custom['placement'] != 1 || empty($custom['value']))
					continue;

				echo '
			<span class="bigbutton">', $custom['value'], '</span>';
			}
		}
		echo '
			<span class="bigbutton">', $message['member']['group_stars'], '</span>
			<div class="big_sign">';
		if ($message['member']['has_messenger'] && $message['member']['can_view_profile'])
			echo '
			', !empty($message['member']['icq']['link']) ? '<a href="' . $message['member']['icq']['href'] . '"><span class="icon-twitch"></span></a>' : '', '
			', !empty($message['member']['msn']['link']) ? '<a href="' . $message['member']['msn']['href'] . '"><span class="icon-windows"></span></a>' : '', '
			', !empty($message['member']['aim']['link']) ? '<a href="' . $message['member']['aim']['href'] . '"><span class="icon-twitch"></span></a>' : '', '
			', !empty($message['member']['yim']['link']) ? '<a href="' . $message['member']['yim']['href'] . '"><span class="icon-yahoo"></span></a>' : '';

		if ($settings['show_profile_buttons'])
		{
			if ($message['member']['can_view_profile'])
				echo '
			<a href="', $message['member']['href'], '"><span class="icon-user-tie"></span></a>';

			if ($message['member']['website']['url'] != '' && !isset($context['disabled_fields']['website']))
				echo '
			<a href="', $message['member']['website']['url'], '" title="' . $message['member']['website']['title'] . '" target="_blank" class="new_win"><span class="icon-earth"></span></a>';

			if (in_array($message['member']['show_email'], array('yes', 'yes_permission_override', 'no_through_forum')))
				echo '
			<a href="', $scripturl, '?action=emailuser;sa=email;msg=', $message['id'], '" rel="nofollow"><span class="icon-envelop"></span></a>';

			if ($context['can_send_pm'])
				echo '
			<a href="', $scripturl, '?action=pm;sa=send;u=', $message['member']['id'], '" title="', $message['member']['online']['is_online'] ? $txt['pm_online'] : $txt['pm_offline'], '"><span class="icon-bubble2"></span></a>';
			echo '
			</div>';
		}
		// Any custom fields for standard placement?
		if (!empty($message['member']['custom_fields']))
		{
			foreach ($message['member']['custom_fields'] as $custom)
				if (empty($custom['placement']) || empty($custom['value']))
					echo '
			<span class="icon-twitch" title="', $custom['title'], ': ', $custom['value'], '"></span>';
		}

		// Are we showing the warning status?
		if ($message['member']['can_see_warning'])
			echo '
			', $context['can_issue_warning'] ? '<a href="' . $scripturl . '?action=profile;area=issuewarning;u=' . $message['member']['id'] . '">' : '', '<span class="icon-warning"></span>', $context['can_issue_warning'] ? '</a>' : '';

	}
	// Otherwise, show the guest's email.
	elseif (!empty($message['member']['email']) && in_array($message['member']['show_email'], array('yes', 'yes_permission_override', 'no_through_forum')))
		echo '
			<a href="', $scripturl, '?action=emailuser;sa=email;msg=', $message['id'], '" rel="nofollow"><span class="icon-envelop"></span></a>';

	echo '
			</div>
		</div>
	</div>	
		
		
		
		</div>
		<div class="bwgrid">
			<div class="bwcell2 center_align">
				<h4 class="largetext">', $message['member']['link'];

		// Show online and offline buttons?
		if (!empty($modSettings['onlineEnable']) && !$message['member']['is_guest'])
			echo '
					', $context['can_send_pm'] ? '<a href="' . $message['member']['online']['href'] . '" title="' . $message['member']['online']['label'] . '">' : '', '<span class="', $message['member']['online']['is_online'] ? 'onoff_on' : 'onoff_off' , '" style="position: absolute;" title="', $message['member']['online']['text'], '"></span>', $context['can_send_pm'] ? '</a>' : '';

		echo '</h4>';

		// Show avatars, images, etc.?
		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($message['member']['avatar']['image']))
				echo '
				<span onclick="showExtra(\'avvy_extra'.$message['id'].'\');" class="roundavatar_full" style="cursor: pointer;"><img src="', $message['member']['avatar']['href'], '" alt="*" /></span>';
		else
				echo '
				<span onclick="showExtra(\'avvy_extra'.$message['id'].'\');"  class="roundavatar_full" style="cursor: pointer;"><img src="', $settings['images_url'], '/guest.png" alt="*" /></span>';
		

		// Are we showing the warning status?
		if (!$message['member']['is_guest'] && $message['member']['can_see_warning'])
			echo '
				<div class="plainbox">', $context['can_issue_warning'] ? '<a href="' . $scripturl . '?action=profile;area=issuewarning;u=' . $message['member']['id'] . '">' : '', '<img src="', $settings['images_url'], '/warning_', $message['member']['warning_status'], '.gif" alt="', $txt['user_warn_' . $message['member']['warning_status']], '" />', $context['can_issue_warning'] ? '</a>' : '', '<span class="warn_', $message['member']['warning_status'], '">', $txt['warn_' . $message['member']['warning_status']], '</span></div>';

		// Done with the information about the poster... on to the post itself.
		echo '
			</div>
			<div class="bwcell14">
				<div class="post_container">
					<div class="floatright">
						<div class="mess_icons_hit">
							<a href="' , $scripturl  , '?action=post;msg=' . $message['id'] . ';topic=' . $context['current_topic'] . '#icon">
								<img src="', $settings['images_url'] , '/post/' , $message['icon'] . '.png" id="messHit' . $message['id'] . '" alt="*" />
							</a>
						</div>
					</div>
					<div class="post_top">
						<a href="', $message['href'], '" class="largetext" rel="nofollow"  id="subject_', $message['id'], '">', $message['subject'], '</a>
						<div class="smalltext">&#171; <strong>', !empty($message['counter']) ? $txt['reply_noun'] . ' #' . $message['counter'] : '', ' ', $txt['on'], ':</strong> ', $message['time'], ' &#187;</div>';


		// Ignoring this user? Hide the post.
		if ($ignoring)
			echo '
						<div id="msg_', $message['id'], '_ignored_prompt" class="plainbox">
							', $txt['ignoring_user'], '
							<a href="#" id="msg_', $message['id'], '_ignored_link" style="display: none;">', $txt['show_ignore_user_post'], '</a>
						</div>';

		echo '
					</div>
					<div class="post" style="overflow: auto;">';

		if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
			echo '
						<div class="plainbox">', $txt['post_awaiting_approval'], '</div>';
		echo '
						<div class="inner" id="msg_', $message['id'], '"', '>', $message['body'], '</div>
					</div>';

		// If this is the first post, (#0) just say when it was posted - otherwise give the reply #.
		if ($message['can_approve'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
					<ul class="horiz_list quickbuttons">';

		// Maybe we can approve it, maybe we should?
		if ($message['can_approve'])
			echo '
						<li class="approve_button"><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a></li>';

		// Can they reply? Have they turned on quick reply?
		if ($context['can_quote'] && !empty($options['display_quick_reply']))
			echo '
						<li class="quote_button"><a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '" onclick="return oQuickReply.quote(', $message['id'], ');">', $txt['quote'], '</a></li>';

		// So... quick reply is off, but they *can* reply?
		elseif ($context['can_quote'])
			echo '
						<li class="quote_button"><a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '">', $txt['quote'], '</a></li>';

		// Can the user modify the contents of this post?
		if ($message['can_modify'])
			echo '
						<li class="modify_button"><a href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], '">', $txt['modify'], '</a></li>';

		// How about... even... remove it entirely?!
		if ($message['can_remove'])
			echo '
						<li class="remove_button"><a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['remove_message'], '?\');">', $txt['remove'], '</a></li>';

		// What about splitting it off the rest of the topic?
		if ($context['can_split'] && !empty($context['real_num_replies']))
			echo '
						<li class="split_button"><a href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '">', $txt['split'], '</a></li>';

		// Can we restore topics?
		if ($context['can_restore_msg'])
			echo '
						<li class="restore_button"><a href="', $scripturl, '?action=restoretopic;msgs=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['restore_message'], '</a></li>';

		if ($message['can_approve'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
					</ul>';

		// Can the user modify the contents of this post?  Show the modify inline image.
		if ($message['can_modify'])
			echo '
					<img class="floatright" src="' , $settings['images_url'] , '/pencil.png" id="modify_button_', $message['id'], '" alt="E" style="cursor: pointer; width: 20px;" onclick="oQuickModify.modifyMsg(\'', $message['id'], '\')">';

		// Show a checkbox for quick moderation?
		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $message['can_remove'])
			echo '
					&nbsp;| <span class="inline_mod_check" style="display: none;position: absolute; margin: 2px 0 0 1rem; " id="in_topic_mod_check_', $message['id'], '"></span>';

		// Assuming there are attachments...
		if (!empty($message['attachment']))
		{
			echo '
					<div id="msg_', $message['id'], '_footer" class="post smalltext wpost">
						<div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';

			$last_approved_state = 1;
			foreach ($message['attachment'] as $attachment)
			{
				// Show a special box for unapproved attachments...
				if ($attachment['is_approved'] != $last_approved_state)
				{
					$last_approved_state = 0;
					echo '
							<fieldset>
								<legend>', $txt['attach_awaiting_approve'];

					if ($context['can_approve'])
						echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

					echo '
								</legend>';
				}
				$ext = substr($attachment['name'], strlen($attachment['name'])-3,3);
				if ($attachment['is_image'])
				{
					if ($attachment['thumbnail']['has_thumb'])
						echo '
								<a href="#att', $attachment['id'], '" class="bwcell33" style="float: left; display: block; overflow: hidden;margin-bottom: 1px;height: 200px;">
									<span class="smalltext narrowcaps">', $attachment['size'], ($attachment['is_image'] ? '<br>' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' | ' : '' ), $attachment['downloads'] . ' ' . $txt['downloads'] . '</span> 
									<img src="', $attachment['thumbnail']['href'] , '" alt="*" id="thumb_', $attachment['id'], '" />
								</a>
								<a href="#_" id="att', $attachment['id'], '" class="lightbox2"><span>x</span><img src="', $attachment['href'], '" alt="*" style="max-width: 100%;max-height: 100%;"></a>';
					else
						echo '
								<span class="bwcell33" style="float: left; display: block; overflow: hidden;margin-bottom: 1px;height: 200px;">
									<span class="smalltext narrowcaps">', $attachment['size'], ($attachment['is_image'] ? ' | ' . $attachment['real_width'] . 'x' . $attachment['real_height']  : ' | '  . $attachment['downloads'].  ' ' . $txt['downloads']) . '</span>
									<img src="' . $attachment['href'] . ';image" alt="*" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/>
								</span>';
				}
				else
					echo '
								<a href="' . $attachment['href'] . '" class="bwcell33" style="float: left; display: block; overflow: hidden;margin-bottom: 1px;height: 200px;">
									<span class="smalltext narrowcaps">' . $attachment['name'] . '<br>', $attachment['size'], $attachment['is_image'] ? ' | ' . $attachment['real_width'] . 'x' . $attachment['real_height'] : ' | ' .  $attachment['downloads'] . '  ' . $txt['downloads'] . '</span>
									<img src="' . $settings['images_url'] . '/filetypes/' , $ext , '.png" style="vertical-align: middle;" alt="*" />
								</a> ';

				if (!$attachment['is_approved'] && $context['can_approve'])
					echo '
								[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
				
			}

			// If we had unapproved attachments clean up.
			if ($last_approved_state == 0)
				echo '
							</fieldset>';

			echo '
						</div>
					</div>
			';
		}

		echo '
					<div class="clear description_board narrow">
						<div id="modified_', $message['id'], '">';
		// Show "� Last Edit: Time by Person �" if this post was edited.
		if ($settings['show_modify'] && !empty($message['modified']['name']))
			echo '
							&#171; <em>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</em> &#187;';

		echo '
						</div>
						<div class="reportlinks">';

		// Maybe they want to report this post to the moderator(s)?
		if ($context['can_report_moderator'])
			echo '
							<a href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $message['counter'], ';msg=', $message['id'], '">', $txt['report_to_mod'], '</a> &nbsp;';

		// Can we issue a warning because of this post?  Remember, we can't give guests warnings.
		if ($context['can_issue_warning'] && !$message['is_message_author'] && !$message['member']['is_guest'])
			echo '
							<a href="', $scripturl, '?action=profile;area=issuewarning;u=', $message['member']['id'], ';msg=', $message['id'], '"><span class="icon-warning" style="color: red;font-size: 90%; " title="', $txt['issue_warning_post'], '"></span></a> ';
		echo '
							<span class="icon-connection" style="font-size: 80%;"></span>';

		// Show the IP to this user for this post - because you can moderate?
		if ($context['can_moderate_forum'] && !empty($message['member']['ip']))
			echo '
							<a href="', $scripturl, '?action=', !empty($message['member']['is_guest']) ? 'trackip' : 'profile;area=tracking;sa=ip;u=' . $message['member']['id'], ';searchip=', $message['member']['ip'], '">', $message['member']['ip'], '</a> <a href="', $scripturl, '?action=helpadmin;help=see_admin_ip" onclick="return reqWin(this.href);" class="help">(?)</a>';
		// Or, should we show it because this is you?
		elseif ($message['can_see_ip'])
			echo '
							<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $message['member']['ip'], '</a>';
		// Okay, are you at least logged in?  Then we can show something about why IPs are logged...
		elseif (!$context['user']['is_guest'])
			echo '
							<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $txt['logged'], '</a>';
		// Otherwise, you see NOTHING!
		else
			echo '
							', $txt['logged'];

		echo '
						</div>';

		// Are there any custom profile fields for above the signature?
		if (!empty($message['member']['custom_fields']))
		{
			$shown = false;
			foreach ($message['member']['custom_fields'] as $custom)
			{
				if ($custom['placement'] != 2 || empty($custom['value']))
					continue;
				if (empty($shown))
				{
					$shown = true;
					echo '
						<div class="custom_fields_above_signature">
							<ul class="reset nolist">';
				}
				echo '
								<li>', $custom['value'], '</li>';
			}
			if ($shown)
				echo '
							</ul>
						</div>';
		}

		// Show the member's signature?
		if (!empty($message['member']['signature']) && empty($options['show_no_signatures']) && $context['signature_enabled'])
			echo '	<hr>
						<div class="signature" id="msg_', $message['id'], '_signature">', $message['member']['signature'], '</div>';

		echo '
					</div>
				</div>
			</div>
		</div>';
	}

	echo '
	</form>
	<a id="lastPost"></a>';

	// Show the page index... "Pages: [1]".
	echo '
	<div class="themepadding"></div>
	<div class="pagesection themepadding">
		<div class="bwgrid">
			<div class="bwcell4">
				<div class="pagelinks ">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#top"><strong>' . $txt['go_up'] . '</strong></a>' : '', '</div>
			</div>
			<div class="bwcell12">
				<div class="bwfloatright">', template_button_strip($normal_buttons), '<span class="wclear">', $context['previous_next'], '</span></div>
			</div>
		</div>
	</div>';

	// Show the lower breadcrumbs.
	echo '
	<div class="themepadding wfull">' ,theme_linktree(),'</div>';

	$mod_buttons = array(
		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['are_sure_remove_topic'] . '\');"', 'url' => $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
		'lock' => array('test' => 'can_lock', 'text' => empty($context['is_locked']) ? 'set_lock' : 'set_unlock', 'image' => 'admin_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lock;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'sticky' => array('test' => 'can_sticky', 'text' => empty($context['is_sticky']) ? 'set_sticky' : 'set_nonsticky', 'image' => 'admin_sticky.gif', 'lang' => true, 'url' => $scripturl . '?action=sticky;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'merge' => array('test' => 'can_merge', 'text' => 'merge', 'image' => 'merge.gif', 'lang' => true, 'url' => $scripturl . '?action=mergetopics;board=' . $context['current_board'] . '.0;from=' . $context['current_topic']),
		'calendar' => array('test' => 'calendar_post', 'text' => 'calendar_link', 'image' => 'linktocal.gif', 'lang' => true, 'url' => $scripturl . '?action=post;calendar;msg=' . $context['topic_first_message'] . ';topic=' . $context['current_topic'] . '.0'),
	);

	// Restore topic. eh?  No monkey business.
	if ($context['can_restore_topic'])
		$mod_buttons[] = array('text' => 'restore_topic', 'image' => '', 'lang' => true, 'url' => $scripturl . '?action=restoretopic;topics=' . $context['current_topic'] . ';' . $context['session_var'] . '=' . $context['session_id']);

	// Allow adding new mod buttons easily.
	call_integration_hook('integrate_mod_buttons', array(&$mod_buttons));

	echo '
	<div id="moderationbuttons" class="themepadding wfull">', template_button_strip($mod_buttons, 'bottom', array('id' => 'moderationbuttons_strip')), '</div>';

	// Show the jumpto box, or actually...let Javascript do it.
	echo '
	<hr class="clear" />
	<div class="themepadding center_align" id="display_jump_to">&nbsp;</div>';

	if ($context['can_reply'] && !empty($options['display_quick_reply']))
	{
		echo '
	<a id="quickreply"></a>
	<div id="quickreplybox" class="clear" style="overflow: auto;">
		<div class="catbg">
			<h3>
				<a href="javascript:oQuickReply.swap();">', $txt['quick_reply'], '</a>
			</h3>
		</div>
		<div id="quickReplyOptions"', $options['display_quick_reply'] == 2 ? '' : ' style="display: none"', '>
			<div class="roundframe">
				<p class="smalltext lefttext">', $txt['quick_reply_desc'], '</p>
				', $context['is_locked'] ? '<p class="alert smalltext">' . $txt['quick_reply_warning'] . '</p>' : '',
				$context['oldTopicError'] ? '<p class="alert smalltext">' . sprintf($txt['error_old_topic'], $modSettings['oldTopicDays']) . '</p>' : '', '
				', $context['can_reply_approved'] ? '' : '<em>' . $txt['wait_for_approval'] . '</em>', '
				', !$context['can_reply_approved'] && $context['require_verification'] ? '<br />' : '', '
				<form action="', $scripturl, '?board=', $context['current_board'], ';action=post2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="submitonce(this);" style="margin: 0;">
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />
					<input type="hidden" name="subject" value="', $context['response_prefix'], $context['subject'], '" />
					<input type="hidden" name="icon" value="xx" />
					<input type="hidden" name="from_qr" value="1" />
					<input type="hidden" name="notify" value="', $context['is_marked_notify'] || !empty($options['auto_notify']) ? '1' : '0', '" />
					<input type="hidden" name="not_approved" value="', !$context['can_reply_approved'], '" />
					<input type="hidden" name="goback" value="', empty($options['return_to_post']) ? '0' : '1', '" />
					<input type="hidden" name="last_msg" value="', $context['topic_last_message'], '" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />';

			// Guests just need more.
			if ($context['user']['is_guest'])
				echo '
					<strong>', $txt['name'], ':</strong> <input type="text" name="guestname" value="', $context['name'], '" size="25" class="input_text" tabindex="', $context['tabindex']++, '" />
					<strong>', $txt['email'], ':</strong> <input type="text" name="email" value="', $context['email'], '" size="25" class="input_text" tabindex="', $context['tabindex']++, '" /><br />';

			// Is visual verification enabled?
			if ($context['require_verification'])
				echo '
					<strong>', $txt['verification'], ':</strong>', template_control_verification($context['visual_verification_id'], 'quick_reply'), '<br />';

			echo '
					<div class="quickReplyContent">
						<textarea style="width: 100%; height: 200px;" name="message" tabindex="', $context['tabindex']++, '"></textarea>
					</div>
					<div class="righttext padding">
						<input type="submit" name="post" value="', $txt['post'], '" onclick="return submitThisOnce(this);" accesskey="s" tabindex="', $context['tabindex']++, '" class="button_submit" />
						<input type="submit" name="preview" value="', $txt['preview'], '" onclick="return submitThisOnce(this);" accesskey="p" tabindex="', $context['tabindex']++, '" class="button_submit" />';

			if ($context['show_spellchecking'])
				echo '
						<input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'postmodify\', \'message\');" tabindex="', $context['tabindex']++, '" class="button_submit" />';

			echo '
					</div>
				</form>
			</div>
		</div>
	</div>';
	}
	else
		echo '
	<br class="clear" />';

	if ($context['show_spellchecking'])
		echo '
	<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>
	<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/spellcheck.js"></script>';

	echo '
	<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/topic.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[';

	if (!empty($options['display_quick_reply']))
		echo '
					var oQuickReply = new QuickReply({
						bDefaultCollapsed: ', !empty($options['display_quick_reply']) && $options['display_quick_reply'] == 2 ? 'false' : 'true', ',
						iTopicId: ', $context['current_topic'], ',
						iStart: ', $context['start'], ',
						sScriptUrl: smf_scripturl,
						sImagesUrl: "', $settings['images_url'], '",
						sContainerId: "quickReplyOptions",
						sImageId: "quickReplyExpand",
						sImageCollapsed: "collapse.gif",
						sImageExpanded: "expand.gif",
						sJumpAnchor: "quickreply"
					});';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $removableMessageIDs), '\'],
						sSessionId: \'', $context['session_id'], '\',
						sSessionVar: \'', $context['session_var'], '\',
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.gif\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.gif\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							bShowModify: ', $settings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container" style="width: 90%">
									<div id="error_box" style="padding: 4px;" class="error"></div>
									<textarea class="editor" name="message" rows="12" style="' . ($context['browser']['is_ie8'] ? 'width: 635px; max-width: 100%; min-width: 100%' : 'width: 100%') . '; margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br />
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
									<input type="hidden" name="msg" value="%msg_id%" />
									<div class="righttext">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit" />&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit" />&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit" />
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" style="width: 90%;" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text" />'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('%subject%'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%<span class="dark_notice wide_notice">'. $context['num_views']. '</span>'), ',

							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), '
						});

						aJumpTo[aJumpTo.length] = new JumpTo({
							sContainerId: "display_jump_to",
							sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">', $context['jump_to']['label'], ':<" + "/label> %dropdown_list%",
							iCurBoardId: ', $context['current_board'], ',
							iCurBoardChildLevel: ', $context['jump_to']['child_level'], ',
							sCurBoardName: "', $context['jump_to']['board_name'], '",
							sBoardChildLevelIndicator: "==",
							sBoardPrefix: "=> ",
							sCatSeparator: "-----------------------------",
							sCatPrefix: "",
							sGoButtonLabel: "', $txt['go'], '"
						});
					}';

	if (!empty($ignoredMsgs))
	{
		echo '
					var aIgnoreToggles = new Array();';

		foreach ($ignoredMsgs as $msgid)
		{
			echo '
					aIgnoreToggles[', $msgid, '] = new smc_Toggle({
						bToggleEnabled: true,
						bCurrentlyCollapsed: true,
						aSwappableContainers: [
							\'msg_', $msgid, '_extra_info\',
							\'msg_', $msgid, '\',
							\'msg_', $msgid, '_footer\',
							\'msg_', $msgid, '_quick_mod\',
							\'modify_button_', $msgid, '\',
							\'msg_', $msgid, '_signature\'

						],
						aSwapLinks: [
							{
								sId: \'msg_', $msgid, '_ignored_link\',
								msgExpanded: \'\',
								msgCollapsed: ', JavaScriptEscape($txt['show_ignore_user_post']), '
							}
						]
					});';
		}
	}

	echo '
	// ]]></script>';
}

function template_poster_extra($message)
{
	global $settings, $context, $txt, $modSettings, $scripturl;

}

?>