<?php
// Version: 2.0 RC3; MessageIndex

function mini_sidebar()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if (!empty($context['boards']))
	{
		$bboards = array();
		foreach ($context['boards'] as $board)
			$bboards[] = '<a href="' . ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?board=' . $board['id'] . '.0'). '">' . $board['name'] . '
			' . ($board['new'] || $board['children_new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). '</a>';
		
		$title = $txt['mini-boards'];
		$rest = count($bboards)-8;
		if($rest < 0) $rest = '';
		mini_leftblock($title, $bboards, 'normal', 8, $rest, 'proboards');
	}
	if(!isset($options['hotdate']))
		$options['hotdate'] = 7;
	$opt = array(1 => 'day', 7 => 'week',30 => 'month',180 => '6month',365 => 'year');
	mini_leftblock('<span style="float: right;color: #888;" class="smalltext"><a href="'.$scripturl.'?action=profile;area=theme;u='.$context['user']['id'].'#hotdate">' . $txt['mini-hotdate-'.$opt[$options['hotdate']]] . '</a></span>' . $txt['mini-toptopics'], mini_topTopics(10,$context['current_board'], (time() - (86400*$options['hotdate']))));
}

function template_main()
{
	global $settings;
	call_user_func('messageindex_'.$settings['themealias']);
}

function messageindex_facebook()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	if(!empty($settings['useportal']))
	{
		if (!empty($context['boards']))
		{
			$bboards = array();
			foreach ($context['boards'] as $board)
				$bboards[] = '<a href="' . ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?board=' . $board['id'] . '.0'). '">' . $board['name'] . '
				' . ($board['new'] || $board['children_new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). '</a>';
			
			$title = $txt['mini-boards'];
			$rest = count($bboards)-8;
			if($rest < 0) $rest = '';
			mini_mainblock($title, $bboards, 'normal', 8, $rest, 'proboards');
		}
		if(!isset($options['hotdate']))
			$options['hotdate'] = 7;
		$opt = array(1 => 'day', 7 => 'week',30 => 'month',180 => '6month',365 => 'year');
		mini_mainblock('<span style="float: right;color: #888;" class="smalltext"><a href="'.$scripturl.'?action=profile;area=theme;u='.$context['user']['id'].'#hotdate">' . $txt['mini-hotdate-'.$opt[$options['hotdate']]] . '</a></span>' . $txt['mini-toptopics'], mini_topTopics(10,$context['current_board'], (time() - (86400*$options['hotdate']))));
	}

	// Create the button set...
	$normal_buttons = array(
		'new_topic' => array(			
			'test' => 'can_post_new', 
			'text' => 'new_topic', 
			'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0', 
			'active' => true,
			'subtemplate' => 'mini_quickpost',
		),
		'post_poll' => array(
			'test' => 'can_post_poll', 
			'text' => 'new_poll', 
			'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll',
		),
		'notify' => array(
			'test' => 'can_mark_notify', 
			'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 
			'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 
			'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']
		),
		'markread' => array(
			'text' => 'mark_read_short', 
			'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;' . $context['session_var'] . '=' . $context['session_id']
		),
	);

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($normal_buttons['markread']);

	if (!$context['no_topic_listing'])
	{
		echo mini_template_button_strip($normal_buttons, 'right');
		
		echo '
		<h2 class="section">' , $context['mini-last']  , '</h2>	
		<span class="smalllinks" style="display: block; clear: both; padding: 4px 4px 12px 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>	';

		if (!empty($options['show_board_desc']) && $context['description'] != '')
			echo '
		<p class="mini-description">', $context['description'], '</p>';

		if (!empty($context['topics']))
		{
			// get the avatars
			$tops = array(); $ids = array(); $tauthors = array(); $stickies=array();
			foreach($context['topics'] as $t => $topic)
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

	
		if (!empty($settings['display_who_viewing']))
		{
			echo '
						<tr class="windowbg2 whos_viewing">
							<td colspan="', !empty($context['can_quick_mod']) ? '6' : '5', '" class="smalltext">';
			if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'], '
							</td>
						</tr>';
		}

		// any pages?
		echo '
		<hr class="divider2" />
		<div class="smalltext" style="color: #888; padding: 0.3em; float: right;">
			<img src="'.$settings['images_url']. '/theme/sticky.png" alt="" />&nbsp;&nbsp; '. $txt['sticky_topic'].'&nbsp;&nbsp;
			<img src="'.$settings['images_url']. '/theme/locked.png" alt="" /> &nbsp;&nbsp;'. $txt['locked_topic'].'&nbsp;&nbsp;
			<img src="'.$settings['images_url']. '/theme/comment.png" alt="" />&nbsp;&nbsp; '. $txt['participation_caption'].'
		</div>
		<span class="smalllinks" style="display: block; padding: 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>';
	}
}

function messageindex_chat()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	if (!empty($context['boards']))
	{
		$bboards = array();
		foreach ($context['boards'] as $board)
			$bboards[] = '<a href="' . ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?board=' . $board['id'] . '.0'). '">' . $board['name'] . '
			' . ($board['new'] || $board['children_new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). '</a>';
		
		$title = $txt['mini-boards'];
		$rest = count($bboards)-8;
		if($rest < 0) $rest = '';
		mini_mainblock($title, $bboards, 'normal', 8, $rest, 'proboards');
		echo '
		<div class="divid"></div>';
	}

	// Create the button set...
	$normal_buttons = array(
		'new_topic' => array(			
			'test' => 'can_post_new', 
			'text' => 'new_topic', 
			'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0', 
			'active' => true,
			'subtemplate' => 'mini_quickpost',
		),
		'post_poll' => array(
			'test' => 'can_post_poll', 
			'text' => 'new_poll', 
			'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll',
		),
		'notify' => array(
			'test' => 'can_mark_notify', 
			'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 
			'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 
			'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']
		),
		'markread' => array(
			'text' => 'mark_read_short', 
			'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;' . $context['session_var'] . '=' . $context['session_id']
		),
	);

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($normal_buttons['markread']);

	if (!$context['no_topic_listing'])
	{
		echo mini_template_button_strip($normal_buttons, 'right');
		
		echo '
		<h2 class="section">' , $context['mini-last']  , '</h2>	
		<span class="smalllinks" style="display: block; clear: both; padding: 4px 4px 12px 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>	';

		if (!empty($options['show_board_desc']) && $context['description'] != '')
			echo '
		<p class="mini-description">', $context['description'], '</p>';

		if (!empty($context['topics']))
		{
			// get the avatars
			$tops = array(); $ids = array(); $tauthors = array(); $stickies=array();
			foreach($context['topics'] as $t => $topic)
			{
				echo '
				<div class="mini_topictable" style="' , $topic['is_sticky'] ? 'font-weight: bold;' : '' , '' , $topic['is_locked'] ? 'font-style: italic;' : '' , '' , $topic['new'] ? 'color: #070;' : '' , '">
					 ' . $topic['last_post']['link'] . ' <span class="whop">'.$topic['last_post']['member']['link'].' '.$txt['mini-wrote'].' ' . protimeformat($topic['last_post']['timestamp']) . ' </span>
					' . ($topic['new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). ' &nbsp;&nbsp;<span class="smalllinks">' . $topic['pages'] . '</span>
				</div><hr class="divider3" />';
			}
		}
		else
			echo '
				<p>', $txt['msg_alert_none'], '</p';

	
		if (!empty($settings['display_who_viewing']))
		{
			echo '
						<tr class="windowbg2 whos_viewing">
							<td colspan="', !empty($context['can_quick_mod']) ? '6' : '5', '" class="smalltext">';
			if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'], '
							</td>
						</tr>';
		}

		// any pages?
		echo '<br>
		<span class="smalllinks" style="display: block; padding: 4px;">', $txt['pages'], ' ', str_replace(array('[',']'),array('&nbsp;','&nbsp;'),$context['page_index']), '&nbsp;</span>';
	}
}


?>