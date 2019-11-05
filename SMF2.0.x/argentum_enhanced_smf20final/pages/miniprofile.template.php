<?php

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix, $smcFunc, $user_info;

	// dump current buffer
	ob_clean();
	
	if(!empty($_GET['u']) && is_numeric($_GET['u']))
		$user = $_GET['u'];
	else
		echo 'Not found...';

	if($user==0)
		die();
	$request = $smcFunc['db_query']('','SELECT m.*, IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType,
		g.group_name
		FROM {db_prefix}members as m
		LEFT JOIN {db_prefix}membergroups AS g ON (g.id_group = m.id_group)
		LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = m.id_member)
		WHERE m.id_member = '. $user.'
		LIMIT 1');

	if($smcFunc['db_num_rows']($request)==0)
		echo 'Not found';	
	else
		$row = $smcFunc['db_fetch_assoc']($request);

	$avy = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']));

	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?rc2" />';
	
	if(!empty($settings['altprof']))
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/altprof.css?rc3" />';

	echo '
	<style type="text/css"><!--
		body{
			background: none;
			background: black\9;
			color: #aaa;
		}
	</style>	
	<div class="miniprofile">
		<img class="avyframe" style="max-height: 65px; float: left; margin: 0 1em 130px 0;" src="' . (!empty($avy) ? $avy : $settings['images_url'].'/TPguest.png') . '" alt="" title="" />
		<h2 class="newprofile">'.$row['real_name'].' <a target="_top" href="'.$scripturl.'?action=profile;u='.$user.'" class="fullprof">[View full profile]</a></h2>
		has written ' . $row['posts'] . ' posts since ' , timeformat($row['date_registered']), '';
		
		if(!empty($row['personal_text']))
			echo '<br><p class="smalltext"><em>- ',$row['personal_text'],'</em></p>';
		
		echo '<hr>
		<div class="floatright" id="minprof">';

		$is_buddy = in_array($user, $user_info['buddies']);
		if($is_buddy)
			echo 'You follow '. $row['real_name'],'
		| 
		<a href="'.$scripturl.'?action=mybuddy;delete='.$user.';'.$context['session_var'].'='.$context['session_id'].'" target="_top">Unfollow</a>';
		else
			echo '
		<a href="'.$scripturl.'?action=mybuddy;adding='.$user.';'.$context['session_var'].'='.$context['session_id'].'" target="_top">Follow</a>';
		
		echo '</div>';

		if(!empty($row['group_name']))
			echo '<b>'.$row['group_name'].'</b>';
		
		if(!empty($row['website_title']) && !empty($row['website_url']))
			echo '<br><a style="color: #ddf;" href="'.$row['website_url'].'" target="_blank">'.$row['website_title'].'</a>';

		
		echo '
		<div class="smalltext">'.$row['real_name'].' was last active at ', timeformat($row['last_login']) , '</div> 
	</div>
	';
	
	
	
	die();

}
?>