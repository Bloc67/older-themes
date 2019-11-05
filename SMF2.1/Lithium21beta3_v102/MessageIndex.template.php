<?php

// Lithium 1.0
// a rewrite theme

function template_main()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	// the child boards
	if (!empty($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		echo '
	<div class="cat_bar">
		<h3 class="catbg">', $txt['sub_boards'], '</h3>
	</div>';
		subtemplate_boards($context['boards']);
	}

	// the messages
	echo '
	<div class="title_bar">
		<h2 class="titlebg">', $context['name'], '</h2>
	</div>';

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'])
		unset($context['normal_buttons']['markread']);

	if(!empty($context['topics']))
		echo '
	<div>
		<a href="#fbottom" class="floatleft"><span class="icon-chevron-down"></span></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// If Quick Moderation is enabled start the form.
	if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] > 0 && !empty($context['topics']))
		echo '
	<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" class="clear" name="quickModForm" id="quickModForm">';

	echo '
		<div id="messageindex">
			<div class="cat_bar">';

	// Are there actually any topics to show?
	if (!empty($context['topics']))
	{
		echo '
				<h3 class="catbg">
					<span>', $context['topics_headers']['subject'], ' / ', $context['topics_headers']['starter'], ' /
					', $context['topics_headers']['replies'], ' / ', $context['topics_headers']['views'], ' /
					', $context['topics_headers']['last_post'];

		// Show a "select all" box for quick moderation?
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1)
			echo '
					 <input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check floatright">';

		echo '	</span>
				</h3>';
	}
	// No topics.... just say, "sorry bub".
	else
		echo '
				<h3 class="subbg">', $txt['topic_alert_none'], '</h3>';

	echo '
			</div>
			<div id="topic_container">';

	subtemplate_topiclist();

	if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']))
	{
		echo '
			<div class="righttext" id="quick_actions">
				<select class="qaction" name="qaction"', $context['can_move'] ? ' onchange="this.form.move_to.disabled = (this.options[this.selectedIndex].value != \'move\');"' : '', '>
					<option value="">--------</option>';

		foreach ($context['qmod_actions'] as $qmod_action)
			if ($context['can_' . $qmod_action])
				echo '
					<option value="' . $qmod_action . '">' . $txt['quick_mod_' . $qmod_action] . '</option>';

		echo '
				</select>';

		// Show a list of boards they can move the topic to.
		if ($context['can_move'])
			echo '
				<span id="quick_mod_jump_to">&nbsp;</span>';

		echo '
				<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" class="button_submit qaction">
			</div>';
	}
	
	echo '
			</div>
		</div>';

	// Finish off the form - again.
	if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] > 0 && !empty($context['topics']))
		echo '
		<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
	</form>';

	if(!empty($context['topics']))
		echo '<br>
	<div class="clear">
		<a href="#fcontent" class="topbottom floatleft"><span class="icon-chevron-up"></span></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';
	
	if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] == 1 && !empty($context['topics']) && $context['can_move'])
		echo '
			<script>
				if (typeof(window.XMLHttpRequest) != "undefined")
					aJumpTo[aJumpTo.length] = new JumpTo({
						sContainerId: "quick_mod_jump_to",
						sClassName: "qaction",
						sJumpToTemplate: "%dropdown_list%",
						iCurBoardId: ', $context['current_board'], ',
						iCurBoardChildLevel: ', $context['jump_to']['child_level'], ',
						sCurBoardName: "', $context['jump_to']['board_name'], '",
						sBoardChildLevelIndicator: "==",
						sBoardPrefix: "=> ",
						sCatSeparator: "-----------------------------",
						sCatPrefix: "",
						bNoRedirect: true,
						bDisabled: true,
						sCustomName: "move_to"
					});
			</script>';
}

function subtemplate_aside()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	// Let them know why their message became unapproved.
	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '</div>';

	if ($context['description'] != '' || !empty($context['moderators']))
	{
		if ($context['description'] != '')
			echo '<br>
	<div class="desc">', $context['description'], '&nbsp;';

		if (!empty($context['moderators']))
			echo '
		', count($context['moderators']) === 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $context['link_moderators']), '.';

		echo '
	</div><hr>';
	}
	echo '
	<div class="top_padding des">', template_button_strip($context['normal_buttons'], 'menu_nav dropmenu nolist full'), '</div>';

	// If this person can approve items and we have some awaiting approval tell them.
	if (!empty($context['unapproved_posts_message']))
	{
		echo '
	<div class="information">
		<span class="alert">!</span>', $context['unapproved_posts_message'], '
	</div>';
	}

	if (!empty($settings['display_who_viewing']))
	{
		echo '
	<div class="information">';
		if ($settings['display_who_viewing'] == 1)
			echo count($context['view_members']), ' ', count($context['view_members']) === 1 ? $txt['who_member'] : $txt['members'];
		else
			echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . (empty($context['view_num_hidden']) || $context['can_moderate_forum'] ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			
		echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'];
		echo '
	</div>';
	}
}

// for the mobie bottom menu, containers
function template_f_menu()
{
	echo '
			<li onclick="fPop_slide(\'#messmenu\'); return false;"><span class="icon-menu"></span><span class="amt smaller">2</span></li>';
}

// for the mobie bottom menu, containers
function template_f_menu_subs()
{
	global $context;

	echo '
			<div class="bot_menu_mobile" id="messmenu">' , template_mob_button_strip($context['normal_buttons']) , '</div>';
}

?>