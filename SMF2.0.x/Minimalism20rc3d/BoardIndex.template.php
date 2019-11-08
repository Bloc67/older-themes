<?php
// Version: 2.0 RC3; BoardIndex

function mini_sidebar()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $smcFunc;

	$bboards = array();
	foreach ($context['categories'] as $category)
	{
		$bboards[] = '<span class="catsection">'. ($category['is_collapsed'] ? '<a href="'.$category['collapse_href'].'">+</a> ' : '') . $category['link']. '</span>';
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

		if (!$category['is_collapsed'])
		{
			foreach ($category['boards'] as $board)
			{
				$bboards[$board['id']] = '<a href="' . ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?board=' . $board['id'] . '.0'). '">' . $board['name'] . '</a>
				' . ($board['new'] || $board['children_new'] ? ' &nbsp;<span class="pronew">'.$txt['new'].'</span>' : ''). '';
				if(!empty($board['children']))
				{
					$bboards[$board['id']] .= '<a href="#" id="a'.$board['id'].'" title="'.$txt['mini-showchildren'].'" onclick="toggleb(\''.$board['id'].'\'); return false;">+</a>
					<div id="cb'.$board['id'].'" style="display: none;">';
					foreach($board['children'] as $ch)
						$bboards[$board['id']] .= '<div class="prochilds"><a href="'.$ch['href'].'">- ' . $ch['name'] .' ' . ($ch['new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). '</a></div>';

					$bboards[$board['id']] .= '
					</div>';
				}
			}
		}
	}
	$title = $txt['mini-boards'];
	$rest = count($bboards)-8;
	if($rest < 0) $rest = '';
	mini_leftblock($title, $bboards, 'normal', 8, $rest, 'proboards');
	if(!empty($context['news_lines']))
		mini_leftblock($txt['news'], $context['news_lines'], 'scroller');
	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
			mini_leftblock($txt['mini-holidays'], $context['calendar_holidays'], 'normal');

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))
		{
			$birthdays = array();			
			foreach ($context['calendar_birthdays'] as $member)
				$birthdays[] = '
			<a class="bluey" href="'. $scripturl. '?action=profile;u='. $member['id']. '">'. $member['name']. '</a>&nbsp;' . $txt['mini-is']. ' ' . $member['age'] . ' '. ($member['is_today'] ? '<b>'.$txt['mini-today'].'</b>' : $txt['mini-soon']);

			mini_leftblock($smcFunc['substr']($txt['birthdays'],0,$smcFunc['strlen']($txt['birthdays'])-1), $birthdays, 'normal');
		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			$events = array();
			foreach ($context['calendar_events'] as $event)
				$events[] = '
					' . ( $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">' ) . $event['title'] . ($event['href'] == '' ? '' : '</a>' );
			mini_leftblock($context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], $events, 'normal');
		}
	}
}

function mini_extramenu()
{
	global $txt, $scripturl, $context;
	
	$menu = array();
	if($context['user']['is_logged'])
		$menu['markasread'] = array(
				'title' => $txt['mark_as_read'],
				'href' => $scripturl . '?action=markasread;sa=all' . $context['session_var'] . '=' . $context['session_id'],
				'show' => true,
				'active_button' => $context['current_action'] == 'markasread',
				'sub_buttons' => array(
				),
			);
	return $menu;
}

function template_main()
{
	global $settings;
	call_user_func('boardindex_'.$settings['themealias']);
}

function boardindex_facebook()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $sourcedir, $smcFunc;

	if(!empty($settings['useportal']))
	{
		$bboards = array();
		foreach ($context['categories'] as $category)
		{
			$bboards[] = '<div class="catsection" >'. ($category['is_collapsed'] ? '<a href="'.$category['collapse_href'].'">+</a> ' : '') . $category['link']. '</div>';
			if (empty($category['boards']) && !$category['is_collapsed'])
				continue;

			if (!$category['is_collapsed'])
			{
				foreach ($category['boards'] as $board)
				{
					$bboards[$board['id']] = '<a href="' . ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?board=' . $board['id'] . '.0'). '">' . $board['name'] . '</a>
					' . ($board['new'] || $board['children_new'] ? ' &nbsp;<span class="pronew">'.$txt['new'].'</span>' : ''). '';
					if(!empty($board['children']))
					{
						$bboards[$board['id']] .= '<a href="#" id="a'.$board['id'].'" title="'.$txt['mini-showchildren'].'" onclick="toggleb(\''.$board['id'].'\'); return false;">+</a>
						<div id="cb'.$board['id'].'" style="display: none;">';
						foreach($board['children'] as $ch)
							$bboards[$board['id']] .= '<div class="prochilds"><a href="'.$ch['href'].'">- ' . $board['name'] .' ' . ($board['new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). '</a></div>';

						$bboards[$board['id']] .= '
						</div>';
					}
				}
			}
		}
		$title = $txt['mini-boards'];
		$rest = count($bboards)-8;
		if($rest < 0) $rest = '';
		mini_mainblock($title, $bboards, 'normal', 8, $rest, 'proboards');
		mini_mainblock($txt['news'], $context['news_lines'], 'scroller');
		// Show information about events, birthdays, and holidays on the calendar.
		if ($context['show_calendar'])
		{
			// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
			if (!empty($context['calendar_holidays']))
				mini_mainblock($txt['mini-holidays'], $context['calendar_holidays'], 'normal');

			// People's birthdays. Like mine. And yours, I guess. Kidding.
			if (!empty($context['calendar_birthdays']))
			{
				$birthdays = array();			
				foreach ($context['calendar_birthdays'] as $member)
					$birthdays[] = '
			<a class="bluey" href="'. $scripturl. '?action=profile;u='. $member['id']. '">'. $member['name']. '</a>&nbsp;' . $txt['mini-is']. ' ' . $member['age'] . ' '. ($member['is_today'] ? '<b>'.$txt['mini-today'].'</b>' : $txt['mini-soon']);

				mini_mainblock($smcFunc['substr']($txt['birthdays'],0,$smcFunc['strlen']($txt['birthdays'])-1), $birthdays, 'normal');
			}
			// Events like community get-togethers.
			if (!empty($context['calendar_events']))
			{
				$events = array();
				foreach ($context['calendar_events'] as $event)
					$events[] = '
						' . ( $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">' ) . $event['title'] . ($event['href'] == '' ? '' : '</a>' );
				mini_mainblock($context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], $events, 'normal');
			}
		}
		echo '<br />';
	}
	
	echo '
	<div class="sectionright" id="mini_recent">';

	if(empty($settings['number_recent_posts']))
		$settings['number_recent_posts'] = 5;

	if($context['user']['is_logged'])
	{
		if(count($user_info['buddies'])>0)
		{
			if(!empty($options['recent_all']))
			{
				if(!isset($_GET['buddies']))
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], false);
					echo '<a href="'.$scripturl.'"><b>'.$txt['all'].'</b></a> &nbsp;<a href="'.$scripturl.'?buddies">'.$txt['buddies'].'</a>';
				}
				else
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], true);
					echo '<a href="'.$scripturl.'">'.$txt['all'].'</a> &nbsp;<a href="'.$scripturl.'?buddies"><b>'.$txt['buddies'].'</b></a>';
				}
			}
			else
			{
				if(!isset($_GET['all']))
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], true);
					echo '<a href="'.$scripturl.'?all">'.$txt['all'].'</a> &nbsp;<a href="'.$scripturl.'"><b>'.$txt['buddies'].'</b></a>';
				}
				else
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], false);
					echo '<a href="'.$scripturl.'?all"><b>'.$txt['all'].'</b></a> &nbsp;<a href="'.$scripturl.'">'.$txt['buddies'].'</a>';
				}
			}
		}
		else
			$recent = mini_recentTopics($settings['number_recent_posts'], false);
	}
	else
		$recent = mini_recentTopics($settings['number_recent_posts'], false);

	echo '
	</div>
	<h2 class="section">' . $txt['mini-recenttopics'] . '</h2>';

	foreach($recent as $re => $rec)
	{
		echo '
	<div class="recent_topic">
		<div class="avatar40h2" style="float: left; margin-top: 4px;">' , !empty($rec['avatar']) ? '<a href="'.$rec['href'].'">'.$rec['avatar'].'</a>' : '<img src="'.$settings['images_url'].'/guest.jpg" alt="*" />' ,'</div>
		<div class="textpart">
			<span class="who">' . $rec['poster']['link'] . ' ' . $txt['mini-wrote'] . '</span> ' . $rec['time'] . ' '.$rec['link'].'' . ($rec['is_new'] ? ' <span class="pronew2">'.$txt['new'].'</span>' : ''). '
			<div class="bodytext">' . $rec['preview'] . '</div>
		</div>
	</div>';
	}
		// Users Online Today
	if(isset($txt['uot_users_online_today']))
	{
		echo '
			<h4 class="titlebg"><span class="left"></span>
				<img class="icon" src="', $settings['images_url'], '/icons/online.gif', '" alt="', $txt['online_users'], '" />', '<span>', $txt['uot_users_online_today'], '</span>
			</h4>
			<p class="inline smalltext">';
		echo
				$txt['uot_total'], ': <b>', $context['num_users_online_today'], '</b>';

			if ($context['viewing_allowed'])
		echo
				' (', $txt['uot_visible'], ': ', ($context['num_users_online_today'] - $context['num_users_hidden_today']), ', ', $txt['uot_hidden'], ': ', $context['num_users_hidden_today'], ')';

				// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
				if (!empty($context['users_online_today']) && $context['viewing_allowed'])
				{
		echo
					'<br />', implode(', ', $context['list_users_online_today']);

					// Showing membergroups?
					if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
		echo
						'<br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
				}
		echo '
			</p>';
	}
}
function boardindex_chat()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $sourcedir, $smcFunc;


	echo '
	<div class="container">
		<div class="col9">
		<h2 class="section2">' , $txt['mini-boards'] , '</h2>';

		foreach ($context['categories'] as $category)
		{
			echo '<h2 class="cats">'. ($category['is_collapsed'] ? '<a href="'.$category['collapse_href'].'">+</a> ' : '') . $category['link']. '</h2>';
			if (empty($category['boards']) && !$category['is_collapsed'])
				continue;

			if (!$category['is_collapsed'])
			{
				echo '<hr class="divider2" />';
				foreach ($category['boards'] as $board)
				{
					echo '<h3 class="cats"><a href="' . ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?board=' . $board['id'] . '.0'). '">' . $board['name'] . '</a>
					' . ($board['new'] || $board['children_new'] ? ' &nbsp;<span class="pronew">'.$txt['new'].'</span>' : ''). '</h3>';
					if(!empty($board['children']))
					{
						echo '
						<div class="cats_children">';
						foreach($board['children'] as $ch)
							echo '<a href="'.$ch['href'].'">' . $ch['name'] .' ' . ($ch['new'] ? ' <span class="pronew">'.$txt['new'].'</span>' : ''). '</a>&nbsp;&nbsp;';

						echo '
						</div>';
					}
				}
			}
		}
	echo '
		</div>
		<div class="col1">&nbsp;</div>
		<div class="col6"><br />
			<div style="float: right;" class="smalltext">';

	if(empty($settings['number_recent_posts']))
		$settings['number_recent_posts'] = 5;

	if($context['user']['is_logged'])
	{
		if(count($user_info['buddies'])>0)
		{
			if(!empty($options['recent_all']))
			{
				if(!isset($_GET['buddies']))
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], false);
					echo '<a href="'.$scripturl.'"><b>'.$txt['all'].'</b></a> &nbsp;<a href="'.$scripturl.'?buddies">'.$txt['buddies'].'</a>';
				}
				else
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], true);
					echo '<a href="'.$scripturl.'">'.$txt['all'].'</a> &nbsp;<a href="'.$scripturl.'?buddies"><b>'.$txt['buddies'].'</b></a>';
				}
			}
			else
			{
				if(!isset($_GET['all']))
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], true);
					echo '<a href="'.$scripturl.'?all">'.$txt['all'].'</a> &nbsp;<a href="'.$scripturl.'"><b>'.$txt['buddies'].'</b></a>';
				}
				else
				{
					$recent = mini_recentTopics($settings['number_recent_posts'], false);
					echo '<a href="'.$scripturl.'?all"><b>'.$txt['all'].'</b></a> &nbsp;<a href="'.$scripturl.'">'.$txt['buddies'].'</a>';
				}
			}
		}
		else
			$recent = mini_recentTopics($settings['number_recent_posts'], false);
	}
	else
		$recent = mini_recentTopics($settings['number_recent_posts'], false);

	echo '</div>
	<h2 class="section">' . $txt['mini-recenttopics'] . '</h2>';

	foreach($recent as $re => $rec)
	{
		echo '
	<div class="recent_topic">
		'.$rec['link'].'' . ($rec['is_new'] ? ' <span class="pronew2">'.$txt['new'].'</span>' : ''). '
	</div>';
	}
		// Users Online Today
	if(isset($txt['uot_users_online_today']))
	{
		echo '
			<h4 class="titlebg"><span class="left"></span>
				<img class="icon" src="', $settings['images_url'], '/icons/online.gif', '" alt="', $txt['online_users'], '" />', '<span>', $txt['uot_users_online_today'], '</span>
			</h4>
			<p class="inline smalltext">';
		echo
				$txt['uot_total'], ': <b>', $context['num_users_online_today'], '</b>';

			if ($context['viewing_allowed'])
		echo
				' (', $txt['uot_visible'], ': ', ($context['num_users_online_today'] - $context['num_users_hidden_today']), ', ', $txt['uot_hidden'], ': ', $context['num_users_hidden_today'], ')';

				// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
				if (!empty($context['users_online_today']) && $context['viewing_allowed'])
				{
		echo
					'<br />', implode(', ', $context['list_users_online_today']);

					// Showing membergroups?
					if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
		echo
						'<br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
				}
		echo '
			</p>';
	}
		mini_mainblock($txt['news'], $context['news_lines'], 'scroller');
		// Show information about events, birthdays, and holidays on the calendar.
		if ($context['show_calendar'])
		{
			// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
			if (!empty($context['calendar_holidays']))
				mini_mainblock($txt['mini-holidays'], $context['calendar_holidays'], 'normal');

			// People's birthdays. Like mine. And yours, I guess. Kidding.
			if (!empty($context['calendar_birthdays']))
			{
				$birthdays = array();			
				foreach ($context['calendar_birthdays'] as $member)
					$birthdays[] = '
			<a class="bluey" href="'. $scripturl. '?action=profile;u='. $member['id']. '">'. $member['name']. '</a>&nbsp;' . $txt['mini-is']. ' ' . $member['age'] . ' '. ($member['is_today'] ? '<b>'.$txt['mini-today'].'</b>' : $txt['mini-soon']);

				mini_mainblock($smcFunc['substr']($txt['birthdays'],0,$smcFunc['strlen']($txt['birthdays'])-1), $birthdays, 'normal');
			}
			// Events like community get-togethers.
			if (!empty($context['calendar_events']))
			{
				$events = array();
				foreach ($context['calendar_events'] as $event)
					$events[] = '
						' . ( $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">' ) . $event['title'] . ($event['href'] == '' ? '' : '</a>' );
				mini_mainblock($context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], $events, 'normal');
			}
		}

	echo '
		</div>
	</div><br><br>';
}

?>