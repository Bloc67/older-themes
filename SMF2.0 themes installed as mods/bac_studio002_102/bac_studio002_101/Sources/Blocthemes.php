<?php

/**
 * @package Blocthemes Admin
 * @version 1.0.2
 * @author Blocthemes - http://www.blocthemes.net
 * Copyright (C) 2014 - 2016 Blocthemes
 *
 */
 
if (!defined('SMF'))
	die('Hacking attempt...');

function AddBTadminItem(&$admin_areas)
{
	global $context,$settings;

	if(!empty($settings['bloctheme']) && !empty($settings['bloctheme_version']))
		$admin_areas['config']['areas']['blocthemes'] = 	array(
			'label' => 'BAC',
			'file' => 'Blocthemes.php',
			'function' => 'Blocthemes',
			'icon' => 'themes.gif',
			'permission' => array('admin_forum'),
			'subsections' => array(
				'current' => array('Theme Options'),
			),
		);
	else
		$admin_areas['config']['areas']['blocthemes'] = 	array(
			'label' => 'BAC',
			'file' => 'Blocthemes.php',
			'function' => 'Blocthemes',
			'icon' => 'themes.gif',
			'permission' => array('admin_forum'),
			'subsections' => array(
				'news' => array('News'),
			),
		);
	return;
}

function AddBTadminMenu(&$buttons)
{
	global $scripturl, $settings;

	$buttons['admin']['sub_buttons']['blocthemes'] = array(
			'title' => 'BAC',
			'href' => $scripturl . '?action=admin;area=blocthemes',
			'show' => allowedTo('manage_forum'),
			'is_last' => true,
	);
	return;
}


function Blocthemes()
{
	global $context,$admin_areas, $settings;

	loadTemplate('Blocthemes');
	loadLanguage('Blocthemes');	

	$context['page_title'] = 'Blocthemes Admin Center';

	// check, are we inside a bloctheme?
	if(!empty($settings['bloctheme']) && !empty($settings['bloctheme_version']))
	{
		$subActions = array(
			'current' => 'BlocthemesOptions',
			'news' => 'BlocthemesNews',
		);
		if (!empty($context['admin_menu_name']))
		{
			$context[$context['admin_menu_name']]['tab_data'] = array(
				'title' => 'Blocthemes Admin Center',
				'description' => 'This is your adminstration center for your installed themes from blocthemes.net',
				'tabs' => array(
					'current' => array(
						'description' => 'The theme options for ' . $settings['bloctheme'] . ' v' . $settings['bloctheme_version'] . '.',
					),
					'news' => array(
						'description' => 'The latest news from the blocthemes.net site.',
					),
				),
			);
		}
	}
	else
	{
		$subActions = array(
			'news' => 'BlocthemesNews',
		);
		if (!empty($context['admin_menu_name']))
		{
			$context[$context['admin_menu_name']]['tab_data'] = array(
				'title' => 'Blocthemes Admin Center',
				'description' => 'This is your adminstration center for your installed themes from blocthemes.net',
				'tabs' => array(
					'news' => array(
						'description' => 'The latest news from the blocthemes.net site.',
					),
				),
			);
		}
	}

	// extract the theme theme id's
	$context['available_bt_themes'] = array();
	$context['available_bt_themes_full'] = array();
	if(!empty($settings['bloctheme']) && !empty($settings['bloctheme_version']))
	{
		// Follow the sa or just go to administration.
		if (isset($_GET['sa']) && !empty($subActions[$_GET['sa']]))
			$subActions[$_GET['sa']]();
		else
			$subActions['current']();
	}
	else
	{
		// Follow the sa or just go to administration.
		if (isset($_GET['sa']) && !empty($subActions[$_GET['sa']]))
			$subActions[$_GET['sa']]();
		else
			$subActions['news']();
	}
}

function BlocthemesNews()
{
	global $context;

	$context['btfeed'] = grabBT_RSS('http://www.blocthemes.net/index.php?board=3;action=.xml;type=rss2;sa=news','btnews',3600);
	$context['sub_template'] = 'btnews';
	$context['page_title'] = $txt['bt_news'];
}

function BlocthemesUsersites()
{
	global $context;

	$context['btfeed'] = grabBT_RSS('http://www.blocthemes.net/index.php?board=11;action=.xml;type=rss2;sa=news','btusersites',3600);
	$context['sub_template'] = 'btusersites';
	$context['page_title'] = $txt['bt_installed'];
}

function BlocthemesShowcase()
{
	global $context;

	$context['btfeed'] = grabBT_RSS('http://www.blocthemes.net/index.php?board=10;action=.xml;type=rss2;sa=news','btshowcase',3600);
	$context['sub_template'] = 'btshowcase';
	$context['page_title'] = $txt['bt_showcase'];
}

function BlocthemesOptions()
{
	global $context, $settings, $smcFunc, $txt;

	isAllowedTo('admin_forum');

	$context['html_headers'] .= '
    <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/colorpicker.min.js"></script>
	';
	
	$old_id = $settings['theme_id'];
	$old_settings = $settings;

	// Submitting!
	if (isset($_POST['submit']))
	{
		checkSession();

		if (empty($_POST['options']))
			$_POST['options'] = array();
		if (empty($_POST['default_options']))
			$_POST['default_options'] = array();

		$inserts = array();
		foreach ($_POST['options'] as $opt => $val)
			$inserts[] = array(0, $old_id, $opt, is_array($val) ? implode(',', $val) : $val);
		
		foreach ($_POST['default_options'] as $opt => $val)
			$inserts[] = array(0, 1, $opt, is_array($val) ? implode(',', $val) : $val);
		
		// Set up the sql query.
		foreach($_FILES as $opt => $val)
		{
			$filelink = btupload_logo($opt, $settings['theme_dir'].'/images/theme');
			if(!empty($filelink))
				$inserts[] = array(0, $old_id, substr($opt,0, strlen($opt)-7), $settings['images_url'].'/theme/'. $filelink);
		}	
		
		// If we're actually inserting something..
		if (!empty($inserts))
		{
			$smcFunc['db_insert']('replace',
				'{db_prefix}themes',
				array('id_member' => 'int', 'id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),
				$inserts,
				array('id_member', 'id_theme', 'variable')
			);
		}

		cache_put_data('theme_settings-' . $old_id, null, 90);
		cache_put_data('theme_settings-1', null, 90);

		// Invalidate the cache.
		updateSettings(array('settings_updated' => time()));
		redirectexit('action=admin;area=blocthemes;sa=options;th=' . $old_id . ';' . $context['session_var'] . '=' . $context['session_id']);
	}

	$context['sub_template'] = 'btoptions';
	$context['page_title'] = $txt['options'];
}

function grabBT_RSS($feed, $what = '', $time = 10200)
{
	// check the cache first, 3 hours are enough..unless specified
	if (!($feed_to_array = cache_get_data('btdata'.$what, $time)))
	{
		$f = simplexml_load_file($feed,NULL,LIBXML_NOCDATA);
		$json_string = json_encode($f);
		$feed_to_array = json_decode($json_string, TRUE);

		cache_put_data('btdata'.$what, $feed_to_array, $time);
		return $feed_to_array;
	}
	return $feed_to_array;
}

function btupload_logo($id, $themedir)
{
	global $sourcedir;

	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES[$id]["name"]);
	$extension = end($temp);

	if ((($_FILES[$id]["type"] == "image/gif") || ($_FILES[$id]["type"] == "image/jpeg") 	|| ($_FILES[$id]["type"] == "image/jpg") 	|| ($_FILES[$id]["type"] == "image/pjpeg") || ($_FILES[$id]["type"] == "image/x-png") || ($_FILES[$id]["type"] == "image/png")) && in_array($extension, $allowedExts)) 
	{
		if ($_FILES[$id]["error"] > 0)
			return;
		else
		{
			move_uploaded_file($_FILES[$id]["tmp_name"], $themedir . '/' . $_FILES[$id]["name"]);
			return $_FILES[$id]["name"];
		}
	}
	else
		return;
}
?>