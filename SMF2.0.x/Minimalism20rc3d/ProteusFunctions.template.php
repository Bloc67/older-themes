<?php
// Version: 2.0 RC3; func

function mini_leftblock($name, $menu, $type = 'none', $show='8', $more='', $id='')
{
	global $txt;

	echo '
	<div' , !empty($id) ? ' id="'.$id.'"' : '' ,' class="leftb">
		<h2>' . $name . '</h2>
		<ul>';
	$count = 0;
	foreach($menu as $m => $me)
	{
		if(!empty($show) && $show>$count)
			echo '<li>' , $me , '</li>';
		else
			echo '<li class="hiddenup" style="display: none;">' , $me , '</li>';
		$count++;
	}
	echo '</ul>';
	if(!empty($show) && !empty($more))
		echo '
		<div id="more'.$id.'" style="clear: left;">' . $txt['mini-and'] .' <a href="#" onclick="javascript: showme(\'hiddenup\',\'more'.$id.'\'); return false;">' . $more . ' '.$txt['mini-more'].'.</a></div>';
	
	echo '
	</div>';
}

function mini_mainblock($name, $menu, $type = 'none', $show='8', $more='', $id='')
{
	global $txt;

	echo '
	<div' , !empty($id) ? ' id="'.$id.'"' : '' ,' class="mainb">
		<h2 class="section">' . $name . '</h2>
		<ul>';

	foreach($menu as $m => $me)
	{
		if(!empty($show))
			echo '<li>' , $me , '</li>';
	}
	echo '
		</ul>
	</div>';
}

function mini_leftblock_def($name, $type = 'start')
{
	global $txt;

	if($type == 'start')
		echo '
	<div' , !empty($id) ? ' id="'.$id.'"' : '' ,' class="leftb2">
		<h2>' . $name . '</h2>
		<div>';
	else
		echo '
		</div>
	</div><br />';
}

function mini_template_button_strip($button_strip)
{
	global $settings, $context, $txt, $scripturl;

	echo '
	<div class="probuttons_container">
		<div class="probuttons">';
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if(!empty($value['subtemplate']))
				echo '
		<a id="a'.$value['text'].'" onclick="togglesub(\''.$value['text'].'\'); return false;" href="#" class="inactive"><span' , !empty($value['active']) ? ' class="enhanced"' : '' , '>' . $txt[$value['text']] . '</span></a>';
			else
				echo '
		<a id="a'.$value['text'].'" href="' . $value['url'] . '" class="inactive"', !empty($value['custom']) ? $value['custom'] : '' , '><span' , !empty($value['active']) ? ' class="enhanced"' : '' , '>' . $txt[$value['text']] . '</span></a>';
		}
	}

	echo '
		</div>
		<div  id="spp" class="subprobuttons" style="display: none;">';

	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if(!empty($value['subtemplate']))
				echo '<div id="sub'.$value['text'].'" >' . call_user_func($value['subtemplate']) . '</div>';
		}
	}
	echo '
		</div>
	</div>';
}
function mini_template_button_strip2($button_strip, $texty, $mess)
{
	global $settings, $context, $txt, $scripturl;

	echo '
	<div class="probuttons_container">
		<div class="probuttons">';
	foreach ($button_strip as $key => $value)
	{
		if(!empty($value['subtemplate']))
			echo '
		<a id="a2'.$mess . '_' . $value['text'].'" onclick="togglesubb(\''.$value['text'].'\', \''.$mess.'\'); return false;" href="#" class="inactive"><span' , !empty($value['active']) ? ' class="enhanced"' : '' , '>' . $txt[$value['text']] . '</span></a>';
		else
			echo '
		<a id="a2'. $mess . '_' . $value['text'].'" href="' . $value['url'] . '" class="inactive"', !empty($value['custom']) ? $value['custom'] : '' , '><span' , !empty($value['active']) ? ' class="enhanced"' : '' , '>' . $txt[$value['text']] . '</span></a>';
	}

	echo '
		</div>
		<div  id="spp2' . $mess. '" class="subprobuttons" style="display: none;">';

	foreach ($button_strip as $key => $value)
	{
		if(!empty($value['subtemplate']))
				echo '<div id="sub2'. $mess . '_' . $value['text'].'" >' . call_user_func($value['subtemplate'],$texty) . '</div>';
	}
	echo '
		</div>
	</div>';
}
function mini_template_button_strip3($button_strip, $texty, $mess)
{
	global $settings, $context, $txt, $scripturl, $smcFunc;

	foreach ($button_strip as $key => $value)
	{
		echo '
		<a href="' . $value['url'] . '" ', !empty($value['custom']) ? $value['custom'] : '' , '>' . $smcFunc['strtoupper']($smcFunc['substr']($txt[$value['text']],0,1)) . $smcFunc['strtolower']($smcFunc['substr']($txt[$value['text']],1,1)). '</a>';
	}

}

function mini_recentTopics($num_recent = 8, $buddy_only = false)
{
	global $context, $settings, $scripturl, $txt, $db_prefix, $user_info;
	global $modSettings, $smcFunc;

	$buddies = $user_info['buddies'];
	if(count($buddies)<1)
		$buddy_only = false;

	if($context['user']['is_logged'])
		// Find all the posts in distinct topics.  Newer ones will have higher IDs.
		$request = $smcFunc['db_query']('substring', '
			SELECT
				m.poster_time, ms.subject, m.id_topic, m.id_member, m.id_msg, b.id_board, b.name AS board_name, t.num_replies, t.num_views,
				IFNULL(mem.real_name, m.poster_name) AS poster_name, mem.avatar, ' . ($user_info['is_guest'] ? '1 AS is_read, 0 AS new_from' : '
				IFNULL(lt.id_msg, IFNULL(lmr.id_msg, 0)) >= m.id_msg_modified AS is_read,
				IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1 AS new_from') . ', SUBSTRING(m.body, 1, 384) AS body, m.smileys_enabled, m.icon,
				IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
				INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
				INNER JOIN {db_prefix}messages AS ms ON (ms.id_msg = t.id_first_msg)
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)' . (!$user_info['is_guest'] ? '
				LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
				LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = b.id_board AND lmr.id_member = {int:current_member})' : '') . '
				LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type!=3)
			WHERE t.id_last_msg >= {int:min_message_id}
				AND {query_wanna_see_board}' . ($modSettings['postmod_active'] ? '
				AND t.approved = {int:is_approved}
				AND m.approved = {int:is_approved}' : '') . '
				' .($buddy_only ? 'AND m.id_member IN ({array_int:buddies}) 
			ORDER BY t.id_last_msg DESC
			LIMIT {int:numrecent}' : '') ,
			array(
				'current_member' => $user_info['id'],
				'include_boards' => empty($include_boards) ? '' : $include_boards,
				'exclude_boards' => empty($exclude_boards) ? '' : $exclude_boards,
				'min_message_id' => $modSettings['maxMsgID'] - 35 * min($num_recent, 5),
				'is_approved' => 1,
				'buddies' => $buddies,
				'numrecent' => $num_recent,
			)
		);
	else
		// Find all the posts in distinct topics.  Newer ones will have higher IDs.
		$request = $smcFunc['db_query']('substring', '
			SELECT
				m.poster_time, ms.subject, m.id_topic, m.id_member, m.id_msg, b.id_board, b.name AS board_name, t.num_replies, t.num_views,
				IFNULL(mem.real_name, m.poster_name) AS poster_name, mem.avatar, ' . ($user_info['is_guest'] ? '1 AS is_read, 0 AS new_from' : '
				IFNULL(lt.id_msg, IFNULL(lmr.id_msg, 0)) >= m.id_msg_modified AS is_read,
				IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1 AS new_from') . ', SUBSTRING(m.body, 1, 384) AS body, m.smileys_enabled, m.icon,
				IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
				INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
				INNER JOIN {db_prefix}messages AS ms ON (ms.id_msg = t.id_first_msg)
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)' . (!$user_info['is_guest'] ? '
				LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
				LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = b.id_board AND lmr.id_member = {int:current_member})' : '') . '
				LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type!=3)
			WHERE t.id_last_msg >= {int:min_message_id}
				AND {query_wanna_see_board}' . ($modSettings['postmod_active'] ? '
				AND t.approved = {int:is_approved}
				AND m.approved = {int:is_approved}' : '') . '
			ORDER BY t.id_last_msg DESC
			LIMIT {int:numrecent}',
			array(
				'current_member' => $user_info['id'],
				'include_boards' => empty($include_boards) ? '' : $include_boards,
				'exclude_boards' => empty($exclude_boards) ? '' : $exclude_boards,
				'min_message_id' => $modSettings['maxMsgID'] - 35 * min($num_recent, 5),
				'is_approved' => 1,
				'numrecent' => $num_recent,
			)
		);
	$posts = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$row['body'] = strip_tags(strtr(parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']), array('<br />' => '&#10;')));
		if ($smcFunc['strlen']($row['body']) > 128)
			$row['body'] = $smcFunc['substr']($row['body'], 0, 128) . '...';

		// Censor the subject.
		censorText($row['subject']);
		censorText($row['body']);

		if (empty($modSettings['messageIconChecks_disable']) && !isset($icon_sources[$row['icon']]))
			$icon_sources[$row['icon']] = file_exists($settings['theme_dir'] . '/images/post/' . $row['icon'] . '.gif') ? 'images_url' : 'default_images_url';

		// Build the array.
		$posts[] = array(
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
			'avatar' => $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="&nbsp;"  />' : '') : (stristr($row['avatar'], 'http://') ? '<img src="' . $row['avatar'] . '" alt="&nbsp;" />' : '<img src="' . $modSettings['avatar_url'] . '/' . $smcFunc['htmlspecialchars']($row['avatar']) . '" alt="&nbsp;" />'),
			'subject' => $row['subject'],
			'replies' => $row['num_replies'],
			'views' => $row['num_views'],
			'short_subject' => shorten_subject($row['subject'], 25),
			'preview' => $row['body'],
			'time' => protimeformat($row['poster_time']),
			'timestamp' => forum_time(true, $row['poster_time']),
			'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . ';topicseen#new',
			'link' => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#new" rel="nofollow">' . $row['subject'] . '</a>',
			// Retained for compatibility - is technically incorrect!
			'new' => !empty($row['is_read']),
			'is_new' => empty($row['is_read']),
			'new_from' => $row['new_from'],
			'icon' => '<img src="' . $settings[$icon_sources[$row['icon']]] . '/post/' . $row['icon'] . '.gif" align="middle" alt="' . $row['icon'] . '"  />',
		);
	}
	$smcFunc['db_free_result']($request);

	return $posts;
}

function mini_whosblock($buddy_only = false)
{
	global $txt, $user_info, $smcFunc, $settings, $modSettings, $scripturl;
	
	$whos = mini_whosOnline();
	echo '
	<div id="whosonline" class="leftb">
		
		<h2><span style="float: right;color: #888;" class="smalltext">' . $whos['num_guests'] .' ' , $whos['num_guests']==1 ? $smcFunc['strtolower']($txt['guest']) : $smcFunc['strtolower']($txt['guests']) , ' </span>
			' . $txt['online'] . '
		</h2>	';
	if(isset($whos['users_online']) && count($whos['users_online'])>0)
	{
		$ids = array(); $names = array();
		foreach($whos['users_online'] as $w => $wh)
		{
			$ids[] = $wh['id'];
			$names[$wh['id']] = $wh['name'];
		}
		$avy = progetAvatars($ids);
		foreach($avy as $a => $av)
			echo '<div class="avatar40hfloat"><a title="', in_array($a, $user_info['buddies']) ? $txt['buddy'] : '' ,'" href="' . $scripturl . '?action=profile;u='.$a.'">' . $av . '</a><div style="width: 40px; overflow: hidden;"><a href="' . $scripturl . '?action=profile;u='.$a.'">'.$names[$a].'</a></div></div>';
	}

	echo '
	
	</div>';
}
function mini_whosblock_chat($buddy_only = false)
{
	global $txt, $user_info, $smcFunc, $settings, $modSettings, $scripturl;
	
	$whos = mini_whosOnline();
	if(isset($whos['users_online']) && count($whos['users_online'])>0)
	{
		$ids = array(); $names = array(); $times = array();
		foreach($whos['users_online'] as $w => $wh)
		{
			$ids[] = $wh['id'];
			$names[$wh['id']] = $wh['name'];
			$times[$wh['id']] = protimeformat($w);
		}
		$avy = progetAvatars($ids);
		foreach($avy as $a => $av)
			echo '
		<div style="float: left; margin-right: 10px;">
			<div class="avatar16h"><a title="', in_array($a, $user_info['buddies']) ? $txt['buddy'] : '' ,'" href="' . $scripturl . '?action=profile;u='.$a.'">' . $av . '</a>
			</div> 
			<a class="smalltext" href="' . $scripturl . '?action=profile;u='.$a.'">'.$names[$a].' - ' . $times[$a] . '</a>
		</div>';
	}
}
function mini_whosOnline()
{
	global $user_info, $txt, $sourcedir, $settings, $modSettings;

	require_once($sourcedir . '/Subs-MembersOnline.php');
	$membersOnlineOptions = array(
		'show_hidden' => allowedTo('moderate_forum'),
		'sort' => 'log_time',
		'reverse_sort' => true,
	);
	$return = getMembersOnlineStats($membersOnlineOptions);
	return $return;
}
function progetAvatars($ids)
{
	global $txt, $user_info, $smcFunc, $settings, $modSettings, $scripturl;

	$request = $smcFunc['db_query']('', '
		SELECT
			mem.real_name, mem.member_name, mem.id_member, mem.show_online,mem.avatar,
			IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType
		FROM {db_prefix}members AS mem
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type!=3)
			WHERE mem.id_member IN ({array_int:ids})',
				array(
				'ids' => $ids,	
			));

	$avy = array();
	if($smcFunc['db_num_rows']($request)>0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$avy[$row['id_member']] = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img ' . (in_array($row['id_member'], $user_info['buddies']) ? 'class="buddyoverlay"' : '' ). ' src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="&nbsp;"  />' : '') : (stristr($row['avatar'], 'http://') ? '<img ' . (in_array($row['id_member'], $user_info['buddies']) ? 'class="buddyoverlay"' : '' ). ' src="' . $row['avatar'] . '" alt="&nbsp;" />' : '<img ' . (in_array($row['id_member'], $user_info['buddies']) ? 'class="buddyoverlay"' : '' ). ' src="' . $modSettings['avatar_url'] . '/' . $smcFunc['htmlspecialchars']($row['avatar']) . '" alt="&nbsp;" />');

		$smcFunc['db_free_result']($request);
	}
	return $avy;
}

// Format a time to make it look purdy.
function protimeformat($log_time, $showall = false, $offset_type = false)
{
	global $context, $user_info, $txt, $modSettings, $smcFunc;

	// Offset the time.
	if (!$offset_type)
		$time = $log_time + ($user_info['time_offset'] + $modSettings['time_offset']) * 3600;
	// Just the forum offset?
	elseif ($offset_type == 'forum')
		$time = $log_time + $modSettings['time_offset'] * 3600;
	else
		$time = $log_time;

	// We can't have a negative date (on Windows, at least.)
	if ($log_time < 0)
		$log_time = 0;

	// decide
	$since = time() - $log_time;
	// seconds?
		if($since==0)
			return $txt['mini-lessthan'] . ' ' .  $txt['mini-ago'];
		elseif($since<60 && $since>0)
			return ($since.' ' . ($since>1 ? $txt['mini-seconds'] : $txt['mini-second']) . ' ' . $txt['mini-ago']);
		// minsutes
		elseif($since>59 && $since<3600)
			return (floor($since/60).' ' . (floor($since/60)>1 ? $txt['mini-minutes'] : $txt['mini-minute']). ' ' . $txt['mini-ago']);
		// hours
		elseif($since>3599 && $since<86400)
			return (floor($since/3600).' ' . (floor($since/3600)>1 ? $txt['mini-hours'] : $txt['mini-hour']). ' ' . $txt['mini-ago']);
		// days
		elseif($since>85399)
			return (floor($since/86400).' ' .(floor($since/86400)>1 ? $txt['mini-days'] : $txt['mini-day']). ' ' . $txt['mini-ago']);
}

function mini_topTopics($num_topics = 10, $brd, $fromdate = 0)
{
	global $db_prefix, $txt, $scripturl, $user_info, $modSettings, $smcFunc, $context;

	if(empty($fromdate))
		$fromdate = time() - (86400*7);
	
	if ($modSettings['totalMessages'] > 100000)
	{

		// !!! Why don't we use {query(_wanna)_see_board}?
		$request = $smcFunc['db_query']('', '
			SELECT id_topic
			FROM {db_prefix}topics
			WHERE num_replies != 0
			AND id_board = {int:brd}
			ORDER BY num_replies DESC
			LIMIT {int:limit}',
			array(
				'brd' => $brd,
				'is_approved' => 1,
				'limit' => $num_topics > 100 ? ($num_topics + ($num_topics / 2)) : 100,
			)
		);
		$topic_ids = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$topic_ids[] = $row['id_topic'];
		$smcFunc['db_free_result']($request);
	}
	else
		$topic_ids = array();

	$request = $smcFunc['db_query']('', '
		SELECT m.subject, m.id_topic, t.num_views, t.num_replies
		FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
		WHERE {query_wanna_see_board}
			AND t.id_board = {int:brd}
			AND m.poster_time > {int:fromdate}
		ORDER BY t.num_replies DESC
		LIMIT {int:limit}',
		array(
			'topic_list' => $topic_ids,
			'is_approved' => 1,
			'limit' => $num_topics,
			'brd' => $brd,
			'fromdate' => $fromdate,
		)
	);
	$topics = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		censorText($row['subject']);
		$topics[] = '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.0">' . $smcFunc['substr']($row['subject'],0, 16) . ' [' . $row['num_replies'] .']</a>';
	}
	$smcFunc['db_free_result']($request);
	return $topics;

}

function mini_quickpost()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $board, $sourcedir, $user_info;

	loadLanguage('Post');
	if (empty($board))
		return;

	require_once($sourcedir . '/Subs-Post.php');
	require_once($sourcedir . '/Subs-Editor.php');

	// Now create the editor.
	$editorOptions = array(
		'id' => 'message',
		'value' => '',
		'labels' => array(
			'post_button' => $txt['sendtopic_send'],
		),
		// add height and width for the editor
		'height' => '175px',
		'width' => '100%',
	);
	create_control_richedit($editorOptions);

	echo '
	<form action="', $scripturl, '?action=post2;board=' . $board, '" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" enctype="multipart/form-data">';

	if (isset($context['name']) && isset($context['email']))
	{
		echo '
		<div class="formitem"><span class="label">'. $txt['name'], '</span><input type="text" name="guestname" size="25" value="', $context['name'], '" tabindex="', $context['tabindex']++, '" class="input_text" /></div>';

		if (empty($modSettings['guest_post_no_email']))
			echo '
		<div class="formitem"><span class="label">' . $txt['email'], '<span><input type="text" name="email" size="25" value="', $context['email'], '" tabindex="', $context['tabindex']++, '" class="input_text" /></div>';
	}
	echo '
		<div class="formitem"><span class="label">', $txt['subject'], '</span><input type="text" name="subject" value="" tabindex="', $context['tabindex']++, '" size="80" maxlength="80" class="input_text" /></div>';

	if(!isset($context['num_replies']) && isset($txt['smftags_topic']))
		echo '
		<div class="formitem"><span class="label">', $txt['smftags_topic'], ' (', $txt['smftags_seperate'], ')</span>
			<input type="text" name="tags"', ' tabindex="', $context['tabindex']++, '" size="80" maxlength="80" />
		</div>';
	$context['can_lock'] = allowedTo('lock_any') || ($user_info['id'] == $ID_MEMBER_POSTER && allowedTo('lock_own'));
	$context['can_sticky'] = allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']);

	// Do we need to show the visual verification image?
	$context['require_verification'] = !$user_info['is_mod'] && !$user_info['is_admin'] && !empty($modSettings['posts_require_captcha']) && ($user_info['posts'] < $modSettings['posts_require_captcha'] || ($user_info['is_guest'] && $modSettings['posts_require_captcha'] == -1));
	if ($context['require_verification'])
	{
		require_once($sourcedir . '/Subs-Editor.php');
		$verificationOptions = array(
			'id' => 'post',
		);
		$context['require_verification'] = create_control_verification($verificationOptions);
		$context['visual_verification_id'] = $verificationOptions['id'];
	}
	echo '
		<div class="formitem"><span class="label">Body</span>', template_control_richedit($editorOptions['id']) , '</div>
		<div class="smalltext" style="padding: 6px;">
				', allowedTo('mark_any_notify') ? '<input type="hidden" name="notify" value="0" /><label for="check_notify"><input type="checkbox" name="notify" id="check_notify" value="1"  /> ' . $txt['notify_replies'] . '</label>' : '', '
				', $context['can_lock'] ? '<input type="hidden" name="lock" value="0" /><label for="check_lock"><input type="checkbox" name="lock" id="check_lock" value="1" /> ' . $txt['lock_topic'] . '</label>' : '', '
				', $context['can_sticky'] ? '<input type="hidden" name="sticky" value="0" /><label for="check_sticky"><input type="checkbox" name="sticky" id="check_sticky" value="1"  /> ' . $txt['sticky_after'] . '</label>' : '', '
				<label for="check_smileys"><input type="checkbox" name="ns" id="check_smileys" value="NS"  /> ', $txt['dont_use_smileys'], '</label>', '
		</div>';

	if ($context['require_verification'])
		echo '
		<div class="formitem"><span class="label">', $txt['verification'], '</span>
			', template_control_verification($context['visual_verification_id'], 'all'), '
		</div>';

	echo '
		', template_control_richedit_buttons($editorOptions['id']), '
		<input type="hidden" name="goback" id="check_back" value="0" />
		<input type="hidden" name="additional_options" id="additional_options" value="0" />
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
	</form>';
}

?>