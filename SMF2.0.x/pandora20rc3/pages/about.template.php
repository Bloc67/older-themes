<?php

function template_main()
{
	global $txt, $scripturl, $user_info;
	global $context, $modSettings, $ID_MEMBER;
	global $board_info, $settings, $db_prefix;

	$tpteam = 2;
	$tpsupport = 22;
	$tpthemes = 24;
	$tplang = 20;
	$tpcode = 30;
	$tpadmin = 32;
	$tpserver = 39;
	$tppress = 40;
	$tpsmf = 41;
	$tpinterface = 38;
	$tpfriend = 37;

	$loaded_ids = array();
    $user_profile=array();
    $memberContext=array();
    $profile=array();
    $context['TPortal']['team']=array();
    $context['TPortal']['friends']=array();

	$select_columns = "
			IFNULL(lo.log_time, 0) AS isOnline, IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType,
			mem.personal_text as personalText, mem.avatar, mem.id_member as ID_MEMBER, mem.member_name as memberName,
			mem.real_name as realName,mem.last_login as lastLogin, mem.website_title as websiteTitle, mem.website_url as websiteUrl, mem.location,   
            mem.posts,mem.id_group as ID_GROUP";
	$select_tables = "
			LEFT JOIN ".$db_prefix."log_online AS lo ON (lo.id_member = mem.id_member)
			LEFT JOIN ".$db_prefix."attachments AS a ON (a.id_member = mem.id_member)";

	// Load the member's data.
	$request = tp_query("
			SELECT ".$select_columns."
			FROM ".$db_prefix."members AS mem ".$select_tables."
			WHERE mem.id_group = ".$tpteam."
                        OR mem.id_group = 1 
                        OR mem.id_group = ".$tpsupport." 
                        OR mem.id_group = ".$tpthemes." 
                        OR mem.id_group = ".$tplang." 
                        OR mem.id_group = ".$tpcode ."
                        OR mem.id_group = ".$tpadmin ."
                        OR mem.id_group = ".$tpinterface." 
                        OR mem.id_group = ".$tpserver ."
                        OR mem.id_group = ".$tppress ."
                        OR mem.id_group = ".$tpsmf ."
                        OR mem.id_group = ".$tpfriend." 
                        ORDER BY mem.last_login DESC", __FILE__, __LINE__);
	$new_loaded_ids = array();
	while ($row = tpdb_fetch_assoc($request))
	{

			$avatar_width = '';
			$avatar_height = '';

                        if($row['ID_GROUP']==2)
                          $groupname='TP Team';
                        elseif($row['ID_GROUP']==22)
                          $groupname='TP Support';
                        elseif($row['ID_GROUP']==1 || $row['ID_GROUP']==32)
                          $groupname='TP Admin';
                        elseif($row['ID_GROUP']==24)
                          $groupname='TP Themes';
                        elseif($row['ID_GROUP']==20)
                          $groupname='TP Language';
                        elseif($row['ID_GROUP']==30)
                          $groupname='TP Code';
                        elseif($row['ID_GROUP']==39)
                          $groupname='TP Server';
                        elseif($row['ID_GROUP']==40)
                          $groupname='TP Press';
                        elseif($row['ID_GROUP']==41)
                          $groupname='TP SMF';
                        elseif($row['ID_GROUP']==38)
                          $groupname='TP Interface';
                        elseif($row['ID_GROUP']==37)
                          $groupname='TP Friend';
		
        // What a monstrous array...
	if($row['ID_GROUP']==37)
		$context['TPortal']['friends'][] = array(
			'username' => $row['memberName'],
			'name' => $row['realName'],
			'posts' => $row['posts'],
			'location' => $row['location'],
			'href' => $scripturl . '?action=profile;u=' . $row['ID_MEMBER'],
			'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '" >' . $row['realName'] . '</a>',
			'blurb' => $row['personalText'],
			'avatar' => array(
				'name' => $row['avatar'],
				'image' => $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img  src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="" border="0" />' : '') : (stristr($row['avatar'], 'http://') ? '<img src="' . $row['avatar'] . '"' . $avatar_width . $avatar_height . ' alt="" border="0" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']) . '" alt="" class="avatar2" border="0" />'),
				'href' => $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				'url' => $row['avatar'] == '' ? '' : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar'])
			),
			'last_login' => empty($row['lastLogin']) ? $txt['never'] : timeformat($row['lastLogin']),
			'last_login_timestamp' => empty($row['lastLogin']) ? 0 : forum_time(0, $row['lastLogin']),
			'website' => array(
				'title' => $row['websiteTitle'],
				'url' => $row['websiteUrl']),
			'online' => array(
				'is_online' => $row['isOnline'],
				'text' => &$txt[$row['isOnline'] ? 'online2' : 'online3'],
				'image_href' => $settings['default_images_url'] . '/' . ($row['isOnline'] ? 'useron' : 'useroff') . '.gif',
			),
		);
	else
		$context['TPortal']['team'][] = array(
			'username' => $row['memberName'],
			'name' => $row['realName'],
			'posts' => $row['posts'],
			'location' => $row['location'],
			'href' => $scripturl . '?action=profile;u=' . $row['ID_MEMBER'],
			'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '" title="' . $row['realName'] . '">' . $row['realName'] . '</a>',
			'blurb' => $row['personalText'],
			'avatar' => array(
				'name' => $row['avatar'],
				'image' => $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img  src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="" border="0" />' : '') : (stristr($row['avatar'], 'http://') ? '<img src="' . $row['avatar'] . '"' . $avatar_width . $avatar_height . ' alt="" border="0" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']) . '" alt="" class="avatar2" border="0" />'),
				'href' => $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				'url' => $row['avatar'] == '' ? '' : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar'])
			),
			'last_login' => empty($row['lastLogin']) ? $txt['never'] : timeformat($row['lastLogin']),
			'last_login_timestamp' => empty($row['lastLogin']) ? 0 : forum_time(0, $row['lastLogin']),
			'website' => array(
				'title' => $row['websiteTitle'],
				'url' => $row['websiteUrl']),
			'online' => array(
				'is_online' => $row['isOnline'],
				'text' => &$txt[$row['isOnline'] ? 'online2' : 'online3'],
				'image_href' => $settings['default_images_url'] . '/' . ($row['isOnline'] ? 'useron' : 'useroff') . '.gif',
			),
			'groupname' => $groupname,
		);



	}
	tpdb_free_result($request);

	echo '
	<div  style="padding: 1em 2em;">
		<h1>TP team</h1>
		<p><em>Meet the people behind TinyPortal!</em></p><br />
		<ul style="margin: 0; padding: 0;">';
	$count=0;
	foreach($context['TPortal']['team'] as $team)
    {
		echo '
			<li style="list-style: none; overflow: hidden; padding: 0; margin: 0 0 1em 0;">
				<div style="margin-right: 1em; width: 120px; float: left; background: 50% 0 url('.$team['avatar']['href'].') no-repeat; height: 110px;" alt="' . $team['name'] .'"></div>
				<h4 class="catbg"><span class="left"></span><img style="position: relative;padding-top: 3px;" src="'.$team['online']['image_href'].'" alt="" /> '.$team['link'].' </h4>
				<h5 style="margin: 0; padding: 1em 0 1em 0; color: #556;">'.$team['groupname'].'</h5>
				<p style="margin: 0;">
					' . $team['name'] . ' was last seen '.$team['last_login'].',  
					and has written '.$team['posts'].' posts. 
					' , !empty($team['location']) ? 'Located in '.$team['location'] : '' , '.
					' , !empty($team['website']['title']) ? ' ( <a href="'.$team['website']['url'].'" target="_blank">'.$team['website']['title'].'</a> )' : '' , '
					' , !empty($team['blurb']) ? '<p><em> - '.$team['blurb'].' - </em></p>' : '' , '
				</p>
			</li>';
	}
	echo '
		</ul>';
	echo '<hr />
		<div style="padding: 0 1em 1em 1em;">
			<h3>Our past contributors and friends of TP:</h3><br />
			<div style="overflow: hidden; padding: 1em;" class="windowbg">';
	$count=0;
	foreach($context['TPortal']['friends'] as $team)
    {
       echo '
			<div style="width: 220px; height: 60px; float: left;">
				<h4 style="margin: 0; padding: 0 0 4px 0;"><img style="position: relative;padding-top: 3px;" src="'.$team['online']['image_href'].'" alt="" /> '.$team['link'].' </h4>
				<p style="margin: 0; padding: 0;" class="middletext">
					Last seen '.$team['last_login'].'
				</p>
			</div>';
	}
	echo '
		</div>';

	echo '
	</div>';
}


?>