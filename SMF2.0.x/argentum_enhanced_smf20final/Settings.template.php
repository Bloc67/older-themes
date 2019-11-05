<?php
/**
 * Simple Machines Forum (SMF)
 *
 *
 * @version 2.0
 */

function template_options()
{
	global $context, $settings, $options, $scripturl, $txt;

	$context['theme_options'] = array(
		array(
			'id' => 'show_board_desc',
			'label' => $txt['board_desc_inside'],
			'default' => true,
		),
		array(
			'id' => 'show_children',
			'label' => $txt['show_children'],
			'default' => true,
		),
		array(
			'id' => 'use_sidebar_menu',
			'label' => $txt['use_sidebar_menu'],
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
			'id' => 'show_no_censored',
			'label' => $txt['show_no_censored'],
			'default' => true,
		),
		array(
			'id' => 'return_to_post',
			'label' => $txt['return_to_post'],
			'default' => true,
		),
		array(
			'id' => 'no_new_reply_warning',
			'label' => $txt['no_new_reply_warning'],
			'default' => true,
		),
		array(
			'id' => 'view_newest_first',
			'label' => $txt['recent_posts_at_top'],
			'default' => true,
		),
		array(
			'id' => 'view_newest_pm_first',
			'label' => $txt['recent_pms_at_top'],
			'default' => true,
		),
		array(
			'id' => 'posts_apply_ignore_list',
			'label' => $txt['posts_apply_ignore_list'],
			'default' => false,
		),
		array(
			'id' => 'wysiwyg_default',
			'label' => $txt['wysiwyg_default'],
			'default' => false,
		),
		array(
			'id' => 'popup_messages',
			'label' => $txt['popup_messages'],
			'default' => true,
		),
		array(
			'id' => 'copy_to_outbox',
			'label' => $txt['copy_to_outbox'],
			'default' => true,
		),
		array(
			'id' => 'pm_remove_inbox_label',
			'label' => $txt['pm_remove_inbox_label'],
			'default' => true,
		),
		array(
			'id' => 'auto_notify',
			'label' => $txt['auto_notify'],
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
			'id' => 'calendar_start_day',
			'label' => $txt['calendar_start_day'],
			'options' => array(
				0 => $txt['days'][0],
				1 => $txt['days'][1],
				6 => $txt['days'][6],
			),
			'default' => true,
		),
		array(
			'id' => 'display_quick_reply',
			'label' => $txt['display_quick_reply'],
			'options' => array(
				0 => $txt['display_quick_reply1'],
				1 => $txt['display_quick_reply2'],
				2 => $txt['display_quick_reply3']
			),
			'default' => true,
		),
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
	);
}

function template_settings()
{
	global $context, $settings, $options, $scripturl, $txt;

	// collect up available widgets
	
	$context['theme_settings'] = array(
		array(
			'id' => 'display_type',
			'label' => $txt['bloc_display'],
			'options' => array(
				0 => $txt['bloc_display0'],
				1 => $txt['bloc_display1'],
			),
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'display_blogboards',
			'label' => $txt['bloc_blogboards'],
			'description' => $txt['bloc_blogboards2'],
			'showboards' => true,
			'type' => 'textarea',
			'customsetting' => true,
		),
		array(
			'id' => 'childrender',
			'label' => $txt['bloc_childrender'],
			'options' => array(
				0 => $txt['bloc_childrender0'],
				1 => $txt['bloc_childrender1'],
				2 => $txt['bloc_childrender2'],
				3 => $txt['bloc_childrender3'],
			),
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'donotshowhot',
			'label' => $txt['bloc_donotshowhot'],
			'customsetting' => true,
		),
		array(
			'id' => 'boardindex_layout',
			'label' => $txt['bloc_boardindexlayout'],
			'options' => array(
				0 => $txt['bloc_boardindexlayout1'],
				1 => $txt['bloc_boardindexlayout2'],
				2 => $txt['bloc_boardindexlayout3'],
			),
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'boardindex_layoutwidth',
			'label' => $txt['bloc_boardindexwidth'],
			'options' => array(
				0 => '25%',
				5 => '31%',
				6 => '37%',
				7 => '43%',
				8 => '50%',
			),
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'header_logo_url',
			'label' => $txt['header_logo_url'],
			'description' => $txt['header_logo_url_desc'],
			'type' => 'text',
		),
		array(
			'id' => 'rsslinks',
			'label' => $txt['bloc_rsslinks'],
			'customsetting' => true,
		),
		array(
			'id' => 'firstpreview',
			'label' => $txt['bloc_firstpreview'],
			'customsetting' => true,
		),
		array(
			'id' => 'avatarboards',
			'label' => $txt['bloc_avatarboards'],
			'customsetting' => true,
		),
		array(
			'id' => 'staticreplybox',
			'label' => $txt['bloc_staticreplybox'],
			'customsetting' => true,
		),
		array(
			'id' => 'area1',
			'label' => 'HTML 1',
			'description' => $txt['bloc_area'],
			'type' => 'textarea',
			'empty' => '',
			'customsetting' => true,
		),
		array(
			'id' => 'area1_where',
			'label' => $txt['bloc_area1']. ' 1?',
			'options' => array(
				0 => $txt['bloc_none'],
				1 => $txt['bloc_copy'],
				2 => $txt['bloc_after'],
				3 => $txt['bloc_between'],
				4 => $txt['bloc_sidebar'],
			),
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'area2',
			'label' => 'HTML 2',
			'description' => $txt['bloc_area'],
			'type' => 'textarea',
			'empty' => '',
			'customsetting' => true,
		),
		array(
			'id' => 'area2_where',
			'label' => $txt['bloc_area1']. ' 2?',
			'options' => array(
				0 => $txt['bloc_none'],
				1 => $txt['bloc_copy'],
				2 => $txt['bloc_after'],
				3 => $txt['bloc_between'],
				4 => $txt['bloc_sidebar'],
			),
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'area3',
			'label' => 'HTML 3',
			'description' => $txt['bloc_area'],
			'type' => 'textarea',
			'empty' => '',
			'customsetting' => true,
		),
		array(
			'id' => 'area3_where',
			'label' => $txt['bloc_area1']. ' 3?',
			'options' => array(
				0 => $txt['bloc_none'],
				1 => $txt['bloc_copy'],
				2 => $txt['bloc_after'],
				3 => $txt['bloc_between'],
				4 => $txt['bloc_sidebar'],
			),
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'google',
			'label' => $txt['bloc_google'],
			'description' => '',
			'type' => 'textarea',
			'empty' => '',
			'customsetting' => true,
		),
		array(
			'id' => 'facebooklike',
			'label' => $txt['bloc_facebook'],
			'customsetting' => true,
		),
		array(
			'id' => 'twitterlike',
			'label' => $txt['bloc_twitter'],
			'customsetting' => true,
		),
		array(
			'id' => 'showmootips',
			'label' => $txt['bloc_mootips'],
			'customsetting' => true,
		),
		array(
			'id' => 'twitter',
			'label' => $txt['bloc_twitterbox'],
			'description' => $txt['bloc_twitterbox2'],
			'type' => 'text',
			'customsetting' => true,
		),
		array(
			'id' => 'twittercount',
			'label' => $txt['bloc_twittercount'],
			'type' => 'number',
			'customsetting' => true,
		),
		array(
			'id' => 'smiley_sets_default',
			'label' => $txt['smileys_default_set_for_theme'],
			'options' => $context['smiley_sets'],
			'type' => 'text',
		),
		array(
			'id' => 'showfriendsbutton',
			'label' => $txt['bloc_showfriendbutton'],
			'customsetting' => true,
		),
		array(
			'id' => 'showfriendlinks',
			'label' => $txt['bloc_showfriendlinks'],
			'customsetting' => true,
		),
		array(
			'id' => 'linktree_link',
			'label' => $txt['current_pos_text_img'],
		),
		array(
			'id' => 'show_mark_read',
			'label' => $txt['enable_mark_as_read'],
		),
		array(
			'id' => 'allow_no_censored',
			'label' => $txt['allow_no_censored'],
		),
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
			'description' => $txt['number_recent_posts_desc'],
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
			'id' => 'show_modify',
			'label' => $txt['last_modification'],
		),
		array(
			'id' => 'show_profile_buttons',
			'label' => $txt['show_view_profile_button'],
		),
		array(
			'id' => 'show_user_images',
			'label' => $txt['user_avatars'],
		),
		array(
			'id' => 'show_blurb',
			'label' => $txt['user_text'],
		),
		array(
			'id' => 'show_gender',
			'label' => $txt['gender_images'],
		),
		array(
			'id' => 'hide_post_group',
			'label' => $txt['hide_post_group'],
			'description' => $txt['hide_post_group_desc'],
		),
	'',
		array(
			'id' => 'show_bbc',
			'label' => $txt['admin_bbc'],
		),
		array(
			'id' => 'additional_options_collapsable',
			'label' => $txt['additional_options_collapsable'],
		),
	);
}

?>