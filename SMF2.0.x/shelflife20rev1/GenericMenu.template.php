<?php

/**
 * @package Blocthemes Admin
 * @version 1.2
 * @theme ShelfLife
 * @author Blocthemes - http://demos.bjornhkristiansen.com
 * Copyright (C) 2014-2016 - Blocthemes
 *
 */

// This contains the html for the side bar of the admin center, which is used for all admin pages.
function template_generic_menu_sidebar_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	<div id="amenu">
		<ul class="dropmenu submenu">';

	// What one are we rendering?
	$context['cur_menu_id'] = isset($context['cur_menu_id']) ? $context['cur_menu_id'] + 1 : 1;
	$menu_context = &$context['menu_data_' . $context['cur_menu_id']];

	foreach ($menu_context['sections'] as $section)
	{
		// Show the section header - and pump up the line spacing for readability.
		echo '
			<li>
				<a id="aa' , $section['id'] , '" class="firstlevel" onclick="subMenu(\'ad_'.$section['id'].'\',\'asubmenus\',\'submenu\');">', $section['title'], '&nbsp;<span class="icon-angle-down more_menus"></span></a>
				<ul id="ad_'.$section['id'].'">';

		// For every area of this section show a link to that area (bold if it's currently selected.)
		foreach ($section['areas'] as $i => $area)
		{
			// Not supposed to be printed?
			if (empty($area['label']))
				continue;

				// Is this the current area, or just some area?
			if ($i == $menu_context['current_area'])
			{
				echo '
					<li><a class="active firstlevel" href="', isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i, $menu_context['extra_parameters'], '">', $area['label'], '</a></li>';

				if (empty($context['tabs']))
					$context['tabs'] = isset($area['subsections']) ? $area['subsections'] : array();
			}
			else
				echo '
					<li><a class="firstlevel" href="', isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i, $menu_context['extra_parameters'], '">', $area['label'], '</a></li>';
		}
		echo '	
				</ul>';
		$firstSection = false;
	}
	// This is where the actual "main content" area for the admin section starts.
	echo '
			</li>
		</ul>
	</div>
	<div class="admbody">
		<div id="inner_admbody">';

	// If there are any "tabs" setup, this is the place to shown them.
	if (!empty($context['tabs']) && empty($context['force_disable_tabs']))
		template_generic_menu_tabs($menu_context);
}

// Part of the sidebar layer - closes off the main bit.
function template_generic_menu_sidebar_below()
{
	global $context, $settings, $options;

	echo '
		</div>
	</div>
	<br class="clear" />';
}

function template_generic_menu_dropdown_above() { template_generic_menu_sidebar_above(); }
function template_generic_menu_dropdown_below() { template_generic_menu_sidebar_below(); }

// Some code for showing a tabbed view.
function template_generic_menu_tabs(&$menu_context)
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Handy shortcut.
	$tab_context = &$menu_context['tab_data'];

	echo '
		<div class="windowbg2">
			<h3 class="admintab">';

	// Exactly how many tabs do we have?
	foreach ($context['tabs'] as $id => $tab)
	{
		// Can this not be accessed?
		if (!empty($tab['disabled']))
		{
			$tab_context['tabs'][$id]['disabled'] = true;
			continue;
		}

		// Did this not even exist - or do we not have a label?
		if (!isset($tab_context['tabs'][$id]))
			$tab_context['tabs'][$id] = array('label' => $tab['label']);
		elseif (!isset($tab_context['tabs'][$id]['label']))
			$tab_context['tabs'][$id]['label'] = $tab['label'];

		// Has a custom URL defined in the main admin structure?
		if (isset($tab['url']) && !isset($tab_context['tabs'][$id]['url']))
			$tab_context['tabs'][$id]['url'] = $tab['url'];
		// Any additional paramaters for the url?
		if (isset($tab['add_params']) && !isset($tab_context['tabs'][$id]['add_params']))
			$tab_context['tabs'][$id]['add_params'] = $tab['add_params'];
		// Has it been deemed selected?
		if (!empty($tab['is_selected']))
			$tab_context['tabs'][$id]['is_selected'] = true;
		// Does it have its own help?
		if (!empty($tab['help']))
			$tab_context['tabs'][$id]['help'] = $tab['help'];
		// Is this the last one?
		if (!empty($tab['is_last']) && !isset($tab_context['override_last']))
			$tab_context['tabs'][$id]['is_last'] = true;
	}

	// Find the selected tab
	foreach ($tab_context['tabs'] as $sa => $tab)
	{
		if (!empty($tab['is_selected']) || (isset($menu_context['current_subsection']) && $menu_context['current_subsection'] == $sa))
		{
			$selected_tab = $tab;
			$tab_context['tabs'][$sa]['is_selected'] = true;
		}
	}

	echo $tab_context['title'], '
		</h3>
		<div class="buttonlist">
			<ul>';

		// Print out all the items in this tab.
		foreach ($tab_context['tabs'] as $sa => $tab)
		{
			if (!empty($tab['disabled']))
				continue;

			echo '
			<li' , !empty($tab['is_selected']) ? ' class="active_tab"' : '' , ' >
				<a' , !empty($tab['is_selected']) ? ' class="active"' : '' , ' href="', isset($tab['url']) ? $tab['url'] : $menu_context['base_url'] . ';area=' . $menu_context['current_area'] . ';sa=' . $sa, $menu_context['extra_parameters'], '">
					', $tab['label'], '
				</a>
			</li>';
		}
		echo '
			</ul>
		</div>
	</div>
	<div class="clear information">', isset($selected_tab['description']) ? $selected_tab['description'] : $tab_context['description'], '</div>
	';
}

?>