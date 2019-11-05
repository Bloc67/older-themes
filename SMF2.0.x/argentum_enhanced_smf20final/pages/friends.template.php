<?php

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix, $smcFunc, $user_info;

	if($context['user']['is_guest'])
		redirectexit('action=login');

	if(!RecentActivity())
		return;

	// if turned off, return to boardindex
	if(empty($settings['showfriendsbutton']))
		redirectexit();


	$membs = array();
	if(!empty($context['posts']))
	{
		foreach($context['posts'] as $m)
				$membs[$m['poster']['id']] = $m['poster']['id'];
	}
	
	foreach($user_info['buddies'] as $m)
				$membs[$m] = $m;

	if(count($membs)>0)
	{
		$request =  $smcFunc['db_query']('',"SELECT mem.id_member as ID_MEMBER, mem.real_name, mem.avatar,
				IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType 
				FROM {db_prefix}members AS mem
				LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
				WHERE mem.id_member IN (" . implode(",",$membs) . ")",array());
			
		$avvy = array();
		$names = array();
		if($smcFunc['db_num_rows']($request)>0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$avvy[$row['ID_MEMBER']]  = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']));
				$names[$row['ID_MEMBER']] = $row['real_name'];
			}
			$smcFunc['db_free_result']($request);
		}
	}

	echo '
	<div id="recent" class="main_section">
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/post/xx.gif" alt="" class="icon" />Friends Activity</span>
			</h3>
		</div>
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>
<div class="container"><div class="col13">

		<div style="padding: 1em 0 0 0;">';
	$alt=true;
	foreach ($context['posts'] as $post)
	{
		$user=$post['poster']['id'];
		echo '
			<div style="overflow: hidden; margin: 0 0 2px 0; padding: 5px;" class="windowbg">
				<div class="container"><div class="col1" >

				<a href="',$scripturl,'?action=miniprofile;u='.$post['poster']['id'].'" rel="width:560,height:260" id="mb'.$post['id'].'" class="mb" >
						<img class="friendsavatar" src="' . (!empty($avvy[$post['poster']['id']]) ? $avvy[$post['poster']['id']] : $settings['images_url'].'/TPguest.png') . '"  alt="" title="'.$post['poster']['name'].'" />
				</a>
				<div class="multiBoxDesc mb'.$post['id'].'">&nbsp;</div>
				</div><div class="col15">				
					<div class="floatright" id="buddybox'.$post['id'].'" style="font-size: 0.8em; padding-right: 1em;">
						<a href="'.$scripturl.'?action=mybuddy;delete='.$user.';'.$context['session_var'].'='.$context['session_id'].'">Unfollow</a>
					</div>
					<span class="greytext" style="font-size: 80%;"><b>' . $post['poster']['link'] . '</b> replied in the topic ' , $post['link'],  '</span>
					<div class="friendspost">', strip_tags($post['message'],"<a><b>"), '..</div>
					<div class="mysmalltext">', $post['time'], '</div>
				</div></div>
			</div>';
	}

	echo '
		</div>
</div><div class="col3">
<div style="padding: 0 2em;">';

	foreach($user_info['buddies'] as $b => $bud)
	{
		echo '<div class="friendslist">
		<img src="' . (!empty($avvy[$bud]) ? $avvy[$bud] : $settings['images_url'].'/TPguest.png') . '"  alt="" title="'.$names[$bud].'" />
				</div>';
	
	}

	echo '
</div></div></div>
		
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>
	</div>';
}

// Find the ten most recent posts.
function RecentActivity()
{
	global $txt, $scripturl, $user_info, $context, $modSettings, $sourcedir, $board, $smcFunc;

	// return if no friends..
	if(count($user_info['buddies'])<1)
		return false;
	
	if (isset($_REQUEST['start']) && $_REQUEST['start'] > 95)
		$_REQUEST['start'] = 95;

	$query_parameters = array();
	$query_this_board = '{query_wanna_see_board}' . (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? '
		AND b.id_board != {int:recycle_board}' : ''). '';
	$query_parameters['max_id_msg'] = max(0, $modSettings['maxMsgID'] - 100 - $_REQUEST['start'] * 6);
	$query_parameters['recycle_board'] = $modSettings['recycle_board'];

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(m.id_msg) as total
		FROM {db_prefix}messages AS m
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
		WHERE ' . $query_this_board . '
			AND m.approved = {int:is_approved}
			AND m.id_member IN (' . implode(",",$user_info['buddies']) . ')
		',
		array_merge($query_parameters, array(
			'is_approved' => 1,
			'offset' => $_REQUEST['start'],
		))
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	
	// !!! This isn't accurate because we ignore the recycle bin.
	$context['page_index'] = constructPageIndex($scripturl . '?action=friends', $_REQUEST['start'], $row['total'], 20, false);

	$request = $smcFunc['db_query']('', '
		SELECT m.id_msg
		FROM {db_prefix}messages AS m
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
		WHERE ' . $query_this_board . '
			AND m.approved = {int:is_approved}
			AND m.id_member IN (' . implode(",",$user_info['buddies']) . ')
		ORDER BY m.id_msg DESC
		LIMIT {int:offset}, {int:limit}',
		array_merge($query_parameters, array(
			'is_approved' => 1,
			'offset' => $_REQUEST['start'],
			'limit' => 20,
		))
	);
	$messages = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$messages[] = $row['id_msg'];
	$smcFunc['db_free_result']($request);

	// Looks like nothin's happen here... or, at least, nothin' you can see...
	if (empty($messages))
	{
		$context['posts'] = array();
		return false;
	}

	// Get all the most recent posts.
	$request = $smcFunc['db_query']('', '
		SELECT
			m.id_msg, m.subject, m.poster_time, LEFT(m.body,180) as body, m.id_topic, t.id_board, b.id_cat,m.smileys_enabled,
			b.name AS bname, c.name AS cname, t.num_replies, m.id_member, 
			IFNULL(mem.real_name, m.poster_name) AS poster_name, t.id_last_msg
		FROM {db_prefix}messages AS m
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			INNER JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
		WHERE m.id_msg IN ({array_int:message_list})
		ORDER BY m.id_msg DESC
		LIMIT ' . count($messages),
		array(
			'message_list' => $messages,
		)
	);
	$counter = $_REQUEST['start'] + 1;
	$context['posts'] = array();
	$board_ids = array('own' => array(), 'any' => array());
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// Censor everything.
		censorText($row['body']);
		censorText($row['subject']);

		// BBC-atize the message.
		$row['body'] = parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']);

		// And build the array.
		$context['posts'][$row['id_msg']] = array(
			'id' => $row['id_msg'],
			'counter' => $counter++,
			'alternate' => $counter % 2,
			'category' => array(
				'id' => $row['id_cat'],
				'name' => $row['cname'],
				'href' => $scripturl . '#c' . $row['id_cat'],
				'link' => '<a href="' . $scripturl . '#c' . $row['id_cat'] . '">' . $row['cname'] . '</a>'
			),
			'board' => array(
				'id' => $row['id_board'],
				'name' => $row['bname'],
				'href' => $scripturl . '?board=' . $row['id_board'] . '.0',
				'link' => '<a href="' . $scripturl . '?board=' . $row['id_board'] . '.0">' . $row['bname'] . '</a>'
			),
			'topic' => $row['id_topic'],
			'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
			'link' => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'] . '" rel="nofollow">' . $row['subject'] . '</a>',
			'start' => $row['num_replies'],
			'subject' => $row['subject'],
			'time' => timeformat($row['poster_time']),
			'timestamp' => forum_time(true, $row['poster_time']),
			'poster' => array(
				'id' => $row['id_member'],
				'name' => $row['poster_name'],
				'href' => empty($row['id_member']) ? '' : $scripturl . '?action=profile;u=' . $row['id_member'],
				'link' => empty($row['id_member']) ? $row['poster_name'] : '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>'
			),
			'message' => $row['body'],
			'can_reply' => false,
			'can_mark_notify' => false,
			'can_delete' => false,
			'delete_possible' => false,
		);

		$board_ids['any'][$row['id_board']][] = $row['id_msg'];
	}
	$smcFunc['db_free_result']($request);
	
	return true;

}

?>