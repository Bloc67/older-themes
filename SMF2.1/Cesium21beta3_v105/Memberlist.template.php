<?php

// Cesium 1.0
// a rewrite theme

function template_main()
{
	global $context, $settings, $scripturl, $txt;

	echo '
	<div class="main_section" id="memberlist">
		<div class="pagesection">
			', template_button_strip($context['memberlist_buttons'], 'right'), '
			<div class="pagelinks floatleft">', $context['page_index'], '</div>
		</div>
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="floatleft">', $txt['members_list'], '</span>';
		if (!isset($context['old_search']))
				echo '
				<span class="bwfloatright">', $context['letter_links'], '</span>';
		echo '
			</h3>
		</div>';

	echo '
		<div id="mlist">
			<table class="table_grid_opt">
			<thead>
				<tr class="title_bar">';

	// Display each of the column headers of the table.
	foreach ($context['columns'] as $key => $column)
	{
		// @TODO maybe find something nicer?
		if ($key == 'email_address' && !$context['can_send_email'])
			continue;

		// This is a selected column, so underline it or some such.
		if ($column['selected'])
			echo '
					<th scope="col" class="', $key, isset($column['class']) ? ' ' . $column['class'] : '', ' selected" style="width: auto;"' . (isset($column['colspan']) ? ' colspan="' . $column['colspan'] . '"' : '') . '>
						<a href="' . $column['href'] . '" rel="nofollow">' . $column['label'] . '</a><span class="generic_icons sort_' . $context['sort_direction'] . '"></span></th>';
		// This is just some column... show the link and be done with it.
		else
			echo '
					<th scope="col" class="', $key, isset($column['class']) ? ' ' . $column['class'] : '', '"', isset($column['width']) ? ' style="width: ' . $column['width'] . '"' : '', isset($column['colspan']) ? ' colspan="' . $column['colspan'] . '"' : '', '>
						', $column['link'], '</th>';
	}
	echo '
				</tr>
			</thead>
			<tbody>';

	// Assuming there are members loop through each one displaying their data.
	if (!empty($context['members']))
	{
		foreach ($context['members'] as $member)
		{
			echo '
				<tr class="windowbg"', empty($member['sort_letter']) ? '' : ' id="letter' . $member['sort_letter'] . '"', '>
					<td class="centertext w1 des">
						', $context['can_send_pm'] ? '<a href="' . $member['online']['href'] . '" title="' . $member['online']['text'] . '">' : '', $settings['use_image_buttons'] ? '<span class="' . ($member['online']['is_online'] == 1 ? 'on' : 'off') . '" title="' . $member['online']['text'] . '"></span>' : $member['online']['label'], $context['can_send_pm'] ? '</a>' : '', '
					</td>
					<td class="lefttext des">
						<b>', $member['link'], '</b>
					</td>
					<td class="lefttext mob">
						<b>', $member['link'], '</b>';
			if (!isset($context['disabled_fields']['posts']))
				echo '
						(', $member['posts'], ')
						 ', empty($member['group']) ? $member['post_group'] : $member['group'], '
						', $context['can_send_pm'] ? '<a href="' . $member['online']['href'] . '" title="' . $member['online']['text'] . '">' : '', '<span class="mob ' . ($member['online']['is_online'] == 1 ? 'on' : 'off') . '" title="' . $member['online']['text'] . '" style="display: inline-block; float: right; margin: 4px 4px 0 0;"></span>', $context['can_send_pm'] ? '</a>' : '', '
					</td>';

		if (!isset($context['disabled_fields']['website']))
			echo '
					<td class="website_url w1 des">', $member['website']['url'] != '' ? '<a href="' . $member['website']['url'] . '" target="_blank" class="new_win"><span class="icon-home" title="' . $member['website']['title'] . '"></span></a>' : '', '</td>';

		// Group and date.
		echo '
					<td class="reg_group des">', empty($member['group']) ? $member['post_group'] : $member['group'], '</td>
					<td class="reg_date des">', $member['registered_date'], '</td>';

		if (!isset($context['disabled_fields']['posts']))
		{
			echo '
					<td class="centertext des" style="white-space: nowrap; width: 15px">', $member['posts'], '</td>
					<td class="centertext statsbar des" style="width: 120px">';

			if (!empty($member['post_percent']))
				echo '
						<span class="backline">
							<span class="percentline" style="width: ' , $member['post_percent'] , '%;"></span>
							<span class="rightcircle" style="left: ' , $member['post_percent'] , '%;"></span>
						</span>';

			echo '
					</td>';
		}

		// Show custom fields marked to be shown here
		if (!empty($context['custom_profile_fields']['columns']))
		{
			foreach ($context['custom_profile_fields']['columns'] as $key => $column)
				echo '
					<td class="righttext">', $member['options'][$key], '</td>';
		}

		echo '
				</tr>';
		}
	}
	// No members?
	else
		echo '
				<tr>
					<td colspan="', $context['colspan'], '" class="windowbg">', $txt['search_no_results'], '</td>
				</tr>';

				echo '
			</tbody>
			</table>
		</div>';

	// Show the page numbers again. (makes 'em easier to find!)
	echo '
		<div class="pagesection">
			<div class="pagelinks floatleft">', $context['page_index'], '</div>';

	// If it is displaying the result of a search show a "search again" link to edit their criteria.
	if (isset($context['old_search']))
		echo '
			<a class="button_link" href="', $scripturl, '?action=mlist;sa=search;search=', $context['old_search_value'], '">', $txt['mlist_search_again'], '</a>';
	echo '
		</div>
	</div><br class="clear">';

}

/**
 * A page allowing people to search the member list.
 */
function template_search()
{
	global $context, $scripturl, $txt;

	// Start the submission form for the search!
	echo '
	<form action="', $scripturl, '?action=mlist;sa=search" method="post" accept-charset="', $context['character_set'], '">
		<div id="memberlist">
			<div class="pagesection">
				', template_button_strip($context['memberlist_buttons'], 'right'), '
			</div>
			<div class="cat_bar">
				<h3 class="catbg mlist">
					<span class="generic_icons filter"></span>', $txt['mlist_search'], '
				</h3>
			</div>
			<div id="advanced_search" class="roundframe noup">
				<dl id="mlist_search" class="settings">
					<dt>
						<label><strong>', $txt['search_for'], ':</strong></label>
					</dt>
					<dd>
						<input type="text" name="search" value="', $context['old_search'], '" size="40" class="input_text">
					</dd>
					<dt>
						<label><strong>', $txt['mlist_search_filter'], ':</strong></label>
					</dt>
					<dd>
						<ul>';

	foreach ($context['search_fields'] as $id => $title)
	{
		echo '
							<li>
								<input type="checkbox" name="fields[]" id="fields-', $id, '" value="', $id, '"', in_array($id, $context['search_defaults']) ? ' checked' : '', ' class="input_check">
								<label for="fields-', $id, '">', $title, '</label>
							</li>';
	}

	echo '
						</ul>
					</dd>
				</dl>
				<input type="submit" name="submit" value="' . $txt['search'] . '" class="button_submit">
			</div>
		</div>
	</form>';
}

?>