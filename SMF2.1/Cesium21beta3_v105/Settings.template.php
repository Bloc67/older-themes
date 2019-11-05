<?php

// Cesium 1.0
// a rewrite theme

function template_options()
{
	global $context, $txt;

	$context['theme_options'] = array(
		$txt['theme_opt_calendar'],
		array(
			'id' => 'calendar_start_day',
			'label' => $txt['calendar_start_day'],
			'options' => array(
				0 => $txt['days'][0],
				1 => $txt['days'][1],
				6 => $txt['days'][6],
			),
			'default' => true,
		),
		$txt['theme_opt_display'],
		array(
			'id' => 'show_children',
			'label' => $txt['show_children'],
			'default' => true,
		),
		array(
			'id' => 'topics_per_page',
			'label' => $txt['topics_per_page'],
			'options' => array(
				0 => $txt['per_page_default'],
				5 => 5,
				10 => 10,
				25 => 25,
				50 => 50,
			),
			'default' => true,
		),
		array(
			'id' => 'messages_per_page',
			'label' => $txt['messages_per_page'],
			'options' => array(
				0 => $txt['per_page_default'],
				5 => 5,
				10 => 10,
				25 => 25,
				50 => 50,
			),
			'default' => true,
		),
		array(
			'id' => 'view_newest_first',
			'label' => $txt['recent_posts_at_top'],
			'default' => true,
		),
		array(
			'id' => 'show_no_avatars',
			'label' => $txt['show_no_avatars'],
			'default' => true,
		),
		array(
			'id' => 'show_no_signatures',
			'label' => $txt['show_no_signatures'],
			'default' => true,
		),
		array(
			'id' => 'posts_apply_ignore_list',
			'label' => $txt['posts_apply_ignore_list'],
			'default' => false,
		),
		$txt['theme_opt_posting'],
		array(
			'id' => 'return_to_post',
			'label' => $txt['return_to_post'],
			'default' => true,
		),
		array(
			'id' => 'auto_notify',
			'label' => $txt['auto_notify'],
			'default' => true,
		),
		array(
			'id' => 'wysiwyg_default',
			'label' => $txt['wysiwyg_default'],
			'default' => false,
		),
		array(
			'id' => 'use_editor_quick_reply',
			'label' => $txt['use_editor_quick_reply'],
			'default' => true,
		),
		array(
			'id' => 'drafts_autosave_enabled',
			'label' => $txt['drafts_autosave_enabled'],
			'default' => true,
		),
		array(
			'id' => 'drafts_show_saved_enabled',
			'label'  => $txt['drafts_show_saved_enabled'],
			'default' => true,
		),
		$txt['theme_opt_moderation'],
		array(
			'id' => 'display_quick_mod',
			'label' => $txt['display_quick_mod'],
			'options' => array(
				0 => $txt['display_quick_mod_none'],
				1 => $txt['display_quick_mod_check'],
				2 => $txt['display_quick_mod_image'],
			),
			'default' => true,
		),
		$txt['theme_opt_personal_messages'],
		array(
			'id' => 'popup_messages',
			'label' => $txt['popup_messages'],
			'default' => true,
		),
		array(
			'id' => 'view_newest_pm_first',
			'label' => $txt['recent_pms_at_top'],
			'default' => true,
		),
		array(
			'id' => 'pm_remove_inbox_label',
			'label' => $txt['pm_remove_inbox_label'],
			'default' => true,
		),
	);
}

/**
 * This pseudo-template defines all the available theme settings (but not their actual values)
 */
function template_settings()
{
	global $context, $scripturl, $txt;

	$context['theme_settings'] = array(
		array(
			'id' => 'header_logo_url',
			'label' => $txt['header_logo_url'],
			'description' => $txt['header_logo_url_desc'],
			'type' => 'text',
		),
		array(
			'id' => 'smiley_sets_default',
			'label' => $txt['smileys_default_set_for_theme'],
			'options' => $context['smiley_sets'],
			'type' => 'text',
		),
	'',
		array(
			'id' => 'enable_news',
			'label' => $txt['enable_random_news'],
		),
	'',
		array(
			'id' => 'show_newsfader',
			'label' => $txt['news_fader'],
		),
		array(
			'id' => 'newsfader_time',
			'label' => $txt['admin_fader_delay'],
			'type' => 'number',
		),
		array(
			'id' => 'number_recent_posts',
			'label' => $txt['number_recent_posts'],
			'description' => $txt['zero_to_disable'],
			'type' => 'number',
		),
		array(
			'id' => 'show_stats_index',
			'label' => $txt['show_stats_index'],
		),
		array(
			'id' => 'show_latest_member',
			'label' => $txt['latest_members'],
		),
		array(
			'id' => 'show_group_key',
			'label' => $txt['show_group_key'],
		),
		array(
			'id' => 'display_who_viewing',
			'label' => $txt['who_display_viewing'],
			'options' => array(
				0 => $txt['who_display_viewing_off'],
				1 => $txt['who_display_viewing_numbers'],
				2 => $txt['who_display_viewing_names'],
			),
			'type' => 'number',
		),
	'',
		array(
			'id' => 'og_image',
			'label' => $txt['og_image'],
			'description' => $txt['og_image_desc'],
			'type' => 'url',
		),
	'',
		array(
			'id' => 'use_blogs',
			'label' => $txt['ces_useblogs'],
			'switch' => 'use_blogs',
		),
		array(
			'id' => 'blogs_boards',
			'label' => $txt['ces_blogsboards'],
			'container' => 'use_blogs',
			'type' => 'text',
			'function' => 'ces_boards',
		),
	'',
		array(
			'id' => 'use_galleries',
			'label' => $txt['ces_usegalleries'],
			'switch' => 'use_galleries',
		),
		array(
			'id' => 'galleries_boards',
			'label' => $txt['ces_galleriesboards'],
			'container' => 'use_galleries',
			'type' => 'text',
			'function' => 'ces_boards',
		),
	'',
		array(
			'id' => 'use_files',
			'label' => $txt['ces_usefiles'],
			'switch' => 'use_files',
		),
		array(
			'id' => 'files_boards',
			'label' => $txt['ces_filesboards'],
			'container' => 'use_files',
			'type' => 'text',
			'function' => 'ces_boards',
		),
	'',
		array(
			'id' => 'use_news',
			'label' => $txt['ces_usenews'],
			'switch' => 'use_news',
		),
		array(
			'id' => 'news_boards',
			'label' => $txt['ces_newsboards'],
			'container' => 'use_news',
			'type' => 'text',
			'function' => 'ces_boards',
		),
	'',
		array(
			'id' => 'use_docs',
			'label' => $txt['ces_usedocs'],
			'switch' => 'use_docs',
		),
		array(
			'id' => 'docs_boards',
			'label' => $txt['ces_docsboards'],
			'container' => 'use_docs',
			'type' => 'text',
			'function' => 'ces_boards',
		),
	'',
		array(
			'id' => 'use_bugs',
			'label' => $txt['ces_usebugs'],
			'switch' => 'use_bugs',
		),
		array(
			'id' => 'bugs_boards',
			'label' => $txt['ces_bugsboards'],
			'container' => 'use_bugs',
			'type' => 'text',
			'function' => 'ces_boards',
		),
	'',
		array(
			'id' => 'ces_showboardindex',
			'label' => $txt['ces_showboardindex'],
			'description' => $txt['ces_showboardindex_desc'],
		),
	'',
		array(
			'id' => 'ces_fullmenus',
			'label' => $txt['ces_fullmenus'],
			'description' => $txt['ces_fullmenus_desc'],
		),
		array(
			'id' => 'ces_fullmenus_keep',
			'label' => $txt['ces_fullmenus_keep'],
			'description' => $txt['ces_fullmenus_keep_desc'],
			'type' => 'textarea',
		),
	);
}

?>