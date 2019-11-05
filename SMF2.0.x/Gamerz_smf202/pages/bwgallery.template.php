<?php

function template_main()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
	
	$galleryboards = '';

	if(!empty($settings['no_gallery']) || empty($settings['galleryboards']))
		redirectexit();
	
	$galleryboards = $settings['galleryboards'];

	// get any pages
	if(!empty($_GET['start']) && is_numeric($_GET['start']))
		$start = $_GET['start'];
	else
		$start = 0;

	// get any boards?
	if(!empty($_GET['b']) && is_numeric($_GET['b']))
		$b = $_GET['b'];
	else
		$b = $galleryboards;

	
	$groll = bw_fetchrecentgallery($b, $start);

	if(allowedTo('manage_permissions'))
	{
		// check the permission for guests

	}
	echo '
	<div id="bwg">';
	foreach($groll as $b => $bw)
	{
		echo '
		<div class="bwitemart">
			<a href="' . $bw['topic']['href'] . '" style="background: url('.$bw['file']['image']['href'].') no-repeat #000; width: ' , $modSettings['attachmentThumbWidth'] , 'px; ">
				<span>' . $bw['file']['filename'] . '</span>
			</a>
		</div>';
	}

	echo '
	</div>
	<div style="clear: both; padding: 1em;" class="middletext">
		', $txt['pages'], ': ', $context['page_index'], '
	</div>';
}

function bw_fetchrecentgallery($b,$start)
{
	global $smcFunc, $context, $modSettings, $scripturl, $txt, $settings;

	if (empty($b))
		return;

	$attachment_ext = array('jpg','png','jpeg');

	// Lets build the query.
	$request = $smcFunc['db_query']('', '
		SELECT
			att.id_attach, att.id_msg, att.filename,t.id_topic, 
			att.width, att.height' . (empty($modSettings['attachmentShowImages']) || empty($modSettings['attachmentThumbnails']) ? '' : ', IFNULL(thumb.id_attach, 0) AS id_thumb, thumb.width AS thumb_width, thumb.height AS thumb_height') . '
		FROM {db_prefix}attachments AS att
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = att.id_msg)
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			LEFT JOIN {db_prefix}attachments AS thumb ON (thumb.id_attach = att.id_thumb)
		WHERE att.attachment_type = 0
			AND att.fileext IN ({array_string:attachment_ext})'.
			(!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : '
			AND t.approved = 1
			AND m.approved = 1
			AND att.approved = 1') . '
			AND m.id_board IN (' . $b . ')
			AND t.id_first_msg = m.id_msg
			AND att.width>0
			AND att.height>0
		ORDER BY att.id_attach DESC
		LIMIT ' . $start. ',20',
		array(
			'attachment_ext' => $attachment_ext,
		)
	);

	// We have something.
	$attachments = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$filename = preg_replace('~&amp;#(\\d{1,7}|x[0-9a-fA-F]{1,6});~', '&#\\1;', htmlspecialchars($row['filename']));

		// Is it an image?
		$attachments[$row['id_attach']] = array(
			'file' => array(
				'filename' => $filename,
				'href' => $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $row['id_attach'],
			),
			'topic' => array(
				'id' => $row['id_topic'],
				'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
			),
		);

		$id_thumb = empty($row['id_thumb']) ? $row['id_attach'] : $row['id_thumb'];
		$attachments[$row['id_attach']]['file']['image'] = array(
				'id' => $id_thumb,
				'width' => $row['width'],
				'height' => $row['height'],
				'img' => '<img src="' . $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $row['id_attach'] . ';image" alt="' . $filename . '" />',
				'thumb' => '<img src="' . $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $id_thumb . ';image" alt="' . $filename . '" />',
				'href' => $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $id_thumb . ';image',
				'link' => '<a href="' . $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $row['id_attach'] . ';image"><img src="' . $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $id_thumb . ';image" alt="' . $filename . '" /></a>',
			);
	}
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query']('', '
		SELECT
			COUNT(att.id_attach) as total 
		FROM {db_prefix}attachments AS att
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = att.id_msg)
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			LEFT JOIN {db_prefix}attachments AS thumb ON (thumb.id_attach = att.id_thumb)
		WHERE att.attachment_type = 0
			AND att.fileext IN ({array_string:attachment_ext})'.
			(!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : '
			AND t.approved = 1
			AND m.approved = 1
			AND att.approved = 1') . '
			AND m.id_board IN (' . $b . ')
			AND t.id_first_msg = m.id_msg
			AND att.width>0
			AND att.height>0
		ORDER BY att.id_attach DESC
		',
		array(
			'attachment_ext' => $attachment_ext,
		)
	);

	$t = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	$context['page_index'] = constructPageIndex($scripturl . '?action=bwgallery;start=%1$d', $start, $t['total'], 20, true);

	return $attachments;
}



?>