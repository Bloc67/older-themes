<?php

function template_shout_archive()
{
	global $context, $settings, $options, $txt, $user_info, $scripturl, $modSettings;

	echo '
	<form action="', $scripturl, '?action=delete_shout', $context['url_direct'], '" method="post">
		<table border="0" cellspacing="1" cellpadding="3" align="center" width="100%" class="borderclass">
			<tr>
				<td class="catbg" colspan="', !empty($context['canDelete']) ? 2 : 1, '" align="center">', $txt['shoutbox_1'], '</td>
			</tr><tr>
				<td class="catbg3" colspan="', !empty($context['canDelete']) ? 2 : 1, '" align="left">', $context['page_index'], '</td>
			</tr>';

	$alternate = false;
	foreach ($context['arc_shouts'] as $shout)
	{
		echo '
			<tr class="', $alternate ? 'windowbg' : 'windowbg2', '">
				<td>';

		if (!empty($context['canDelete']))
			echo '
					<a href="', $scripturl, '?action=delete_shout;sesc=', $context['session_id'], ';sid=', $shout['id'], $context['url_direct'], '"><img src="', $settings['images_url'], '/TPdelete.gif" align="right" border="0" alt="X"></a>';
		echo '
					<span>', $shout['time'], '</span> - ', $shout['link'], ' -' . $shout['message'];
		echo '
				</td>';

		if (!empty($context['canDelete']))
			echo '
				<td width="4%" align="center">
					<input type="checkbox" name="ind_delete[]" value="', $shout['id'], '" class="check" />
				</td>';
		echo '
			</tr>';

		$alternate = !$alternate;
	}
	echo '
			<tr>
				<td class="catbg3" colspan="', !empty($context['canDelete']) ? 2 : 1, '" align="left" width="100%">
					<div style="float: left">', $context['page_index'], '</div>
					<div style="float: right">
						', $context['canDelete'] ? '<input type="submit" value="' . $txt['shout_delete_selected'] . '" name="delete_sel" />' : '', '
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
	</form>';

	if ($modSettings['enablearchiveshout'] == 1 && $context['canPost'])
	{
		echo '
		<p><table border="0" cellspacing="1" cellpadding="3" align="center" width="100%" class="borderclass">
			<tr>
				<td align="center">';

		template_shout_form();

		echo '
				</td>
			</tr>
		</table></p>';
	}
	
	// Pull the delete options for the administrator
	if (!empty($context['canDelete']))
		echo '
		<p><table border="0" cellspacing="1" cellpadding="4" align="center" width="100%">
			<tr>
				<td class="catbg" align="center" colspan="2">', $txt['shoutbox_42'], '
				</td>
			</tr>
			<tr>
				<td class="windowbg2" valign="top" width="50%">
					<form action="', $scripturl, '?action=delete_shout_age" method="post">', $txt['shoutbox_2'], '
						<input type="text" name="age" id="age" value="25" size="4" /> ', $txt['shoutbox_3'], '
				</td>
				<td class="windowbg2" valign="top">
					<input type="submit" name="submit" value="', $txt['shoutbox_5'], '" />
					<input type="hidden" name="sc" value="', $context['session_id'], '" />
					</form><br />
				</td>
			</tr><tr>
				<td class="windowbg2" valign="top" width="50%">
					<form action="', $scripturl, '?action=delete_all_shouts" method="post">', $txt['shoutbox_4'], ' ', $modSettings['shoutlimit'], '
				</td>
				<td class="windowbg2" valign="top">
					<input type="submit" name="submit" value="', $txt['shoutbox_5'], '" />
					<input type="hidden" name="sc" value="', $context['session_id'], '" />
					</form>
				</td>
			</tr>
		</table></p>';
}

function template_shout_box()
{
	global $context, $settings, $options, $txt, $user_info, $scripturl, $modSettings, $forum_version;

	// Only do auto refresh if we have it enabled!
	if (!empty($modSettings['shout_enableXML']))
	{
		// The code for handling auto refresh
		if ($forum_version < 'SMF 1.1')
		echo '
		<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/script_shout.js?1.03"></script>';
	
		echo '
		<script language="JavaScript1.2" type="text/javascript"><!--
			setTimeout("doAutoReload();", 5000);
	
			function doAutoReload()
			{
				if (window.XMLHttpRequest)
				{
					getXMLDocument("', $scripturl, '?action=shout_xml;xml", onDocReceived);
					setTimeout("doAutoReload();", 5000);
				}
			}
			function onDocReceived(XMLDoc)
			{
				// Where we create our new shouts.
				var insertData = \'\';
				// Number of shouts.
				var numShouts = XMLDoc.getElementsByTagName("shout").length;
				// Useful vars.
				var shoutID, shoutMemberName, shoutMemberID, shoutMemberEmail, shoutCanDelete, shoutTime, shoutLink, shoutMessage;
	
				// Do each shout in turn.
				for (i = 0; i < numShouts; i++)
				{
					shoutID = XMLDoc.getElementsByTagName("shout")[i].getAttribute("id");
	
					// If this shout exists... continue.
					if (document.getElementById("shout_" + shoutID))
						continue;
	
					shoutMemberName = XMLDoc.getElementsByTagName("shout")[i].getAttribute("member_name");
					shoutMemberID = XMLDoc.getElementsByTagName("shout")[i].getAttribute("member_id");
					shoutMemberEmail = XMLDoc.getElementsByTagName("shout")[i].getAttribute("member_email");
					shoutCanDelete = XMLDoc.getElementsByTagName("shout")[i].getAttribute("can_delete");
					shoutTime = XMLDoc.getElementsByTagName("shout")[i].getAttribute("time");
					shoutMessage = XMLDoc.getElementsByTagName("shout")[i].getAttribute("message");
					shoutLink = XMLDoc.getElementsByTagName("shout")[i].getAttribute("link");
	
					insertData = 
						\'<div id="shout_\' + shoutID + \'">\';
					if (shoutCanDelete)
						insertData += \'<a href="', $scripturl, '?action=delete_shout;sesc=', $context['session_id'], ';sid=\' + shoutID + \'"><img src="', $settings['images_url'], '/deleteshout.gif" border="0" alt="X"></a> \';
	
					insertData += shoutTime + \' \' + shoutLink + \' -\' + shoutMessage + \'', $modSettings['shoutsep'], '</div>\';';
	
					// Do the actual insert.
					if ($modSettings['shoutdir'] == 0)
						echo '
					setOuterHTML(document.getElementById("new_shout"), \'<span id="new_shout"></span>\' + insertData);';
					else
						echo '
					setOuterHTML(document.getElementById("new_shout"), insertData + \'<span id="new_shout"></span>\');';
			echo '
				}
			}
		//--></script>';
	}

	// Scrolling?
	if ($modSettings['enablescrollshout'] == 1)
		echo '
			<marquee direction="', $modSettings['shoutscrolldir'], '" width="', $modSettings['shoutscrollwidth'], '" 
			height="', $modSettings['shoutscrollheight'], '" scrollamount="', $modSettings['shoutscrollspeed'], '" 
			scrolldelay="', $modSettings['shoutscrolldelay'], '" onmouseover="this.stop()" onmouseout="this.start()">';

	// Insert the shouts, top or bottom depending on direction.
	if ($modSettings['shoutdir'] == 0)
		echo '
			<span id="new_shout"></span>';

	// Loop through the shouts!
	foreach ($context['shouts'] as $shout)
	{
		echo '
			<div class="smfshout_frame" id="shout_', $shout['id'], '">';
		// If it's an admin - they get the delete button...
		if (!empty($context['canDelete']))
			echo '
				<a href="', $scripturl, '?action=delete_shout;sesc=', $context['session_id'], ';sid=', $shout['id'], $context['url_direct'], '"><img src="', $settings['images_url'], '/TPdelete.gif" align="right" border="0" alt="X"></a>';
		echo '
			 ', $shout['link'] , '<br />', $shout['time'], '<div class="smfshout_body">', $shout['message'], '</div>';
		echo '
			</div>';
		
	}
	// For inserting new shouts...
	if ($modSettings['shoutdir'] != 0)
		echo '
			<span id="new_shout"></span>';

	// End the marquee?
	if ($modSettings['enablescrollshout'] == 1)
		echo '
			</marquee>';

	echo '
			<p style="text-align: center;"><a href="', $scripturl, '?action=shout_archive">', $txt['shoutbox_43'], '</a></p>';

	// Are we showing the shout form?
	if ($context['show_shout_form'] && function_exists('shout_form'))
		shout_form();
}

function template_shout_form()
{
	global $context, $settings, $options, $txt, $user_info, $scripturl, $modSettings, $ID_MEMBER;

	echo '
	<center>
		<form action="', $scripturl, '?action=shout" method="post">
			<input type="hidden" value="', $context['qstr'], '" name="qstr" />
			<input type="hidden" value="" name="email" />';

	if ($user_info['is_guest'])
	{
		echo '
				<input type="hidden" value="0" name="memberID" />
				<input type="text" value="', $txt['shoutbox_6'], '" name="displayname" maxlength="100" onfocus="this.value=\'\'" /><br />';

		if (empty($modSettings['shout_noGuestEmail']))
			echo '
				<input type="text" value="', $txt['shoutbox_7'], '" name="email" maxlength="100" onfocus="this.value=\'\'" /><br />';
	}
	else
		echo '
				<input type="hidden" value="', $user_info['name'], '" name="displayname" />
				<input type="hidden" value="', $ID_MEMBER, '" name="memberID" />';

	echo '
			<input type="text" value="', $txt['shoutbox_8'], '" name="message" size="16" maxlength="100" onfocus="if (this.value == \'', $txt['shoutbox_8'], '\')this.value=\'\'" /><br />
			<input type="submit" name="submit" style="margin-top: 5px;" value="', $txt['shoutbox_9'], '" />
			<input type="hidden" name="sc" value="', $context['session_id'], '" />
		</form>
	</center>';

}

// This template handles the xml refresh things...
function template_xml_shout()
{
	global $context, $settings, $options, $txt, $modSettings;

	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '>
<smf>';
	foreach ($context['shouts'] as $shout)
	{
		echo '
		<shout id="', $shout['id'], '" can_delete="', $context['canDelete'], '" time="', $shout['time'], '" member_email="', $shout['email'], '" member_id="', $shout['memberID'], '" member_name="', $shout['displayname'], '" message="', $shout['message'], '" link="', $shout['link'], '" />';
	}
	echo '
</smf>';
}

?>