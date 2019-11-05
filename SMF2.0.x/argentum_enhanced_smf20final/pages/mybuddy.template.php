<?php

function template_main()
{
	global $context, $user_info, $settings, $smcFunc,$scripturl;

	if($context['user']['is_guest'])
	{
		ob_clean();
		echo 'You need to login first.';
		die();
	}
	checksession('get');
	
	if(!empty($_GET['adding']) && is_numeric($_GET['adding']))
	{
		$bud = $_GET['adding'];
		// does the member exist?
		$request = $smcFunc['db_query']('','SELECT COUNT(id_member) as total FROM {db_prefix}members WHERE id_member = '. $bud);
		$found=$smcFunc['db_fetch_assoc']($request);
		if($found['total']>0)
		{
			if(!in_array($bud, $user_info['buddies']) && $user_info['id']!=$bud)
			{	
				$user_info['buddies'][] = $bud;
				$request=$smcFunc['db_query']('',"UPDATE {db_prefix}members SET buddy_list ='". implode(",",$user_info['buddies']) ."' WHERE id_member =". $user_info['id']);
			}
			else
				redirectexit();

			redirectexit('action=profile;area=lists;u='.$user_info['id']);
		}
	}
	elseif(!empty($_GET['delete']) && is_numeric($_GET['delete']))
	{
		$bud = $_GET['delete'];
		if(in_array($bud, $user_info['buddies']))
		{	
			removeFromArray($user_info['buddies'],$bud);
			$request=$smcFunc['db_query']('',"UPDATE {db_prefix}members SET buddy_list ='". implode(",",$user_info['buddies']) ."' WHERE id_member =". $user_info['id']);
		}
		redirectexit('action=profile;area=lists;u='.$user_info['id']);
	}
}

function removeFromArray(&$array, $key)
{
	foreach($array as $j=>$i)
	{
		if($i == $key)
		{
			unset($array[$j]);
		}
	}
}

?>