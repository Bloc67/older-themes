<?php

// Lithium 1.0
// a rewrite theme

function template_generic_menu_dropdown_above()
{
	global $context;

	// Which menu are we rendering?
	$context['cur_menu_id'] = isset($context['cur_menu_id']) ? $context['cur_menu_id'] + 1 : 1;
	$menu_context = &$context['menu_data_' . $context['cur_menu_id']];

	// Load the menu
	echo '

	<div id="admenu" style="display: none;">';
	template_generic_menu($menu_context);
	echo '
	</div>';
	// This is the main table - we need it so we can keep the content to the right of it.
	echo '
				<div id="admin_content">
					<div class="tabby des">';

	template_generic_menu_tabs($menu_context);
	echo '
					</div>';
}

/**
 * Part of the admin layer - used with generic_menu_dropdown_above to close the admin content div.
 */
function template_generic_menu_dropdown_below()
{
	echo '
				</div>';
}

function template_generic_menu(&$menu_context)
{
	global $context;

	$subs = ''; $subsubs = '';
	echo '<div class="generic_container mob">
				<div class="generic_menu rootmenu" id="adm-menu_', $context['cur_menu_id'], '">
					<ul class=" menu_nav dropmenu nolist" style="z-index: 3 !important;">';

	// Main areas first.
	foreach ($menu_context['sections'] as $section)
	{
		echo '
						<li>
							<a class="', !empty($section['selected']) ? 'active ' : '', '" href="', $section['url'], $menu_context['extra_parameters'], '">', $section['title'], '</a>
							<a href="#" onclick="fPop_slide_sub(\'#subs'.$section['id'].'\',\'#subtrigger' . $section['id']. '\', \'.amenu\'); return false;">
								<span class="icon-chevron-down" id="subtrigger' . $section['id'] . '"></span>
							</a>';
		$subs .= '
					<div class="generic_menu sub_generic amenu" id="subs' . $section['id'] . '" style="'. (!empty($section['selected']) ? '' : 'display: none;'). '">
						<ul class="menu_nav dropmenu nolist">';

		// For every area of this section show a link to that area (bold if it's currently selected.)
		// @todo Code for additional_items class was deprecated and has been removed. Suggest following up in Sources if required.
		foreach ($section['areas'] as $i => $area)
		{
			// Not supposed to be printed?
			if (empty($area['label']))
				continue;

			$subs .= '
								<li>
									<a class="'. $area['icon_class']. (!empty($area['selected']) ? ' active ' : ''). '" href="'. (isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i). $menu_context['extra_parameters']. '">'. $area['label']. '</a>
									' . (!empty($area['subsections']) ? '
									<a href="#" onclick="fPop_slide_sub(\'#subsubs'.$i.'\',\'#subsubtrigger' . $i. '\',\'.bmenu\'); return false;">
										<span class="icon-chevron-down" id="subsubtrigger' . $i . '"></span>
									</a>' : '');

			// Is this the current area, or just some area?
			if (!empty($area['selected']) && empty($context['tabs']))
					$context['tabs'] = isset($area['subsections']) ? $area['subsections'] : array();

			// Are there any subsections?
			if (!empty($area['subsections']))
			{
				$subsubs .= '
								<div class="generic_menu subsub_generic amenu bmenu" id="subsubs'.$i.'" style="'. (!empty($area['selected']) ? '' : 'display: none;'). '"">
									<ul class="nolist dropmenu menu_nav">';

				foreach ($area['subsections'] as $sa => $sub)
				{
					if (!empty($sub['disabled']))
						continue;

					$url = isset($sub['url']) ? $sub['url'] : (isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i) . ';sa=' . $sa;

					$subsubs .= '
										<li>
											<a '. (!empty($sub['selected']) ? 'class="active" ' : ''). ' href="'. $url. $menu_context['extra_parameters']. '">'. $sub['label']. '</a>
										</li>';
				}

				$subsubs .= '
									</ul>
								</div>';
			}

			$subs .= '
								</li>';
		}
		$subs .= '
							</ul>
						</div>';
		echo '
						</li>';
	}

	echo '
					</ul>
				</div>';
	echo $subs.$subsubs;
	echo '
		</div>';

	// for non-mobile
	echo '
				<div class="des" id="admm">
					<ul class="dropmenu nolist dropdown_menu_', $context['cur_menu_id'], '">';

	// Main areas first.
	foreach ($menu_context['sections'] as $section)
	{
		echo '
						<li class="adminroot', !empty($section['areas']) ? ' subsections' : '', '"><a class="', !empty($section['selected']) ? 'active ' : '', '" href="', $section['url'], $menu_context['extra_parameters'], '">', $section['title'], '</a>
							<ul>';

		// For every area of this section show a link to that area (bold if it's currently selected.)
		// @todo Code for additional_items class was deprecated and has been removed. Suggest following up in Sources if required.
		foreach ($section['areas'] as $i => $area)
		{
			// Not supposed to be printed?
			if (empty($area['label']))
				continue;

			echo '
								<li', !empty($area['subsections']) ? ' class="subsections"' : '', '>';

			echo '
									<a class="', $area['icon_class'], !empty($area['selected']) ? ' chosen ' : '', '" href="', (isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i), $menu_context['extra_parameters'], '">', $area['icon'], $area['label'], '</a>';

			// Is this the current area, or just some area?
			if (!empty($area['selected']) && empty($context['tabs']))
					$context['tabs'] = isset($area['subsections']) ? $area['subsections'] : array();

			// Are there any subsections?
			if (!empty($area['subsections']))
			{
				echo '
									<ul>';

				foreach ($area['subsections'] as $sa => $sub)
				{
					if (!empty($sub['disabled']))
						continue;

					$url = isset($sub['url']) ? $sub['url'] : (isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i) . ';sa=' . $sa;

					echo '
										<li>
											<a ', !empty($sub['selected']) ? 'class="chosen" ' : '', ' href="', $url, $menu_context['extra_parameters'], '">', $sub['label'], '</a>
										</li>';
				}

				echo '
									</ul>';
			}

			echo '
								</li>';
		}
		echo '
							</ul>
						</li>';
	}

	echo '
					</ul>
				</div>';


}

/**
 * The code for displaying the menu
 *
 * @param array $menu_context An array of menu context data
 */
function template_generic_menu_tabs(&$menu_context)
{
	global $context, $settings, $scripturl, $txt;

	// Handy shortcut.
	$tab_context = &$menu_context['tab_data'];

	if (!empty($tab_context['title']))
	{
		echo '
					<div class="cat_bar">', (function_exists('template_admin_quick_search') ? '
						<form action="' . $scripturl . '?action=admin;area=search" method="post" accept-charset="' . $context['character_set'] . '">' : ''), '
							<h3 class="catbg">';

		// The function is in Admin.template.php, but since this template is used elsewhere too better check if the function is available
		if (function_exists('template_admin_quick_search'))
			template_admin_quick_search();

		// Exactly how many tabs do we have?
		if (!empty($context['tabs']))
		{
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
		}

		// Show an icon and/or a help item?
		if (!empty($selected_tab['icon_class']) || !empty($tab_context['icon_class']) || !empty($selected_tab['icon']) || !empty($tab_context['icon']) || !empty($selected_tab['help']) || !empty($tab_context['help']))
		{
			if (!empty($selected_tab['icon_class']) || !empty($tab_context['icon_class']))
				echo '<span class="', !empty($selected_tab['icon_class']) ? $selected_tab['icon_class'] : $tab_context['icon_class'], ' icon"></span>';
			elseif (!empty($selected_tab['icon']) || !empty($tab_context['icon']))
				echo '<img src="', $settings['images_url'], '/icons/', !empty($selected_tab['icon']) ? $selected_tab['icon'] : $tab_context['icon'], '" alt="" class="icon">';

			if (!empty($selected_tab['help']) || !empty($tab_context['help']))
				echo '<a href="', $scripturl, '?action=helpadmin;help=', !empty($selected_tab['help']) ? $selected_tab['help'] : $tab_context['help'], '" onclick="return reqOverlayDiv(this.href);" class="help"><span class="generic_icons help" title="', $txt['help'], '"></span></a>';

			echo $tab_context['title'];
		}
		else
		{
			echo '
							', $tab_context['title'];
		}

		echo '
							</h3>', (function_exists('template_admin_quick_search') ? '
						</form>' : ''), '
					</div>';
	}

	// Shall we use the tabs? Yes, it's the only known way!
	if (!empty($selected_tab['description']) || !empty($tab_context['description']))
		echo '
					<div class="desc">
						', !empty($selected_tab['description']) ? $selected_tab['description'] : $tab_context['description'], '
					</div>';

	// Print out all the items in this tab (if any).
	if (!empty($context['tabs']))
	{
		// The admin tabs.
		echo '
					<div id="adm_submenus" class="generic_menu tabsmenu">
						<ul class="menu_nav nolist">';

		foreach ($tab_context['tabs'] as $sa => $tab)
		{
			if (!empty($tab['disabled']))
				continue;

			if (!empty($tab['is_selected']))
			{
				echo '
							<li>
								<a class="active" href="', isset($tab['url']) ? $tab['url'] : $menu_context['base_url'] . ';area=' . $menu_context['current_area'] . ';sa=' . $sa, $menu_context['extra_parameters'], isset($tab['add_params']) ? $tab['add_params'] : '', '">', $tab['label'], '</a>
							</li>';
			}
			else
				echo '
							<li>
								<a href="', isset($tab['url']) ? $tab['url'] : $menu_context['base_url'] . ';area=' . $menu_context['current_area'] . ';sa=' . $sa, $menu_context['extra_parameters'], isset($tab['add_params']) ? $tab['add_params'] : '', '">', $tab['label'], '</a>
							</li>';
		}

		// the end of tabs
		echo '
						</ul>
					</div>';

	}
}




?>