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
	
	// Let them know, if their report was a success!
	if ($context['report_sent'])
	{
		echo '
			<div class="windowbg" id="profile_success">
				', $txt['report_sent'], '
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
				<div class="cat_bar">
					<h3 class="catbg">
						', $txt['poll'], '
					</h3>
				</div>
				<div class="windowbg">
					<div class="content" id="poll_options">
						<h4 id="pollquestion">
							<em>', $context['poll']['question'], '</em>
						</h4><br>';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			echo '
					<div class="bwgrid">';

			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
						<div class="bwcell8"><div class="middletext', $option['voted_this'] ? ' voted' : '', '">', $option['option'], '</div></div>
						<div class="bwcell6"><div class="middletext statsbar', $option['voted_this'] ? ' voted' : '', '">';

				if ($context['allow_poll_view'])
					echo '
							<span class="bar"><span style="width: ' ,$option['percent'] , '%;"></span></span>
						</div></div>
						<div class="bwcell2"><div class="percentage smalltext">&nbsp;&nbsp;', $option['votes'], ' (', $option['percent'], '%)';

				echo '
						</div></div>';
			}

			echo '
					</div>';

			if ($context['allow_poll_view'])
				echo '
						<p><strong>', $txt['poll_total_voters'], ':</strong> ', $context['poll']['total_votes'], '</p>';
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
							<ul class="reset options">';

			// Show each option with its button - a radio likely.
			foreach ($context['poll']['options'] as $option)
				echo '
								<li class="middletext">', $option['vote_button'], ' <label for="', $option['id'], '">', $option['option'], '</label></li>';

			echo '
							</ul>
							<div class="submitbutton">
								<input type="submit" value="', $txt['poll_vote'], '" class="button_submit" />
								<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							</div>
						</form>';
		}

		// Is the clock ticking?
		if (!empty($context['poll']['expire_time']))
			echo '
						<p><strong>', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ':</strong> ', $context['poll']['expire_time'], '</p>';

		echo '
					</div>
				</div>
			</div>
			<div id="pollmoderation">';

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
			</div><br style="clear: both;"><hr>';
	}

	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
			<div class="linked_events">
				<div class="title_bar">
					<h3 class="titlebg headerpadding">', $txt['calendar_linked_events'], '</h3>
				</div>
				<div class="windowbg">
					<span class="topslice"><span></span></span>
					<div class="content">
						<ul class="reset">';

		foreach ($context['linked_calendar_events'] as $event)
			echo '
							<li>
								', ($event['can_edit'] ? '<a href="' . $event['modify_href'] . '"> <img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="" title="' . $txt['modify'] . '" class="edit_event" /></a> ' : ''), '<strong>', $event['title'], '</strong>: ', $event['start_date'], ($event['start_date'] != $event['end_date'] ? ' - ' . $event['end_date'] : ''), '
							</li>';

		echo '
						</ul>
					</div>
				</div>
			</div><br class="clear" /><hr>';
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
	// Show the topic information - icon, subject, etc.
	echo '
			<div id="forumposts" style="clear: both; " >
				<div class="cat_bar">
					<h3 class="catbg">
						<span class="floatleft" style="margin-right: 3em;">', $context['subject'], ' &nbsp;(', $txt['read'], ' ', $context['num_views'], ' ', $txt['times'], ')</span>
						<span class="floatleft righttext">',  template_button_strip($normal_buttons, 'left',array(),true,true), '</span>
					</h3>
				</div>
				<div class="themepadding greytext clear">
					<span class="pagelinks">', convertpages($context['page_index']), ' <a href="#lastPost" title="' . $txt['go_down'] . '">' , substr($txt['bottom'],0,1) , '</a></span>
				</div><br><br>
				<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="padding : 1em 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	$removableMessageIDs = array();
	$alternate = false;

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if ($message['can_remove'])
			$removableMessageIDs[] = $message['id'];

		// Show the message anchor and a "new" anchor if this message is new.
		if ($message['id'] != $context['first_message'])
			echo '
				<a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '';

		echo '
				<div class="bwgrid', !$message['approved'] ? ' approve' : '', '">
					<div class="bwcell2">';

		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($message['member']['avatar']['href']))
			echo '	
						<span class="ravatar" style="background-image: url(', $message['member']['avatar']['href'], ');">';
		else
			echo '	<span class="ravatar">';

		
		echo '
						</span>';
		// Show online and offline buttons?
		if (!empty($modSettings['onlineEnable']) && !$message['member']['is_guest'])
			echo !empty($message['member']['online']['is_online']) ? '<p>&nbsp;<span class="icon-star"></span>&nbsp;<span class="toplist smalltext">'. $message['member']['online']['label'] .'</span></p>' : '';

		// Done with the information about the poster... on to the post itself.
		echo '
				</div>
				<div class="bwcell14">
					<h4 class="titlebg" id="subject_', $message['id'], '">
						<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">' , $message['member']['name'] , '</a>
						&nbsp;<span class="smalltext greytext">', $message['time'], ' </span>
					</h4>
					<div id="msg_', $message['id'], '_quick_mod"></div>
					<div class="post">';

		if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
			echo '
						<div class="approve_post">
							', $txt['post_awaiting_approval'], '
						</div>';
		echo '
						<div class="inner" id="msg_', $message['id'], '"', '>', $message['body'];
		// If this is the first post, (#0) just say when it was posted - otherwise give the reply #.
		if ($message['can_approve'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
					<ul class="horiz_list toplist floatright greytext" style="margin-top: 1rem;">';

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

		// Show a checkbox for quick moderation?
		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $message['can_remove'])
			echo '
						<li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_', $message['id'], '"></li>';

		if ($message['can_approve'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
					</ul>';
						
		echo '						
						</div>
					</div>';

		// Can the user modify the contents of this post?  Show the modify inline image.
		if ($message['can_modify'])
			echo '
					<span class="icon-star floatright" title="', $txt['modify_msg'], '" id="modify_button_', $message['id'], '" style="cursor: pointer; font-size: 1.2em;opacity: 0.5; margin-left: 1rem;" onclick="oQuickModify.modifyMsg(\'', $message['id'], '\')"></span>';

		// Assuming there are attachments...
		if (!empty($message['attachment']))
		{
			echo '
					<div id="msg_', $message['id'], '_footer" class="attachments smalltext">
						<div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';

			
			$maxheight = $modSettings['attachmentThumbWidth']*0.5;

			// first off, the pictures
			foreach($message['attachment'] as $temp)
			{
				if(!empty($temp['is_image']))
					echo '		
								<a href="#att', $temp['id'], '" style="height: ' , $maxheight , 'px;" class="attbox' , !$temp['is_approved'] ? ' napprove" title="' . $txt['attach_awaiting_approve'] : '','">
									' , !$temp['is_approved'] ? '<span class="arrowcorner"></span>' : '' , '
									<img src="', $temp['thumbnail']['href'], '" alt="" style="width: ' , $modSettings['attachmentThumbWidth'] , 'px;" id="thumb_', $temp['id'], '" />
								</a>
								<a href="#_" id="att', $temp['id'], '" class="lightbox2" style="background-image: url(', $temp['href'], ');"><span>' , $txt['find_close'] , '</span></a>';
			}	
			echo '<br style="clear: both;"><hr>';

			$last_approved_state = 1;
			foreach ($message['attachment'] as $attachment)
			{
				
				if ($attachment['is_image'])
					continue;
				
				$icon = checkextension($attachment['name']);
				// Show a special box for unapproved attachments...
				if ($attachment['is_approved'] != $last_approved_state)
				{
					$last_approved_state = 0;
					echo '
									<fieldset>
										<legend>', $txt['attach_awaiting_approve'];

					if ($context['can_approve'])
						echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

					echo '</legend>';
				}
				echo '
										<a href="' . $attachment['href'] . '"><span class="' . $icon . '"></span>&nbsp;' . $attachment['name'] . '</a> ';

				if (!$attachment['is_approved'] && $context['can_approve'])
					echo '
										[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
				echo '
										(', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' - ' . $txt['attach_viewed'] : ' - ' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.)<br />';
			}

			// If we had unapproved attachments clean up.
			if ($last_approved_state == 0)
				echo '
									</fieldset>';

			echo '
								</div>
							</div>';
		}

		echo '<hr>
						<div class="smalltext">';

		// Show "� Last Edit: Time by Person �" if this post was edited.
		if ($settings['show_modify'] && !empty($message['modified']['name']))
			echo '
								<span class="icon-info" title="', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '"></span>';

		// Maybe they want to report this post to the moderator(s)?
		if ($context['can_report_moderator'])
			echo '
								<a href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $message['counter'], ';msg=', $message['id'], '" title="', $txt['report_to_mod'], '"><span class="icon-qustion"></span></a> &nbsp;';

		// Can we issue a warning because of this post?  Remember, we can't give guests warnings.
		if ($context['can_issue_warning'] && !$message['is_message_author'] && !$message['member']['is_guest'])
			echo '
								<a href="', $scripturl, '?action=profile;area=issuewarning;u=', $message['member']['id'], ';msg=', $message['id'], '" title="', $txt['issue_warning_post'], '"><span class="icon-question"></span></a>';
		echo '
								<span class="icon-connection"></span>';

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
							</div>
						<br></div>
					</div>
				<hr class="post_separator" style="clear: both;margin-bottom: 2rem;" />';
	}

	echo '
				</form>
			</div>
			<a id="lastPost"></a>';

	// Show the page index... "Pages: [1]".
	echo '
			<div style="clear: both;">
				<div class="floatright smalltext greytext">
				', template_button_strip($normal_buttons, 'right'), '</div>
				<div class="pagelinks" style="clear: both;">', convertpages($context['page_index']), '<a href="#top" title="' . $txt['go_up'] . '">' , substr($txt['top'],0,1) , '</a></div>
			</div>';


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
			<div><hr><div class="floatright">', template_button_strip($mod_buttons, 'bottom', array('id' => 'moderationbuttons_strip')), '</div></div>';

	if ($context['can_reply'] && !empty($options['display_quick_reply']))
	{
		echo '<br class="clear" />
			<a id="quickreply"></a>
			<div class="tborder" id="quickreplybox">
				<div class="catbg">
					<h3>
						', $txt['quick_reply'], '
					</h3>
				</div>
				<div id="quickReplyOptions"', $options['display_quick_reply'] == 2 ? '' : ' style="display: none"', '>
					<div >
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
								<textarea cols="600" rows="7" name="message" tabindex="', $context['tabindex']++, '"></textarea>
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
					<span class="lowerframe"><span></span></span>
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
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape($txt['topic'] . ': %subject% &nbsp;(' . $txt['read'] . ' ' . $context['num_views'] . ' ' . $txt['times'] . ')'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), '
						});
					}';


	echo '
				// ]]></script>';
}

?>