<?php
// Version: 2.0 RC3; Recent

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<div id="recent" class="main_section">
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/post/xx.gif" alt="" class="icon" />',$txt['recent_posts'],'</span>
			</h3>
		</div>
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

	foreach ($context['posts'] as $post)
	{
		echo '
			<div class="', $post['alternate'] == 0 ? 'windowbg' : 'windowbg2', ' core_posts">
				<span class="topslice"><span></span></span>
				<div class="content">
					<div class="counter">', $post['counter'], '</div>
					<div class="topic_details">
						<h5>', $post['board']['link'], ' / ', $post['link'], '</h5>
						<span class="smalltext">&#171;&nbsp;', $txt['last_post'], ' ', $txt['by'], ' <strong>', $post['poster']['link'], ' </strong> ', $txt['on'], '<em> ', $post['time'], '</em>&nbsp;&#187;</span>
					</div>
					<div class="list_posts">', $post['message'], '</div>
				</div>';

		if ($post['can_reply'] || $post['can_mark_notify'] || $post['can_delete'])
			echo '
				<div class="quickbuttons_wrap">
					<ul class="reset smalltext quickbuttons">';

		// If they *can* reply?
		if ($post['can_reply'])
			echo '
						<li class="reply_button"><a href="', $scripturl, '?action=post;topic=', $post['topic'], '.', $post['start'], '"><span>', $txt['reply'], '</span></a></li>
						<li class="quote_button"><a href="', $scripturl, '?action=post;topic=', $post['topic'], '.', $post['start'], ';quote=', $post['id'], '"><span>', $txt['quote'], '</span></a></li>';

		// Can we request notification of topics?
		if ($post['can_mark_notify'])
			echo '
						<li class="notify_button"><a href="', $scripturl, '?action=notify;topic=', $post['topic'], '.', $post['start'], '"><span>', $txt['notify'], '</span></a></li>';

		// How about... even... remove it entirely?!
		if ($post['can_delete'])
			echo '
						<li class="remove_button"><a href="', $scripturl, '?action=deletemsg;msg=', $post['id'], ';topic=', $post['topic'], ';recent;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['remove_message'], '?\');"><span>', $txt['remove'], '</span></a></li>';

		if ($post['can_reply'] || $post['can_mark_notify'] || $post['can_delete'])
			echo '
					</ul>
				</div>';

		echo '
				<span class="botslice clear"><span></span></span>
			</div>';

	}

	echo '
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>
	</div>';
}

function template_unread()
{
	global $settings;
	call_user_func('unread_'.$settings['themealias']);
}

function unread_facebook()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
	<h2 class="section">' , !$context['showing_all_topics'] ? $txt['unread_topics_visit'] : $txt['unread_topics_all']  , '</h2>
	<span class="smalllinks" style="display: block; float: right; clear: right; padding: 0 4px 4px 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>
	';

	if ($settings['show_mark_read'])
	{
		// Generate the button strip.
		$mark_read = array(
			'markread' => array(
				'text' => !empty($context['no_board_limits']) ? 'mark_as_read' : 'mark_read_short', 
				'url' => $scripturl . '?action=markasread;sa=' . (!empty($context['no_board_limits']) ? 'all' : 'board' . $context['querystring_board_limits']) . ';' . $context['session_var'] . '=' . $context['session_id']
			),
		);
	}
	if (!empty($context['topics']) && !$context['showing_all_topics'])
		$mark_read['readall'] = array(
			'text' => 'unread_topics_all', 
			'active' => true,
			'url' => $scripturl . '?action=unread;all' . $context['querystring_board_limits'], 'active' => true
		);

	// Are there actually any topics to show?
	if (!empty($context['topics']))
	{
		if(!empty($mark_read))
			echo '<div style="overflow: hidden; margin-bottom: 1em; clear: both;">' , mini_template_button_strip($mark_read, 'right'), '</div>';

		// get the avatars
		$tops = array(); $ids = array(); $tauthors = array();
		foreach($context['topics'] as $t => $topic)
		{
			$tops[$t] = '
			<div class="mini_topictable">
				' . protimeformat($topic['last_post']['timestamp']) . ' <span class="whop">'.$topic['last_post']['member']['link'].'</span> '.$txt['mini-wrote'].' '.$txt['in'].' ' . $topic['last_post']['link'] . '
				 &nbsp;&nbsp;<span class="smalllinks">' . $topic['pages'] . '</span>
				<span style="float: right;">
					' . ($topic['is_sticky'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/sticky.png" alt="*" />' : '') . '
					' . ($topic['is_locked'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/locked.png" alt="*" />' : '') . '
					' . ($topic['is_posted_in'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/comment.png" alt="*" />' : '') . '
				</span>
				' . (empty($options['show_unreadpreview']) ? '<div class="smalltext" style="padding-left: 44px;">' . $topic['last_post']['preview'] .'</div>' : '') . '
			</div>';
			$tauthors[$t] = $ids[] = $topic['last_post']['member']['id'];
		}
		$avy = progetAvatars($ids); 
		foreach($tops as $t => $op)
		{
			echo '<div class="topictable"><div class="avatar16h2">' . (!empty($avy[$tauthors[$t]]) ? $avy[$tauthors[$t]] : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />') . '</div>' . $op . '</div>';
		}
		echo '
		<div style="margin-top: 1em;border-top: solid 1px #ccc; overflow: hidden; clear: both;"><span class="smalllinks" style="display: block; float: right; clear: right; padding: 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>';
		if(!empty($mark_read))
			echo '<div style="clear: both;">', 	mini_template_button_strip($mark_read, 'right'), '</div>';
		echo '
		</div>';
	}
	else
		echo '
			<p>', $context['showing_all_topics'] ? $txt['msg_alert_none'] : $txt['unread_topics_visit_none'], '</p';
}
function unread_chat()
{
}

function template_replies()
{
	global $settings;
	call_user_func('replies_'.$settings['themealias']);
}

function replies_facebook()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
	<h2 class="section">' , $txt['unread_replies']  , '</h2>
	<span class="smalllinks" style="display: block; float: right; clear: right; padding: 0 4px 4px 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>
	';

	if ($settings['show_mark_read'])
	{
		// Generate the button strip.
		$mark_read = array(
			'markread' => array(
				'text' => !empty($context['no_board_limits']) ? 'mark_as_read' : 'mark_read_short', 
				'url' => $scripturl . '?action=markasread;sa=' . (!empty($context['no_board_limits']) ? 'all' : 'board' . $context['querystring_board_limits']) . ';' . $context['session_var'] . '=' . $context['session_id']
			),
		);
	}

	// Are there actually any topics to show?
	if (!empty($context['topics']))
	{
		// get the avatars
		$tops = array(); $ids = array(); $tauthors = array();
		foreach($context['topics'] as $t => $topic)
		{
			$tops[$t] = '
			<div class="mini_topictable">
				' . protimeformat($topic['last_post']['timestamp']) . ' <span class="whop">'.$topic['last_post']['member']['link'].'</span> '.$txt['mini-wrote'].' '.$txt['in'].' ' . $topic['last_post']['link'] . '
				 &nbsp;&nbsp;<span class="smalllinks">' . $topic['pages'] . '</span>
				<span style="float: right;">
					' . ($topic['is_sticky'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/sticky.png" alt="*" />' : '') . '
					' . ($topic['is_locked'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/locked.png" alt="*" />' : '') . '
					' . ($topic['is_posted_in'] ? '<img class="nofloat" src="'.$settings['images_url'].'/theme/comment.png" alt="*" />' : '') . '
				</span>
				' . (empty($options['show_unreadpreview']) ? '<div class="smalltext">' . $topic['last_post']['preview'] .'</div>' : '') . '
			</div>';
			$tauthors[$t] = $ids[] = $topic['last_post']['member']['id'];
		}
		$avy = progetAvatars($ids); 
		foreach($tops as $t => $op)
		{
			echo '<div class="topictable"><div class="avatar16h2">' . (!empty($avy[$tauthors[$t]]) ? $avy[$tauthors[$t]] : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />') . '</div>' . $op . '</div>';
		}
		echo '
		<div style="margin-top: 1em;border-top: solid 1px #ccc; overflow: hidden; "><span class="smalllinks" style="display: block; float: right; clear: right; padding: 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>';
		if(!empty($mark_read))
			echo 	'<div style="clear: both;">', mini_template_button_strip($mark_read, 'right'), '</div>';
		echo '
		</div>';
	}
	else
		echo '
			<p>', $context['showing_all_topics'] ? $txt['msg_alert_none'] : $txt['unread_topics_visit_none'], '</p';
}

function replies_chat()
{
}
?>