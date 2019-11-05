<?php

// Cesium 1.0
// a rewrite theme

function template_recent()
{
	global $context, $txt, $scripturl;

	echo '
	<div id="recent" class="main_section">
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="xx"></span>',$txt['recent_posts'],'
			</h3>
		</div>
		<div class="pagesection">', $context['page_index'], '</div><br>';

	if (empty($context['posts']))
	{
		echo '
			<div class="windowbg">', $txt['no_messages'], '</div>';
	}

	foreach ($context['posts'] as $post)
	{
		echo '
			<div class="', $post['css_class'], '">
					<div class="counter floatleft" style="margin-right: 1rem;">', $post['counter'], '</div>
					<div class="topic_details">
						<h4>', $post['board']['link'], ' / ', $post['link'], '</h4>
						<span class="smalltext">', $txt['last_poster'], ' <strong>', $post['poster']['link'], ' </strong> - ', $post['time'], '</span>
					</div>
					<div class="post" style="margin-top: 1rem;">', $post['message'], '</div>
					';

		if ($post['can_reply'] || $post['can_quote'] || $post['can_delete'])
			echo '
					<div class="toppadding"><ul class="nolist horiz_list">';

		// If they *can* reply?
		if ($post['can_reply'])
			echo '
						<li><a href="', $scripturl, '?action=post;topic=', $post['topic'], '.', $post['start'], '"><span class="generic_icons reply_button"></span>', $txt['reply'], '</a></li>';

		// If they *can* quote?
		if ($post['can_quote'])
			echo '
						<li><a href="', $scripturl, '?action=post;topic=', $post['topic'], '.', $post['start'], ';quote=', $post['id'], '"><span class="generic_icons quote"></span>', $txt['quote_action'], '</a></li>';

		// How about... even... remove it entirely?!
		if ($post['can_delete'])
			echo '
						<li><a href="', $scripturl, '?action=deletemsg;msg=', $post['id'], ';topic=', $post['topic'], ';recent;', $context['session_var'], '=', $context['session_id'], '" data-confirm="', $txt['remove_message'], '" class="you_sure"><span class="generic_icons remove_button"></span>', $txt['remove'], '</a></li>';

		if ($post['can_reply'] || $post['can_quote'] || $post['can_delete'])
			echo '
					</ul></div>';

		echo '<br class="clear">	
			</div>';

	}

	echo '
		<div class="pagesection">', $context['page_index'], '</div>
	</div>';
}

/**
 * Template for showing unread posts
 */
function template_unread()
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	echo '
	<div class="title_bar">
		<h2 class="titlebg">', $txt['unread_topics_visit'], '</h2>
	</div>';

	if ($context['showCheckboxes'])
		echo '
	<form action="', $scripturl, '?action=quickmod" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;">
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		<input type="hidden" name="qaction" value="markread">
		<input type="hidden" name="redirect_url" value="action=unread', (!empty($context['showing_all_topics']) ? ';all' : ''), $context['querystring_board_limits'], '">';

	if (!empty($context['topics']))
	{
		echo '
		<div class="pagesection">
			<a href="#fbottom" class="topbottom floatleft"><span class="icon-chevron-down"></span></a>
			<div class="pagelinks">', $context['page_index'], '</div>
		</div>
		<div class="cat_bar">
			<h3 class="catbg">
				<span>
					<a href="', $scripturl, '?action=unread', $context['showing_all_topics'] ? ';all' : '', $context['querystring_board_limits'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					/	<a href="', $scripturl, '?action=unread', $context['showing_all_topics'] ? ';all' : '', $context['querystring_board_limits'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] == 'replies' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					/	<a href="', $scripturl, '?action=unread', $context['showing_all_topics'] ? ';all' : '', $context['querystring_board_limits'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>	';

		// Show a "select all" box for quick moderation?
		if ($context['showCheckboxes'])
			echo '
					<input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check floatright">';

		echo '
				</span>
			</h3>
		</div>
		<div id="topic_container">';

		subtemplate_topiclist();

		echo '
		</div>
		<div class="pagesection clear">
			<a href="#fcontent" class="topbottom floatleft"><span class="icon-chevron-up"></span></a>
			<div class="pagelinks">', $context['page_index'], '</div>
		</div>';
	}
	else
		echo '
				<b>', $context['showing_all_topics'] ? $txt['topic_alert_none'] : $txt['unread_topics_visit_none'], '</b>';

	if ($context['showCheckboxes'])
		echo '
	</form>';

}
function subtemplate_aside_twin()
{
	global $context;

	if(!empty($context['recent_buttons']))
		echo '
	<div class="top_padding des">', template_button_strip($context['recent_buttons'], 'menu_nav dropmenu nolist full'), '</div>';
}

// for the mobie bottom menu, containers
function template_f_menu_twin()
{
	global $context;

	if(!empty($context['recent_buttons']))
		echo '
			<li onclick="fPop_slide(\'#recmenu\'); return false;"><span class="icon-menu"></span><span class="amt smaller">2</span></li>';
}

// for the mobie bottom menu, containers
function template_f_menu_subs_twin()
{
	global $context;

	if(!empty($context['recent_buttons']))
		echo '
			<div class="bot_menu_mobile" id="recmenu">' , template_mob_button_strip($context['recent_buttons']) , '</div>';
}

/**
 * Template for showing unread replies (eg new replies to topics you've posted in)
 */
function template_replies()
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	echo '
	<div class="title_bar">
		<h2 class="titlebg">', $txt['unread_replies'], '</h2>
	</div>';

	if ($context['showCheckboxes'])
		echo '
	<form action="', $scripturl, '?action=quickmod" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;">
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		<input type="hidden" name="qaction" value="markread">
		<input type="hidden" name="redirect_url" value="action=unreadreplies', (!empty($context['showing_all_topics']) ? ';all' : ''), $context['querystring_board_limits'], '">';

	if (!empty($context['topics']))
	{
		echo '
		<div class="pagesection">
			<a href="#fbottom" class="topbottom floatleft"><span class="icon-chevron-down"></span></a>
			<div class="pagelinks">', $context['page_index'], '</div>
		</div>
		<div class="cat_bar">
			<h3 class="catbg">
				<span>
					<a href="', $scripturl, '?action=unreadreplies', $context['querystring_board_limits'], ';sort=subject', $context['sort_by'] === 'subject' && $context['sort_direction'] === 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] === 'subject' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					/ <a href="', $scripturl, '?action=unreadreplies', $context['querystring_board_limits'], ';sort=replies', $context['sort_by'] === 'replies' && $context['sort_direction'] === 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] === 'replies' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					/ <a href="', $scripturl, '?action=unreadreplies', $context['querystring_board_limits'], ';sort=last_post', $context['sort_by'] === 'last_post' && $context['sort_direction'] === 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] === 'last_post' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>';

		// Show a "select all" box for quick moderation?
		if ($context['showCheckboxes'])
			echo '
					<input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check floatright">';

		echo '
				</span>
			</h3>
		</div>
		<div id="topic_container">';

		subtemplate_topiclist();

		echo '
		</div>
		<div class="pagesection clear">
			<a href="#fcontent" class="topbottom floatleft"><span class="icon-chevron-up"></span></a>
			<div class="pagelinks">', $context['page_index'], '</div>
		</div>';
	}
	else
		echo '
				<b>', $context['showing_all_topics'] ? $txt['topic_alert_none'] : $txt['unread_topics_visit_none'], '</b>';

	if ($context['showCheckboxes'])
		echo '
	</form>';

}

?>