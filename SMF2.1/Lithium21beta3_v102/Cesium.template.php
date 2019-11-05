<?php

// Lithium 1.0
// a rewrite theme

/* the template to show a list of boards. Used from BoardIndex, Messageindex */
function subtemplate_boards($cat)
{
	global $context, $txt, $scripturl, $settings;

	echo '
	<div class="bwgrid f_boards">';	
	
	foreach ($cat as $board)
	{
		echo '
		<div class="bwcell16" style="margin-bottom: 2rem;">
			<div class="bwgrid" style="position: relative;">
				<div class="bwcell2">';

		if(!isset($board['is_redirect']))
			$board['is_redirect']=false;

		echo '
					<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '" ', !empty($board['board_tooltip']) ? ' title="' . $board['board_tooltip'] . '"' : '', '>
						<span class="boardicon_' , $board['board_class'] , '"><span class="icon-user"></span></span>
					</a>';

		echo '
				</div>
				<div class="bwcell14 mobile_topmargin"><div class="bwgutter_right">
					<h4 class="mobile_header"><a href="', $board['href'], '" id="b', $board['id'], '">', $board['name'], '</a></h4>
					<p class="greytext des">', $board['description'], '</p>
					<p class="greytext stats">
						<b>', comma_format($board['posts']), '</b> ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], '
						', $board['is_redirect'] ? '' : ' | <b>' . comma_format($board['topics']) . '</b> ' . $txt['board_topics'], '
					</p>';

		if (!empty($board['last_post']['id']))
			echo '
					<p class="greytext lastpost des">', $board['last_post']['last_post_message'], '</p>
					<p class="greytext lastpost mob">', $board['last_post']['member']['link'], ' &quot;', $board['last_post']['link'], '&quot;</p>
					';

		if (!empty($board['children']))
		{
			$children = array();
			foreach ($board['children'] as $child)
			{

				if (isset($child['is_redirect']) && !$child['is_redirect'])
					$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="board_new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><span class="new_posts">' . $txt['new'] . '</span>' : '') . '</a>';
				else
					$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . ' - ' . $child['short_description'] . '">' . $child['name'] . '</a>';

				$children[] = $child['new'] ? '<strong>' . $child['link'] . '</strong>' : $child['link'];
			}

		echo '
					<p class="greytext smalltext"><strong>', $txt['sub_boards'], '</strong>: ', implode($children), '</p>';
		}
		echo '
				</div></div>
			</div>
		</div>';
	}
	echo '
	</div>';
}

/* the template to show a list of topics. Used from MessageIndex, Recent(unread and unreadreplies) */
function subtemplate_topiclist()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;
		
	foreach ($context['topics'] as $topic)
	{
		
		echo '
	<div class="bwgrid">
		<div class="bwcell2 des" style="position: relative;">
			<span class="topicicon_off">';

		if($topic['is_locked'])
			echo '
			<span class="icon-lock icon-overlay"></span>';

		if(!empty($topic['first_post']['member']['avatar']['href']))
		{
			if(substr($topic['first_post']['member']['avatar']['href'],strlen($topic['first_post']['member']['avatar']['href'])-12)== '/default.png')
				echo '
			<span class="avatar_topic' , $topic['is_locked'] ? ' faded' : '' , '" style="background-image: url(' . $settings['images_url'] . '/avatar.png);"></span>';
			else
				echo '
			<span class="avatar_topic' , $topic['is_locked'] ? ' faded' : '' , '" style="background-image: url(' . $topic['first_post']['member']['avatar']['href'] . ');"></span>';
		}
		else
			echo '
			<span class="avatar_topic' , $topic['is_locked'] ? ' faded' : '' , '" style="background-image: url(' . $settings['images_url'] . '/avatar.png);"></span>';

		echo '
			</span>
		</div>
		<div class="bwcell14 mob_tag" style="position: relative;">
			<div class="mob floatright" style="position: relative;">';
		
		if($topic['is_locked'])
			echo '
				<span class="icon-lock icon-overlay"></span>';

		if(!empty($topic['first_post']['member']['avatar']['href']))
		{
			if(substr($topic['first_post']['member']['avatar']['href'],strlen($topic['first_post']['member']['avatar']['href'])-12)== '/default.png')
				echo '
				<span class="avatar_topic' , $topic['is_locked'] ? ' faded' : '' , '" style="background-image: url(' . $settings['images_url'] . '/avatar.png);"></span>';
			else
				echo '
				<span class="avatar_topic' , $topic['is_locked'] ? ' faded' : '' , '" style="background-image: url(' . $topic['first_post']['member']['avatar']['href'] . ');"></span>';
		}
		else
			echo '
				<span class="avatar_topic' , $topic['is_locked'] ? ' faded' : '' , '" style="background-image: url(' . $settings['images_url'] . '/avatar.png);"></span>';

		echo '
			</div>
			<div class="des floatright preview" style="text-align: right; margin: 0.5rem 0 0 2rem; width: 30%;"><b><a href="' . $topic['last_post']['href'] . '">' , $txt['last_post'] , '</b></a> ' , $txt['by'] , ' ' , $topic['last_post']['member']['link'] , '<br>' , $topic['last_post']['time'] , '</div>
			<div class="title_bar" style="width: 65%; float: left; vertical-align: top; white-space: normal !important;">
				<h4 class="titlebg">
					' , $topic['first_post']['link'];
		
		if ((isset($topic['new']) && $topic['new']) && $context['user']['is_logged'])
			echo '
					<span class="new_icon"></span>';
		
		if ($topic['is_sticky'])
			echo '
					<span class="icon-pin mob_inline icon-smaller"></span>
					<span class="icon-pin icon-notice des"></span>';
		if (isset($topic['is_redirect']) && $topic['is_redirect'])
			echo '
					<span class="icon-forward mob_inline icon-smaller"></span>
					<span class="icon-forward icon-notice des"></span>';
		if ($topic['is_poll'])
			echo '
					<span class="icon-chart mob_inline icon-smaller"></span>
					<span class="icon-chart icon-notice des"></span>';
		
		echo '
				</h4>
			</div>
			<div class="preview" style="clear: left;">
				' , $topic['first_post']['member']['link'] , ' / ' , $topic['first_post']['time'], ' 
				<span class="des" style="display: block;">' , $topic['views'] ,  ' ' , $txt['views'] , '  ' , $topic['replies'] ,  ' ' , $txt['replies'] , '</span>
				<span class="mob"><br>' , $txt['last_post'] , ' ' , $txt['by'] , ' <b>' , $topic['last_post']['member']['link'] , '</b> - ' , $topic['last_post']['time'] , '</span>
			</div>';

		// Show the quick moderation options?
		if (!empty($context['can_quick_mod']))
		{
			echo '
			<div class="moderation floatright clear_right">';
			if ($options['display_quick_mod'] == 1)
				echo '
				<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="input_check">';
			else
			{
				// Check permissions on each and show only the ones they are allowed to use.
				if ($topic['quick_mod']['remove'])
					echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions%5B', $topic['id'], '%5D=remove;', $context['session_var'], '=', $context['session_id'], '" class="you_sure"><span class="icon-cross2" title="', $txt['remove_topic'], '"></span></a>';

				if ($topic['quick_mod']['lock'])
					echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions%5B', $topic['id'], '%5D=lock;', $context['session_var'], '=', $context['session_id'], '" class="you_sure"><span class="icon-', $topic['is_locked'] ? 'unlock' : 'lock' , '" title="', $topic['is_locked'] ? $txt['set_unlock'] : $txt['set_lock'], '"></span></a>';

				if ($topic['quick_mod']['sticky'])
					echo '<a href="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], ';actions%5B', $topic['id'], '%5D=sticky;', $context['session_var'], '=', $context['session_id'], '" class="you_sure"><span class="icon-tag2" title="', $topic['is_sticky'] ? $txt['set_nonsticky'] : $txt['set_sticky'], '"></span></a>';

				if ($topic['quick_mod']['move'])
					echo '<a href="', $scripturl, '?action=movetopic;current_board=', $context['current_board'], ';board=', $context['current_board'], '.', $context['start'], ';topic=', $topic['id'], '.0"><span class="icon-fast-forward" title="', $txt['move_topic'], '"></span></a>';
			}
			echo '
			</div>';
		}
		if (isset($context['showCheckboxes']) && $context['showCheckboxes'])
			echo '
			<div class="moderation floatright">
				<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="input_check">
			</div>';

		echo '
		</div>
	</div>';
	}
}

/* show calndar posts. Used in calednar views week and month */
function subtemplate_calendar_items($what, $show_small = false)
{
	global $scripturl, $txt;

	$divs = '';
	foreach ($what as $event)
	{
		if($show_small)
		{
			echo '
				<div class="cal_small event_wrapper', $event['starts_today'] == true ? ' event_starts_today' : '', $event['ends_today'] == true ? ' event_ends_today' : '', $event['allday'] == true ? ' allday' : '', $event['is_selected'] ? ' sel_event' : '', '">
					<div>
						<a href="' . $event['href'] . '" title="' , $event['title'] , '"><span class="icon-calendar3 icon-bigger" onclick="fPop_toggle(\'#event' . $event['id'] . '\'); return false;"></span></a>';
						
			$divs .= '
						<div id="event'.$event['id'].'" style="display: none;" class="calitem calmore">
							<a href="' . $event['href'] . '" title="' . $event['title'] . '">'. $event['title'].'</a>';
			
			// If they can edit the event, show an icon they can click on....
			if ($event['can_edit'])
				$divs .= '
							<a class="modify_event" href="'. $event['modify_href']. '">
								<span class="icon-cog" title="'. $txt['calendar_edit']. '"></span>
							</a>';
			// Exporting!
			if ($event['can_export'])
				$divs .= '
							<a class="modify_event" href="'. $event['export_href']. '">
								<span class="icon-box" title="'. $txt['calendar_export']. '"></span>
							</a>';

			$divs .= '
							<div>
								<b>';

			if (!empty($event['start_time_local']) && $event['starts_today'] == true)
				$divs .= (trim(str_replace(':00 ', ' ', $event['start_time_local'])));
			elseif (!empty($event['end_time_local']) && $event['ends_today'] == true)
				$divs .= (strtolower($txt['ends'])). ' '. (trim(str_replace(':00 ', ' ', $event['end_time_local'])));
			elseif (!empty($event['allday']))
				$divs .= $txt['calendar_allday'];
			
			$divs .= '
								</b>';
			if (!empty($event['location']))
				$divs .= '
								<div class="event_location">' . $event['location'] . '</div>';

			$divs .= '
							</div>
						</div>';
			echo '
					</div>
				</div>';
		}
		else
		{
			echo '
				<div class="event_wrapper', $event['starts_today'] == true ? ' event_starts_today' : '', $event['ends_today'] == true ? ' event_ends_today' : '', $event['allday'] == true ? ' allday' : '', $event['is_selected'] ? ' sel_event' : '', '">
					<span class="calitem">
						<a href="' . $event['href'] . '" title="' , $event['title'] , '">', $event['title'],'</a>&nbsp; ';
			
			// If they can edit the event, show an icon they can click on....
			if ($event['can_edit'])
				echo '
						<a class="modify_event" href="', $event['modify_href'], '">
							<span class="icon-cog" title="', $txt['calendar_edit'], '"></span>
						</a>';
			// Exporting!
			if ($event['can_export'])
				echo '
						<a class="modify_event" href="', $event['export_href'], '">
							<span class="icon-box" title="', $txt['calendar_export'], '"></span>
						</a>';

			echo '	<span class="icon-plus" onclick="fPop_toggle(\'#event' . $event['id'] . '\'); return false;"></span>
					</span>
					<div id="event'.$event['id'].'" style="display: none;" class="calitem calmore">
						<b>';

			if (!empty($event['start_time_local']) && $event['starts_today'] == true)
				echo trim(str_replace(':00 ', ' ', $event['start_time_local']));
			elseif (!empty($event['end_time_local']) && $event['ends_today'] == true)
				echo strtolower($txt['ends']), ' ', trim(str_replace(':00 ', ' ', $event['end_time_local']));
			elseif (!empty($event['allday']))
				echo $txt['calendar_allday'];
			echo '
						</b>';
			if (!empty($event['location']))
				echo '
						<div class="event_location">' . $event['location'] . '</div>';

			echo '
					</div>
				</div>';
		}
	}
	echo $divs;
}

function subtemplate_single_post($message)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	$ignoring = false;

	if ($message['can_remove'])
		$context['removableMessageIDs'][] = $message['id'];

	// Are we ignoring this message?
	if (!empty($message['is_ignored']))
	{
		$ignoring = true;
		$context['ignoredMsgs'][] = $message['id'];
	}

	echo '
	<div class="', $message['css_class'], '">', $message['id'] != $context['first_message'] ? '
		<a id="msg' . $message['id'] . '"></a>' . ($message['first_new'] ? '<a id="new"></a>' : '') : '', '
		<div class="bwgrid">
			<div class="bwcell3 des">
				<div class="avatar_display_container">	';
				
	// Show online and offline buttons?
	if (!$message['member']['is_guest'])
		echo '
					<div class="' . ($message['member']['online']['is_online'] == 1 ? 'member_on' : 'member_off') . '">';
	else
		echo '
					<div class="member_off">';

	// Show the user's avatar.
	if(substr($message['member']['avatar']['href'],strlen($message['member']['avatar']['href'])-12)== '/default.png' || empty($message['member']['avatar']['href']))
		echo '
					<a href="', $message['member']['href'], '" style="background-image: url(', $settings['images_url'], '/avatar.png);" alt="" class="avatar_display" /></a>';
	else
		echo '
					<a href="', $message['member']['href'], '" style="background-image: url(', $message['member']['avatar']['href'], ');" alt="" class="avatar_display" /></a>';

	echo '
					</div>
				</div>
				<h4 class="subject centertext">', $message['member']['link'], '</h4>';

	// Show the member's primary group (like 'Administrator') if they have one.
	if (!empty($message['member']['group']))
		echo '
				<div class="centertext">', $message['member']['group'], '</div>';

	// Show how many posts they have made.
	if (!isset($context['disabled_fields']['posts']))
		echo '
				<div class="stats largetext centertext">', $message['member']['posts'], '</div>';
	echo '
			</div>
			<div class="bwcell13">
				<div class="bwgutter_left">
					<div>
						<div class="floatright mob avatar_mob_container">';
				
	// Show online and offline buttons?
	if (!$message['member']['is_guest'] && !empty($modSettings['onlineEnable']))
		echo '
							', $context['can_send_pm'] ? '<a href="' . $message['member']['online']['href'] . '" title="' . $message['member']['online']['label'] . '">' : '', '<span class="' . ($message['member']['online']['is_online'] == 1 ? 'member_on' : 'member_off') . '" title="' . $message['member']['online']['text'] . '"></span>', $context['can_send_pm'] ? '</a>' : '';
	else
		echo '
							<span class="member_invisible"></span>';

	// Show the user's avatar.
	if(substr($message['member']['avatar']['href'],strlen($message['member']['avatar']['href'])-12)== '/default.png' || empty($message['member']['avatar']['href']))
		echo '
							<a href="', $message['member']['href'], '" style="background-image: url(', $settings['images_url'], '/avatar.png);" alt="" class="avatar_display" /></a>';
	else
		echo '
							<a href="', $message['member']['href'], '" style="background-image: url(', $message['member']['avatar']['href'], ');" alt="" class="avatar_display" /></a>';

	echo '
						</div>
						<div id="subject_', $message['id'], '" class="smalltext subjectsmall', (empty($modSettings['subject_toggle']) ? ' subject_hidden' : ''), '">
						<a class="greytext" href="', $message['href'], '" rel="nofollow">', $message['subject'], '</a></div>
						<div class="greytext desc">', $message['time'], '</div>
					</div>';
	if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
		echo '
					<div class="approve_post">', $txt['post_awaiting_approval'], '</div>';

	echo '
					<div class="post" data-msgid="', $message['id'], '" id="msg_', $message['id'], '"', $ignoring ? ' style="display:none;"' : '', '>' , $message['body'] , '</div>';
	
	// Assuming there are attachments...
	if (!empty($message['attachment']))
	{
		$non_attach = array();
		echo '
					<div class="attached">
						<div class="bwgrid">';
		foreach ($message['attachment'] as $attachment)
		{
			$d='';
			// Do we want this attachment to not be showed here?
			if (!empty($modSettings['dont_show_attach_under_post']) && !empty($context['show_attach_under_post'][$attachment['id']]))
				continue;

			echo '
						<div id="msg_', $message['id'], '_footer" class="bwcell' , !empty($settings['number_attach']) ? $settings['number_attach'] : '33' , '">
							<div class="att_inner">';

			if ($attachment['is_image'])
			{
				// Show a special box for unapproved attachments...
				if (!$attachment['is_approved'])
				{
					$last_approved_state = 0;
					echo '
									<fieldset>
										<legend>', $txt['attach_awaiting_approve'];

					if ($context['can_approve'])
						echo '
											&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

					echo '
										</legend>';
				}
				if ($attachment['thumbnail']['has_thumb'])
					echo '
									<a href="#" onclick="fPop_showImage(\'#attbig' . $attachment['id'] . '\'); return false;">
										<img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" class="atc_img" title="' .$attachment['name'] . ' / '. $attachment['size']. ($attachment['is_image'] ? ' / ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' / ' . sprintf($txt['attach_viewed'], $attachment['downloads']) : ' / ' . sprintf($txt['attach_downloaded'], $attachment['downloads'])). '" />
									</a>
									<div class="attbig" style="display: none;" id="attbig' . $attachment['id'] . '">
										<div class="single_action full"><a href="#" onclick="fPop_showImage(\'#attbig' . $attachment['id'] . '\'); return false;" class="button">' , $txt['find_close'] , '</a></div>
										<div class="single_action full"><a href="', $attachment['href'], ';image" class="button">' , $txt['preview'] , '</a></div>
										<img src="', $attachment['href'], ';image" alt="" />
										<p class="smalltext">' , $attachment['name'] , '</p>
									</div>';
				else
					echo '
									<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '" class="atc_img">';

				if (!$attachment['is_approved'])
				{
					if ($context['can_approve'])
						echo '
										[<a href="'. $scripturl. '?action=attachapprove;sa=approve;aid='. $attachment['id']. ';'. $context['session_var']. '='. $context['session_id']. '">'. $txt['approve']. '</a>]&nbsp;|&nbsp;[<a href="'. $scripturl. '?action=attachapprove;sa=reject;aid='. $attachment['id'], ';'. $context['session_var']. '='. $context['session_id']. '">'. $txt['delete']. '</a>] ';
					echo '
									</fieldset>';
				}
			}
			else
			{
				// Show a special box for unapproved attachments...
				if (!$attachment['is_approved'])
				{
					$d .= '
									<fieldset>
										<legend>'. $txt['attach_awaiting_approve'];

					if ($context['can_approve'])
						$d .= '
											&nbsp;[<a href="'. $scripturl. '?action=attachapprove;sa=all;mid='. $message['id']. ';'. $context['session_var']. '='. $context['session_id']. '">'. $txt['approve_all']. '</a>]';

					$d .= '
										</legend>';
				}
				$d .= '
									<a href="' . $attachment['href'] . '"><span class="icon-attachment"></span>&nbsp;<b title="' .$attachment['name'] . '">' . substr($attachment['name'],0,25) . '..</b></a> / '. $attachment['size']. ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' / ' . sprintf($txt['attach_viewed'], $attachment['downloads']) : ' / ' . sprintf($txt['attach_downloaded'], $attachment['downloads']));
				if (!$attachment['is_approved'])
				{
					if ($context['can_approve'])
						$d .= '
										[<a href="'. $scripturl. '?action=attachapprove;sa=approve;aid='. $attachment['id']. ';'. $context['session_var']. '='. $context['session_id']. '">'. $txt['approve']. '</a>]&nbsp;|&nbsp;[<a href="'. $scripturl. '?action=attachapprove;sa=reject;aid='. $attachment['id']. ';'. $context['session_var']. '='. $context['session_id']. '">'. $txt['delete']. '</a>] ';
					$d .= '
									</fieldset>';
				}
				$non_attach[] = $d;
			}
			echo '
							</div>
						</div>';
		}
		echo '
					</div>
				</div>';
		
		if(count($non_attach)>0)
			echo '
				<div class="att_single">' , implode('</div><div class="att_single">', $non_attach), '</div>';
	}
	
	// And stuff below the attachments.
	if ($context['can_report_moderator'] || !empty($context['can_see_likes']) || !empty($context['can_like']) || $message['can_approve'] || $message['can_unapprove'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'] || $context['can_quote'])
	echo '
				<div class="toppadding">';

	// What about likes?
	if (!empty($modSettings['enable_likes']))
	{
		echo '
					<ul class="floatleft nolist">';

		if (!empty($message['likes']['can_like']))
		{
			echo '
						<li class="like_button" id="msg_', $message['id'], '_likes"', $ignoring ? ' style="display:none;"' : '', '><a href="', $scripturl, '?action=likes;ltype=msg;sa=like;like=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" class="msg_like"><span class="generic_icons ', $message['likes']['you'] ? 'unlike' : 'like', '"></span> ', $message['likes']['you'] ? $txt['unlike'] : $txt['like'], '</a></li>';
		}

		if (!empty($message['likes']['count']) && !empty($context['can_see_likes']))
		{
			$context['some_likes'] = true;
			$count = $message['likes']['count'];
			$base = 'likes_';
			if ($message['likes']['you'])
			{
				$base = 'you_' . $base;
				$count--;
			}
			$base .= (isset($txt[$base . $count])) ? $count : 'n';

			echo '
						<li class="like_count smalltext">', sprintf($txt[$base], $scripturl . '?action=likes;sa=view;ltype=msg;like=' . $message['id'] . ';' . $context['session_var'] . '=' . $context['session_id'], comma_format($count)), '</li>';
		}

		echo '
					</ul>';
	}

	// Show the quickbuttons, for various operations on posts.
	if ($message['can_approve'] || $message['can_unapprove'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'] || $context['can_quote'] || $context['can_report_moderator'])
	{
		echo '	<div class="generic_menu rootmenu qbuttons">
					<ul class="menu_nav nolist dropmenu">';

		// Can they quote? if so they can select and quote as well!
		if ($context['can_quote'])
			echo '
						<li><a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '" onclick="return oQuickReply.quote(', $message['id'], ');"><span class="generic_icons quote"></span>', $txt['quote_action'], '</a></li>
						<li style="display:none;" id="quoteSelected_', $message['id'], '"><a href="javascript:void(0)"><span class="generic_icons quote_selected"></span>', $txt['quote_selected_action'], '</a></li>';

		// Can the user modify the contents of this post? Show the modify inline image.
		if ($message['can_modify'])
			echo '
						<li class="quick_edit"><a href="javascript:void(0)" title="', $txt['modify_msg'], '" class="modifybutton" id="modify_button_', $message['id'], '" onclick="oQuickModify.modifyMsg(\'', $message['id'], '\', \'', !empty($modSettings['toggle_subject']), '\')"><span class="generic_icons quick_edit_button"></span>', $txt['quick_edit'], '</a></li>';

		if ($message['can_approve'] || $message['can_unapprove'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
						<li class="post_options"><a href="#" onclick="fPop_toggle(\'#qmore'.$message['id'].'\'); return false;">', $txt['post_options'],'</a></li>';

		// Maybe they want to report this post to the moderator(s)?
		if ($context['can_report_moderator'])
			echo '
						<li class="report_link"><a href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $message['counter'], ';msg=', $message['id'], '">', $txt['report_to_mod'], '</a></li>';

		if ($message['can_approve'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
					</ul>
					</div>';
		
		echo '	<div id="qmore'.$message['id'].'" style="display: none;" class="generic_menu sub_generic">
						<ul class="dropmenu nolist menu_nav">';

		// Can the user modify the contents of this post?
		if ($message['can_modify'])
			echo '
							<li><a href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], '"><span class="generic_icons modify_button"></span>', $txt['modify'], '</a></li>';

		// How about... even... remove it entirely?!
		if ($context['can_delete'] && ($context['topic_first_message'] == $message['id']))
			echo '
							<li><a href="', $scripturl, '?action=removetopic2;topic=', $context['current_topic'], '.', $context['start'], ';', $context['session_var'], '=', $context['session_id'], '" data-confirm="', $txt['are_sure_remove_topic'], '" class="you_sure"><span class="generic_icons remove_button"></span>', $txt['remove_topic'], '</a></li>';
		elseif ($message['can_remove'] && ($context['topic_first_message'] != $message['id']))
			echo '
							<li><a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" data-confirm="', $txt['remove_message_question'], '" class="you_sure"><span class="generic_icons remove_button"></span>', $txt['remove'], '</a></li>';

		// What about splitting it off the rest of the topic?
		if ($context['can_split'] && !empty($context['real_num_replies']))
			echo '
							<li><a href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '"><span class="generic_icons split_button"></span>', $txt['split'], '</a></li>';

		// Can we issue a warning because of this post? Remember, we can't give guests warnings.
		if ($context['can_issue_warning'] && !$message['is_message_author'] && !$message['member']['is_guest'])
			echo '
							<li><a href="', $scripturl, '?action=profile;area=issuewarning;u=', $message['member']['id'], ';msg=', $message['id'], '"><span class="generic_icons warn_button"></span>', $txt['issue_warning'], '</a></li>';

		// Can we restore topics?
		if ($context['can_restore_msg'])
			echo '
							<li><a href="', $scripturl, '?action=restoretopic;msgs=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons restore_button"></span>', $txt['restore_message'], '</a></li>';

		// Maybe we can approve it, maybe we should?
		if ($message['can_approve'])
			echo '
							<li><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons approve_button"></span>', $txt['approve'], '</a></li>';

		// Maybe we can unapprove it?
		if ($message['can_unapprove'])
			echo '
							<li><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons unapprove_button"></span>', $txt['unapprove'], '</a></li>';

		echo '
						</ul>
					</div>';

		// Show a checkbox for quick moderation?
		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $message['can_remove'])
			echo '
					<p class="floatright" style="display: none;" id="in_topic_mod_check_', $message['id'], '"></p>';
		else
			echo '
					<br>';
	}

	if ($context['can_report_moderator'] || !empty($context['can_see_likes']) || !empty($context['can_like']) || $message['can_approve'] || $message['can_unapprove'] || $context['can_reply'] || $message['can_modify'] || $message['can_remove'] || $context['can_split'] || $context['can_restore_msg'] || $context['can_quote'] || $context['can_report_moderator'])
		echo '
				</div></div>';

	echo '
			<div class="moderatorbar clear"><div class="bwgutter_left">';

	// Are there any custom profile fields for above the signature?
	if (!empty($message['custom_fields']['above_signature']))
	{
		echo '
				<div class="custom_fields_above_signature">
					<ul class="nolist">';

		foreach ($message['custom_fields']['above_signature'] as $custom)
			echo '
						<li class="custom ', $custom['col_name'], '">', $custom['value'], '</li>';

		echo '
					</ul>
				</div>';
	}

	// Show the member's signature?
	if (!empty($message['member']['signature']) && empty($options['show_no_signatures']) && $context['signature_enabled'])
		echo '
				<div class="signature" id="msg_', $message['id'], '_signature"', $ignoring ? ' style="display:none;"' : '', '>', $message['member']['signature'], '</div>';


	// Are there any custom profile fields for below the signature?
	if (!empty($message['custom_fields']['below_signature']))
	{
		echo '
				<div class="custom_fields_below_signature">
					<ul class="nolist">';

		foreach ($message['custom_fields']['below_signature'] as $custom)
			echo '
						<li class="custom ', $custom['col_name'], '">', $custom['value'], '</li>';

		echo '
					</ul>
				</div>';
	}
	if(!$context['user']['is_logged'])
		echo '</div>';

	echo '
			</div></div>
			</div>
		</div>
	</div>';
}

function subtemplate_blog_single($post, $comment = false, $first = true)
{
	global $scripturl, $context, $txt, $settings;

	if(substr($post['member']['avatar']['href'],strlen($post['member']['avatar']['href'])-12)=='/default.png')
		$post['member']['avatar']['href'] = $settings['images_url'].'/avatar.png';
	
	if(!$comment)
	{
		$tgs = ces_parsebbc($post['body']);
		echo '
	<div class="bwgrid">	
		<div class="bwcell16"><div>
			<a href="' , $post['member']['href'] , '" class="floatright">	
				<img src="' , $post['member']['avatar']['href']. '" alt="" class="avatar_blog author" />
			</a>
			<h3 class="blogheader">
				<strong style="font-size: 120%; font-weight: 900; letter-spacing: 0;">
					<a href="' , $post['href'] , '">' , $post['subject'] , '</a>
					' , !empty($post['is_new']) ? '<span class="new_icon"></span>' : '' , '
				</strong>
				<div class="h3date">
					' , $post['time'] , ' <span class="smaller">' , $txt['by'] ,' </span>
					<a href="' , $post['member']['href'] , '">	
						<span class="smaller">' ,$post['member']['name'] , '</span>
					</a>
				</div>
			</h3>
			<div class="bwgrid">
				<div class="bwcell4">';
		
		if(!empty($post['attachment']))
		{
			$att = '';
			foreach($post['attachment'] as $p => $b)
				if(!empty($b['is_image']))
					$att = $b['href'].';image';
			
			echo '
						<div class="bwgutter_right blog_attach_container">
							<div class="blog_attachment" style="background-image: url(' , $att, ');"></div>
						</div>';
		}
		echo '&nbsp;
					</div>
					<div class="bwcell12">
						<div class="blogpost">' , $post['body'] , '</div>
						<div class="blogtext smalltext toppadding"><hr style="opacity: 0.4;">' , $context['topicinfo']['num_views'] , ' ' . $txt['views'] . ' &nbsp;&nbsp;' , $context['topicinfo']['num_replies'] , ' ' . $txt['replies'] . '
						' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '</div>';

		if(!empty($post['attachment']))
		{
			echo '
						<div class="bwgrid">';
			$att = '';
			foreach($post['attachment'] as $p => $b)
			{	
				echo '
							<div class="bwcell1">
								<div style="padding: 0 15% 10% 0;">';
				
				if(!empty($b['is_image']))
				{
					if ($b['thumbnail']['has_thumb'])
						echo '
										<a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;">
											<div class="blog_attachment" style="background-image: url(' , $b['thumbnail']['href'], ');"></div>
										</a>
										<div class="attbig" style="display: none;" id="attbig' . $b['id'] . '">
											<div class="single_action full"><a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;" class="button">' , $txt['find_close'] , '</a></div>
											<div class="single_action full"><a href="', $b['href'], ';image" class="button">' , $txt['preview'] , '</a></div>
											<img src="', $b['href'], ';image" alt="" />
											<p class="smalltext">' , $b['name'] , '</p>
										</div>';
					else
						echo '
										<img src="' . $b['href'] . ';image" alt="" width="' . $b['width'] . '" height="' . $b['height'] . '" class="atc_img">';
				}
				else
					echo '
									<div class="centertext"><a href="' , $att ,'"><span class="icon-clip"></span><br>' , $b['name'] , '</a></div>';
				
				echo '				
								</div>
							</div>';
			}
			echo '
						</div><br class="clear">';
		}
		ces_displaybuttons($post);
		ces_typelinks($tgs);		
		echo '
					</div>
				</div></div>
			</div>
		</div><br>';
	}
	// a comment then
	else
		ces_displaycomments($post);

}

function subtemplate_galleries_single($post, $comment = false, $first = true)
{
	global $scripturl, $context, $txt, $settings;

	if(substr($post['member']['avatar']['href'],strlen($post['member']['avatar']['href'])-12)=='/default.png')
		$post['member']['avatar']['href'] = $settings['images_url'].'/avatar.png';
	
	if(!$comment)
	{
		$tgs = ces_parsebbc($post['body']);
		if(!empty($post['attachment']))
		{
			echo '
	<div id="attframe" style="display: none; ">
		<div class="bwgrid">
			<div class="bwcell2"><br class="des">
				<span class="des icon-arrow-left icon-bigger" onclick="fPop_hideAtt(); return false;" style="cursor:pointer; color: #eee;"></span>
				<span class="icon-arrow-left mob toppadding" onclick="fPop_hideAtt(); return false;" style="font-size: 3rem; color: #eee; text-align: center; margin: auto; cursor:pointer;"></span>
				<div id="bwgallery_row">
					<br class="des"><br class="des">

				';

			foreach($post['attachment'] as $p => $b)
			{	
				echo '
				<div style="width: 98%; margin: 2px;">';
				
				if(!empty($b['is_image']))
				{
					if ($b['thumbnail']['has_thumb'])
						echo '
					<a class="des" href="#"  title="' , $b['name'] , ' - ' , $b['downloads'] , ' ' , $txt['downloads'] , ' - ' . $b['size'] . '"  onclick="fPop_showImage_only(\'#attbig' . $b['id'] . '\',\'.attbigg\'); return false;">
						<div class="blog_attachment" style="background-image: url(' , $b['thumbnail']['href'], ');"></div>
					</a>
					<div class="blog_attachment mob" style="background-image: url(' , $b['href'], ');"></div>
					<div class="attbig_name" title="' , $b['name'] , '">' , $b['name'] , '</div>';
					else
						echo '
					<a class="des" href="#"  title="' , $b['name'] , ' - ' , $b['downloads'] , ' ' , $txt['downloads'] , ' - ' . $b['size'] . '"  onclick="fPop_showImage_only(\'#attbig' . $b['id'] . '\',\'.attbigg\'); return false;">
						<div class="blog_attachment" style="background-image: url(' , $b['href'], ');"></div>
					</a>
					<div class="blog_attachment mob" style="background-image: url(' , $b['href'], ');"></div>
					<div class="attbig_name" title="' , $b['name'] , '">' , $b['name'] , '</div>';
				}
				echo '				
				</div>';
			}
		}	
		echo '</div>
			</div>
			<div class="des bwcell14" style="background: #111;">';

		foreach($post['attachment'] as $p => $b)
			echo '
				<img class="attbigg" src="' . $b['href'] . ';image" style="margin-left: 10px; max-height: 95.5vh; max-width: 100%; display: none;" id="attbig'.$b['id'].'" alt="" />';
		
		echo '
			</div>
		</div>
	</div>
	<div class="bwgrid">	
		<div class="bwcell16"><div>
			<a href="' , $post['member']['href'] , '" class="floatright">	
				<img src="' , $post['member']['avatar']['href']. '" alt="" class="avatar_blog author" />
			</a>
			<h3 class="blogheader">
				<strong style="font-size: 120%; font-weight: 900; letter-spacing: 0;">
					<a href="' , $post['href'] , '">' , $post['subject'] , '</a>
					' , !empty($post['is_new']) ? '<span class="new_icon"></span>' : '' , '
				</strong>
				<div class="h3date">
					' , $post['time'] , ' <span class="smaller">' , $txt['by'] ,' </span>
					<a href="' , $post['member']['href'] , '">	
						<span class="smaller">' ,$post['member']['name'] , '</span>
					</a>
				</div>
			</h3>
			<div class="bwgrid">
					<div class="bwcell16">';

		if(!empty($post['attachment']))
		{
			echo '
						<div class="bwgrid">';
			$att = '';
			foreach($post['attachment'] as $p => $b)
			{	
				echo '
							<div class="bwcell4 w4">
								<div class="bwgallery2">';
				
				if(!empty($b['is_image']))
				{
					if ($b['thumbnail']['has_thumb'])
						echo '
									<a href="#" title="' , $b['name'] , ' - ' , $b['downloads'] , ' ' , $txt['downloads'] , ' - ' . $b['size'] . '"  onclick="fPop_showAtt(\'#attbig' . $b['id'] . '\'); return false;">
										<div class="blog_attachment" style="background-image: url(' , $b['thumbnail']['href'], ');"></div>
									</a>';
					else
						echo '
									<a href="#" title="' , $b['name'] , ' - ' , $b['downloads'] , ' ' , $txt['downloads'] , ' - ' . $b['size'] . '" onclick="fPop_showAtt(\'#attbig' . $b['id'] . '\'); return false;">
										<div class="blog_attachment" style="background-image: url(' , $b['href'], ');"></div>
									</a>';
				}
				else
					echo '
									<div class="centertext"><a href="' , $att ,'"><span class="icon-clip"></span><br>' , $b['name'] , '</a></div>';
				
				echo '				
								</div>
							</div>';
			}
		}	
		echo '
						</div><br class="clear">
						<div class="blogpost">' , $post['body'] , '</div>
						<div class="blogtext smalltext toppadding"><hr style="opacity: 0.4;">' , $context['topicinfo']['num_views'] , ' ' . $txt['views'] . ' &nbsp;&nbsp;' , $context['topicinfo']['num_replies'] , ' ' . $txt['replies'] . '
						' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '</div>';
		

		ces_displaybuttons($post);
		ces_typelinks($tgs);		

		echo '
					</div>
				</div></div>
			</div>
		</div><br>';
	}
	// a comment then
	else
		ces_displaycomments($post);

}

function subtemplate_news_single($post, $comment = false, $first = true)
{
	global $scripturl, $context, $txt, $settings;

	if(substr($post['member']['avatar']['href'],strlen($post['member']['avatar']['href'])-12)=='/default.png')
		$post['member']['avatar']['href'] = $settings['images_url'].'/avatar.png';
	
	if(!$comment)
	{
		$tgs = ces_parsebbc($post['body']);
		
		echo '
	<div class="bwgrid clear">	
		<div class="bwcell16">';
		
		if(!empty($post['attachment']))
		{
			$att = '';
			foreach($post['attachment'] as $p => $b)
				if(!empty($b['is_image']))
					$att = $b['href'].';image';
			
			echo '
						<div class="blog_attach_container">
							<div class="blog_attachment b16" style="background-image: url(' , $att, ');"></div>
						</div>';
		}
		else
			echo '
						<div class="blog_attach_container">
							<div class="blog_attachment b16" style="background-image: url(' , $settings['images_url'], '/newsbg.jpg);"></div>
						</div>';
		echo '
		</div>
		<div class="bwcell16"><div>
			<a href="' , $post['member']['href'] , '" class="floatright">	
				<img src="' , $post['member']['avatar']['href']. '" alt="" class="avatar_blog author" />
			</a>
			<h3 class="blogheader" style="padding: 2rem 0;">
				<strong style="font-size: 200%; font-weight: 900; letter-spacing: 0;">
					<a href="' , $post['href'] , '">' , $post['subject'] , '</a>
					' , !empty($post['is_new']) ? '<span class="new_icon"></span>' : '' , '
				</strong>
				<div class="h3date">
					' , $post['time'] , ' <span class="smaller">' , $txt['by'] ,' </span>
					<a href="' , $post['member']['href'] , '">	
						<span>' ,$post['member']['name'] , '</span>
					</a>
				</div>
			</h3>';

		if(!empty($tgs['ingress']))
			echo '
			<div class="ces_ingress">' , $tgs['ingress'], '</div>';
		
		echo '
			<div class="blogpost">
				' , !empty($tgs['slogan']) ? '<div class="ces_slogan noticebox floatright" style="margin-left: 2rem; width: 40%; font-weight: bold; ">' . $tgs['slogan'] . '</div>' : '' , '
				
				' , $post['body'] , '
			</div>
			<div class="blogtext smalltext toppadding"><hr style="opacity: 0.4;">' , $context['topicinfo']['num_views'] , ' ' . $txt['views'] . ' &nbsp;&nbsp;' , $context['topicinfo']['num_replies'] , ' ' . $txt['replies'] . '
			' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '</div>
			<div class="bwgrid">
					<div class="bwcell16">';

		if(!empty($post['attachment']))
		{
			echo '
						<div class="bwgrid">';
			$att = '';
			foreach($post['attachment'] as $p => $b)
			{	
				echo '
							<div class="bwcell1">
								<div style="padding: 0 15% 10% 0;">';
				
				if(!empty($b['is_image']))
				{
					if ($b['thumbnail']['has_thumb'])
						echo '
										<a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;">
											<div class="blog_attachment" style="background-image: url(' , $b['thumbnail']['href'], ');"></div>
										</a>
										<div class="attbig" style="display: none;" id="attbig' . $b['id'] . '">
											<div class="single_action full"><a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;" class="button">' , $txt['find_close'] , '</a></div>
											<div class="single_action full"><a href="', $b['href'], ';image" class="button">' , $txt['preview'] , '</a></div>
											<img src="', $b['href'], ';image" alt="" />
											<p class="smalltext">' , $b['name'] , '</p>
										</div>';
					else
						echo '
										<img src="' . $b['href'] . ';image" alt="" width="' . $b['width'] . '" height="' . $b['height'] . '" class="atc_img">';
				}
				else
					echo '
									<div class="centertext"><a href="' , $att ,'"><span class="icon-clip"></span><br>' , $b['name'] , '</a></div>';
				
				echo '				
								</div>
							</div>';
			}
			echo '
						</div>';
		}

		ces_displaybuttons($post);
		ces_typelinks($tgs);		
		echo '
					</div>
				</div></div>
			</div>
		</div><br>';
	}
	// a comment then
	else
		ces_displaycomments($post);

}
function subtemplate_docs_single($post, $comment = false, $first = true)
{
	global $scripturl, $context, $txt, $settings;

	if(substr($post['member']['avatar']['href'],strlen($post['member']['avatar']['href'])-12)=='/default.png')
		$post['member']['avatar']['href'] = $settings['images_url'].'/avatar.png';
	
	if(!$comment)
	{
		$tgs = ces_parsebbc($post['body']);
		
		echo '
	<div class="bwgrid clear">	
		<div class="bwcell16">';
		
		if(!empty($post['attachment']))
		{
			$att = '';
			foreach($post['attachment'] as $p => $b)
				if(!empty($b['is_image']))
					$att = $b['href'].';image';
			
			echo '
						<div class="blog_attach_container">
							<div class="blog_attachment b16" style="background-image: url(' , $att, ');"></div>
						</div>';
		}
		else
			echo '
						<div class="blog_attach_container">
							<div class="blog_attachment b16" style="background-image: url(' , $settings['images_url'], '/newsbg.jpg);"></div>
						</div>';
		echo '
		</div>
		<div class="bwcell16"><div>
			<a href="' , $post['member']['href'] , '" class="floatright">	
				<img src="' , $post['member']['avatar']['href']. '" alt="" class="avatar_blog author" />
			</a>
			<h3 class="blogheader" style="padding: 2rem 0;">
				<strong style="font-size: 200%; font-weight: 900; letter-spacing: 0;">
					<a href="' , $post['href'] , '">' , $post['subject'] , '</a>
					' , !empty($post['is_new']) ? '<span class="new_icon"></span>' : '' , '
				</strong>
				<div class="h3date">
					' , $post['time'] , ' <span class="smaller">' , $txt['by'] ,' </span>
					<a href="' , $post['member']['href'] , '">	
						<span>' ,$post['member']['name'] , '</span>
					</a>
				</div>
			</h3>';

		if(!empty($tgs['ingress']))
			echo '
			<div class="ces_ingress">' , $tgs['ingress'], '</div>';
		
		echo '
			<div class="blogpost">
				' , !empty($tgs['slogan']) ? '<div class="ces_slogan noticebox floatright" style="margin-left: 2rem; width: 20%; font-weight: bold; ">' . $tgs['slogan'] . '</div>' : '' , '
				
				' , $post['body'] , '
			</div>
			<div class="blogtext smalltext toppadding"><hr style="opacity: 0.4;">' , $context['topicinfo']['num_views'] , ' ' . $txt['views'] . ' &nbsp;&nbsp;' , $context['topicinfo']['num_replies'] , ' ' . $txt['replies'] . '
			' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '</div>
			<div class="bwgrid">
					<div class="bwcell16">';

		if(!empty($post['attachment']))
		{
			echo '
						<div class="bwgrid">';
			$att = '';
			foreach($post['attachment'] as $p => $b)
			{	
				echo '
							<div class="bwcell1">
								<div style="padding: 0 15% 10% 0;">';
				
				if(!empty($b['is_image']))
				{
					if ($b['thumbnail']['has_thumb'])
						echo '
										<a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;">
											<div class="blog_attachment" style="background-image: url(' , $b['thumbnail']['href'], ');"></div>
										</a>
										<div class="attbig" style="display: none;" id="attbig' . $b['id'] . '">
											<div class="single_action full"><a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;" class="button">' , $txt['find_close'] , '</a></div>
											<div class="single_action full"><a href="', $b['href'], ';image" class="button">' , $txt['preview'] , '</a></div>
											<img src="', $b['href'], ';image" alt="" />
											<p class="smalltext">' , $b['name'] , '</p>
										</div>';
					else
						echo '
										<img src="' . $b['href'] . ';image" alt="" width="' . $b['width'] . '" height="' . $b['height'] . '" class="atc_img">';
				}
				else
					echo '
									<div class="centertext"><a href="' , $att ,'"><span class="icon-clip"></span><br>' , $b['name'] , '</a></div>';
				
				echo '				
								</div>
							</div>';
			}
			echo '
						</div>';
		}

		ces_displaybuttons($post);
		ces_typelinks($tgs);		
		echo '
					</div>
				</div></div>
			</div>
		</div><br>';
	}
	// a comment then
	else
		ces_displaycomments($post);
}
function subtemplate_bugs_single($post, $comment = false, $first = true)
{
	global $scripturl, $context, $txt, $settings;

	if(substr($post['member']['avatar']['href'],strlen($post['member']['avatar']['href'])-12)=='/default.png')
		$post['member']['avatar']['href'] = $settings['images_url'].'/avatar.png';
	
	if(!$comment)
	{
		$tgs = ces_parsebbc($post['body']);
		
		echo '
	<div class="bwgrid clear">	
		<div class="bwcell16"><div>
			<h3 class="blogheader" style="padding: 2rem 0;">
				<strong style="font-size: 170%; text-transform: none; font-weight: 300; letter-spacing: 0;">
					<a href="' , $post['href'] , '">' , $post['subject'] , '</a>
					' , !empty($post['is_new']) ? '<span class="new_icon"></span>' : '' , '
				</strong>
				<div class="h3date">
					' , $post['time'] , ' <span class="smaller">' , $txt['by'] ,' </span>
					<a href="' , $post['member']['href'] , '">	
						<span>' ,$post['member']['name'] , '</span>
					</a>
				</div>
			</h3>
			<div style="overflow: hidden;">
				', !empty($tgs['version']) ? '<span class="floatleft ces_version">'.$tgs['version'].'</span>' : '', ' 		
				', isset($tgs['status']) ? '<span class="floatleft ces_status'. $tgs['status'].'">'.$txt['ces_status'.$tgs['status']].'</span>' : '' , ' 		
				', !empty($tgs['tag']) ? '<span class="floatleft ces_status_tag">'.$tgs['tag'].'</span>' : '' , ' 		
				', !empty($tgs['endversion']) ? '<span class="floatleft ces_status2">'.$tgs['endversion'].'</span>' : '', ' 		
			</div><hr class="clear">
			<div class="blogpost">
				' , $post['body'] , '
			</div>
			', !empty($tgs['solution']) ? '<div class="information"><b>'.$tgs['solution'].'</b></div>' : '' , ' 		
			<div class="blogtext smalltext toppadding"><hr style="opacity: 0.4;">' , $context['topicinfo']['num_views'] , ' ' . $txt['views'] . ' &nbsp;&nbsp;' , $context['topicinfo']['num_replies'] , ' ' . $txt['replies'] . '
			' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '</div>
			<div class="bwgrid">
					<div class="bwcell16">';

		if(!empty($post['attachment']))
		{
			echo '
						<div class="bwgrid">';
			$att = '';
			foreach($post['attachment'] as $p => $b)
			{	
				echo '
							<div class="bwcell1">
								<div class="att_inner">';
				
				if(!empty($b['is_image']))
				{
					if ($b['thumbnail']['has_thumb'])
						echo '
										<a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;">
											<div class="blog_attachment" style="background-image: url(' , $b['thumbnail']['href'], ');"></div>
										</a>
										<div class="attbig" style="display: none;" id="attbig' . $b['id'] . '">
											<div class="single_action full"><a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;" class="button">' , $txt['find_close'] , '</a></div>
											<div class="single_action full"><a href="', $b['href'], ';image" class="button">' , $txt['preview'] , '</a></div>
											<img src="', $b['href'], ';image" alt="" />
											<p class="smalltext">' , $b['name'] , '</p>
										</div>';
					else
						echo '
										<img src="' . $b['href'] . ';image" alt="" width="' . $b['width'] . '" height="' . $b['height'] . '" class="atc_img">';
				}
				else
					echo '
									<div class="centertext"><a href="' , $att ,'"><span class="icon-clip"></span><br>' , $b['name'] , '</a></div>';
				
				echo '				
								</div>
							</div>';
			}
			echo '
						</div>';
		}

		ces_displaybuttons($post);
		ces_typelinks($tgs);		
		echo '
					</div>
				</div></div>
			</div>
		</div><br>';
	}
	// a comment then
	else
		ces_displaycomments($post);
}

function subtemplate_files_single($post, $comment = false, $first = true)
{
	global $scripturl, $context, $txt, $settings;

	if(substr($post['member']['avatar']['href'],strlen($post['member']['avatar']['href'])-12)=='/default.png')
		$post['member']['avatar']['href'] = $settings['images_url'].'/avatar.png';
	
	if(!$comment)
	{
		$tgs = ces_parsebbc($post['body']);
		echo '
	<div class="bwgrid">	
		<a href="' , $post['member']['href'] , '" class="floatright">	
			<img src="' , $post['member']['avatar']['href']. '" alt="" class="avatar_blog author" />
		</a>
		<h3 class="blogheader">
			<strong style="font-size: 120%; font-weight: 900; letter-spacing: 0;">
				<a href="' , $post['href'] , '">' , $post['subject'] , '</a>
				' , !empty($post['is_new']) ? '<span class="new_icon"></span>' : '' , '
			</strong>
			<div class="h3date">
				' , $post['time'] , ' <span class="smaller">' , $txt['by'] ,' </span>
				<a href="' , $post['member']['href'] , '">	
					<span class="smaller">' ,$post['member']['name'] , '</span>
				</a>
			</div>
		</h3>
		<div class="bwgrid">
			<div class="bwcell16">
				<div class="blogpost">' , $post['body'] , '</div>
				<div class="blogtext smalltext toppadding">
					<hr style="opacity: 0.4;">' , $context['topicinfo']['num_views'] , ' ' . $txt['views'] . ' &nbsp;&nbsp;' , $context['topicinfo']['num_replies'] , ' ' . $txt['replies'] . '
					' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '
				</div>';

		if(!empty($post['attachment']))
		{
			echo '<fieldset class="files_fieldset"><legend>' , $txt['ces_files'] , '</legend>
				<div class="bwgrid" style="padding-top: 1.5rem; position: relative;">';
			$att = '';
			foreach($post['attachment'] as $p => $b)
			{	
				echo '
					<div class="clear">
						<div style="padding: 0 0 1rem 0;">';
				
				if(!empty($b['is_image']))
				{
					if ($b['thumbnail']['has_thumb'])
						echo '
							<a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;" style="border: solid 1px #ddd; background-image: url(' , $b['thumbnail']['href'], ');" class="ces_attach_file floatright" />&nbsp;</a>
							<div class="attbig" style="position: absolute; display: none; left: 0;" id="attbig' . $b['id'] . '">
								<div class="single_action full"><a href="#" onclick="fPop_showImage(\'#attbig' . $b['id'] . '\'); return false;" class="button">' , $txt['find_close'] , '</a></div>
								<div class="single_action full"><a href="', $b['href'], ';image" class="button">' , $txt['preview'] , '</a></div>
								<img src="', $b['href'], ';image" alt="" />
								<p class="smalltext"><strong>' , $b['name'] , '</strong></p>
							</div>';
				else
						echo '
							<span style="background-image: url(' , $b['href'], ');" class="ces_attach_file floatright">&nsbp;</span>';
				}
				
				echo '
							<div  style="width: calc(100% - 5rem - 40px); padding-right: 2rem; margin-right: 2rem; ">
								<div class="mob"><a href="' , $b['href'] , '" title="' , $b['name'] , '"><strong>' , substr($b['name'],0,20) , strlen($b['name'])>20 ? '...' : '', '</strong></a></div>
								<div class="des"><a href="' , $b['href'] , '" title="' , $b['name'] , '"><strong>' , $b['name'], '</strong></a></div>
								<span class="stats_high" style="opacity: 0.7;">' , $b['downloads'] , ' ' , $txt['downloads'] , '<span class="icon-faded">&nbsp;|&nbsp;</span>' , $b['size'] , '</span>
							</div>
						</div>
					</div><br class="clear" /><hr class="icon-faded" />';
			}
			echo '
				</div></fieldset>';
		}
		
		echo '
		<br>';
		ces_displaybuttons($post);
		ces_typelinks($tgs);		
		
		echo '
			</div>
		</div>
	</div><br>';
	}
	// a comment then
	else
		ces_displaycomments($post);

}

function ces_displaybuttons($post)
{
	global $scripturl, $context, $txt, $settings;

	// Show the quickbuttons, for various operations on posts.
	if ($post['can_approve'] || $post['can_unapprove'] || $context['can_reply'] || $post['can_modify'] || $post['can_remove'] || $context['can_split'] || $context['can_restore_msg'] || $context['can_quote'] || $context['can_report_moderator'])
	{
		echo '<div class="generic_menu rootmenu qbuttons">
					<ul class="menu_nav nolist dropmenu">';

		// Can they quote? if so they can select and quote as well!
		if ($context['can_quote'])
			echo '
						<li><a href="', $scripturl, '?action=post;quote=', $post['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '" onclick="return oQuickReply.quote(', $post['id'], ');"><span class="generic_icons quote"></span>', $txt['quote_action'], '</a></li>
						<li style="display:none;" id="quoteSelected_', $post['id'], '"><a href="javascript:void(0)"><span class="generic_icons quote_selected"></span>', $txt['quote_selected_action'], '</a></li>';

		// Can the user modify the contents of this post?
		if ($post['can_modify'])
			echo '
							<li><a href="', $scripturl, '?action=post;msg=', $post['id'], ';topic=', $context['current_topic'], '.', $context['start'], '"><span class="generic_icons modify_button"></span>', $txt['modify'], '</a></li>';

		// Maybe they want to report this post to the moderator(s)?
		if ($context['can_report_moderator'])
			echo '
						<li class="report_link"><a href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $post['counter'], ';msg=', $post['id'], '">', $txt['report_to_mod'], '</a></li>';
		if ($post['can_approve'] || $post['can_unapprove'] || $post['can_modify'] || $post['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
						<li class="post_options"><a href="#" onclick="fPop_toggle(\'#qmore'.$post['id'].'\'); return false;">', $txt['post_options'],'</a></li>';


		if ($post['can_approve'] || $context['can_reply'] || $post['can_modify'] || $post['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
					</ul>
					</div>';
		
		echo '	<div id="qmore'.$post['id'].'" style="display: none;" class="generic_menu sub_generic">
						<ul class="dropmenu nolist menu_nav">';

		// How about... even... remove it entirely?!
		if ($context['can_delete'] && ($context['topic_first_message'] == $post['id']))
			echo '
							<li><a href="', $scripturl, '?action=removetopic2;topic=', $context['current_topic'], '.', $context['start'], ';', $context['session_var'], '=', $context['session_id'], '" data-confirm="', $txt['are_sure_remove_topic'], '" class="you_sure"><span class="generic_icons remove_button"></span>', $txt['remove_topic'], '</a></li>';
		elseif ($post['can_remove'] && ($context['topic_first_message'] != $post['id']))
			echo '
							<li><a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '" data-confirm="', $txt['remove_message_question'], '" class="you_sure"><span class="generic_icons remove_button"></span>', $txt['remove'], '</a></li>';

		// What about splitting it off the rest of the topic?
		if ($context['can_split'] && !empty($context['real_num_replies']))
			echo '
							<li><a href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $post['id'], '"><span class="generic_icons split_button"></span>', $txt['split'], '</a></li>';

		// Can we issue a warning because of this post? Remember, we can't give guests warnings.
		if ($context['can_issue_warning'] && !$post['is_message_author'] && !$post['member']['is_guest'])
			echo '
							<li><a href="', $scripturl, '?action=profile;area=issuewarning;u=', $post['member']['id'], ';msg=', $post['id'], '"><span class="generic_icons warn_button"></span>', $txt['issue_warning'], '</a></li>';

		// Can we restore topics?
		if ($context['can_restore_msg'])
			echo '
							<li><a href="', $scripturl, '?action=restoretopic;msgs=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons restore_button"></span>', $txt['restore_message'], '</a></li>';

		// Maybe we can approve it, maybe we should?
		if ($post['can_approve'])
			echo '
							<li><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons approve_button"></span>', $txt['approve'], '</a></li>';

		// Maybe we can unapprove it?
		if ($post['can_unapprove'])
			echo '
							<li><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons unapprove_button"></span>', $txt['unapprove'], '</a></li>';

		echo '
						</ul>
					</div>';
	}
}

function ces_typelinks($tgs)
{
	global $scripturl, $txt;

	$all = '';
	if(!empty($tgs['gallerylink']))
	{
		$a = explode(',',$tgs['gallerylink']);
		$all .= '<a href="' . $scripturl . '?topic=' . $a[0] . '" class="typelink gallerylink"><span class="icon-image" style="color: #888;"></span> ' . $a[1] . '</a>';
	}	
	if(!empty($tgs['filelink']))
	{
		$a = explode(',',$tgs['filelink']);
		$all .= '<a href="' . $scripturl . '?topic=' . $a[0] . '" class="typelink filelink"><span class="icon-clip" style="color: #888;"></span> ' . $a[1] . '</a>';
	}	
	if(!empty($tgs['doclink']))
	{
		$a = explode(',',$tgs['doclink']);
		$all .= '<a href="' . $scripturl . '?topic=' . $a[0] . '" class="typelink doclink"><span class="icon-book" style="color: #888;"></span> ' . $a[1] . '</a>';
	}	
	if(!empty($all))
		echo '
	<fieldset><legend style="opacity: 0.3;">' , $txt['ces_linked'] , '</legend>', $all ,'</fieldset>';
}

function ces_displaycomments($post)
{
	global $scripturl, $context, $txt, $settings;
	
	echo '
	<div class="bwgrid">
		<div class="bwcell4">&nbsp;</div>
		<div class="bwcell12">
			<a href="' , $post['member']['href'] , '" class="floatright">	
				<img src="' , $post['member']['avatar']['href']. '" alt="" class="avatar_blog" />
			</a>
			<h3 class="blogheader">
				' , !empty($post['is_new']) ? '<span class="new_icon"></span>' : '' , '
				<span class="h3date">' , $post['time'] , '  <span class="smaller">' , $txt['by'] , '</span><a href="' , $post['member']['href'] , '"><b>' ,$post['member']['name'] , '</b></a></span>
			</h3>
			<div class="blogcomment">' , $post['body'] , '</div>
			<div>' ,  isset($post['likes']) ? '&nbsp;&nbsp;' . $post['likes']['count'] . ' ' . $txt['likes'] : '' , '</div><br>';


	// Show the quickbuttons, for various operations on posts.
	if ($post['can_approve'] || $post['can_unapprove'] || $context['can_reply'] || $post['can_modify'] || $post['can_remove'] || $context['can_split'] || $context['can_restore_msg'] || $context['can_quote'] || $context['can_report_moderator'])
	{
		echo '
			<div class="generic_menu rootmenu qbuttons" style="opacity: 0.7;">
				<ul class="menu_nav nolist dropmenu">';

		// Can they quote? if so they can select and quote as well!
		if ($context['can_quote'])
			echo '
					<li><a href="', $scripturl, '?action=post;quote=', $post['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '" onclick="return oQuickReply.quote(', $post['id'], ');"><span class="generic_icons quote"></span>', $txt['quote_action'], '</a></li>
					<li style="display:none;" id="quoteSelected_', $post['id'], '"><a href="javascript:void(0)"><span class="generic_icons quote_selected"></span>', $txt['quote_selected_action'], '</a></li>';

		// Can the user modify the contents of this post? Show the modify inline image.
		if ($post['can_modify'])
			echo '
					<li class="quick_edit"><a href="javascript:void(0)" title="', $txt['modify_msg'], '" class="modifybutton" id="modify_button_', $post['id'], '" onclick="oQuickModify.modifyMsg(\'', $post['id'], '\', \'', !empty($modSettings['toggle_subject']), '\')"><span class="generic_icons quick_edit_button"></span>', $txt['quick_edit'], '</a></li>';

		if ($post['can_approve'] || $post['can_unapprove'] || $post['can_modify'] || $post['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
					<li class="post_options"><a href="#" onclick="fPop_toggle(\'#qmore'.$post['id'].'\'); return false;">', $txt['post_options'],'</a></li>';

		// Maybe they want to report this post to the moderator(s)?
		if ($context['can_report_moderator'])
			echo '
					<li class="report_link"><a href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $post['counter'], ';msg=', $post['id'], '">', $txt['report_to_mod'], '</a></li>';

		if ($post['can_approve'] || $context['can_reply'] || $post['can_modify'] || $post['can_remove'] || $context['can_split'] || $context['can_restore_msg'])
			echo '
				</ul>
			</div>';
		
		echo '	
			<div id="qmore'.$post['id'].'" style="display: none;" class="generic_menu sub_generic">
				<ul class="dropmenu nolist menu_nav">';

		// Can the user modify the contents of this post?
		if ($post['can_modify'])
			echo '
					<li><a href="', $scripturl, '?action=post;msg=', $post['id'], ';topic=', $context['current_topic'], '.', $context['start'], '"><span class="generic_icons modify_button"></span>', $txt['modify'], '</a></li>';

		// How about... even... remove it entirely?!
		if ($context['can_delete'] && ($context['topic_first_message'] == $post['id']))
			echo '
					<li><a href="', $scripturl, '?action=removetopic2;topic=', $context['current_topic'], '.', $context['start'], ';', $context['session_var'], '=', $context['session_id'], '" data-confirm="', $txt['are_sure_remove_topic'], '" class="you_sure"><span class="generic_icons remove_button"></span>', $txt['remove_topic'], '</a></li>';
		elseif ($post['can_remove'] && ($context['topic_first_message'] != $post['id']))
			echo '
					<li><a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '" data-confirm="', $txt['remove_message_question'], '" class="you_sure"><span class="generic_icons remove_button"></span>', $txt['remove'], '</a></li>';

		// What about splitting it off the rest of the topic?
		if ($context['can_split'] && !empty($context['real_num_replies']))
			echo '
					<li><a href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $post['id'], '"><span class="generic_icons split_button"></span>', $txt['split'], '</a></li>';

		// Can we issue a warning because of this post? Remember, we can't give guests warnings.
		if ($context['can_issue_warning'] && !$post['is_message_author'] && !$post['member']['is_guest'])
			echo '
					<li><a href="', $scripturl, '?action=profile;area=issuewarning;u=', $post['member']['id'], ';msg=', $post['id'], '"><span class="generic_icons warn_button"></span>', $txt['issue_warning'], '</a></li>';

		// Can we restore topics?
		if ($context['can_restore_msg'])
			echo '
					<li><a href="', $scripturl, '?action=restoretopic;msgs=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons restore_button"></span>', $txt['restore_message'], '</a></li>';

		// Maybe we can approve it, maybe we should?
		if ($post['can_approve'])
			echo '
					<li><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons approve_button"></span>', $txt['approve'], '</a></li>';

		// Maybe we can unapprove it?
		if ($post['can_unapprove'])
			echo '
					<li><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $post['id'], ';', $context['session_var'], '=', $context['session_id'], '"><span class="generic_icons unapprove_button"></span>', $txt['unapprove'], '</a></li>';

		echo '
				</ul>
			</div>';
	}
				
	echo '
		</div>
	</div><br>';
}

function ces_getuserinfo($id)
{
	global $scripturl, $txt, $settings, $smcFunc, $context;

	$request = $smcFunc['db_query']('substring', '
		SELECT
			mem.id_member, mem.avatar, COALESCE(af.id_attach, 0) AS first_member_id_attach, af.filename AS first_member_filename, af.attachment_type AS first_member_attach_type, 
			mem.real_name, mem.posts, mem.id_group, mem.id_post_group, mg.group_name AS member_group,mgp.group_name AS poster_group 
		FROM {db_prefix}members AS mem
			LEFT JOIN {db_prefix}attachments AS af ON (af.id_member = mem.id_member)
			LEFT JOIN {db_prefix}membergroups AS mg ON (mem.id_group = mg.id_group)
			LEFT JOIN {db_prefix}membergroups AS mgp ON (mem.id_post_group = mgp.id_group)
		WHERE mem.id_member = ' . $id . '
		LIMIT 1'
	);
	$return = array();
	if($smcFunc['db_num_rows']($request)>0) 
	{	
		$row = $smcFunc['db_fetch_assoc']($request);

		// Build the array.
		$return = array(
			'id' => $row['id_member'],
			'name' => $row['real_name'],
			'posts' => $row['posts'],
			'group' => !empty($row['member_group']) ? $row['member_group'] : $row['poster_group'],
			'href' => $scripturl . '?action=profile;u=' . $row['id_member'],
			'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>'
		);
		$return['avatar'] = set_avatar_data(array(
			'avatar' => $row['avatar'],
			'filename' => !empty($row['first_member_filename']) ? $row['first_member_filename'] : '',
		));
		$avy = $return['avatar']['href'];
		if(substr($avy,strlen($avy)-12)=='/default.png')
			$return['avatar']['href'] = $settings['images_url'].'/avatar.png';
	}
	return $return;	
}

function ces_topics($type)
{
	global $scripturl, $txt, $user_info, $settings;
	global $modSettings, $smcFunc, $context;

	// check if keywords have been set
	if(isset($_GET['top']))
	{
		$order='ORDER BY t.num_views DESC';
		$context['cesblog_header'] = $txt['ces_showtop_'.$type];
	}
	else
		$order='ORDER BY m.poster_time DESC';
	
	// check if a board is set
	if(isset($_GET['b']) && is_numeric($_GET['b']) && in_array($_GET['b'], explode(',',$settings[$type.'_boards'])))
	{
		$brd=' AND b.id_board = '. $_GET['b'];
		$current_cesboard = $_GET['b'];
		// we also need to be able to create stuff...

		$context['ces_can_post_new'] = allowedTo('post_new',$current_cesboard) || ($modSettings['postmod_active'] && allowedTo('post_unapproved_topics',$current_cesboard));

	}
	else
		$brd='AND b.id_board IN(' .$settings[$type.'_boards'] . ')';

	// check if keywords have been set
	if(isset($_GET['u']) && is_numeric($_GET['u']))
	{
		$extra='AND m.id_member = '. $_GET['u'];
		$extra_u = $_GET['u'];
		$context['cesblog']['notops'] = 1;
		// get info about poster
		$context['cesblog_currentmember'] = ces_getuserinfo($_GET['u']);
	}
	else
		$extra='';

	$page = isset($_GET['start']) && is_numeric($_GET['start']) ? $_GET['start'] : 0;

	if(empty($settings['use_'.$type]) || empty($settings[$type.'_boards']))
		redirectexit();

	// get boards
	ces_getboards($type);

	// get the total of blogs first
	$request = $smcFunc['db_query']('substring', '
		SELECT
			Count(m.id_msg) as total
		FROM ({db_prefix}topics AS t,{db_prefix}messages AS m)
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
			LEFT JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
		WHERE {query_wanna_see_board}' . ($modSettings['postmod_active'] ? '
			AND m.approved = {int:is_approved}' : '') . '
			AND t.id_board = b.id_board
			' . $brd. '
			AND m.id_msg = t.id_first_msg
			'.$extra,
		array(
			'current_member' => $user_info['id'],
			'is_approved' => 1,
		)
	);
	$context['cesblog'] = array(	);

	if($smcFunc['db_num_rows']($request)>0) 
	{	
		$row = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);
		$context['page_index'] = constructPageIndex($scripturl . '?action='.$type.(!empty($top) ? ';top' : '').(!empty($extra_u) ? ';u=' : ''), $page, $row['total'], 10);
	}
	// check if a profile was used
	if(!empty($extra_u))
		$context['cesblog_header'] = sprintf($txt['ces_showmember_'.$type], '<a href="' .$scripturl . '?action=profile;u='.$extra_u.'">' . $context['cesblog_currentmember']['name']. '</a>');

	// check if a board was used
	if(!empty($current_cesboard))
		$context['cesblog_header'] = sprintf($txt['ces_showboard_'.$type], $context['cesboards'][$current_cesboard]['name']);

	$request = $smcFunc['db_query']('substring', '
		SELECT
			m.poster_time, m.subject, m.id_topic, m.id_member, m.id_msg, m.id_board, m.likes, b.name AS board_name,
			t.num_replies, t.num_views,
			mem.avatar, COALESCE(af.id_attach, 0) AS first_member_id_attach, af.filename AS first_member_filename, af.attachment_type AS first_member_attach_type, 
			IFNULL(mem.real_name, m.poster_name) AS poster_name, ' . ($user_info['is_guest'] ? '1 AS is_read, 0 AS new_from' : '
			IFNULL(lt.id_msg, IFNULL(lmr.id_msg, 0)) >= m.id_msg_modified AS is_read,
			IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1 AS new_from') . ', m.body, m.smileys_enabled
		FROM ({db_prefix}topics AS t,{db_prefix}messages AS m)
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
			LEFT JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			LEFT JOIN {db_prefix}attachments AS af ON (af.id_member = mem.id_member)
			' . (!$user_info['is_guest'] ? '
			LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = m.id_topic AND lt.id_member = {int:current_member})
			LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = m.id_board AND lmr.id_member = {int:current_member})' : '') . '
		WHERE {query_wanna_see_board}' . ($modSettings['postmod_active'] ? '
			AND m.approved = {int:is_approved}' : '') . '
			AND t.id_board = b.id_board
			' . $brd . '
			AND m.id_msg = t.id_first_msg
			' . $extra . '
		' . $order . '
		LIMIT ' .$page . ',10',
		array(
			'current_member' => $user_info['id'],
			'is_approved' => 1,
		)
	);
	$context['posts'] = array(); $messages = array();$topics = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$row['body'] = parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']);
		$row['cesparams'] = ces_parsebbc($row['body']);
		// Censor it!
		censorText($row['subject']);
		censorText($row['body']);

		// Build the array.
		$context['posts'][$row['id_msg']] = array(
			'id' => $row['id_msg'],
			'board' => array(
				'id' => $row['id_board'],
				'name' => $row['board_name'],
				'href' => $scripturl . '?board=' . $row['id_board'] . '.0',
				'link' => '<a href="' . $scripturl . '?board=' . $row['id_board'] . '.0">' . $row['board_name'] . '</a>'
			),
			'topic' => $row['id_topic'],
			'poster' => array(
				'id' => $row['id_member'],
				'name' => $row['poster_name'],
				'href' => empty($row['id_member']) ? '' : $scripturl . '?action=profile;u=' . $row['id_member'],
				'link' => empty($row['id_member']) ? $row['poster_name'] : '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>'
			),
			'cesparams' => $row['cesparams'],
			'num_views' => $row['num_views'],
			'num_replies' => $row['num_replies'],
			'subject' => $row['subject'],
			'body' => $row['body'],
			'time' => timeformat($row['poster_time']),
			'timestamp' => forum_time(true, $row['poster_time']),
			'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . ';topicseen#new',
			'link' => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'] . '" rel="nofollow">' . $row['subject'] . '</a>',
			'new' => !empty($row['is_read']),
			'is_new' => empty($row['is_read']),
			'new_from' => $row['new_from'],
		);
		$context['posts'][$row['id_msg']]['poster']['avatar'] = set_avatar_data(array(
			'avatar' => $row['avatar'],
			'filename' => !empty($row['first_member_filename']) ? $row['first_member_filename'] : '',
		));
		$messages[] = $row['id_msg'];
		$topics[$row['id_msg']] = $row['id_topic'];
		$avy = $context['posts'][$row['id_msg']]['poster']['avatar']['href'];
		if(substr($avy,strlen($avy)-12)=='/default.png')
			$context['posts'][$row['id_msg']]['poster']['avatar']['href'] = $settings['images_url'].'/avatar.png';
		
		// Get the likes for each message.
		if (!empty($modSettings['enable_likes']))
			$context['posts'][$row['id_msg']]['likes'] = array(
				'count' => $row['likes'],
			);
	}
	$smcFunc['db_free_result']($request);

	$context['loaded_attachments'] = array();

	// If there _are_ messages here... (probably an error otherwise :!)
	if (!empty($messages))
	{
		// Fetch attachments.
		if (!empty($modSettings['attachmentEnable']) && allowedTo('view_attachments'))
		{
			$request = $smcFunc['db_query']('', '
				SELECT
					a.id_attach, a.id_folder, a.id_msg, a.filename, a.file_hash, COALESCE(a.size, 0) AS filesize, a.downloads, a.approved,
					a.attachment_type, a.fileext,
					a.width, a.height' . (empty($modSettings['attachmentShowImages']) || empty($modSettings['attachmentThumbnails']) ? '' : ',
					COALESCE(thumb.id_attach, 0) AS id_thumb, thumb.width AS thumb_width, thumb.height AS thumb_height') . '
				FROM {db_prefix}attachments AS a' . (empty($modSettings['attachmentShowImages']) || empty($modSettings['attachmentThumbnails']) ? '' : '
					LEFT JOIN {db_prefix}attachments AS thumb ON (thumb.id_attach = a.id_thumb)') . '
				WHERE a.id_msg IN ({array_int:message_list})
				ORDER BY a.id_attach ASC',
				array(
					'message_list' => $messages,
					'attachment_type' => 0,
					'is_approved' => 1,
				)
			);
			$temp = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				if (!$row['approved'] && $modSettings['postmod_active'] && !allowedTo('approve_posts') && (!isset($all_posters[$row['id_msg']]) || $all_posters[$row['id_msg']] != $user_info['id']))
					continue;

				$temp[$row['id_attach']] = $row;

				if (!isset($context['loaded_attachments'][$row['id_msg']]))
					$context['loaded_attachments'][$row['id_msg']] = '';
			}
			$smcFunc['db_free_result']($request);

			ksort($temp);
			$context['loaded_attachments_total']= array();

			foreach ($temp as $b => $row)
			{
				if(!isset(	$context['loaded_attachments_total'][$row['id_msg']]))
					$context['loaded_attachments_total'][$row['id_msg']]=0;

				$context['loaded_attachments_total'][$row['id_msg']]++;

				if($type=='files')
					$context['loaded_attachments'][$row['id_msg']] = $scripturl . '?action=dlattach;topic=' . $topics[$row['id_msg']] . '.0;attach=' . $b;
				else
				{
					if(in_array($row['fileext'],array('jpg','png','jpeg')) && $row['attachment_type']==0)
						$context['loaded_attachments'][$row['id_msg']] = $scripturl . '?action=dlattach;topic=' . $topics[$row['id_msg']] . '.0;attach=' . $b.';image';
				}
			}
		}
	}
}

function ces_parsebbc($body)
{
	global $context, $txt, $settings;

	$tags = array();
	$a = explode('[ces',$body);
	if(!isset($a[1]))
		return;

	unset($a[0]);
	foreach($a as $b => $data)
	{
		$what=substr($data,7, strpos($data,']')-7);
		$start = strpos($data, ']')+1;
		$end = strpos($data,'[/ces]');
		$tags[$what] = substr($data,$start,$end-$start);
	}

	return $tags;
}

function ces_getboards($type)
{
	global $smcFunc, $context, $settings;

	$request = $smcFunc['db_query']('substring', '
		SELECT
			b.id_board, b.name, b.id_parent, b.num_topics, b.unapproved_topics
		FROM {db_prefix}boards AS b
		WHERE {query_wanna_see_board}
			AND b.id_board IN(' .$settings[$type.'_boards'] . ')'
		);
	$context['cesboards'] = array();

	if($smcFunc['db_num_rows']($request)>0) 
	{	
		while($row = $smcFunc['db_fetch_assoc']($request))
			$context['cesboards'][$row['id_board']] = $row;

		$smcFunc['db_free_result']($request);
	}
}


function cesthemes($current_id)
{
	global $context, $txt, $smcFunc, $settings;

	// check updates
	if($context['user']['is_admin'])
	{
		// check if other blocthemes exists
		$request = $smcFunc['db_query']('substring', '
			SELECT
				t.id_theme, t.variable,tn.value as name
			FROM {db_prefix}themes AS t
				LEFT JOIN {db_prefix}themes AS tn ON (tn.id_theme = t.id_theme && tn.variable="name")
			WHERE t.id_member = 0
				AND t.id_theme != ' . $current_id . '
				AND t.variable IN("use_' .(implode('","use_', $settings['ces_boardtypes'])). '")
				');
		
		$a = array();
		if($smcFunc['db_num_rows']($request)>0) 
		{	
			while($row = $smcFunc['db_fetch_assoc']($request))
				$a[$row['id_theme']] = $row['name'];

			$smcFunc['db_free_result']($request);
		}
	}
	return $a;
}

?>