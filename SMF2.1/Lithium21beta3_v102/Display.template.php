<?php

// Lithium 1.0
// a rewrite theme

function template_main()
{
	global $settings, $board;

	if(!empty($settings['use_blogs']) && in_array($board,explode(",",$settings['blogs_boards'])))
		template_blog_display();
	elseif(!empty($settings['use_galleries']) && in_array($board,explode(",",$settings['galleries_boards'])))
		template_galleries_display();
	elseif(!empty($settings['use_files']) && in_array($board,explode(",",$settings['files_boards'])))
		template_files_display();
	elseif(!empty($settings['use_news']) && in_array($board,explode(",",$settings['news_boards'])))
		template_news_display();
	elseif(!empty($settings['use_bugs']) && in_array($board,explode(",",$settings['bugs_boards'])))
		template_bugs_display();
	elseif(!empty($settings['use_docs']) && in_array($board,explode(",",$settings['docs_boards'])))
		template_docs_display();
	else
		template_normal_display();
}

function template_normal_display()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($context['report_sent'])
		echo '
	<div class="information">', $txt['report_sent'], '</div>';

	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '	</div>';

	echo '
	<div class="title_bar">
		<h2 class="titlebg">
			', $context['subject'], '</span>', ($context['is_locked']) ? ' <span class="icon-lock"></span>' : '', ($context['is_sticky']) ? ' <span class="icon-tag2"></span>' : '', '
		</h2>
	</div>
	<div class="preview">',$txt['started_by'], ' ', $context['topic_poster_name'], ', ', $context['topic_started_time'], '</div>';

	template_display_poll();
	template_display_events();

	echo '
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '';

	if($context['topicinfo']['num_replies']>0)
		echo '
	<div>
		<a href="#bot" class="topbottom floatleft" id="top"><span class="icon-chevron-down"></span></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';
	
	echo '
	<div id="forumposts">
		<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	$context['ignoredMsgs'] = array();
	$context['removableMessageIDs'] = array();

	// Get all the messages...
	while ($message = $context['get_message']())
		template_single_post($message);

	echo '
		</form>
	</div>';
	
	if($context['topicinfo']['num_replies']>0)
		echo '
	<div>
		<a href="#top" class="topbottom floatleft" id="bot"><span class="icon-chevron-up"></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// Show quickreply
	if ($context['can_reply'])
		template_quickreply();

	echo '
	<script>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
	{
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $context['removableMessageIDs']), '\'],
						sSessionId: smf_session_id,
						sSessionVar: smf_session_var,
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.png\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.png\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanSplit: ', $context['can_split'] ? 'true' : 'false', ',
						sSplitButtonLabel: \'', $txt['quickmod_split_selected'], '\',
						sSplitButtonImage: \'split_selected.png\',
						sSplitButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';
	}

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							sClassName: \'quick_edit\',
							bShowModify: ', $modSettings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" class="error"></div>
									<textarea class="editor" name="message" style="margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br>
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '">
									<input type="hidden" name="msg" value="%msg_id%">
									<div class="righttext quickModifyMargin">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit">&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit">&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit">
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text">'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%'), ',
							sTemplateReasonEdit: ', JavaScriptEscape($txt['reason_for_edit'] . ': <input type="text" name="modify_reason" value="%modify_reason%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text quickModifyMargin">'), ',
							sTemplateReasonNormal: ', JavaScriptEscape('%modify_text'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), ($context['can_reply']) ? ',
							sFormRemoveAccessKeys: \'postmodify\'' : '', '
						});

					}';

	if (!empty($context['ignoredMsgs']))
		echo '
					ignore_toggles([', implode(', ', $context['ignoredMsgs']), '], ', JavaScriptEscape($txt['show_ignore_user_post']), ');';

	echo '
				</script>';
}

function template_display_poll()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Is this topic also a poll?
	if ($context['is_poll'])
	{
		echo '
	<div id="poll">
		<div class="sub_bar">
			<h3 class="subbg">
				', $context['poll']['is_locked'] ? '<span class="generic_icons lock"></span>' : '', ' ', $context['poll']['question'], '
			</h3>
		</div>
		<div class="toppadding">
			<div id="poll_options">';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			echo '
			<dl class="statspanel settings">';

			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
				<dt class="', $option['voted_this'] ? ' voted' : '', '">', $option['option'], '</dt>
				<dd class="', $option['voted_this'] ? ' voted' : '', '">';

				if ($context['allow_results_view'])
					echo 	empty($context['hide_num_posts']) ? '<span class="floatright">'.$option['votes'].'</span>' : '', '
					<span class="backline">
						<span class="percentline" style="width: ' , $option['percent'] , '%;"></span>
						<span class="rightcircle" style="left: ' , $option['percent'] , '%;"></span>
					</span>';
				
				echo '
				</dd>';
			}
			echo '
				</dl>';

			if ($context['allow_results_view'])
				echo '
				<div><hr>', $txt['poll_total_voters'], ': ', $context['poll']['total_votes'], '</div>';
		}
		// They are allowed to vote! Go to it!
		else
		{
			echo '
				<form action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';

			// Show a warning if they are allowed more than one option.
			if ($context['poll']['allowed_warning'])
				echo '
					<p class="smallpadding">', $context['poll']['allowed_warning'], '</p>';

			echo '
					<ul class="nolist">';

			// Show each option with its button - a radio likely.
			foreach ($context['poll']['options'] as $option)
				echo '
						<li>', $option['vote_button'], ' <label for="', $option['id'], '">', $option['option'], '</label></li>';

			echo '
					</ul><br>
					<div class="single_action">
						<input type="submit" value="', $txt['poll_vote'], '" class="button_submit">
						<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
					</div>
				</form>';
		}

		// Is the clock ticking?
		if (!empty($context['poll']['expire_time']))
			echo '
				<p><strong>', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ':</strong> ', $context['poll']['expire_time'], '</p>';

		echo '
			</div>
		</div>
	</div>
	<div id="pollmoderation">';

		template_button_strip($context['poll_buttons'], 'nolist horiz_list');

		echo '<hr><br class="clear" />
	</div>';
	}
}

function template_display_events()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
<div class="toppadding">
	<div class="cat_bar">
		<h3 class="catbg">', $txt['calendar_linked_events'], '</h3>
	</div>
	<div class="infobox">
		<ul class="nolist">';

		foreach ($context['linked_calendar_events'] as $event)
		{
			echo '
			<li>
				<b class="event_title"><a href="', $scripturl, '?action=calendar;event=', $event['id'], '">', $event['title'], '</a></b>';

			if ($event['can_edit'])
				echo ' &nbsp;<a href="' . $event['modify_href'] . '"><span class="icon-cog" title="', $txt['calendar_edit'], '"></span></a>';

			if ($event['can_export'])
				echo ' &nbsp;<a href="' . $event['export_href'] . '"><span class="icon-box" title="', $txt['calendar_export'], '"></span></a>';

			echo '
				<br>';

			if (!empty($event['allday']))
			{
				echo '<time datetime="' . $event['start_iso_gmdate'] . '">', trim($event['start_date_local']), '</time>', ($event['start_date'] != $event['end_date']) ? ' &ndash; <time datetime="' . $event['end_iso_gmdate'] . '">' . trim($event['end_date_local']) . '</time>' : '';
			}
			else
			{
				// Display event info relative to user's local timezone
				echo '<time datetime="' . $event['start_iso_gmdate'] . '">', trim($event['start_date_local']), ', ', trim($event['start_time_local']), '</time> &ndash; <time datetime="' . $event['end_iso_gmdate'] . '">';

				if ($event['start_date_local'] != $event['end_date_local'])
					echo trim($event['end_date_local']) . ', ';

				echo trim($event['end_time_local']);

				// Display event info relative to original timezone
				if ($event['start_date_local'] . $event['start_time_local'] != $event['start_date_orig'] . $event['start_time_orig'])
				{
					echo '</time> (<time datetime="' . $event['start_iso_gmdate'] . '">';

					if ($event['start_date_orig'] != $event['start_date_local'] || $event['end_date_orig'] != $event['end_date_local'] || $event['start_date_orig'] != $event['end_date_orig'])
						echo trim($event['start_date_orig']), ', ';

					echo trim($event['start_time_orig']), '</time> &ndash; <time datetime="' . $event['end_iso_gmdate'] . '">';

					if ($event['start_date_orig'] != $event['end_date_orig'])
						echo trim($event['end_date_orig']) . ', ';

					echo trim($event['end_time_orig']), ' ', $event['tz_abbrev'], '</time>)';
				}
				// Event is scheduled in the user's own timezone? Let 'em know, just to avoid confusion
				else
					echo ' ', $event['tz_abbrev'], '</time>';
			}

			if (!empty($event['location']))
				echo '<span class="location">', $event['location'],'</span>';

			echo '<br class="clear">
			</li>';
		}
		echo '
		</ul>
	</div>
</div>';
	}
}

// blogs
function template_blog_display()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($context['report_sent'])
		echo '
	<div class="information">', $txt['report_sent'], '</div>';

	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '	</div>';

	echo '
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '', '
	<div id="forumposts">
		<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	if($context['current_page']>0)
		echo '
	<div class="largetext toppadding"><hr>
		<a href="' , $scripturl , '?topic=' , $context['current_topic'] , '.0"><span class="icon-arrow-left"></span> ', $txt['ces_back2topic'] , '</a>
	</div>';

	$context['ignoredMsgs'] = array();
	$context['removableMessageIDs'] = array();

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if($message['id']==$context['topicinfo']['id_first_msg'])
		{
			subtemplate_blog_single($message, false);
			echo '
			</form>';
			template_display_poll();
			template_display_events();
			echo '
			<h3 class="catbg">' , $txt['ces_comments'] , '</h3>';
			if($context['topicinfo']['num_replies']>0)
				echo '
			<div>
				<a href="#bot" class="topbottom floatleft" id="top"><span class="icon-chevron-down"></span></a>
				<div class="pagelinks">', $context['page_index'], '</div>
			</div>';
		}
		else
			subtemplate_blog_single($message, true);

	}
	echo '
	</div>';
	if($context['topicinfo']['num_replies']>0)
		echo '
	<div>
		<a href="#top" class="topbottom floatleft" id="bot"><span class="icon-chevron-up"></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// Show quickreply
	if ($context['can_reply'])
		template_quickreply();

	echo '
	<script>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
	{
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $context['removableMessageIDs']), '\'],
						sSessionId: smf_session_id,
						sSessionVar: smf_session_var,
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.png\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.png\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanSplit: ', $context['can_split'] ? 'true' : 'false', ',
						sSplitButtonLabel: \'', $txt['quickmod_split_selected'], '\',
						sSplitButtonImage: \'split_selected.png\',
						sSplitButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';
	}

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							sClassName: \'quick_edit\',
							bShowModify: ', $modSettings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" class="error"></div>
									<textarea class="editor" name="message" style="margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br>
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '">
									<input type="hidden" name="msg" value="%msg_id%">
									<div class="righttext quickModifyMargin">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit">&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit">&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit">
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text">'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%'), ',
							sTemplateReasonEdit: ', JavaScriptEscape($txt['reason_for_edit'] . ': <input type="text" name="modify_reason" value="%modify_reason%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text quickModifyMargin">'), ',
							sTemplateReasonNormal: ', JavaScriptEscape('%modify_text'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), ($context['can_reply']) ? ',
							sFormRemoveAccessKeys: \'postmodify\'' : '', '
						});

					}';

	if (!empty($context['ignoredMsgs']))
		echo '
					ignore_toggles([', implode(', ', $context['ignoredMsgs']), '], ', JavaScriptEscape($txt['show_ignore_user_post']), ');';

	echo '
				</script>';
}
// galleries
function template_galleries_display()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($context['report_sent'])
		echo '
	<div class="information">', $txt['report_sent'], '</div>';

	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '	</div>';

	echo '
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '', '
	<div id="forumposts">
		<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	if($context['current_page']>0)
		echo '
	<div class="largetext toppadding"><hr>
		<a href="' , $scripturl , '?topic=' , $context['current_topic'] , '.0"><span class="icon-arrow-left"></span> ', $txt['ces_back2topic'] , '</a>
	</div>';

	$context['ignoredMsgs'] = array();
	$context['removableMessageIDs'] = array();

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if($message['id']==$context['topicinfo']['id_first_msg'])
		{
			subtemplate_galleries_single($message, false);
			echo '
			</form>';
			template_display_poll();
			template_display_events();
			echo '
			<h3 class="catbg">' , $txt['ces_comments'] , '</h3>';
		}
		else
			subtemplate_galleries_single($message, true);

	}
	echo '
	</div>
	<div>
		<a href="#top" class="topbottom floatleft" id="bot"><span class="icon-chevron-up"></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// Show quickreply
	if ($context['can_reply'])
		template_quickreply();

	echo '
	<script>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
	{
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $context['removableMessageIDs']), '\'],
						sSessionId: smf_session_id,
						sSessionVar: smf_session_var,
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.png\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.png\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanSplit: ', $context['can_split'] ? 'true' : 'false', ',
						sSplitButtonLabel: \'', $txt['quickmod_split_selected'], '\',
						sSplitButtonImage: \'split_selected.png\',
						sSplitButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';
	}

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							sClassName: \'quick_edit\',
							bShowModify: ', $modSettings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" class="error"></div>
									<textarea class="editor" name="message" style="margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br>
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '">
									<input type="hidden" name="msg" value="%msg_id%">
									<div class="righttext quickModifyMargin">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit">&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit">&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit">
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text">'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%'), ',
							sTemplateReasonEdit: ', JavaScriptEscape($txt['reason_for_edit'] . ': <input type="text" name="modify_reason" value="%modify_reason%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text quickModifyMargin">'), ',
							sTemplateReasonNormal: ', JavaScriptEscape('%modify_text'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), ($context['can_reply']) ? ',
							sFormRemoveAccessKeys: \'postmodify\'' : '', '
						});

					}';

	if (!empty($context['ignoredMsgs']))
		echo '
					ignore_toggles([', implode(', ', $context['ignoredMsgs']), '], ', JavaScriptEscape($txt['show_ignore_user_post']), ');';

	echo '
				</script>';
}

// files
function template_files_display()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($context['report_sent'])
		echo '
	<div class="information">', $txt['report_sent'], '</div>';

	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '	</div>';

	echo '
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '', '
	<div id="forumposts">
		<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	if($context['current_page']>0)
		echo '
	<div class="largetext toppadding"><hr>
		<a href="' , $scripturl , '?topic=' , $context['current_topic'] , '.0"><span class="icon-arrow-left"></span> ', $txt['ces_back2topic'] , '</a>
	</div>';

	$context['ignoredMsgs'] = array();
	$context['removableMessageIDs'] = array();

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if($message['id']==$context['topicinfo']['id_first_msg'])
		{
			subtemplate_files_single($message, false);
			echo '
			</form>';
			template_display_poll();
			template_display_events();
			echo '
			<h3 class="catbg">' , $txt['ces_comments'] , '</h3>';
		}
		else
			subtemplate_files_single($message, true);

	}
	echo '
	</div>
	<div>
		<a href="#top" class="topbottom floatleft" id="bot"><span class="icon-chevron-up"></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// Show quickreply
	if ($context['can_reply'])
		template_quickreply();

	echo '
	<script>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
	{
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $context['removableMessageIDs']), '\'],
						sSessionId: smf_session_id,
						sSessionVar: smf_session_var,
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.png\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.png\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanSplit: ', $context['can_split'] ? 'true' : 'false', ',
						sSplitButtonLabel: \'', $txt['quickmod_split_selected'], '\',
						sSplitButtonImage: \'split_selected.png\',
						sSplitButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';
	}

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							sClassName: \'quick_edit\',
							bShowModify: ', $modSettings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" class="error"></div>
									<textarea class="editor" name="message" style="margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br>
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '">
									<input type="hidden" name="msg" value="%msg_id%">
									<div class="righttext quickModifyMargin">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit">&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit">&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit">
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text">'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%'), ',
							sTemplateReasonEdit: ', JavaScriptEscape($txt['reason_for_edit'] . ': <input type="text" name="modify_reason" value="%modify_reason%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text quickModifyMargin">'), ',
							sTemplateReasonNormal: ', JavaScriptEscape('%modify_text'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), ($context['can_reply']) ? ',
							sFormRemoveAccessKeys: \'postmodify\'' : '', '
						});

					}';

	if (!empty($context['ignoredMsgs']))
		echo '
					ignore_toggles([', implode(', ', $context['ignoredMsgs']), '], ', JavaScriptEscape($txt['show_ignore_user_post']), ');';

	echo '
				</script>';
}

// bugs
function template_bugs_display()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($context['report_sent'])
		echo '
	<div class="information">', $txt['report_sent'], '</div>';

	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '	</div>';

	echo '
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '', '
	<div id="forumposts">
		<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	if($context['current_page']>0)
		echo '
	<div class="largetext toppadding"><hr>
		<a href="' , $scripturl , '?topic=' , $context['current_topic'] , '.0"><span class="icon-arrow-left"></span> ', $txt['ces_back2topic'] , '</a>
	</div>';

	$context['ignoredMsgs'] = array();
	$context['removableMessageIDs'] = array();

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if($message['id']==$context['topicinfo']['id_first_msg'])
		{
			subtemplate_bugs_single($message, false);
			echo '
			</form>';
			template_display_poll();
			template_display_events();
			echo '
			<h3 class="catbg">' , $txt['ces_comments'] , '</h3>';
		}
		else
			subtemplate_bugs_single($message, true);

	}
	echo '
	</div>';
	if($context['topicinfo']['num_replies']>0)
		echo '
	<div>
		<a href="#top" class="topbottom floatleft" id="bot"><span class="icon-chevron-up"></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// Show quickreply
	if ($context['can_reply'])
		template_quickreply();

	echo '
	<script>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
	{
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $context['removableMessageIDs']), '\'],
						sSessionId: smf_session_id,
						sSessionVar: smf_session_var,
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.png\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.png\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanSplit: ', $context['can_split'] ? 'true' : 'false', ',
						sSplitButtonLabel: \'', $txt['quickmod_split_selected'], '\',
						sSplitButtonImage: \'split_selected.png\',
						sSplitButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';
	}

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							sClassName: \'quick_edit\',
							bShowModify: ', $modSettings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" class="error"></div>
									<textarea class="editor" name="message" style="margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br>
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '">
									<input type="hidden" name="msg" value="%msg_id%">
									<div class="righttext quickModifyMargin">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit">&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit">&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit">
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text">'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%'), ',
							sTemplateReasonEdit: ', JavaScriptEscape($txt['reason_for_edit'] . ': <input type="text" name="modify_reason" value="%modify_reason%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text quickModifyMargin">'), ',
							sTemplateReasonNormal: ', JavaScriptEscape('%modify_text'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), ($context['can_reply']) ? ',
							sFormRemoveAccessKeys: \'postmodify\'' : '', '
						});

					}';

	if (!empty($context['ignoredMsgs']))
		echo '
					ignore_toggles([', implode(', ', $context['ignoredMsgs']), '], ', JavaScriptEscape($txt['show_ignore_user_post']), ');';

	echo '
				</script>';
}

// docs
function template_docs_display()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($context['report_sent'])
		echo '
	<div class="information">', $txt['report_sent'], '</div>';

	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '	</div>';

	echo '
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '', '
	<div id="forumposts">
		<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	if($context['current_page']>0)
		echo '
	<div class="largetext toppadding"><hr>
		<a href="' , $scripturl , '?topic=' , $context['current_topic'] , '.0"><span class="icon-arrow-left"></span> ', $txt['ces_back2topic'] , '</a>
	</div>';

	$context['ignoredMsgs'] = array();
	$context['removableMessageIDs'] = array();

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if($message['id']==$context['topicinfo']['id_first_msg'])
		{
			subtemplate_docs_single($message, false);
			echo '
			</form>';
			template_display_poll();
			template_display_events();
			echo '
			<h3 class="catbg">' , $txt['ces_comments'] , '</h3>';
		}
		else
			subtemplate_docs_single($message, true);

	}
	echo '
	</div>';
	if($context['topicinfo']['num_replies']>0)
		echo '
	<div>
		<a href="#top" class="topbottom floatleft" id="bot"><span class="icon-chevron-up"></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// Show quickreply
	if ($context['can_reply'])
		template_quickreply();

	echo '
	<script>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
	{
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $context['removableMessageIDs']), '\'],
						sSessionId: smf_session_id,
						sSessionVar: smf_session_var,
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.png\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.png\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanSplit: ', $context['can_split'] ? 'true' : 'false', ',
						sSplitButtonLabel: \'', $txt['quickmod_split_selected'], '\',
						sSplitButtonImage: \'split_selected.png\',
						sSplitButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';
	}

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							sClassName: \'quick_edit\',
							bShowModify: ', $modSettings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" class="error"></div>
									<textarea class="editor" name="message" style="margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br>
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '">
									<input type="hidden" name="msg" value="%msg_id%">
									<div class="righttext quickModifyMargin">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit">&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit">&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit">
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text">'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%'), ',
							sTemplateReasonEdit: ', JavaScriptEscape($txt['reason_for_edit'] . ': <input type="text" name="modify_reason" value="%modify_reason%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text quickModifyMargin">'), ',
							sTemplateReasonNormal: ', JavaScriptEscape('%modify_text'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), ($context['can_reply']) ? ',
							sFormRemoveAccessKeys: \'postmodify\'' : '', '
						});

					}';

	if (!empty($context['ignoredMsgs']))
		echo '
					ignore_toggles([', implode(', ', $context['ignoredMsgs']), '], ', JavaScriptEscape($txt['show_ignore_user_post']), ');';

	echo '
				</script>';
}

// news
function template_news_display()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($context['report_sent'])
		echo '
	<div class="information">', $txt['report_sent'], '</div>';

	if ($context['becomesUnapproved'])
		echo '
	<div class="noticebox">', $txt['post_becomesUnapproved'], '	</div>';

	echo '
	<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '', '
	<div id="forumposts">
		<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	if($context['current_page']>0)
		echo '
	<div class="largetext toppadding"><hr>
		<a href="' , $scripturl , '?topic=' , $context['current_topic'] , '.0"><span class="icon-arrow-left"></span> ', $txt['ces_back2topic'] , '</a>
	</div>';

	$context['ignoredMsgs'] = array();
	$context['removableMessageIDs'] = array();

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if($message['id']==$context['topicinfo']['id_first_msg'])
		{
			subtemplate_news_single($message, false);
			echo '
			</form>';
			template_display_poll();
			template_display_events();
			echo '
			<h3 class="catbg" id="comments">' , $txt['ces_comments'] , '</h3>';
		}
		else
			subtemplate_news_single($message, true);

	}
	echo '
	</div>';
	if($context['topicinfo']['num_replies']>0)
		echo '
	<div>
		<a href="#top" class="topbottom floatleft" id="bot"><span class="icon-chevron-up"></a>
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	// Show quickreply
	if ($context['can_reply'])
		template_quickreply();

	echo '
	<script>';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
	{
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $context['removableMessageIDs']), '\'],
						sSessionId: smf_session_id,
						sSessionVar: smf_session_var,
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.png\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.png\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanSplit: ', $context['can_split'] ? 'true' : 'false', ',
						sSplitButtonLabel: \'', $txt['quickmod_split_selected'], '\',
						sSplitButtonImage: \'split_selected.png\',
						sSplitButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';
	}

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							sClassName: \'quick_edit\',
							bShowModify: ', $modSettings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container">
									<div id="error_box" class="error"></div>
									<textarea class="editor" name="message" style="margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br>
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '">
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '">
									<input type="hidden" name="msg" value="%msg_id%">
									<div class="righttext quickModifyMargin">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit">&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit">&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit">
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text">'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape('%subject%'), ',
							sTemplateReasonEdit: ', JavaScriptEscape($txt['reason_for_edit'] . ': <input type="text" name="modify_reason" value="%modify_reason%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text quickModifyMargin">'), ',
							sTemplateReasonNormal: ', JavaScriptEscape('%modify_text'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), ($context['can_reply']) ? ',
							sFormRemoveAccessKeys: \'postmodify\'' : '', '
						});

					}';

	if (!empty($context['ignoredMsgs']))
		echo '
					ignore_toggles([', implode(', ', $context['ignoredMsgs']), '], ', JavaScriptEscape($txt['show_ignore_user_post']), ');';

	echo '
				</script>';
}

function template_single_post($message)
{
	subtemplate_single_post($message);
}

/**
 * The template for displaying the quick reply box.
 */
function template_quickreply()
{
	global $context, $modSettings, $scripturl, $options, $txt;
	echo '<br>
		<a id="quickreply"></a>
		<div class="tborder" id="quickreplybox">
			<div class="cat_bar">
				<h3 class="catbg">
					', $txt['quick_reply'], '
				</h3>
			</div>
			<div id="quickReplyOptions">
				<div class="roundframe">', empty($options['use_editor_quick_reply']) ? '
					<div class="preview">' . $txt['quick_reply_desc'] . '</div>' : '', '
					', $context['is_locked'] ? '<p class="noticebox">' . $txt['quick_reply_warning'] . '</p>' : '',
					!empty($context['oldTopicError']) ? '<p class="noticebox">' . sprintf($txt['error_old_topic'], $modSettings['oldTopicDays']) . '</p>' : '', '
					', $context['can_reply_approved'] ? '' : '<em>' . $txt['wait_for_approval'] . '</em>', '
					', !$context['can_reply_approved'] && $context['require_verification'] ? '<br>' : '', '
					<form action="', $scripturl, '?board=', $context['current_board'], ';action=post2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="submitonce(this);">
						<input type="hidden" name="topic" value="', $context['current_topic'], '">
						<input type="hidden" name="subject" value="', $context['response_prefix'], $context['subject'], '">
						<input type="hidden" name="icon" value="xx">
						<input type="hidden" name="from_qr" value="1">
						<input type="hidden" name="notify" value="', $context['is_marked_notify'] || !empty($options['auto_notify']) ? '1' : '0', '">
						<input type="hidden" name="not_approved" value="', !$context['can_reply_approved'], '">
						<input type="hidden" name="goback" value="', empty($options['return_to_post']) ? '0' : '1', '">
						<input type="hidden" name="last_msg" value="', $context['topic_last_message'], '">
						<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
						<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '">';

		// Guests just need more.
		if ($context['user']['is_guest'])
			echo '
						<dl id="post_header" class="settings">
							<dt>
								', $txt['name'], ':
							</dt>
							<dd>
								<input type="text" name="guestname" size="25" value="', $context['name'], '" tabindex="', $context['tabindex']++, '" class="input_text">
							</dd>
							<dt>
								', $txt['email'], ':
							</dt>
							<dd>
								<input type="email" name="email" size="25" value="', $context['email'], '" tabindex="', $context['tabindex']++, '" class="input_text" required>
							</dd>
						</dl>';

		echo '
						', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message'), '
						<script>
							function insertQuoteFast(messageid)
							{
								if (window.XMLHttpRequest)
									getXMLDocument(smf_prepareScriptUrl(smf_scripturl) + \'action=quotefast;quote=\' + messageid + \';xml;pb=', $context['post_box_name'], ';mode=\' + (oEditorHandle_', $context['post_box_name'], '.bRichTextEnabled ? 1 : 0), onDocReceived);
								else
									reqWin(smf_prepareScriptUrl(smf_scripturl) + \'action=quotefast;quote=\' + messageid + \';pb=', $context['post_box_name'], ';mode=\' + (oEditorHandle_', $context['post_box_name'], '.bRichTextEnabled ? 1 : 0), 240, 90);
								return false;
							}
							function onDocReceived(XMLDoc)
							{
								var text = \'\';
								for (var i = 0, n = XMLDoc.getElementsByTagName(\'quote\')[0].childNodes.length; i < n; i++)
									text += XMLDoc.getElementsByTagName(\'quote\')[0].childNodes[i].nodeValue;
								$("#', $context['post_box_name'], '").data("sceditor").InsertText(text);

								ajax_indicator(false);
							}
						</script>';

	// Is visual verification enabled?
	if ($context['require_verification'])
	{
		echo '
					<div class="post_verification">
						<strong>', $txt['verification'], ':</strong>
						', template_control_verification($context['visual_verification_id'], 'all'), '
					</div>';
	}

	// Finally, the submit buttons.
	echo '
					<div class="clear" id="post_confirm_buttons">
						', template_control_richedit_buttons($context['post_box_name']), '
					</div>';
		echo '
					</form>
				</div>
			</div>
		</div>
		<br class="clear">';

	// draft autosave available and the user has it enabled?
	if (!empty($context['drafts_autosave']))
		echo '
			<script>
				var oDraftAutoSave = new smf_DraftAutoSave({
					sSelf: \'oDraftAutoSave\',
					sLastNote: \'draft_lastautosave\',
					sLastID: \'id_draft\',', !empty($context['post_box_name']) ? '
					sSceditorID: \'' . $context['post_box_name'] . '\',' : '', '
					sType: \'', 'quick', '\',
					iBoard: ', (empty($context['current_board']) ? 0 : $context['current_board']), ',
					iFreq: ', (empty($modSettings['masterAutoSaveDraftsDelay']) ? 60000 : $modSettings['masterAutoSaveDraftsDelay'] * 1000), '
				});
			</script>';

	if ($context['show_spellchecking'])
		echo '
			<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value=""></form>';

	echo '
				<script>
					var oQuickReply = new QuickReply({
						bDefaultCollapsed: false,
						iTopicId: ', $context['current_topic'], ',
						iStart: ', $context['start'], ',
						sScriptUrl: smf_scripturl,
						sImagesUrl: smf_images_url,
						sContainerId: "quickReplyOptions",
						sImageId: "quickReplyExpand",
						sClassCollapsed: "toggle_up",
						sClassExpanded: "toggle_down",
						sJumpAnchor: "quickreply",
						bIsFull: true
					});
					var oEditorID = "', $context['post_box_name'], '";
					var oEditorObject = oEditorHandle_', $context['post_box_name'], ';
					var oJumpAnchor = "quickreply";
				</script>';
}


function subtemplate_aside()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
	<span class="nextlinks">', $context['previous_next'], '</span>
	<hr class="des">
	<div class="top_padding des">', template_button_strip($context['normal_buttons'], 'menu_nav dropmenu nolist full'), '</div>
	<hr class="des">
	<div class="top_padding des">', template_button_strip($context['mod_buttons'], 'menu_nav dropmenu nolist full'), '</div>';

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

		// Show just numbers...?
		if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
		// Or show the actual people viewing the topic?
		else
			echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) || $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');

		// Now show how many guests are here too.
		echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_topic'], '
	</div>';
	}
	echo '
	<div id="display_jump_to">&nbsp;</div>';
}

// for the mobie bottom menu, containers
function template_f_menu()
{
	echo '
			<li onclick="fPop_slide(\'#dismenu\'); return false;"><span class="icon-menu"></span><span class="amt smaller">2</span></li>
			<li onclick="fPop_slide(\'#dismodmenu\'); return false;"><span class="icon-menu"></span><span class="amt smaller">3</span></li>';
}

// for the mobie bottom menu, containers
function template_f_menu_subs()
{
	global $context;

	echo '
			<div class="bot_menu_mobile" id="dismenu">' , template_mob_button_strip($context['normal_buttons']) , '</div>
			<div class="bot_menu_mobile" id="dismodmenu">' , template_mob_button_strip($context['mod_buttons']) , '</div>';
}

function subtemplate_headers()
{
	echo '<link href="https://fonts.googleapis.com/css?family=Catamaran:400,700,900" rel="stylesheet">';
}

?>