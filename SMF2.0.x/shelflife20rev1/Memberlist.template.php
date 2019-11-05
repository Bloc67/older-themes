<?php

/**
 * @package Blocthemes Admin
 * @version 1.2
 * @theme ShelfLife
 * @author Blocthemes - http://demos.bjornhkristiansen.com
 * Copyright (C) 2014-2016 - Blocthemes
 *
 */

function template_main() 
{ 
	global $context, $settings, $options, $scripturl, $txt;

	// Build the memberlist button array.
	$memberlist_buttons = array(
			'view_all_members' => array('text' => 'view_all_members', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=all', 'active'=> true),
			'mlist_search' => array('text' => 'mlist_search', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=search'),
		);

	echo '
	<div class="main_section" id="memberlist">
		<div class="catbg">
			<h3>', $txt['members_list'], '</h3>';
		if (!isset($context['old_search']))
				echo '
			<span class="subtitle">', $context['letter_links'], '</span>';
		echo '
		</div>
		<div class="pagesection">
			', template_button_strip($memberlist_buttons, 'right'), '
			<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>
		</div>';

	echo '
		<div id="mlist" class="tborder topic_table">
			<table class="table_grid" cellspacing="0" width="100%">
			<thead>
				<tr class="catbg">';

	// Display each of the column headers of the table.
	foreach ($context['columns'] as $column)
	{
		// We're not able (through the template) to sort the search results right now...
		if (isset($context['old_search']))
			echo '
					<th scope="col" class="', isset($column['class']) ? ' ' . $column['class'] : '', '"', isset($column['width']) ? ' width="' . $column['width'] . '"' : '', isset($column['colspan']) ? ' colspan="' . $column['colspan'] . '"' : '', '>
						', $column['label'], '</th>';
		// This is a selected column, so underline it or some such.
		elseif ($column['selected'])
			echo '
					<th scope="col" class="', isset($column['class']) ? ' ' . $column['class'] : '', '" style="width: auto;"' . (isset($column['colspan']) ? ' colspan="' . $column['colspan'] . '"' : '') . ' nowrap="nowrap">
						<a href="' . $column['href'] . '" rel="nofollow">' . $column['label'] . ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" /></a></th>';
		// This is just some column... show the link and be done with it.
		else
			echo '
					<th scope="col" class="', isset($column['class']) ? ' ' . $column['class'] : '', '"', isset($column['width']) ? ' width="' . $column['width'] . '"' : '', isset($column['colspan']) ? ' colspan="' . $column['colspan'] . '"' : '', '>
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
				<tr ', empty($member['sort_letter']) ? '' : ' id="letter' . $member['sort_letter'] . '"', '>
					<td class="windowbg2">
						', $context['can_send_pm'] ? '<a href="' . $member['online']['href'] . '" title="' . $member['online']['text'] . '">' : '', '<span class="onoff_' , $member['online']['is_online'] ? 'on' : 'off' , '">', $context['can_send_pm'] ? '</a>' : '', '
					</td>
					<td class="windowbg lefttext">', $member['link'], '</td>
					<td class="windowbg2">', $member['show_email'] == 'no' ? '' : '<a href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $member['id'] . '" rel="nofollow"><span class="icon-envelop"></span></a>', '</td>';

		if (!isset($context['disabled_fields']['website']))
			echo '
					<td class="windowbg">', $member['website']['url'] != '' ? '<a href="' . $member['website']['url'] . '" target="_blank" class="new_win"><span class="icon-earth"></span></a>' : '', '</td>';

		// ICQ?
		if (!isset($context['disabled_fields']['icq']))
			echo '
					<td class="windowbg2">', $member['icq']['link'], '</td>';

		// AIM?
		if (!isset($context['disabled_fields']['aim']))
			echo '
					<td class="windowbg2">', $member['aim']['link'], '</td>';

		// YIM?
		if (!isset($context['disabled_fields']['yim']))
			echo '
					<td class="windowbg2">', $member['yim']['link'], '</td>';

		// MSN?
		if (!isset($context['disabled_fields']['msn']))
			echo '
					<td class="windowbg2">', $member['msn']['link'], '</td>';

		// Group and date.
		echo '
					<td class="windowbg lefttext">', empty($member['group']) ? $member['post_group'] : $member['group'], '</td>
					<td class="windowbg lefttext">', $member['registered_date'], '</td>';

		if (!isset($context['disabled_fields']['posts']))
		{
			echo '
					<td class="windowbg2" style="white-space: nowrap" width="15">', $member['posts'], '</td>
					<td class="windowbg" style="width: 120px;">';

			if (!empty($member['post_percent']))
				echo '
						<div class="statsbar morebar">
							<div style="width: ', $member['post_percent'], '%;"></div>
						</div>';

			echo '
					</td>';
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

	// Show the page numbers again. (makes 'em easier to find!)
	echo '
			</tbody>
			</table>
		</div>';

	echo '
		<div class="pagesection clear">
			<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>';

	// If it is displaying the result of a search show a "search again" link to edit their criteria.
	if (isset($context['old_search']))
		echo '
			<div class="floatright">
				<a href="', $scripturl, '?action=mlist;sa=search;search=', $context['old_search_value'], '">', $txt['mlist_search_again'], '</a>
			</div>';
	echo '<br>
		</div>
	</div>';

}

// A page allowing people to search the member list.
function template_search()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Build the memberlist button array.
	$memberlist_buttons = array(
			'view_all_members' => array('text' => 'view_all_members', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=all'),
			'mlist_search' => array('text' => 'mlist_search', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=search', 'active' => true),
		);

	// Start the submission form for the search!
	echo '
	<form action="', $scripturl, '?action=mlist;sa=search" method="post" accept-charset="', $context['character_set'], '">
		<div id="memberlist">
			<div class="pagesection">
				', template_button_strip($memberlist_buttons, ''), '
			</div>
			<div class="catbg clear">
				<h3>
					', $txt['mlist_search'], '
				</h3>
			</div>';

	// Display the input boxes for the form.
	echo '	<div id="memberlist_search" class="clear">
				<div style="overflow: hidden;">
					<div id="mlist_search" class="themepadding">
						<div id="search_term_input" class="bwgrid">
							<div class="bwcell2"><strong>', $txt['search_for'], ':</strong></div>
							<div class="bwcell10"><input type="text" name="search" value="', $context['old_search'], '"class="fullwidth input_text"  /></div> 
							<div class="bwcell4"><input type="submit" name="submit" value="' . $txt['search'] . '" style="margin: 1rem 0;" class="button_submit" /></div>
						</div><br>
						<div class="bwgrid">';

	$count = 0;
	foreach ($context['search_fields'] as $id => $title)
	{
		echo '
							<span class="bwcell33">
								<label for="fields-', $id, '"><input type="checkbox" name="fields[]" id="fields-', $id, '" value="', $id, '" ', in_array($id, $context['search_defaults']) ? 'checked="checked"' : '', ' class="input_check" />&nbsp; ', $title, '</label><br />
							</span>';
	}
		echo '
						</div><br class="clear" />
					</div>
				</div>
			</div>
		</div>
	</form>';
}

?>