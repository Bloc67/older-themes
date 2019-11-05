<?php

/**
 * @package Blocthemes Admin
 * @version 1.2
 * @theme ShelfLife
 * @author Blocthemes - http://demos.bjornhkristiansen.com
 * Copyright (C) 2014-2016 - Blocthemes
 *
 */

function template_board($brds)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	$next = 1; $alt = true;
	foreach ($brds as $board)
	{
		echo '
<div class="windowbg" style="overflow: hidden;">
	<div class="info">
		<div class="bwgrid">
			<div class="bwcell11 wfull">
				<div class="bwgrid">
					<div class="w2">
						<a href="' , ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '" class="board_icon">&nbsp;</a>
					</div>
					<div class="w14"><div style="padding-left: 1rem;">
						<a class="largetext" href="', $board['href'], '" id="b', $board['id'], '">', $board['name'], '</a>';

		if ($board['new'] || $board['children_new'])
			echo '
						<span class="onoff_', $board['new'] ? 'on' : 'on2', '" title="', $txt['new_posts'], '"></span>';
		elseif ($board['is_redirect'])
			echo '
						<span class="onoff_redirect"></span>';
		else
			echo '
						<span class="onoff_off" title="', $txt['old_posts'], '"></span>';

		// Has it outstanding posts for approval?
		if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
			echo '
						<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';

		echo '
						<div>', $board['description'] , '</div>
						<div class="lastpost whide">';

		if (!empty($board['last_post']['id']))
			echo '
							<strong>', $txt['last_post'], '</strong>  ', $txt['by'], ' ', $board['last_post']['member']['link'] , '	', $txt['in'], ' ', $board['last_post']['link'], '	', $txt['on'], ' ', $board['last_post']['time'];
		
		echo '
						</div>';
		// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
		if (!empty($board['children']))
		{
			// Sort the links into an array with new boards bold so it can be imploded.
			$children = array();
			/* Each child in each board's children has:
					id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
			foreach ($board['children'] as $child)
			{
				if (!$child['is_redirect'])
					$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><span class="onoff_on"></span>' : '') . '</a>';
				else
					$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '">' . $child['name'] . '</a>';

				// Has it posts awaiting approval?
				if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
					$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

				$children[] = $child['new'] ? '<strong>' . $child['link'] . '</strong>' : $child['link'];
			}
			echo '
						<p>', $txt['parent_boards'], ': ', implode(', ', $children), '</p>';
		}
		$next++;
		echo '
					</div></div>
				</div>
			</div>
			<div class="bwcell5 whide">
				<div><br>
					<div class="largetext center_align">', $board['posts'], ' <span class="greytext">|</span> ', $board['topics'] . '</div>
					<div class="subtitle center_align">' ,  $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], $board['is_redirect'] ? '' : ' | ' .$txt['board_topics'], '</div>
				</div>
			</div>
		</div>
	</div>
</div>';
		$alt = !$alt; $first = false;
	}
}

function template_topiclist($tps, $checkboxes = false)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	foreach ($context['topics'] as $topic)
	{
		// Is this topic pending approval, or does it have any posts pending approval?
		if (isset($context['can_approve_posts']) && $context['can_approve_posts'] && $topic['unapproved_posts'])
			$color_class = !$topic['approved'] ? 'approvetbg' : 'approvebg';
		// We start with locked and sticky topics.
		elseif ($topic['is_sticky'] && $topic['is_locked'])
			$color_class = 'stickybg locked_sticky';
		// Sticky topics should get a different color, too.
		elseif ($topic['is_sticky'])
			$color_class = 'stickybg';
		// Locked topics get special treatment as well.
		elseif ($topic['is_locked'])
			$color_class = 'lockedbg';
		// Last, but not least: regular topics.
		else
			$color_class = 'windowbg';

		// Some columns require a different shade of the color class.
		$alternate_class = $color_class . '2';

		echo '
			<tr>
				<td class="icon1 ', $color_class, '">
					<img src="', $settings['images_url']. '/post/' . $topic['first_post']['icon'], '.png" alt="" />
				</td>
				<td class="subject ', $alternate_class, '">
					<div ', (!empty($topic['quick_mod']['modify']) ? 'id="topic_' . $topic['first_post']['id'] . '" onmouseout="mouse_on_div = 0;" onmouseover="mouse_on_div = 1;" ondblclick="modify_topic(\'' . $topic['id'] . '\', \'' . $topic['first_post']['id'] . '\');"' : ''), '>
						', $topic['is_sticky'] ? '<strong>' : '', '<span id="msg_' . $topic['first_post']['id'] . '"><span class="largetext">', $topic['first_post']['link'], '</span>' , (isset($topic['approved']) && isset($context['can_approve_posts']) && !$context['can_approve_posts'] && !$topic['approved'] ? '&nbsp;<em>(' . $txt['awaiting_approval'] . ')</em>' : ''), '</span>', $topic['is_sticky'] ? '</strong>' : '';

		if ($topic['is_locked'])
				echo '
						<span class="icon-lock" style="margin-left: 0.5rem; font-size: 120%;"></span>';

		// Is this topic new? (assuming they are logged in!)
		if (isset($topic['new']) && $topic['new'] && $context['user']['is_logged'])
				echo '
						<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><span class="onoff_on" title="', $txt['new'], '"></span></a>';

		echo '
						<div>', $txt['started_by'], ' ', $topic['first_post']['member']['link'], '
							<small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>
						</div>
					</div>
				</td>
				<td class="stats ', $color_class, '">
					', $topic['replies'], ' ', $txt['replies'], '
					<br />
					', $topic['views'], ' ', $txt['views'], '
				</td>
				<td class="lastpost ', $alternate_class, '">
					<a href="', $topic['last_post']['href'], '"><span class="icon-circle-right"></span></a>
					', $topic['last_post']['time'], '<br />
					', $txt['by'], ' ', $topic['last_post']['member']['link'], '
				</td>';

		if($checkboxes)
			echo '
				<td class="windowbg2" valign="middle" align="center">
					<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="input_check" />
				</td>';

		echo '
			</tr>';
	}
}


?>