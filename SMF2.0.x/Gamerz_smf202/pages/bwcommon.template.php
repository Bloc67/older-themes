<?php

function bwstartElement($parser, $tagName, $attrs)
{

	global $insideitem, $tag;

	if($insideitem)
		$tag = $tagName;
	elseif($tagName == "ITEM")
		$insideitem = true;
	elseif($tagName == "ENTRY")
		$insideitem = true;
	elseif($tagName == "IMAGE")
		$insideitem = true;
}

function bwcharacterData($parser, $data)
{
	// The function used to parse all other data than tags
	global $insideitem, $tag, $title, $description, $link , $tpimage , $tpbody, $curl, $content_encoded, $pubdate, $content, $created;

	if ($insideitem)
	{
		switch ($tag)
		{
			case "TITLE":
				$title .= $data;
				break;
			case "DESCRIPTION":
				$description .= $data;
				break;
			case "LINK":
				$link .= $data;
				break;
			case "IMAGE":
				$tpimage .= $data;
				break;
			case "BODY":
				$tpbody .= $data;
				break;
			case "URL":
				$curl .= $data;
				break;
			case "CONTENT:ENCODED":
				$content_encoded .= $data;
				break;
			case "CONTENT":
				$content .= $data;
				break;
			case "PUBDATE":
				$pubdate .= $data;
				break;
			case "CREATED":
				$created .= $data;
				break;
		}
	}
}

function bwendElement($parser, $tagName)
{

	// This function is used when an end-tag is encountered.
	global $context, $insideitem, $tag, $title, $description, $link, $tpimage, $curl, $content_encoded, $pubdate, $content, $created;

	// RSS/RDF feeds
	if ($tagName == "ITEM")
	{
		echo '
<div class="rss_title">';
		printf("<a href='%s'>%s</a>", trim($link),htmlspecialchars(trim($title)));
		echo '
</div>';
		if(!empty($pubdate))
				echo '
<div class="rss_date">' . $pubdate . '</div>';
			echo '
<div class="rss_body">';
		if(!empty($content_encoded))
			echo ($content_encoded); // Print out the live journal entry
		else
			echo ($description); // Print out the live journal entry
		
		echo '
</div>';
		$title = $description = $link = $insideitem = $curl = $content_encoded = $pubdate = false;
	}
}

function bwRSS($override = '', $template = "bwendElement", $total=10)
{

	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	$backend=$override;

	$insideitem = false;
	$tag = "";
	$title = "";
	$description = "";
	$link = "";
	$curl = "";
	$content_encoded = "";
	$pubdate = "";

	$xml_parser = xml_parser_create('ISO-8859-1');

	xml_set_element_handler($xml_parser, "bwstartElement", $template);
	xml_set_character_data_handler($xml_parser, "bwcharacterData");

	// Open the actual datafile:

	$fp = fopen($backend, "r");
    
	$xmlerr='';
	$shown = 0;
	// Run through it line by line and parse:
	while ($data = fread($fp, 4096))
	{
		xml_parse($xml_parser, $data, feof($fp)) or $xmlerr=(sprintf("XML error: %s at line %d",
		xml_error_string(xml_get_error_code($xml_parser)),
		xml_get_current_line_number($xml_parser)));
		if($xmlerr!='')
			break;
	}
	// Close the datafile
	fclose($fp);

	// Free any memmory used
	xml_parser_free($xml_parser);
	if($xmlerr!='')
		echo $xmlerr;
}

function bw_clean($tag)
{
	return preg_replace("/[^a-zA-Z0-9]/","",$tag);
}

function fetchdir($dir)
{
	global $boarddir;

	$files = array();
	if ($handle = opendir($dir)) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if($file!= '.' && $file!='..' && $file!='.htaccess')
				$files[] = $file;

		}
		closedir($handle);
	}
	return $files;
}

// Recent topic list:   [board] Subject by Poster	Date
function ctheme_boardNews($board = 1, $num_recent = 5, $size=170)
{
	global $context, $settings, $scripturl, $txt, $db_prefix, $smcFunc;
	global $user_info, $modSettings;

	$request =  $smcFunc['db_query']('',"
			SELECT t.id_first_msg as ID_FIRST_MSG
			FROM (" . $db_prefix . "topics as t, " . $db_prefix . "boards as b)
			WHERE t.id_board=b.id_board
			AND t.id_board = " . $board ."
			ORDER BY t.id_first_msg DESC
			LIMIT " . $num_recent);

	$posts = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$posts[] = $row['ID_FIRST_MSG'];
	$smcFunc['db_free_result']($request);

	if (empty($posts))
		return array();

	$request =  $smcFunc['db_query']('',"
			SELECT 
			m.subject,  LEFT(m.body,".$size.") as body, 
			IFNULL(mem.real_name, m.poster_name) AS realName, m.poster_time as date, mem.avatar,mem.posts, mem.date_registered as dateRegistered,mem.last_login as lastLogin,
			IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType, t.id_board as category, b.name as category_name,
			t.num_replies as numReplies, t.id_topic as id, m.id_member as authorID, t.num_views as views,t.num_replies as replies, t.locked
			FROM (" . $db_prefix . "topics AS t, " . $db_prefix . "messages AS m)
			LEFT JOIN " . $db_prefix . "members AS mem ON (mem.id_member = m.id_member)
			LEFT JOIN " . $db_prefix . "attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type!=3)
			LEFT JOIN " . $db_prefix . "boards AS b ON (b.id_board = t.id_board)
			WHERE m.id_msg IN (" . implode(', ', $posts) . ")
			AND m.id_msg = t.id_first_msg
			ORDER BY m.poster_time DESC
			LIMIT " . $num_recent );
	
	$retposts= array();
	if($smcFunc['db_num_rows']($request)>0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			$row['avatar'] = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="&nbsp;"  />' : '') : (stristr($row['avatar'], 'http://') ? '<img src="' . $row['avatar'] . '" alt="&nbsp;" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar']) . '" alt="&nbsp;" />');
			$retposts[] = $row;
		}
		$smcFunc['db_free_result']($request);
	}
	return $retposts;
}

// Recent topic list:   [board] Subject by Poster	Date
function ctheme_boardNewsWithAttach($board = 1, $num_recent = 5, $size=170)
{
	global $context, $settings, $scripturl, $txt, $db_prefix, $smcFunc;
	global $user_info, $modSettings;

	$request =  $smcFunc['db_query']('',"
			SELECT t.id_first_msg as ID_FIRST_MSG
			FROM (" . $db_prefix . "topics as t, " . $db_prefix . "boards as b)
			WHERE t.id_board=b.id_board
			AND t.id_board = " . $board ."
			ORDER BY t.id_first_msg DESC
			LIMIT " . $num_recent);

	$posts = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$posts[] = $row['ID_FIRST_MSG'];
	$smcFunc['db_free_result']($request);

	if (empty($posts))
		return array();

	$request =  $smcFunc['db_query']('',"
			SELECT 
			m.subject,  LEFT(m.body,".$size.") as body, 
			IFNULL(mem.real_name, m.poster_name) AS realName, m.poster_time as date, mem.avatar,mem.posts, mem.date_registered as dateRegistered,mem.last_login as lastLogin,
			t.id_board as category, b.name as category_name,
			t.num_replies as numReplies, t.id_topic as id, m.id_member as authorID, t.num_views as views,t.num_replies as replies, t.locked,
			IFNULL(thumb.id_attach, 0) AS thumb_id, thumb.filename as thumb_filename
			FROM (" . $db_prefix . "topics AS t, " . $db_prefix . "messages AS m)
			LEFT JOIN " . $db_prefix . "members AS mem ON (mem.id_member = m.id_member)
			LEFT JOIN " . $db_prefix . "attachments AS thumb ON (m.id_msg = thumb.id_msg AND thumb.attachment_type = 3)
			LEFT JOIN " . $db_prefix . "boards AS b ON (b.id_board = t.id_board)
			WHERE m.id_msg IN (" . implode(', ', $posts) . ")
			AND m.id_msg = t.id_first_msg
			ORDER BY m.poster_time DESC
			LIMIT " . $num_recent );
	
	$retposts= array();
	if($smcFunc['db_num_rows']($request)>0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			if(!empty($row['thumb_id']))
				$row['illustration'] = $scripturl . '?action=dlattach;topic=' . $row['id'] . '.0;attach=' . $row['thumb_id'] . ';image';
			$retposts[] = $row;
		}
		$smcFunc['db_free_result']($request);
	}
	return $retposts;
}




?>