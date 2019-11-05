<?php
/**
 * Simple Machines Forum (SMF)
 *
 *
 * @version 2.0
 */

function template_blog()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info;
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
						<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/topic/', $context['poll']['is_locked'] ? 'normal_poll_locked' : 'normal_poll', '.gif" alt="" class="icon" /> ', $txt['poll'], '</span>
					</h3>
				</div>
				<div class="windowbg">
					<span class="topslice"><span></span></span>
					<div class="content" id="poll_options">
						<h4 id="pollquestion">
							', $context['poll']['question'], '
						</h4>';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			echo '
					<dl class="options">';

			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
						<dt class="middletext', $option['voted_this'] ? ' voted' : '', '">', $option['option'], '</dt>
						<dd class="middletext statsbar', $option['voted_this'] ? ' voted' : '', '">';

				if ($context['allow_poll_view'])
					echo '
							', $option['bar_ndt'], '
							<span class="percentage">', $option['votes'], ' (', $option['percent'], '%)</span>';

				echo '
						</dd>';
			}

			echo '
					 </dl>';

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
					<span class="botslice"><span></span></span>
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
			</div>';
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
					<span class="botslice"><span></span></span>
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
			<div class="pagesection">
				', template_button_strip($normal_buttons, 'right'), '
				<div class="mypages align_left">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#lastPost"><strong>' . $txt['go_down'] . '</strong></a>' : '', '</div>
			</div>';

	// Show the topic information - icon, subject, etc.
	echo '
			<div id="forumposts">
				<div class="cat_bar">
					<h3 class="catbg">
						<img src="', $settings['images_url'], '/topic/', $context['class'], '.gif" align="bottom" alt="" />
						<span id="author">', $txt['author'], '</span>
						<span id="top_subject">', $txt['topic'], ': ', $context['subject'], ' &nbsp;(', $txt['read'], ' ', $context['num_views'], ' ', $txt['times'], ')</span>
					</h3>
				</div>';
	echo '
			<p id="whoisviewing" class="smalltext" style="overflow: hidden;">';

	if (!empty($settings['display_who_viewing']) || !empty($settings['facebooklike']) || !empty($settings['twitterlike']))
	{
		echo '
				<div id="whoisviewing" class="smalltext" style="overflow: hidden; padding: 0.5em 0;">';

		if(!empty($settings['twitterlike']))
			echo '
		<div style="float: right;">
			<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="'.$settings['twitter'].'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		</div>
		';

		if(!empty($settings['facebooklike']))
			echo '
		<div style="float: right; margin: 2px 8px 0 0;"><a style="text-decoration: none;" name="fb_share" type="button_count" href="http://www.facebook.com/sharer.php?u='.$scripturl . '?topic=' . $context['current_topic'].'">
		Share this topic</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></div>';

		if (!empty($settings['display_who_viewing'])) 
		{
			// Show just numbers...?
			if ($settings['display_who_viewing'] == 1)
					echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
			// Or show the actual people viewing the topic?
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) || $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');

			// Now show how many guests are here too.
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_topic'];
		}
		echo '
					</div>';
	}

	echo '
				<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	
	$ignoredMsgs = array();
	$removableMessageIDs = array();
	$alternate = false;
	$first = true;
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
				<div class="', $message['approved'] ? ($message['alternate'] == 0 ? 'windowbg' : 'windowbg2') : 'approvebg', '">
					<span class="topslice"><span></span></span>
					<div class="post_wrapper">';

		// Show information about the poster of this message.
		echo '
						<div class="container">';

		
		if (!empty($message['member']['avatar']['image']))
			$mootips = '
					<img class=floatright src='. $message['member']['avatar']['href']. '  />';
		else
			$mootips = '
					<img width=65 height=65 class=floatright  src='. $settings['images_url']. '/TPguest.png  />';

		$mootips .= '
				<b class=largetext>' . $message['member']['name'] . '</b>';

		// Show the member's custom title, if they have one.
		if (!empty($message['member']['title']))
			$mootips .= '
								'. $message['member']['title'];

		// Show the member's primary group (like 'Administrator') if they have one.
		if (!empty($message['member']['group']))
			$mootips .= '
								<br>'. $message['member']['group'];

		// Don't show these things for guests.
		if (!$message['member']['is_guest'])
		{
			// Show the post group if and only if they have no other group or the option is on, and they are in a post group.
			if ((empty($settings['hide_post_group']) || $message['member']['group'] == '') && $message['member']['post_group'] != '')
				$mootips .= '
								<br>'. $message['member']['post_group'];
			$mootips .= '
								<br>'. $message['member']['group_stars'];

			// Show how many posts they have made.
			if (!isset($context['disabled_fields']['posts']))
				$mootips .= '
								<br>'. $txt['member_postcount']. ': '. $message['member']['posts'];

			// Show their personal text?
			if (!empty($settings['show_blurb']) && $message['member']['blurb'] != '')
				$mootips .= '
								<br>'. $message['member']['blurb'];

			// This shows the popular messaging icons.
			if ($message['member']['has_messenger'] && $message['member']['can_view_profile'])
				$mootips .= '<br>
										'. (!empty($message['member']['icq']['link']) ? $message['member']['icq']['link'] : ''). '
										' . (!empty($message['member']['msn']['link']) ? $message['member']['msn']['link'] : ''). '
										' .(!empty($message['member']['aim']['link']) ? $message['member']['aim']['link'] : ''). '
										'.(!empty($message['member']['yim']['link']) ? $message['member']['yim']['link'] : '').'';

		}
		
		echo '
							<h5 id="subject_', $message['id'], '">';
			// Show avatars, images, etc.?
			if (!empty($message['member']['avatar']['image']))
			{	
				echo '
							<div class="disavatar"><a href="',$scripturl,'?action=miniprofile;u='.$message['member']['id'].'" rel="width:560,height:260" id="mb'.$message['id'].'" class="mb" >
								<img src="', $message['member']['avatar']['href'], '" alt="" />
								<b>'.$message['member']['name'].'</b>
								<span></span><div class="multiBoxDesc mb'.$message['id'].' mbHidden"></div>';
				echo '<div class="smalltext" style="padding: 5px; font-weight: normal;" id="buddybox'.$message['id'].'">';

				if(!empty($modSettings['enable_buddylist']) && empty($settings['showfriendlinks']) && $message['member']['id']!=$context['user']['id'])
				{
					$is_buddy = in_array($message['member']['id'], $user_info['buddies']);
					$user=$message['member']['id'];
					if($is_buddy)
						echo 'You follow '. $message['member']['link'],'
					<br>
					<a href="'.$scripturl.'?action=mybuddy;delete='.$user.';'.$context['session_var'].'='.$context['session_id'].'">Unfollow</a> | <a href="',$scripturl,'?action=friends">Friends Activity</a>';
					else
						echo '
					<a href="'.$scripturl.'?action=mybuddy;adding='.$user.';'.$context['session_var'].'='.$context['session_id'].'">Follow</a>';
				}

				echo '
				</div>';
				echo '</div>';
			}
			else
			{
				echo '
							<div class="disavatar"><a href="',$scripturl,'?action=miniprofile;u='.$message['member']['id'].'" rel="width:560,height:260" id="mb'.$message['id'].'" class="mb" >
							<img  src="', $settings['images_url'], '/TPguest.png" alt="" />
								<b>'.$message['member']['name'].'</b>
								<span></span><div class="multiBoxDesc mb'.$message['id'].' mbHidden"></div>
							</a>
			<div class="smalltext" style="padding: 5px; font-weight: normal; " id="buddybox'.$message['id'].'">';

			if(!empty($modSettings['enable_buddylist']) && empty($settings['showfriendlinks']) && $message['member']['id']!=$context['user']['id'])
				{
					$is_buddy = in_array($message['member']['id'], $user_info['buddies']);
					$user=$message['member']['id'];
					if($is_buddy)
						echo 'You follow '. $message['member']['link'],'
					<br>
					<a href="'.$scripturl.'?action=mybuddy;delete='.$user.';'.$context['session_var'].'='.$context['session_id'].'">Unfollow</a> | <a href="',$scripturl,'?action=friends">Friends Activity</a>';
					else
						echo '
					<a href="'.$scripturl.'?action=mybuddy;adding='.$user.';'.$context['session_var'].'='.$context['session_id'].'">Follow</a>';
				}
			
			echo '
			</div>
							
							</div>';
			}
			echo '	<b class="' , $message['member']['online']['is_online'] ? 'myonline' : 'myoffline'   , '">' , $message['member']['link'], '</b> <span style="color: #999; font-weight: normal;">wrote</span>
						<a class="mysubject" href="', $message['href'], '" rel="nofollow">', $message['subject'], '</a>';
		echo '
						<span class="smalltext" style="font-size: 90%; color: #888; font-weight: normal;"> ', $txt['on'], ' </strong> ', $message['time'], ' </span>
					</h5>';
	

		// Ignoring this user? Hide the post.
		if ($ignoring)
			echo '
							<div id="msg_', $message['id'], '_ignored_prompt">
								', $txt['ignoring_user'], '
								<a href="#" id="msg_', $message['id'], '_ignored_link" style="display: none;">', $txt['show_ignore_user_post'], '</a>
							</div>';

		// Show the post itself, finally!
		echo '
							<div class="post">';

		if ($settings['show_modify'] && !empty($message['modified']['name']))
			echo '
					<div class="smalltext" style="padding-bottom: 5px; font-style: italic;">', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</div>';
		
		echo '<span style="height: 4px; display: block; clear: both;"></span>';
		if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
			echo '
								<div class="approve_post">
									', $txt['post_awaiting_approval'], '
								</div>';

		echo '
								<div class="inner" id="msg_', $message['id'], '"', '>', $message['body'], '</div>
							';

		// Assuming there are attachments...
		if (!empty($message['attachment']))
		{
			echo '
							<div id="msg_', $message['id'], '_footer" class="attachments smalltext">
								<div style="overflow: hidden;">';

			$last_approved_state = 1;
			foreach ($message['attachment'] as $attachment)
			{
				echo '<div style="float: left; margin: 0 2px 2px 0;">';
				if ($attachment['is_image'])
				{
					if ($attachment['thumbnail']['has_thumb'])
						echo '
										<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" rel="lightbox"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '"  /></a>';
					else
						echo '
										<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '" border="0" /><br />';
				}
				echo '<div class="attachbot smalltext"><a href="' . $attachment['href'] . '"><b>' . $attachment['name'] . '</b></a><br>
							', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . '<br>' . $txt['attach_viewed'] : '<br>' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . '
							</div>
						</div>';
				
			}

			echo '
								</div>
							</div>';
		}

		echo '			<br />';


			echo '
								<div class="myquick" style="text-align: right;">';

		// Can they reply? Have they turned on quick reply?
		if ($context['can_reply'] && !empty($options['display_quick_reply']))
			echo '
									<a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';num_replies=', $context['num_replies'], '" onclick="return oQuickReply.quote(', $message['id'], ');">', $txt['quote'], '</a>';

		// So... quick reply is off, but they *can* reply?
		elseif ($context['can_reply'])
			echo '
									<a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';num_replies=', $context['num_replies'], '">', $txt['quote'], '</a>';

		// Can the user modify the contents of this post?
		if ($message['can_modify'])
			echo '
									| <a href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], '">', $txt['modify'], '</a>';

		// How about... even... remove it entirely?!
		if ($message['can_remove'])
			echo '
									| <a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['remove_message'], '?\');">', $txt['remove'], '</a>';

		// What about splitting it off the rest of the topic?
		if ($context['can_split'] && !empty($context['num_replies']))
			echo '
									| <a href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '">', $txt['split'], '</a>';

		// Can we restore topics?
		if ($context['can_restore_msg'])
			echo '
									| <a href="', $scripturl, '?action=restoretopic;msgs=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['restore_message'], '</a>';

			echo '
								</div>';

			// Show the member's signature?
		if (!empty($message['member']['signature']) && empty($options['show_no_signatures']) && $context['signature_enabled'])
			echo '
							<div class="signature" id="msg_', $message['id'], '_signature">', $message['member']['signature'], '</div>';
	echo '
						</div>
						</div>
					</div>
					<span class="botslice"><span></span></span>
				</div>
					';
	if(!empty($settings['area1_where']) && $settings['area1_where']==3)
		echo '<p class="windowbg3" style="padding: 8px; ">', $settings['area1'], '</p>';
	if(!empty($settings['area1_where']) && $settings['area1_where']==2 && $first)
		echo '<p class="windowbg3" style="padding: 8px; ">', $settings['area1'], '</p>';
	
	if(!empty($settings['area2_where']) && $settings['area2_where']==3)
		echo '<p class="windowbg3" style="padding: 8px; ">', $settings['area2'], '</p>';
	if(!empty($settings['area2_where']) && $settings['area2_where']==2 && $first)
		echo '<p class="windowbg3" style="padding: 8px; ">', $settings['area2'], '</p>';
	
	if(!empty($settings['area3_where']) && $settings['area3_where']==3)
		echo '<p class="windowbg3" style="padding: 8px; ">', $settings['area3'], '</p>';
	if(!empty($settings['area3_where']) && $settings['area3_where']==2 && $first)
		echo '<p class="windowbg3" style="padding: 8px; ">', $settings['area3'], '</p>';

	$first = false;
	}

	echo '
				</form>
			</div>
			<a id="lastPost"></a>';

	// Show the page index... "Pages: [1]".
	echo '
			<div class="pagesection">
				', template_button_strip($normal_buttons, 'right'), '
				<div class="mypages align_left">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#top"><strong>' . $txt['go_up'] . '</strong></a>' : '', '</div>
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
			<div id="moderationbuttons">', template_button_strip($mod_buttons, 'bottom', array('id' => 'moderationbuttons_strip')), '</div>';

	// Show the jumpto box, or actually...let Javascript do it.
	echo '
			<div class="plainbox" id="display_jump_to">&nbsp;</div>';

	if ($context['can_reply'] && !empty($options['display_quick_reply']))
	{
		echo '
			<a id="quickreply"></a>
			<div id="quickreplybox" class="sreply" ' , ((!empty($options['quickhide']) && !empty($settings['staticreplybox'])) || empty($settings['staticreplybox'])) ? ' style="position: relative;bottom: 0;width: auto;"' : '' , '>
				<h3 class="windowbg3" style="padding: 0.5em; border-bottom: solid 1px #aaa;">';
		if(!empty($settings['staticreplybox']))
			echo '<span style="float: right;">
		<img id="quickreplyhide" alt="" src="'.$settings['images_url'].'/theme/quickhide.png" />  
		<img id="quickreplyshow" alt="" src="'.$settings['images_url'].'/theme/quickshow.png" />  
		<img id="quickreplydark" alt="" src="'.$settings['images_url'].'/theme/quickdark.png" />  
		<img id="quickreplylight" alt="" src="'.$settings['images_url'].'/theme/quicklight.png" />  
					</span>';
		echo '
						', $txt['quick_reply'], '
				</h3>
				<div id="sreply"' , !empty($options['quickdark']) && !empty($settings['staticreplybox']) ? ' style="opacity: 0.6;"' : '' , '>
					<div style="padding: 0 1em;">
						<p class="smalltext lefttext" style="margin-top: 0; padding-top: 8px;">', $txt['quick_reply_desc'], '</p>
						', $context['is_locked'] ? '<p class="alert smalltext" style="margin-top: 0; padding-top: 8px;">' . $txt['quick_reply_warning'] . '</p>' : '',
						$context['oldTopicError'] ? '<p class="alert smalltext" style="margin-top: 0; padding-top: 8px;">' . sprintf($txt['error_old_topic'], $modSettings['oldTopicDays']) . '</p>' : '', '
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
								<textarea cols="75" rows="7" style="', $context['browser']['is_ie8'] ? 'max-width: 100%; min-width: 100%' : 'width: 100%', '; height: 100px;" name="message" tabindex="', $context['tabindex']++, '"></textarea>
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
	if(!empty($settings['staticreplybox']))
		echo '
	<script type="text/javascript" >

		// the quickreply toggler
		window.addEvent(\'domready\', function() {
			var qr = $(\'quickreplybox\');

			$(\'quickreplyhide\').addEvent(\'click\', function(e){
				smf_setThemeOption(\'quickhide\', \'1\', '.$settings['theme_id'].', \''.$context['session_id'].'\' , \''.$context['session_var'].'\', \'\');
				e.stop();
				qr.style.position = \'relative\';
				qr.style.width = \'auto\';
				qr.tween(\'bottom\',0);
			});
			$(\'quickreplyshow\').addEvent(\'click\', function(e){
				smf_setThemeOption(\'quickhide\', \'0\', '.$settings['theme_id'].', \''.$context['session_id'].'\' , \''.$context['session_var'].'\', \'\');
				e.stop();
				qr.style.position = \'fixed\';
				qr.style.width = \'' . $settings['qr_width'] . '\';
				qr.tween(\'bottom\',\'100px\');
			});
		});	
		// the quickreply toggler
		window.addEvent(\'domready\', function() {
			var qr = $(\'sreply\');

			$(\'quickreplydark\').addEvent(\'click\', function(e){
				smf_setThemeOption(\'quickdark\', \'1\', '.$settings['theme_id'].', \''.$context['session_id'].'\' , \''.$context['session_var'].'\', \'\');
				e.stop();
				qr.tween(\'opacity\',\'0.6\');
			});
			$(\'quickreplylight\').addEvent(\'click\', function(e){
				smf_setThemeOption(\'quickdark\', \'0\', '.$settings['theme_id'].', \''.$context['session_id'].'\' , \''.$context['session_var'].'\', \'\');
				e.stop();
				qr.tween(\'opacity\',\'1\');
			});
		});	
	</script>
			';
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
									<textarea class="editor" name="message" rows="12" style="' . ($context['browser']['is_ie8'] ? 'max-width: 100%; min-width: 100%' : 'width: 100%') . '; margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br />
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

						aIconLists[aIconLists.length] = new IconList({
							sBackReference: "aIconLists[" + aIconLists.length + "]",
							sIconIdPrefix: "msg_icon_",
							sScriptUrl: smf_scripturl,
							bShowModify: ', $settings['show_modify'] ? 'true' : 'false', ',
							iBoardId: ', $context['current_board'], ',
							iTopicId: ', $context['current_topic'], ',
							sSessionId: "', $context['session_id'], '",
							sSessionVar: "', $context['session_var'], '",
							sLabelIconList: "', $txt['message_icon'], '",
							sBoxBackground: "transparent",
							sBoxBackgroundHover: "#ffffff",
							iBoxBorderWidthHover: 1,
							sBoxBorderColorHover: "#adadad" ,
							sContainerBackground: "#ffffff",
							sContainerBorder: "1px solid #adadad",
							sItemBorder: "1px solid #ffffff",
							sItemBorderHover: "1px dotted gray",
							sItemBackground: "transparent",
							sItemBackgroundHover: "#e0e0f0"
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

?>