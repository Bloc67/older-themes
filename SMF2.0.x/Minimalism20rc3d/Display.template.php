<?php
// Version: 2.0 RC3; Display

function template_main()
{
	global $settings;
	call_user_func('display_'.$settings['themealias']);
}

function display_facebook()
{
	global $context, $sourcedir, $settings, $options, $txt, $scripturl, $modSettings;

	require_once($sourcedir. '/Subs-Post.php');
	// Build the normal button array.
	$normal_buttons = array(
		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';num_replies=' . $context['num_replies'], 'active' => true),
		'add_poll' => array('test' => 'can_add_poll', 'text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start']),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : '') . 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_topic'] : $txt['notification_enable_topic']) . '\');"', 'url' => $scripturl . '?action=notify;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'mark_unread' => array('test' => 'can_mark_unread', 'text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'send' => array('test' => 'can_send_topic', 'text' => 'send_topic', 'image' => 'sendtopic.gif', 'lang' => true, 'url' => $scripturl . '?action=emailuser;sa=sendtopic;topic=' . $context['current_topic'] . '.0'),
		'print' => array('text' => 'print', 'image' => 'print.gif', 'lang' => true, 'custom' => 'rel="new_win nofollow"', 'url' => $scripturl . '?action=printpage;topic=' . $context['current_topic'] . '.0'),
	);
	

	// Is this topic also a poll?
	if ($context['is_poll'])
	{
		// Build the poll moderation button array.
		$poll_buttons = array(
			'vote' => array('test' => 'allow_return_vote', 'text' => 'poll_return_vote', 'image' => 'poll_options.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start']),
			'results' => array('test' => 'show_view_results_button', 'text' => 'poll_results', 'image' => 'poll_results.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start'] . ';viewresults'),
			'change_vote' => array('test' => 'allow_change_vote', 'text' => 'poll_change_vote', 'image' => 'poll_change_vote.gif', 'lang' => true, 'url' => $scripturl . '?action=vote;topic=' . $context['current_topic'] . '.' . $context['start'] . ';poll=' . $context['poll']['id'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'lock' => array('test' => 'allow_lock_poll', 'text' => (!$context['poll']['is_locked'] ? 'poll_lock' : 'poll_unlock'), 'image' => 'poll_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lockvoting;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'edit' => array('test' => 'allow_edit_poll', 'text' => 'poll_edit', 'image' => 'poll_edit.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;topic=' . $context['current_topic'] . '.' . $context['start']),
			'remove_poll' => array('test' => 'can_remove_poll', 'text' => 'poll_remove', 'image' => 'admin_remove_poll.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['poll_remove_warn'] . '\');"', 'url' => $scripturl . '?action=removepoll;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		);

		echo '<div style="float: right;">', mini_template_button_strip($poll_buttons), '</div>';
		
		echo '
	<h2 class="section"> ', $txt['poll'], '	</h2>
	<h3>', $context['poll']['question'], '</h3>';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
	<div class="voteitem">', $option['voted_this'] ? '<em>' : '', $option['option'], $option['voted_this'] ? '</em>' : '', '</div>';

				if ($context['allow_poll_view'])
					echo '<div class="smalltext" style="float: right;">' , $option['votes'] .  ' ('. $option['percent'] , '%)</div>
	<div class="voteitem_container"><div style="width: ',  $option['percent'], '%;"></div></div>';
			}
			if ($context['allow_poll_view'])
				echo '
	<p><strong>', $txt['poll_total_voters'], ':</strong> ', $context['poll']['total_votes'], '</p>';
		}
		// They are allowed to vote! Go to it!
		else
		{
			echo '
	<form style="clear: both; margin-top: 1em;" action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';

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
		<div style="margin: 8px;" class="submitbutton">
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
	<div>';


		echo '
	</div><br />';
	}

	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
			<div class="linked_events">
				<h2 class="section">', $txt['calendar_linked_events'], '</h2>
				</div>
				<div class="windowbg">
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
			</div>';
	}
	
	echo '
	<div style="text-align: right; margin-bottom: 1em;">'. mini_template_button_strip($normal_buttons, 'right'), '</div>
	<h2 class="section">' . $context['subject'] .'</h2>
	<span class="smalllinks" style="display: block; clear: both; padding: 4px 4px 12px 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>
	';
	
	
	$posts = array(); $avys = array();
	while ($message = pro_callback())
	{
		$posts[] = $message;
		$avys[] = $message['member']['id']; 
	}
	$avy = progetAvatars($avys); 
	foreach($posts as $re => $rec)
	{
		$quicks = array();
		// Maybe we can approve it, maybe we should?
		if ($rec['can_approve'])
			$quicks['approve'] = array(
				'text' => 'approve', 
				'url' => $scripturl . '?action=moderate;area=postmod;sa=approve;topic='. $context['current_topic']. '.'. $context['start']. ';msg='. $rec['id']. ';'. $context['session_var']. '='. $context['session_id'], 
				'active' => true,
			);
			
		// Can they reply? Have they turned on quick reply?
		if ($context['can_reply'] && !empty($options['display_quick_reply']))
			$quicks['reply'] = array(
				'text' => 'quote', 
				'url' => $scripturl . '?action=post;quote='. $rec['id']. ';topic='. $context['current_topic']. '.'. $context['start']. ';num_replies='. $context['num_replies'], 
				'active' => true,
				'subtemplate' => 'pro_quote',
			);
		// Can the user modify the contents of this post?
		if ($rec['can_modify'])
			$quicks['modify'] = array(
				'text' => 'modify', 
				'url' => $scripturl . '?action=post;msg='. $rec['id']. ';topic='. $context['current_topic']. '.'. $context['start'], 
				'active' => true,
			);

		// How about... even... remove it entirely?!
		if ($rec['can_remove'])
			$quicks['remove'] = array(
				'text' => 'remove', 
				'url' => $scripturl . '?action=deletemsg;topic='. $context['current_topic']. '.'. $context['start']. ';msg='. $rec['id']. ';'. $context['session_var']. '='. $context['session_id'], 
				'active' => true,
			);

		// What about splitting it off the rest of the topic?
		if ($context['can_split'] && !empty($context['num_replies']))
			$quicks['split'] = array(
				'text' => 'split', 
				'url' => $scripturl . '?action=splittopics;topic='. $context['current_topic']. '.0;at='. $rec['id'], 
				'active' => true,
			);
		// Can we restore topics?
		if ($context['can_restore_msg'])
			$quicks['restore'] = array(
				'text' => 'restore_message', 
				'url' => $scripturl . '?action=restoretopic;msgs='. $rec['id']. ';'. $context['session_var']. '='. $context['session_id'], 
				'active' => true,
			);

		censorText($bdy);
		$bdy = $rec['body'];
		$final = '[quote author=' . $rec['member']['name'] . ' link=topic=' . $context['current_topic'] . '.msg' . $rec['id'] . '#msg' . $rec['id'] . ' date=' . $rec['timestamp'] . ']' . strip_tags($bdy) .  '[/quote]';
		$rec['body'] = parse_bbc($rec['body'], $rec['smileys_enabled'], $rec['id_msg']);

		echo '
	<div class="recent_topic" id="msg'.$rec['id_msg'].'">', $rec['first_new'] ? '<a id="new"></a>' : '' , '
		<div class="avatar60w" style="float: left; margin-top: 4px;">' . (!empty($avy[$rec['member']['id']]) ? $avy[$rec['member']['id']] : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />') . '</div>
		<div class="textpart" style="padding-left: 25px; ">
			<span class="who" style="font-size: 90%;">' . $rec['member']['link'] . ' ' . $txt['mini-wrote'] . '</span> ' . protimeformat($rec['timestamp']) . ' ' . ($rec['first_new'] ? ' <span class="pronew2">'.$txt['new'].'</span>' : ''). '
			<div class="bodytext">' . $rec['body'] . '</div>
			<div>' , mini_template_button_strip2($quicks,$final, $rec['id']) , '</div>';

		// Assuming there are attachments...
		if (!empty($rec['attachment']))
		{
			echo '
			<div id="msg_', $rec['id'], '_footer" style="margin: 1em 0; overflow: hidden;">
				<div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';

			$last_approved_state = 1;
			foreach ($rec['attachment'] as $attachment)
			{
				echo '
				<div class="mini_attach">';
				// Show a special box for unapproved attachments...
				if ($attachment['is_approved'] != $last_approved_state)
				{
					$last_approved_state = 0;
					echo '
						<fieldset>
							<legend>', $txt['attach_awaiting_approve'], '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $rec['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]</legend>';
				}

				if ($attachment['is_image'])
				{
					if ($attachment['thumbnail']['has_thumb'])
						echo '
							<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" border="0" /></a><br>';
					else
						echo '
							<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '" border="0" /><br />';
				}
				echo '
							<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a> ';

				if (!$attachment['is_approved'])
					echo '
							[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
				echo '
							<br />', $attachment['size'], ' | ' . $attachment['downloads'] . ' ' . $txt['downloads'];
				echo '
				</div>';
			}

			// If we had unapproved attachments clean up.
			if ($last_approved_state == 0)
				echo '
						</fieldset>';

			echo '
					</div>
			</div>';
		}

		echo '
		</div>
	</div>';
	}
	$mod_buttons = array(
		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['are_sure_remove_topic'] . '\');"', 'url' => $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
		'lock' => array('test' => 'can_lock', 'text' => empty($context['is_locked']) ? 'set_lock' : 'set_unlock', 'image' => 'admin_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lock;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'sticky' => array('test' => 'can_sticky', 'text' => empty($context['is_sticky']) ? 'set_sticky' : 'set_nonsticky', 'image' => 'admin_sticky.gif', 'lang' => true, 'url' => $scripturl . '?action=sticky;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'merge' => array('test' => 'can_merge', 'text' => 'merge', 'image' => 'merge.gif', 'lang' => true, 'url' => $scripturl . '?action=mergetopics;board=' . $context['current_board'] . '.0;from=' . $context['current_topic']),
		'calendar' => array('test' => 'calendar_post', 'text' => 'calendar_link', 'image' => 'linktocal.gif', 'lang' => true, 'url' => $scripturl . '?action=post;calendar;msg=' . $context['topic_first_message'] . ';topic=' . $context['current_topic'] . '.0'),
	);
	if (!empty($settings['display_who_viewing']))
	{
		echo '
		<p id="whoisviewing" class="smalltext">';

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
	echo '
	<hr class="divider2" />
	<span class="smalllinks" style=" display: block; clear: right; padding: 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>
	<div style="clear: both; ">', mini_template_button_strip($mod_buttons, 'left'), '</div>';

	if(!empty($context['topic_tags']))
	{		
	// Tagging System
		echo '
		<h2 class="section">', $txt['smftags_topic'], '</h2>
		<div class="clearfix windowbg largepadding">';

			foreach ($context['topic_tags'] as $i => $tag)
			{
				echo '<a href="' . $scripturl . '?action=tags;tagid=' . $tag['ID_TAG']  . '">' . $tag['tag'] . '</a>&nbsp;';
				if(!$context['user']['is_guest'] && allowedTo('smftags_del'))
				echo '<a href="' . $scripturl . '?action=tags;sa=deletetag;tagid=' . $tag['ID']  . '"><font color="#FF0000">[X]</font></a>&nbsp;';

			}

			global $topic;
			if(!$context['user']['is_guest'] && allowedTo('smftags_add'))
			echo '
			&nbsp;<a href="' . $scripturl . '?action=tags;sa=addtag;topic=',$topic, '">' . $txt['smftags_addtag'] . '</a>';

		echo '
			</div>';
		// End Tagging System
	}

	// Added by Related Topics
	if (!empty($context['related_topics'])) // TODO: Have ability to display no related topics?
	{
		echo '<br />
			<h2 class="section">', $txt['related_topics'], '</h2>';

		if (!empty($context['related_topics']))
		{
			// get the avatars
			$tops = array(); $ids = array(); $tauthors = array(); $stickies=array();
			foreach($context['related_topics'] as $t => $topic)
			{
				if($topic['is_sticky'])
					$stickies[] = $t;
				$tops[$t] = '
				<div class="mini_topictable">
					' . protimeformat($topic['last_post']['timestamp']) . ' <span class="whop">'.$topic['last_post']['member']['link'].'</span> '.$txt['mini-wrote'].' '.$txt['in'].' ' . $topic['last_post']['link'] . '
					' . ($topic['new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). ' &nbsp;&nbsp;<span class="smalllinks">' . $topic['pages'] . '</span>
					<span style="float: right;">
						' . ($topic['is_sticky'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/sticky.png" alt="*" />' : '') . '
						' . ($topic['is_locked'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/locked.png" alt="*" />' : '') . '
						' . ($topic['is_posted_in'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/comment.png" alt="*" />' : '') . '
					</span>
				</div>';
				$tauthors[$t] = $ids[] = $topic['last_post']['member']['id'];

			}
			$avy = progetAvatars($ids); $alt=true;
			foreach($tops as $t => $op)
			{
				echo '<div class="topictable' , in_array($t,$stickies) ? '2' : '' , '"><div class="avatar16h">' . (!empty($avy[$tauthors[$t]]) ? $avy[$tauthors[$t]] : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />') . '</div>' . $op . '</div>';
				$alt = !$alt;
			}
		}
		else
			echo '
							<p>', $txt['msg_alert_none'], '</p';
	}
}

function display_chat()
{
	global $context, $sourcedir, $settings, $options, $txt, $scripturl, $modSettings;

	// Build the normal button array.
	$normal_buttons = array(
		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';num_replies=' . $context['num_replies'], 'active' => true),
		'add_poll' => array('test' => 'can_add_poll', 'text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start']),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : '') . 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_topic'] : $txt['notification_enable_topic']) . '\');"', 'url' => $scripturl . '?action=notify;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'mark_unread' => array('test' => 'can_mark_unread', 'text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'send' => array('test' => 'can_send_topic', 'text' => 'send_topic', 'image' => 'sendtopic.gif', 'lang' => true, 'url' => $scripturl . '?action=emailuser;sa=sendtopic;topic=' . $context['current_topic'] . '.0'),
		'print' => array('text' => 'print', 'image' => 'print.gif', 'lang' => true, 'custom' => 'rel="new_win nofollow"', 'url' => $scripturl . '?action=printpage;topic=' . $context['current_topic'] . '.0'),
	);
	

	// Is this topic also a poll?
	if ($context['is_poll'])
	{
		// Build the poll moderation button array.
		$poll_buttons = array(
			'vote' => array('test' => 'allow_return_vote', 'text' => 'poll_return_vote', 'image' => 'poll_options.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start']),
			'results' => array('test' => 'show_view_results_button', 'text' => 'poll_results', 'image' => 'poll_results.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start'] . ';viewresults'),
			'change_vote' => array('test' => 'allow_change_vote', 'text' => 'poll_change_vote', 'image' => 'poll_change_vote.gif', 'lang' => true, 'url' => $scripturl . '?action=vote;topic=' . $context['current_topic'] . '.' . $context['start'] . ';poll=' . $context['poll']['id'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'lock' => array('test' => 'allow_lock_poll', 'text' => (!$context['poll']['is_locked'] ? 'poll_lock' : 'poll_unlock'), 'image' => 'poll_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lockvoting;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'edit' => array('test' => 'allow_edit_poll', 'text' => 'poll_edit', 'image' => 'poll_edit.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;topic=' . $context['current_topic'] . '.' . $context['start']),
			'remove_poll' => array('test' => 'can_remove_poll', 'text' => 'poll_remove', 'image' => 'admin_remove_poll.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['poll_remove_warn'] . '\');"', 'url' => $scripturl . '?action=removepoll;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		);

		echo '<div style="float: right;">', mini_template_button_strip($poll_buttons), '</div>';
		
		echo '
	<h2 class="section"> ', $txt['poll'], '	</h2>
	<h3>', $context['poll']['question'], '</h3>';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
	<div class="voteitem">', $option['voted_this'] ? '<em>' : '', $option['option'], $option['voted_this'] ? '</em>' : '', '</div>';

				if ($context['allow_poll_view'])
					echo '<div class="smalltext" style="float: right;">' , $option['votes'] .  ' ('. $option['percent'] , '%)</div>
	<div class="voteitem_container"><div style="width: ',  $option['percent'], '%;"></div></div>';
			}
			if ($context['allow_poll_view'])
				echo '
	<p><strong>', $txt['poll_total_voters'], ':</strong> ', $context['poll']['total_votes'], '</p>';
		}
		// They are allowed to vote! Go to it!
		else
		{
			echo '
	<form style="clear: both; margin-top: 1em;" action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';

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
		<div style="margin: 8px;" class="submitbutton">
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
	<div>';


		echo '
	</div><br />';
	}

	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
			<div class="linked_events">
				<h2 class="section">', $txt['calendar_linked_events'], '</h2>
				</div>
				<div class="windowbg">
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
			</div>';
	}
	
	echo '
	<div style="float: left; margin-bottom: 0.5em; margin-left: -9px;">', mini_template_button_strip($normal_buttons, 'right'), '</div><br style="clear: both;" />
	<h2 class="section2">' . $context['subject'] .'</h2>
	<span class="smalllinks" style="display: block; clear: both; padding: 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>	<hr class="divider2" />

	';
	
	
	$posts = array(); $avys = array();
	while ($message = pro_callback())
	{
		$posts[] = $message;
		$avys[] = $message['member']['id']; 
	}
	$avy = progetAvatars($avys); 

	echo '
	<div class="container">
		<div class="col3">
			<div style="margin-right: 15px;">';
	foreach($posts as $a => $rec)
		echo '<div class="avatar40h" style="float: left; margin-top: 4px; margin-right: 4px;"><a title="'.$rec['member']['name']. $txt['mini-postedthis'].protimeformat($rec['timestamp']).'" href="#msg'. $rec['id'] .'">' . (!empty($avy[$rec['member']['id']]) ? $avy[$rec['member']['id']] : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />') . '</a></div>';
	
	echo '</div>
		</div>
		<div class="col13">';

	foreach($posts as $re => $rec)
	{
		$quicks = array();
		// Maybe we can approve it, maybe we should?
		if ($rec['can_approve'])
			$quicks['approve'] = array(
				'text' => 'approve', 
				'url' => $scripturl . '?action=moderate;area=postmod;sa=approve;topic='. $context['current_topic']. '.'. $context['start']. ';msg='. $rec['id']. ';'. $context['session_var']. '='. $context['session_id'], 
				'active' => true,
			);
			
		// Can they reply? Have they turned on quick reply?
		if ($context['can_reply'] && !empty($options['display_quick_reply']))
			$quicks['reply'] = array(
				'text' => 'quote', 
				'url' => $scripturl . '?action=post;quote='. $rec['id']. ';topic='. $context['current_topic']. '.'. $context['start']. ';num_replies='. $context['num_replies'], 
				'active' => true,
				'subtemplate' => 'pro_quote',
			);
		// Can the user modify the contents of this post?
		if ($rec['can_modify'])
			$quicks['modify'] = array(
				'text' => 'modify', 
				'url' => $scripturl . '?action=post;msg='. $rec['id']. ';topic='. $context['current_topic']. '.'. $context['start'], 
				'active' => true,
			);

		// How about... even... remove it entirely?!
		if ($rec['can_remove'])
			$quicks['remove'] = array(
				'text' => 'remove', 
				'url' => $scripturl . '?action=deletemsg;topic='. $context['current_topic']. '.'. $context['start']. ';msg='. $rec['id']. ';'. $context['session_var']. '='. $context['session_id'], 
				'active' => true,
			);

		// What about splitting it off the rest of the topic?
		if ($context['can_split'] && !empty($context['num_replies']))
			$quicks['split'] = array(
				'text' => 'split', 
				'url' => $scripturl . '?action=splittopics;topic='. $context['current_topic']. '.0;at='. $rec['id'], 
				'active' => true,
			);
		// Can we restore topics?
		if ($context['can_restore_msg'])
			$quicks['restore'] = array(
				'text' => 'restore_message', 
				'url' => $scripturl . '?action=restoretopic;msgs='. $rec['id']. ';'. $context['session_var']. '='. $context['session_id'], 
				'active' => true,
			);

		censorText($bdy);
		$bdy = $rec['body'];
		$final = '[quote author=' . $rec['member']['name'] . ' link=topic=' . $context['current_topic'] . '.msg' . $rec['id'] . '#msg' . $rec['id'] . ' date=' . $rec['timestamp'] . ']' . strip_tags($bdy) .  '[/quote]';
		$rec['body'] = parse_bbc($rec['body'], $rec['smileys_enabled'], $rec['id_msg']);

		echo '
	<div class="recent_topic" id="msg'.$rec['id'].'">
		<div class="container">
			<div class="col3">
				<span class="who">' . $rec['member']['link'] . '</span>
				<div class="avatar16h" style="float: none; margin: 5px; "><a title="'.$rec['member']['name']. '" href="'. $scripturl . '?action=profile;u=' . $rec['member']['id'] .'">' . (!empty($avy[$rec['member']['id']]) ? $avy[$rec['member']['id']] : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />') . '</a></div>
			</div>
			<div class="col13">
				<div class="smalltext" style="color: #777; padding-bottom: 6px;">' . protimeformat($rec['timestamp'],true) . ($rec['first_new'] ? ' <span class="pronew2">'.$txt['new'].'</span>' : ''). '</div>
				<div class="bodytext">' . $rec['body'] . '</div>
				<div style="font-size: 90%; text-transform: auto;">' , mini_template_button_strip2($quicks,$final, $rec['id']) , '</div>';

		// Assuming there are attachments...
		if (!empty($rec['attachment']))
		{
			echo '
		<div id="msg_', $rec['id'], '_footer" style="margin: 1em 0; overflow: hidden;">
			<div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';

			$last_approved_state = 1;
			foreach ($rec['attachment'] as $attachment)
			{
				echo '
				<div class="mini_attach">';
				// Show a special box for unapproved attachments...
				if ($attachment['is_approved'] != $last_approved_state)
				{
					$last_approved_state = 0;
					echo '
						<fieldset>
							<legend>', $txt['attach_awaiting_approve'], '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $rec['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]</legend>';
				}

				if ($attachment['is_image'])
				{
					if ($attachment['thumbnail']['has_thumb'])
						echo '
							<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" border="0" /></a><br>';
					else
						echo '
							<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '" border="0" /><br />';
				}
				echo '
							<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a> ';

				if (!$attachment['is_approved'])
					echo '
							[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
				echo '
							<br />', $attachment['size'], ' | ' . $attachment['downloads'] . ' ' . $txt['downloads'];
				echo '
				</div>';
			}

			// If we had unapproved attachments clean up.
			if ($last_approved_state == 0)
				echo '
						</fieldset>';

			echo '
			</div>
		</div>';
		}

		echo '
			</div>
		</div>';

		echo '
	</div><br>';
	}
	
	echo '
		</div>
	</div>';
	
	$mod_buttons = array(
		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['are_sure_remove_topic'] . '\');"', 'url' => $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
		'lock' => array('test' => 'can_lock', 'text' => empty($context['is_locked']) ? 'set_lock' : 'set_unlock', 'image' => 'admin_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lock;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'sticky' => array('test' => 'can_sticky', 'text' => empty($context['is_sticky']) ? 'set_sticky' : 'set_nonsticky', 'image' => 'admin_sticky.gif', 'lang' => true, 'url' => $scripturl . '?action=sticky;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'merge' => array('test' => 'can_merge', 'text' => 'merge', 'image' => 'merge.gif', 'lang' => true, 'url' => $scripturl . '?action=mergetopics;board=' . $context['current_board'] . '.0;from=' . $context['current_topic']),
		'calendar' => array('test' => 'calendar_post', 'text' => 'calendar_link', 'image' => 'linktocal.gif', 'lang' => true, 'url' => $scripturl . '?action=post;calendar;msg=' . $context['topic_first_message'] . ';topic=' . $context['current_topic'] . '.0'),
	);
	if (!empty($settings['display_who_viewing']))
	{
		echo '
		<p id="whoisviewing" class="smalltext">';

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
	echo '
	<hr class="divider2" />
	<span class="smalllinks" style=" display: block; clear: right; padding: 0 4px 4px 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>
	<div style="clear: both; ">', mini_template_button_strip($mod_buttons, 'left'), '</div>';

	if(!empty($context['topic_tags']))
	{		
	// Tagging System
		echo '
		<h2 class="section">', $txt['smftags_topic'], '</h2>
		<div class="clearfix windowbg largepadding">';

			foreach ($context['topic_tags'] as $i => $tag)
			{
				echo '<a href="' . $scripturl . '?action=tags;tagid=' . $tag['ID_TAG']  . '">' . $tag['tag'] . '</a>&nbsp;';
				if(!$context['user']['is_guest'] && allowedTo('smftags_del'))
				echo '<a href="' . $scripturl . '?action=tags;sa=deletetag;tagid=' . $tag['ID']  . '"><font color="#FF0000">[X]</font></a>&nbsp;';

			}

			global $topic;
			if(!$context['user']['is_guest'] && allowedTo('smftags_add'))
			echo '
			&nbsp;<a href="' . $scripturl . '?action=tags;sa=addtag;topic=',$topic, '">' . $txt['smftags_addtag'] . '</a>';

		echo '
			</div>';
		// End Tagging System
	}

	// Added by Related Topics
	if (!empty($context['related_topics'])) // TODO: Have ability to display no related topics?
	{
		echo '<br />
			<h2 class="section">', $txt['related_topics'], '</h2>';

		if (!empty($context['related_topics']))
		{
			// get the avatars
			$tops = array(); $ids = array(); $tauthors = array(); $stickies=array();
			foreach($context['related_topics'] as $t => $topic)
			{
				if($topic['is_sticky'])
					$stickies[] = $t;
				$tops[$t] = '
				<div class="mini_topictable">
					' . protimeformat($topic['last_post']['timestamp']) . ' <span class="whop">'.$topic['last_post']['member']['link'].'</span> '.$txt['mini-wrote'].' '.$txt['in'].' ' . $topic['last_post']['link'] . '
					' . ($topic['new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). ' &nbsp;&nbsp;<span class="smalllinks">' . $topic['pages'] . '</span>
					<span style="float: right;">
						' . ($topic['is_sticky'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/sticky.png" alt="*" />' : '') . '
						' . ($topic['is_locked'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/locked.png" alt="*" />' : '') . '
						' . ($topic['is_posted_in'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/comment.png" alt="*" />' : '') . '
					</span>
				</div>';
				$tauthors[$t] = $ids[] = $topic['last_post']['member']['id'];

			}
			$avy = progetAvatars($ids); $alt=true;
			foreach($tops as $t => $op)
			{
				echo '<div class="topictable' , in_array($t,$stickies) ? '2' : '' , '"><div class="avatar16h">' . (!empty($avy[$tauthors[$t]]) ? $avy[$tauthors[$t]] : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />') . '</div>' . $op . '</div>';
				$alt = !$alt;
			}
		}
		else
			echo '
				<p>', $txt['msg_alert_none'], '</p';
	}
}

function pro_quote($texty)
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $board, $sourcedir, $user_info;
	
	echo '
	<form action="', $scripturl, '?action=post2', empty($context['current_board']) ? '' : ';board=' . $context['current_board'], '" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="submitonce(this);" style="margin: 0;">
		<input type="hidden" name="topic" value="', $context['current_topic'], '" />
		<input type="hidden" name="subject" value="', $context['response_prefix'], $context['subject'], '" />
		<input type="hidden" name="icon" value="xx" />
		<input type="hidden" name="from_qr" value="1" />
		<input type="hidden" name="notify" value="', $context['is_marked_notify'] || !empty($options['auto_notify']) ? '1' : '0', '" />
		<input type="hidden" name="not_approved" value="', !$context['can_reply_approved'], '" />
		<input type="hidden" name="goback" value="1" />
		<input type="hidden" name="num_replies" value="', $context['num_replies'], '" />
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />';
					
	// Guests just need more.
	if ($context['user']['is_guest'])
		echo '
		<strong>', $txt['name'], ':</strong> <input type="text" name="guestname" value="', $context['name'], '" size="25" class="formitems" tabindex="', $context['tabindex']++, '" />
		<strong>', $txt['email'], ':</strong> <input type="text" name="email" value="', $context['email'], '" size="25" class="formitem" tabindex="', $context['tabindex']++, '" /><br />';

	// Is visual verification enabled?
	if ($context['require_verification'])
		echo '
		<strong>', $txt['verification'], ':</strong>', template_control_verification($context['visual_verification_id'], 'quick_reply'), '<br />';

	echo '
		<div class="formitem">
			<textarea cols="75" rows="14" style="', $context['browser']['is_ie8'] ? 'max-width: 100%; min-width: 100%' : 'width: 100%', '; height: 200px;" name="message" tabindex="', $context['tabindex']++, '">'.$texty.'
</textarea>
		</div>
		<div class="righttext padding">
			<input type="submit" name="post" value="', $txt['post'], '" onclick="return submitThisOnce(this);" accesskey="s" tabindex="', $context['tabindex']++, '" class="formitem" />
			<input type="submit" name="preview" value="', $txt['preview'], '" onclick="return submitThisOnce(this);" accesskey="p" tabindex="', $context['tabindex']++, '" class="formitem" />';

	if ($context['show_spellchecking'])
		echo '
			<input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'postmodify\', \'message\');" tabindex="', $context['tabindex']++, '" class="button_submit" />';

	echo '
		</div>
	</form>';
}

// Callback for the message display(modified).
function pro_callback($reset = false)
{
	global $settings, $txt, $modSettings, $scripturl, $options, $user_info, $smcFunc;
	global $memberContext, $context, $messages_request, $topic, $attachments, $topicinfo;

	static $counter = null;

	// If the query returned false, bail.
	if ($messages_request == false)
		return false;

	// Remember which message this is.  (ie. reply #83)
	if ($counter === null || $reset)
		$counter = empty($options['view_newest_first']) ? $context['start'] : $context['total_visible_posts'] - $context['start'];

	// Start from the beginning...
	if ($reset)
		return @$smcFunc['db_data_seek']($messages_request, 0);

	// Attempt to get the next message.
	$message = $smcFunc['db_fetch_assoc']($messages_request);
	if (!$message)
	{
		$smcFunc['db_free_result']($messages_request);
		return false;
	}

	// $context['icon_sources'] says where each icon should come from - here we set up the ones which will always exist!
	if (empty($context['icon_sources']))
	{
		$stable_icons = array('xx', 'thumbup', 'thumbdown', 'exclamation', 'question', 'lamp', 'smiley', 'angry', 'cheesy', 'grin', 'sad', 'wink', 'moved', 'recycled', 'wireless', 'clip');
		$context['icon_sources'] = array();
		foreach ($stable_icons as $icon)
			$context['icon_sources'][$icon] = 'images_url';
	}

	// Message Icon Management... check the images exist.
	if (empty($modSettings['messageIconChecks_disable']))
	{
		// If the current icon isn't known, then we need to do something...
		if (!isset($context['icon_sources'][$message['icon']]))
			$context['icon_sources'][$message['icon']] = file_exists($settings['theme_dir'] . '/images/post/' . $message['icon'] . '.gif') ? 'images_url' : 'default_images_url';
	}
	elseif (!isset($context['icon_sources'][$message['icon']]))
		$context['icon_sources'][$message['icon']] = 'images_url';

	// If you're a lazy bum, you probably didn't give a subject...
	$message['subject'] = $message['subject'] != '' ? $message['subject'] : $txt['no_subject'];

	// Are you allowed to remove at least a single reply?
	$context['can_remove_post'] |= allowedTo('delete_own') && (empty($modSettings['edit_disable_time']) || $message['poster_time'] + $modSettings['edit_disable_time'] * 60 >= time()) && $message['id_member'] == $user_info['id'];

	// If it couldn't load, or the user was a guest.... someday may be done with a guest table.
	if (!loadMemberContext($message['id_member'], true))
	{
		// Notice this information isn't used anywhere else....
		$memberContext[$message['id_member']]['name'] = $message['poster_name'];
		$memberContext[$message['id_member']]['id'] = 0;
		$memberContext[$message['id_member']]['group'] = $txt['guest_title'];
		$memberContext[$message['id_member']]['link'] = $message['poster_name'];
		$memberContext[$message['id_member']]['email'] = $message['poster_email'];
		$memberContext[$message['id_member']]['show_email'] = showEmailAddress(true, 0);
		$memberContext[$message['id_member']]['is_guest'] = true;
	}
	else
	{
		$memberContext[$message['id_member']]['can_view_profile'] = allowedTo('profile_view_any') || ($message['id_member'] == $user_info['id'] && allowedTo('profile_view_own'));
		$memberContext[$message['id_member']]['is_topic_starter'] = $message['id_member'] == $context['topic_starter_id'];
		$memberContext[$message['id_member']]['can_see_warning'] = !isset($context['disabled_fields']['warning_status']) && $memberContext[$message['id_member']]['warning_status'] && (($context['user']['can_mod'] || !empty($modSettings['warning_show'])) || ($memberContext[$message['id_member']]['id'] == $context['user']['id'] && !empty($modSettings['warning_show']) && $modSettings['warning_show'] == 1));
	}

	$memberContext[$message['id_member']]['ip'] = $message['poster_ip'];

	// Do the censor thang.
	censorText($message['body']);
	censorText($message['subject']);

	// Run BBC interpreter on the message.

	// Compose the memory eat- I mean message array.
	$output = array(
		'attachment' => loadAttachmentContext($message['id_msg']),
		'id_msg' => $message['id_msg'],
		'smileys_enabled' => $message['smileys_enabled'],
		'alternate' => $counter % 2,
		'id' => $message['id_msg'],
		'href' => $scripturl . '?topic=' . $topic . '.msg' . $message['id_msg'] . '#msg' . $message['id_msg'],
		'link' => '<a href="' . $scripturl . '?topic=' . $topic . '.msg' . $message['id_msg'] . '#msg' . $message['id_msg'] . '" rel="nofollow">' . $message['subject'] . '</a>',
		'member' => &$memberContext[$message['id_member']],
		'icon' => $message['icon'],
		'icon_url' => $settings[$context['icon_sources'][$message['icon']]] . '/post/' . $message['icon'] . '.gif',
		'subject' => $message['subject'],
		'time' => timeformat($message['poster_time']),
		'timestamp' => forum_time(true, $message['poster_time']),
		'counter' => $counter,
		'modified' => array(
			'time' => timeformat($message['modified_time']),
			'timestamp' => forum_time(true, $message['modified_time']),
			'name' => $message['modified_name']
		),
		'body' => $message['body'],
		'new' => empty($message['is_read']),
		'approved' => $message['approved'],
		'first_new' => isset($context['start_from']) && $context['start_from'] == $counter,
		'is_ignored' => !empty($modSettings['enable_buddylist']) && !empty($options['posts_apply_ignore_list']) && in_array($message['id_member'], $context['user']['ignoreusers']),
		'can_approve' => !$message['approved'] && $context['can_approve'],
		'can_unapprove' => $message['approved'] && $context['can_approve'],
		'can_modify' => (!$context['is_locked'] || allowedTo('moderate_board')) && (allowedTo('modify_any') || (allowedTo('modify_replies') && $context['user']['started']) || (allowedTo('modify_own') && $message['id_member'] == $user_info['id'] && (empty($modSettings['edit_disable_time']) || !$message['approved'] || $message['poster_time'] + $modSettings['edit_disable_time'] * 60 > time()))),
		'can_remove' => allowedTo('delete_any') || (allowedTo('delete_replies') && $context['user']['started']) || (allowedTo('delete_own') && $message['id_member'] == $user_info['id'] && (empty($modSettings['edit_disable_time']) || $message['poster_time'] + $modSettings['edit_disable_time'] * 60 > time())),
		'can_see_ip' => allowedTo('moderate_forum') || ($message['id_member'] == $user_info['id'] && !empty($user_info['id'])),
	);

	// Is this user the message author?
	$output['is_message_author'] = $message['id_member'] == $user_info['id'];

	if (empty($options['view_newest_first']))
		$counter++;
	else
		$counter--;

	return $output;
}

?>