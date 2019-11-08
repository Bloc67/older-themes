<?php
// Version: 0.1; Bugtracker

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

		// show theme tabs
		echo '
	<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 10px;">
		<tr>
			<td class="mirrortab_first">&nbsp;</td>';

if(!isset($settings['submenu'])){
    $ct = 'bugs';

    if($context['BTracker']['subaction']=='roadmap')
   		$ct='progress';

	// Show the all themes
	echo ($ct=='bugs') ? '<td class="mirrortab_active_first">&nbsp;</td>' : '' , '
				<td valign="top" class="mirrortab_' , $ct == 'bugs' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=bugtracker">Bugtracker</a>
				</td>' , $ct == 'bugs' ? '<td class="mirrortab_active_last">&nbsp;</td>' : '';

	// Show all SMF
	echo ($ct=='progress') ? '<td class="mirrortab_active_first">&nbsp;</td>' : '' , '
				<td valign="top" class="mirrortab_' , $context['BTracker']['subaction'] == 'roadmap' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=bugtracker;sa=roadmap">History</a>
				</td>' , $context['BTracker']['subaction'] == 'roadmap' ? '<td class="mirrortab_active_last">&nbsp;</td>' : '';

	echo '
				<td class="mirrortab_last">&nbsp;</td>
			</tr>
		</table>';
}
    echo '<div class="tborder">';
    echo '<div class="catbg" style="padding: 5px;">Bugtracker</div>';
	if($context['BTracker']['subaction']=='' && !isset($context['BTracker']['searchresults']))
	{
		echo '
			<div class="titlebg" style="padding: 5px;">Main</div>
				<table cellpadding="4" width="100%" cellspacing="2" class="windowbg"><tr>
					<td  valign="top" width="80%">';

		// show error message if present
		if(isset($context['BTracker']['error']))
			echo '<p>'.$context['BTracker']['error'].'</p>';
		else{
			$colorset=array('ffd0d0','ffffff','fff0f0');
			if(sizeof($context['BTracker']['bugs'])>0){
				echo '

			<table cellpadding="4" cellspacing="1" width="100%" class="windowbg3">';
			echo '<tr class="titlebg">
								<td colspan="2" class="smalltext">Bug</td>
								<td class="smalltext">Last updated</td>
								<td class="smalltext">Submitter</td>
								<td class="smalltext">Status</td>
								<td class="smalltext">For</td>
								<td class="smalltext">Type</td>
					     	 </tr>';
				foreach($context['BTracker']['bugs'] as $bug){
					echo '<tr >
								<td><img src="tp-images/bug_'.$bug['status'].'.png" alt="" /></td>
								<td ',$bug['status']!=1 ? 'style="font-weight: bold;"' : '' ,'>'.$bug['link'].'</td>
								<td class="smalltext">'.$bug['lastdate'].'</td>
								<td>'.$bug['submitter'].'</td>
								<td>'.$bug['statustitle'].'</td>
								<td>'.$context['BTracker']['tpversions'][$bug['devVersion']].'</td>
								<td>'.$context['BTracker']['tptypes'][$bug['type']].'</td>
					     	 </tr>';
				}
				echo '</table><div class="smalltext" style="margin-top: 2ex; font-weight: bold;">'.$context['BTracker']['pageindex'].'</div>';
			}
		}
		// show announcement and shortcuts
		echo '</td><td valign="top" width="20%">';
		bt_sidepanel();
		echo '</td></tr></table>';
	}
	// roadmap
	elseif($context['BTracker']['subaction']=='roadmap'){
		$colorset=array('ffd0d0','f0fff0','fff0f0');
		echo '<table width="100%" cellspacing="0" cellspacing="0"><tr><td valign="top" width="50%">';
		$version=true; $last='-99';
		foreach($context['BTracker']['roadmap'] as $rmap){
			if($rmap['type']<2){
				if($last!=$rmap['devVersion']){
					$version=true;
					$last=$rmap['devVersion'];
				}
				else
					$version=false;

				if($version)
					echo '<div class="titlebg" style="padding: 0.3em;">',$context['BTracker']['tpversions'][$rmap['devVersion']],'</div>';
				echo '<div class="windowbg3" style="padding: 1px;"><div class="smalltext" style="padding: 0.3em;">',$rmap['link'],'('.$rmap['type'].')</div></div>';
			}
		}
		echo '</div></td><td valign="top">';
		$version=true; $last='-99';
		foreach($context['BTracker']['roadmap'] as $rmap){
			if($rmap['type']==2){
				if($last!=$rmap['devVersion']){
					$version=true;
					$last=$rmap['devVersion'];
				}
				else
					$version=false;

				if($version)
					echo '<div class="titlebg" style="padding: 0.3em;">',$context['BTracker']['tpversions'][$rmap['devVersion']],'</div>';

				echo '<div class="windowbg3" style="padding: 1px;"><div class="smalltext" style="padding: 0.3em;">',$rmap['link'],'</div></div>';
		}
		}
	}

	// single bug
	elseif(substr($context['BTracker']['subaction'],0,3)=='bug'){
		echo '
	<form name="Bugtracker" action="'.$scripturl.'?action=bugtracker;sa=" method="post">
		<div class="titlebg" style="padding: 5px;">Single bug</div>
		<table cellpadding="4" width="100%" cellspacing="1" class="windowbg"><tr>
			<td valign="top" width="80%">';

		foreach($context['BTracker']['buginfo'] as $bug){
			echo '<table align="center" width="100%" cellpadding="5" cellspacing="1" class="bordercolor">
				<tr class="windowbg2"><td width="150" align="right">Title:</td><td>
					' , $context['user']['is_admin'] ? '<input style="width: 95%;" name="bt_bugtitle" type="text" value="'.$bug['title'].'">' :  $bug['title'] , '</td></tr>
				<tr class="windowbg2" ><td width="150" align="right">Status:</td>
				<td style="font-weight: bold;">';
		if($context['BTracker']['is_cvs'])
			echo '
				<select size="1" name="bt_bugstate">
					<option value="0" ' , $bug['status']=='0' ? 'selected' : '' , '>Not assigned</option>
					<option value="1" ' , $bug['status']=='1' ? 'selected' : '' , '>Closed</option>
					<option value="2" ' , $bug['status']=='2' ? 'selected' : '' , '>Open</option>
					' , $context['user']['is_admin'] ? '<option value="-1" ' . ($bug['status']=='-1' ? 'selected' : '' ) . '>DELETED</option>' : '' , '
				</select>
			';
		else
			echo $bug['statustitle'].'</td></tr>';

		echo '
				<tr class="windowbg2"><td width="150" align="right">Type:</td>
				<td style="font-weight: bold;">';
		if($context['user']['is_admin'])
			echo '
				<select size="1" name="bt_bugtype">
					<option value="0" ' , $bug['type']=='0' ? 'selected' : '' , '>'.$context['BTracker']['tptypes'][0].'</option>
					<option value="1" ' , $bug['type']=='1' ? 'selected' : '' , '>'.$context['BTracker']['tptypes'][1].'</option>
					<option value="2" ' , $bug['type']=='2' ? 'selected' : '' , '>'.$context['BTracker']['tptypes'][2].'</option>
				</select>
			';
		else
			echo $context['BTracker']['tptypes'][$bug['type']].'</td></tr>';

		echo '
				<tr class="windowbg2"><td align="right">Submitted by:</td><td>'.$bug['submitter'].'</td></tr>
				<tr class="windowbg2"><td align="right">Reported:</td><td>'.$bug['firstdate'].'</td></tr>
				<tr class="windowbg2"><td align="right">Last update:</td><td >'.$bug['lastdate'].'</td></tr>
				<tr class="windowbg2"><td align="right">TP version:</td><td>'.$bug['tpversion'].'</td></tr>
				<tr class="windowbg2"><td align="right">SMF version:</td><td>'.$bug['smfversion'].'</td></tr>
				<tr class="windowbg2" ><td align="right">Assigned to:</td><td>';
				if($context['BTracker']['is_cvs'])
					echo '<div style="float: right; font-weight: bold;"><a href="index.php?action=bugtracker;sa=assign'.$bug['id'].'">' . ($bug['assignedLink']=='' ? 'Assign me!' : 'Re-assign') . '</a></div>';
				echo $bug['assignedLink'];

				echo '
				</td></tr>
				<tr class="windowbg2"><td align="right" valign="top">Post:</td><td>'.$bug['text']. '</td></tr>';
		if($bug['devVersion']>0)
			echo'
				<tr class="windowbg2" ><td align="right" valign="top"><b>Developers note:</b></td><td>
				<div style="margin-bottom: 5px; padding: 0.5em; border: solid 1px red;">Corrected for <b>'.$context['BTracker']['tpversions'][$bug['devVersion']].'</b>
				('.$bug['devTime'].')</div>' , $context['user']['is_admin'] ? '<textarea  style="width: 95%;" name="bt_devnotes" rows=10 cols=40 wrap="on">'.$bug['devNoteraw'].'</textarea><br />'.$bug['devNote'] : $bug['devNote'] , '</td></tr>';

		if($context['user']['is_admin'])
				echo '
				<tr class="windowbg2" ><td align="right">Solution added to</td><td>
									<input type="hidden" name="bt_devtime" value="'.time().'" />
									<select size="1" name="bt_solvedversion">
  										<option value="0" ' , $bug['devVersion']==0 ? 'selected' : '' , '>-None-</option>
  										<option value="1" ' , $bug['devVersion']==1 ? 'selected' : '' , '>0.9.5</option>
  										<option value="5" ' , $bug['devVersion']==5 ? 'selected' : '' , '>0.9.6</option>
  										<option value="6" ' , $bug['devVersion']==6 ? 'selected' : '' , '>0.9.7</option>
  										<option value="7" ' , $bug['devVersion']==7 ? 'selected' : '' , '>0.9.8</option>
  										<option value="8" ' , $bug['devVersion']==8 ? 'selected' : '' , '>1.0</option>
  										<option value="3" ' , $bug['devVersion']==3 ? 'selected' : '' , '>Site issue</option>
  										<option value="4" ' , $bug['devVersion']==4 ? 'selected' : '' , '>Other</option>
									</select>
				</td></tr>';

				echo '
				<tr class="windowbg2"><td align="right" valign="top">Solution:</td><td >';
			if(($context['BTracker']['is_cvs'] && $bug['solutionraw']=='') || $context['user']['is_admin'] || $context['BTracker']['can_edit'])
				echo '<textarea name="bt_solution"  style="width: 95%;" rows=8 cols=40 wrap="on">' ,$bug['solutionraw'], '</textarea>';
			else{
				if($context['BTracker']['is_cvs'])
					echo '<a href="index.php?action=bugtracker;sa=bug'.$bug['id'].';edit">[edit]</a><br />';
				echo $bug['solution'];
			}
			if($context['user']['is_admin'])
				echo $bug['solution'];

			echo '</td></tr>
					' , $context['BTracker']['is_cvs'] ? '<tr class="windowbg2">
							<td colspan="2" class="windowbg" align="center"><input type="submit" value="Send">
				<input type="hidden" name="sc" value="'. $context['session_id']. '" />
				<input type="hidden" name="BTracker" value="'.$bug['id'].'" />
								</td>
							</tr>' : '' , '
			</table>';
		}
		echo '</td><td valign="top" width="20%">';
		bt_sidepanel();
		echo '</td></tr></table>
	</form>';
	}
	// thanks
	elseif($context['BTracker']['subaction']=='thanks'){
		echo '<div class="titlebg" style="padding: 5px;">Thank you for your submission!</div>
		<table cellpadding="4" width="100%" cellspacing="1" class="windowbg"><tr>
					<td valign="top">Your submitted bug report has been added to the the tracker and will be examined as soon as possible.';

		echo '</td><td valign="top" width="20%">';
		bt_sidepanel();
		echo '</td></tr></table>';
	}
	// search results
	elseif($context['BTracker']['subaction']=='' && isset($context['BTracker']['searchresults'])){
		echo '<div class="titlebg" style="padding: 5px;">Search results</div>
		<table cellpadding="4" width="100%" cellspacing="1" class="windowbg"><tr>
					<td valign="top"><div style="padding: 1em 3em; font-size: 1.3em;">Search results for "'.$context['BTracker']['search'].'"';
		foreach($context['BTracker']['searchresults'] as $res)
			echo '<div style="padding: 0.5em; font-size: 10pt;">#'.$res['id'].'&nbsp; <b>' . $res['link'] . '</b><br><span class="smalltext">...'.$res['text'].'...</span></div>';

		echo '
					</div></td><td valign="top" width="20%">';
		bt_sidepanel();
		echo '</td></tr></table>';
	}
	// report bug
	elseif($context['BTracker']['subaction']=='reportbug')
	{
		echo '
				<div class="titlebg" style="padding: 5px">Report a bug!</div>
		<table cellpadding="4" width="100%" cellspacing="1" class="windowbg"><tr>
					<td valign="top" width="80%">
				<form name="Bugtracker" action="'.$scripturl.'?action=bugtracker;sa=report" method="post">
						<table cellpadding="4" width="100%" cellspacing="1" class="bordercolor">
							<tr class="windowbg2">
								<td valign="top" colspan="2" class="windowbg2"><span class="smalltext">
									In this section you can report any bugs you find in TinyPortal, as well as enhancement/bugfixes you might have.
								</span></td>
							</tr>
							<tr class="windowbg2">
								<td valign="top" align="right" width="35%" class="windowbg2">Title:</td>
								<td class="windowbg2"><input style="width: 85%;" name="bt_title" type="text" value=""></td>
							</tr>
							<tr class="windowbg2">
								<td valign="top" class="windowbg2" align="right">SMF & TP version:</td>
								<td class="windowbg2">
									<select size="1" name="bt_smfversion">
  										<option value="1">SMF 1.1 RC3</option>
  										<option value="2">SMF 1.1 RC2</option>
  										<option value="3">SMF 1.1 RC1</option>
  										<option value="4">SMF 1.1 beta3</option>
  										<option value="5">SMF 1.0.5-1.0.9</option>
 										<option value="6" selected>SMF 1.1</option>
 										<option value="7">SMF 1.1.1</option>
									</select>
									<select size="1" name="bt_tpversion">';

			echo '
  										<option value="10">TP 1.0 alpha</option>
  										<option value="9">TP 0.98 beta</option>
  										<option value="8" selected>TP 0.97 beta</option>
  										<option value="7">TP 0.96 beta</option>
 									</select>
								</td>
							</tr>
							<tr class="windowbg2">
								<td valign="top" align="right" width="35%" class="windowbg2">Description:</td>
								<td class="windowbg2"><textarea name="bt_text" style="width: 85%; height: 200px;" wrap="on"></textarea>
								<br />You can use BBC codes in the description field.</td>
							</tr>
						<tr class="windowbg2">
								<td valign="top" align="right" width="35%" class="windowbg2">Type:</td>
								<td class="windowbg2">
									<input name="bt_bugtype" type="radio" value="0" checked> TP Bug / Error<br />
									<input name="bt_bugtype" type="radio" value="1"> Site error<br />
									<input name="bt_bugtype" type="radio" value="2"> Feature request<br />
									<input name="bt_bugsubmitter" type="hidden" value="'.$context['user']['id'].'">
								</td>
							</tr>
						<tr class="windowbg2">
								<td colspan="2" class="windowbg" align="center"><input type="submit" value="Send">
								</td>
							</tr>
		     			</table>
				</form>
				';

		echo '</td><td valign="top" width="20%">';
		bt_sidepanel();
		echo '</td></tr></table>';
	}
	echo '</div></div>';
}
function bt_sidepanel()
{
		global $scripturl, $context;

		$colorset=array('ffd0d0','ffffff','fff0f0');
		// tools
		echo '
		<form method=post action="index.php?action=bugtracker" style="padding: 5px;">
			<input value="' , isset($context['BTracker']['search']) ? $context['BTracker']['search'] : '' , '" type="text" name="bsearch" style="margin-bottom: 5px;"><input type="submit" value="Search">
		</form>
		<div class="smalltext" style="margin-top: 2px; padding: 2px; border: solid 1px #a0a0a0;"><a href="'.$scripturl.'?action=bugtracker;t=wait">'.$context['BTracker']['all_wait'].' bugs not assigned yet</a></div>
		<div class="smalltext" style="margin-top: 2px; padding: 2px; border: solid 1px #a0a0a0;"><a href="'.$scripturl.'?action=bugtracker;t=open">'.$context['BTracker']['all_open'].' open bugs</a></div>
		<div class="smalltext" style="margin-top: 2px; padding: 2px; border: solid 1px #a0a0a0;"><a href="'.$scripturl.'?action=bugtracker;t=closed">'.$context['BTracker']['all_closed'].' closed bugs</a></div>
		<div class="smalltext" style="margin-top: 2px;"><a href="'.$scripturl.'?action=bugtracker"><b>'.$context['BTracker']['all'].' bugs submitted</b></a><br />
		<a href="'.$scripturl.'?action=bugtracker;t=tpbug">'.$context['BTracker']['all_tpbug'].' TP bugs</a><br />
		<a href="'.$scripturl.'?action=bugtracker;t=sitebug">'.$context['BTracker']['all_sitebug'].' Site bugs</a><br />
		<a href="'.$scripturl.'?action=bugtracker;t=feature">'.$context['BTracker']['all_feature'].' feature requests</a>
		</div><br />
		';
		if($context['user']['is_logged'])
			echo '<a href="'.$scripturl.'?action=bugtracker;t=submit">Show my reported bugs</a>';

		if($context['BTracker']['is_cvs'])
			echo '<br /><a href="'.$scripturl.'?action=bugtracker;t=assign">Show my assigned bugs</a>';

		echo '<br /><br />
		<h4 style="margin:0;">Sort by</h4>
					<a href="'.$scripturl.'?action=bugtracker">Show all</a><br />
					<a href="'.$scripturl.'?action=bugtracker;t=smf">Sort by SMF version</a><br />
					<a href="'.$scripturl.'?action=bugtracker;t=tp">Sort by TP version</a><hr />';


		echo '
		<h4 style="margin: 2ex 0 0 0;"><a href="index.php?action=bugtracker;sa=reportbug">Report a bug?</a> <img src="Themes/default/images/post/bug.gif" alt="" /></h4>
		If you have discovered a bug in TP, search if it has been reported before and if not, <a href="index.php?action=bugtracker;sa=reportbug">please report it</a>.
					';
}

?>