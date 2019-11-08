<?php
// Version: 1.1; Settings

function template_options()
{
	global $context, $settings, $options, $scripturl, $txt;

	$context['theme_options'] = array(
		array(
			'id' => 'show_board_desc',
			'label' => $txt[732],
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
			'id' => 'auto_notify',
			'label' => $txt['auto_notify'],
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
			'id' => 'fullwidth',
			'label' => 'Use full width for the forum?',
		),
		array(
			'id' => 'rsslinks',
			'label' => 'Show RSS icons on Boardindex?',
		),
		array(
			'id' => 'avatarboards',
			'label' => 'Show avatars on Boardindex?',
		),
		array(
			'id' => 'avatarboards_width',
			'label' => 'Width of avatar-row in pixels:',
			'type' => 'number',
		),
		array(
			'id' => 'avatarboards_height',
			'label' => 'Height of avatar-row in pixels:',
			'type' => 'number',
		),
		array(
			'id' => 'messagepreview',
			'label' => 'Show topic summary in topic list?',
		),
		array(
			'id' => 'messagepreview_last',
			'label' => 'Show the summary from last post, instead of original post?',
		),
		array(
			'id' => 'colorversion',
			'label' => 'Choose other color:',
			'choices' => array(
				'' => 'Blue version(default)',				
				'red' => 'Red version',
				'green' => 'Green version',
				'violet' => 'Purple version',
				'black' => 'Dark version',
			),
			'type' => 'radioimage',
		),
		array(
			'id' => 'custom_pages',
			'label' => 'Custom pages',
			'description' => 'use the form: file[.template],title,type where type can be 1.) page or 2.) link. Use "link" for a regualr link, "page" for a custom template file. ',
			'type' => 'textarea',
			'empty' => 'homepage,News,page|about,Custom,page|http.//www.blocweb.net,TP,link',
		),
		array(
			'id' => 'area1',
			'label' => 'HTML-box 1',
			'description' => 'Insert your own HTML code',
			'type' => 'textarea',
			'empty' => '',
		),
		array(
			'id' => 'area1_where',
			'label' => 'Where to show <b>HTML-box 1</b>?',
			'options' => array(
				0 => '-none-',
				1 => 'Under the copyright',
				2 => 'After first post',
				3 => 'Between each post',
			),
		),
		array(
			'id' => 'area2',
			'label' => 'HTML-box 2',
			'description' => 'Insert your own HTML code',
			'type' => 'textarea',
			'empty' => '',
		),
		array(
			'id' => 'area2_where',
			'label' => 'Where to show <b>HTML-box 2</b>?',
			'options' => array(
				0 => '-none-',
				1 => 'Under the copyright',
				2 => 'After first post',
				3 => 'Between each post',
			),
		),
		array(
			'id' => 'area3',
			'label' => 'HTML-box 3',
			'description' => 'Insert your own HTML code',
			'type' => 'textarea',
			'empty' => '',
		),
		array(
			'id' => 'area3_where',
			'label' => 'Where to show <b>HTML-box 3</b>?',
			'options' => array(
				0 => '-none-',
				1 => 'Under the copyright',
				2 => 'After first post',
				3 => 'Between each post',
			),
			'description' => '<br /><br />',
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
		),
		array(
			'id' => 'show_modify',
			'label' => $txt[383],
		),
		array(
			'id' => 'show_member_bar',
			'label' => $txt[510],
		),
		array(
			'id' => 'linktree_link',
			'label' => $txt[522],
		),
		array(
			'id' => 'show_profile_buttons',
			'label' => $txt[523],
		),
		array(
			'id' => 'show_mark_read',
			'label' => $txt[618],
		),
		array(
			'id' => 'linktree_inline',
			'label' => $txt['smf105'],
			'description' => $txt['smf106'],
		),
		array(
			'id' => 'show_sp1_info',
			'label' => $txt['smf200'],
		),
		array(
			'id' => 'allow_no_censored',
			'label' => $txt['allow_no_censored'],
		),
		array(
			'id' => 'show_bbc',
			'label' => $txt[740],
		),
		array(
			'id' => 'additional_options_collapsable',
			'label' => $txt['additional_options_collapsable'],
		),
		array(
			'id' => 'enable_news',
			'label' => $txt[379],
		),
		array(
			'id' => 'show_newsfader',
			'label' => $txt[387],
		),
		array(
			'id' => 'newsfader_time',
			'label' => $txt[739],
			'type' => 'number',
		),
		array(
			'id' => 'show_user_images',
			'label' => $txt[384],
		),
		array(
			'id' => 'show_blurb',
			'label' => $txt[385],
		),
		array(
			'id' => 'show_latest_member',
			'label' => $txt[382],
		),
		array(
			'id' => 'use_image_buttons',
			'label' => $txt[521],
		),
		array(
			'id' => 'show_gender',
			'label' => $txt[386],
		),
		array(
			'id' => 'hide_post_group',
			'label' => $txt['hide_post_group'],
			'description' => $txt['hide_post_group_desc'],
		),
	);
}

?>