<?php
/**
 * Helios theme for SMF
 *
 * @theme SMF
 * @author Blocweb
 * @copyright 2011 Blocweb
 * @license http://www.blocweb.net/license.txt BSD
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

        $context['theme_settings'] = array(
                array(
                        'id' => 'topbar',
                        'label' => 'Top Bar',
                        'description' => 'Use HTML code in a full-width box on top of the forum.',
                        'type' => 'textarea',
                ),
                array(
                        'id' => 'forum_width',
                        'label' => $txt['forum_width'],
                        'description' => $txt['forum_width_desc'],
                        'type' => 'text',
                ),
                array(
                        'id' => 'default_theme_color',
                        'label' => $txt['def_theme_color'],
                        'options' => array(
                                'brown' => 'Original Helios',
                                'red' => 'Red Helios',
                                'green' => 'Green Helios',
                                'silver' => 'Silver Helios',
                                'blue' => 'Blue Helios',
                                'golden' => 'Golden Helios',
                        ),
			'type' => 'text',
			'images' => 1,
                ),
                array(
                        'id' => 'color_change_off',
                        'label' => $txt['theme_color_off'],
                ),
               array(
                        'id' => 'custombutton1_use',
                        'label' => $txt['custombutton1_use'],
                ),
                array(
                        'id' => 'custombutton1_name',
                        'label' => $txt['custombutton1'],
                        'description' => $txt['custombutton_desd'],
                        'type' => 'text',
                ),
                array(
                        'id' => 'custombutton1_link',
                        'label' => $txt['custombutton1_link'],
                        'description' => '',
                        'type' => 'text',
                ),
                array(
                        'id' => 'custombutton1_member',
                        'label' => $txt['custombutton_member'],
                ),
                array(
                        'id' => 'custombutton2_use',
                        'label' => $txt['custombutton2_use'],
                ),
                array(
                        'id' => 'custombutton2_name',
                        'label' => $txt['custombutton2'],
                        'description' => $txt['custombutton_desd'],
                        'type' => 'text',
                ),
                array(
                        'id' => 'custombutton2_link',
                        'label' => $txt['custombutton2_link'],
                        'description' => '',
                        'type' => 'text',
                ),
                array(
                        'id' => 'custombutton2_member',
                        'label' => $txt['custombutton_member'],
                ),
                array(
                        'id' => 'custombutton3_use',
                        'label' => $txt['custombutton3_use'],
                ),
                array(
                        'id' => 'custombutton3_name',
                        'label' => $txt['custombutton3'],
                        'description' => $txt['custombutton_desd'],
                        'type' => 'text',
                ),
                array(
                        'id' => 'custombutton3_link',
                        'label' => $txt['custombutton3_link'],
                        'description' => $txt['custombutton_desd2'],
                        'type' => 'text',
                ),
                array(
                        'id' => 'custombutton3_member',
                        'label' => $txt['custombutton_member'],
                ),

		array(
			'id' => 'header_logo_url',
			'label' => $txt['header_logo_url'],
			'description' => $txt['header_logo_url_desc'],
			'type' => 'text',
		),
		array(
			'id' => 'number_recent_posts',
			'label' => $txt['number_recent_posts'],
			'description' => $txt['number_recent_posts_desc'],
			'type' => 'number',
		),
		array(
			'id' => 'display_who_viewing',
			'label' => $txt['who_display_viewing'],
			'options' => array(
				0 => $txt['who_display_viewing_off'],
				1 => $txt['who_display_viewing_numbers'],
				2 => $txt['who_display_viewing_names'],
			),
		),
		array(
			'id' => 'smiley_sets_default',
			'label' => $txt['smileys_default_set_for_theme'],
			'options' => $context['smiley_sets'],
			'type' => 'text',
		),
		array(
			'id' => 'show_modify',
			'label' => $txt['last_modification'],
		),
		array(
			'id' => 'show_member_bar',
			'label' => $txt['member_list_bar'],
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
