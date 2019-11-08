<?php

/**
 * @package Blocthemes Admin
 * @version 1.0
 * @author Blocthemes - http://www.blocthemes.net
 * Copyright (C) 2014 - Blocthemes
 *
 */

global $sourcedir, $boarddir, $boardurl, $txt, $context, $settings, $modSettings, $smcFunc;
global $db_prefix, $existing_tables;

if(!defined('SMF'))
	die('<strong>Install Error:</strong> - You are not running this from the package center, please do.');
	
// Make sure we have all the $smcFunc stuff
if (!array_key_exists('db_create_table', $smcFunc))
    db_extend('packages');

// the theme files
$td = 'Studio001';

$hooks = array(
	'integrate_pre_include' => '$sourcedir/Blocthemes.php',
	'integrate_admin_areas' => 'AddBTadminItem',
	'integrate_menu_buttons' => 'AddBTadminMenu',
);
// check if they are already there...no need it seems :)
if(empty($context['uninstalling']))
{
	foreach ($hooks as $hook => $function)
		add_integration_function($hook, $function);

	// put it into KnownThemes already
	require_once($sourcedir . '/Subs-Package.php');
	$thdir = $boarddir.'/Themes/'.$td;

	if (!is_dir($thdir) || !file_exists($thdir . '/theme_info.xml'))
			fatal_error('theme_install_error', false);

	$theme_name = basename($thdir);
	$theme_dir = $thdir;


	// Something go wrong?
	if ($theme_dir != '' && basename($theme_dir) != 'Themes')
	{
		// Defaults.
		$install_info = array(
			'theme_url' => $boardurl . '/Themes/' . basename($theme_dir),
			'images_url' => isset($images_url) ? $images_url : $boardurl . '/Themes/' . basename($theme_dir) . '/images',
			'theme_dir' => $theme_dir,
			'theme_bloctheme' => 1,
			'name' => $theme_name
		);

		if (file_exists($theme_dir . '/theme_info.xml'))
		{
			$theme_info = file_get_contents($theme_dir . '/theme_info.xml');

			$xml_elements = array(
				'name' => 'name',
				'theme_layers' => 'layers',
				'theme_templates' => 'templates',
				'based_on' => 'based-on',
			);
			foreach ($xml_elements as $var => $name)
			{
				if (preg_match('~<' . $name . '>(?:<!\[CDATA\[)?(.+?)(?:\]\]>)?</' . $name . '>~', $theme_info, $match) == 1)
					$install_info[$var] = $match[1];
			}

			if (preg_match('~<images>(?:<!\[CDATA\[)?(.+?)(?:\]\]>)?</images>~', $theme_info, $match) == 1)
			{
				$install_info['images_url'] = $install_info['theme_url'] . '/' . $match[1];
				$explicit_images = true;
			}
			if (preg_match('~<extra>(?:<!\[CDATA\[)?(.+?)(?:\]\]>)?</extra>~', $theme_info, $match) == 1)
				$install_info += unserialize($match[1]);
		}

		// Find the newest id_theme.
		$result = $smcFunc['db_query']('', 'SELECT MAX(id_theme)	FROM {db_prefix}themes',	array());
		list ($id_theme) = $smcFunc['db_fetch_row']($result);
		$smcFunc['db_free_result']($result);

		// This will be theme number...
		$id_theme++;

		$inserts = array();
		foreach ($install_info as $var => $val)
			$inserts[] = array($id_theme, $var, $val);

		if (!empty($inserts))
			$smcFunc['db_insert']('insert',	'{db_prefix}themes', array('id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),$inserts,array('id_theme', 'variable'));

		updateSettings(array('knownThemes' => strtr($modSettings['knownThemes'] . ',' . $id_theme, array(',,' => ','))));
		updateSettings(array('theme_guests' => (int) $id_theme));
	}
	// add the package server - if not already applied.
	$request = $smcFunc['db_query']('','SELECT COUNT(id_server) as count FROM {db_prefix}package_servers WHERE name="BAC"');
	if($smcFunc['db_num_rows']($request)>0)
	{
		$row = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);
		if($row['count']>0)
			$do = false;
		else 
			$do = true;
	}
	else
		$do = true;
	
	if($do)	
		$smcFunc['db_query']('','INSERT INTO {db_prefix}package_servers (name,url) VALUES("BAC","http://www.blocthemes.net/mods")');
}
else
{
	// here we need to check if we got more blocthemes, so check them.
	$request = $smcFunc['db_query']('','SELECT COUNT(id_theme) AS total FROM {db_prefix}themes WHERE variable = "theme_bloctheme"');
	$total=$smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	if($total['total']>1)
		$do = false;
	else 
		$do = true;

	if($do)
	{
		foreach ($hooks as $hook => $function)
			remove_integration_function($hook, $function);
	}
	// remove from knownthemes
	$request = $smcFunc['db_query']('','SELECT id_theme FROM {db_prefix}themes WHERE variable="name" AND value="' . $td . '" LIMIT 1');
	if($smcFunc['db_num_rows']($request)>0)
	{
		$row = $smcFunc['db_fetch_assoc']($request);
		$th = $row['id_theme'];
		$smcFunc['db_free_result']($request);

		// You can't delete the default theme!
		if ($th == 1)
			fatal_lang_error('no_access', false);

		$known = explode(',', $modSettings['knownThemes']);
		for ($i = 0, $n = count($known); $i < $n; $i++)
		{
			if ($known[$i] == $th)
				unset($known[$i]);
		}

		$smcFunc['db_query']('', 'DELETE FROM {db_prefix}themes WHERE id_theme = {int:current_theme}', array('current_theme' => $th,));
		$smcFunc['db_query']('', 'UPDATE {db_prefix}members SET id_theme = {int:default_theme} WHERE id_theme = {int:current_theme}', array( 'default_theme' => 0,'current_theme' => $th,	));
		$smcFunc['db_query']('', 'UPDATE {db_prefix}boards SET id_theme = {int:default_theme} WHERE id_theme = {int:current_theme}', array('default_theme' => 0,'current_theme' => $th,));

		$known = strtr(implode(',', $known), array(',,' => ','));

		// Fix it if the theme was the overall default theme.
		if ($modSettings['theme_guests'] == $th)
			updateSettings(array('theme_guests' => '1', 'knownThemes' => $known));
		else
			updateSettings(array('knownThemes' => $known));

		if($do)
			updateSettings(array('theme_guests' => '1', 'knownThemes' => $known));
	}
}

$context['installation_done'] = true;

?>