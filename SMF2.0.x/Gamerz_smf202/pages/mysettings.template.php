<?php

function template_main()
{
	global $context, $settings, $options, $scripturl, $txt, $smcFunc;
	
	// check the access first
	isAllowedTo('admin_forum');
	
	// a subpage defined?
	if(isset($_GET['sa']) && in_array($_GET['sa'], array('menu','blog','gallery','about','addtopitem','edittopitem','removetopitem','removechilditem')))
		$sub = $_GET['sa'];
	else
		$sub = 'menu';
	
	if(!empty($settings['blogboards']))
		$blogboards = explode(",",$settings['blogboards']);
	else
		$blogboards = array();

	if(!empty($settings['galleryboards']))
		$galleryboards = explode(",",$settings['galleryboards']);
	else
		$galleryboards = array();


	
	if(!empty($settings['topmenus']))
	{
		$topmenus = explode(",",$settings['topmenus']);
		// some precautions
		$checks = array('no_blog' => 'bwblog','no_gallery'=>'bwgallery','no_about' => 'about');
		foreach($checks as $check => $item)
		{
			if(!empty($settings[$check]))
			{
				foreach($topmenus as $t => $td)
				{
					if($td == $item)
						unset($topmenus[$t]);
				}
			}
		}
	}
	else
		$topmenus = array();
	
	if(!empty($settings['childmenus']))
	{
		$childmenus = unserialize($settings['childmenus']);
	}
	else
		$childmenus = array();

	if(!empty($settings['extramenus']))
	{
		$extramenus = unserialize($settings['extramenus']);
	}
	else
		$extramenus = array();

	$topmenutexts = array();
	foreach($context['menu_buttons'] as $but => $a)
	{
		$topmenutexts[$but] = $a['title'];
		if(!empty($a['sub_buttons']))
		{
			foreach($a['sub_buttons'] as $but2 => $a2)
				$topmenutexts[$but2] = $a2['title'];
		}
	}
	// ok, got some input from forms?
	if(!empty($_POST['mysettings_submit']))
	{
		checksession('post');
		// the menu options
		foreach($_POST as $what => $data)
		{
			if(substr($what, 0,7)=='addtop_')
			{
				$topmenus[] = substr($what,7);
			}
			elseif(substr($what, 0,7)=='remove_')
			{
				$ww = substr($what,7);
				foreach($topmenus as $where => $val)
				{
					if($ww == $val)
						unset($topmenus[$where]);
					// any childmenus?
					foreach($childmenus as $child => $parent)
					{
						if($ww == $parent)
							unset($childmenus[$child]);
					}
				}
				if(isset($childmenus[$ww]))
					unset($childmenus[$ww]);
			}
			elseif(substr($what, 0,12)=='removechild_')
			{
				$ww = substr($what,12);
				{
					if(isset($childmenus[$ww]))
						unset($childmenus[$ww]);
				}
			}
			elseif(substr($what, 0,6)=='move1_' || substr($what, 0,6)=='move2_' || substr($what, 0,6)=='move3_' || substr($what, 0,6)=='move4_')
			{
				if($data!='')
				{
					$ww = substr($what,6);
					// check a few things
					if(!in_array($ww, $topmenus))
					{
						$childmenus[$ww] = $data;
					}
				}
			}
			elseif($what=='new_id')
			{
				$id = $data;
				$extramenus[$id] = array(
					'id' => $id,
					'href' => !empty($_POST['new_href']) ? $_POST['new_href'] : '', 
					'title' => !empty($_POST['new_title']) ? htmlentities($_POST['new_title']) : '', 
				);
				if(!empty($_POST['parent']))
					$childmenus[$id] = $_POST['parent'];

				updateMySettings('childmenus',serialize($childmenus),'');
				updateMySettings('extramenus',serialize($extramenus),'action=mysettings;sa=menu');
			}
			elseif(substr($what,0,6) == 'old_id')
			{
				$id = substr($what,6);
				$extramenus[$id] = array(
					'id' => $id,
					'title' => $_POST['old_title'.$id], 
					'href' => $_POST['old_href'.$id], 
				);
				updateMySettings('extramenus',serialize($extramenus),'action=mysettings;sa=edittopitem;u='.$id.';'.$context['session_var'].'='.$context['session_id']);
			}
		}
		$inserts = array(0, 1, 'topmenus', implode(',', $topmenus));		
		$smcFunc['db_insert']('replace',
				'{db_prefix}themes',
				array('id_member' => 'int', 'id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),
				$inserts,
				array('id_member', 'id_theme', 'variable')
			);
	
		$inserts = array(0, 1, 'childmenus', serialize($childmenus));		
		$smcFunc['db_insert']('replace',
				'{db_prefix}themes',
				array('id_member' => 'int', 'id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),
				$inserts,
				array('id_member', 'id_theme', 'variable')
			);
		cache_put_data('theme_settings-1', null, 90);

		// Invalidate the cache.
		updateSettings(array('settings_updated' => time()));
		redirectexit('action=mysettings;sa='.$sub);
	}

	// 
	if(!empty($_POST['bboard']))
	{
		checksession('post');
		// the menu options
		$bboard = array();
		foreach($_POST as $what => $data)
		{
			if(substr($what, 0,9)=='blogboard')
				$bboard[] = $data;

		}
		$inserts = array(0, 1, 'blogboards', implode(",",$bboard));		
		$smcFunc['db_insert']('replace',
				'{db_prefix}themes',
				array('id_member' => 'int', 'id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),
				$inserts,
				array('id_member', 'id_theme', 'variable')
			);
		cache_put_data('theme_settings-1', null, 90);

		// Invalidate the cache.
		updateSettings(array('settings_updated' => time()));
		redirectexit('action=mysettings;sa=blog');
	}		
	elseif(!empty($_POST['gboard']))
	{
		checksession('post');
		// the menu options
		$gboard = array();
		foreach($_POST as $what => $data)
		{
			if(substr($what, 0,12)=='galleryboard')
				$gboard[] = $data;

		}
		$inserts = array(0, 1, 'galleryboards', implode(",",$gboard));		
		$smcFunc['db_insert']('replace',
				'{db_prefix}themes',
				array('id_member' => 'int', 'id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),
				$inserts,
				array('id_member', 'id_theme', 'variable')
			);
		cache_put_data('theme_settings-1', null, 90);

		// Invalidate the cache.
		updateSettings(array('settings_updated' => time()));
		redirectexit('action=mysettings;sa=gallery');
	}		
	elseif(!empty($_POST['colors']))
	{
		checksession('post');
		// the menu options
		updateMySettings('colorversion', $_POST['colorversion'] ,'',$settings['theme_id']);
		if(isset($_POST['no_colorselect']))
			updateMySettings('no_colorselect', $_POST['no_colorselect'] ,'',$settings['theme_id']);
		else
			updateMySettings('no_colorselect', '' ,'',$settings['theme_id']);
		
		redirectexit('action=mysettings;sa=color');
	}		
	elseif(!empty($_POST['aboutbox']))
	{
		checksession('post');
		$what = $_POST['aboutbox'];
		$pre = '<?php
function template_main()
{
?>
<div style="padding: 0.4em;">
<!--split-->
';
		$post = '
<!--split-->
</div>
<?php
}
?>';
		$success = file_put_contents($settings['theme_dir'].'/pages/about.template.php', $pre.$what.$post);
		redirectexit('action=mysettings;sa=about');
	}		
	

	// the top menu
	echo '
	<div style="overflow: hidden; text-align: center; margin: auto;">';
	$headers = array(
		'Menus',
		'Blogs',
		'Galleries',
		'Color',
		'About',
		'More..',
		);
	$texts = array(
		'Manage the main menu.',
		'Set which boards to show as blog boards.',
		'Set which boards to show as gallery boards.',
		'Choose a color-version.',
		'Edit the About page.',
		'More current theme settings',
		);
	foreach(array('menu','blog','gallery','color','about') as $count => $ico)
		echo '<a class="mysettings" title="'. $texts[$count] . '" href="' , $scripturl , '?action=mysettings;sa=' . $ico . '">
	<img ' . ($ico==$sub ? 'class="active"' : '') . ' src="' . $settings['images_url'] . '/settings/' . $ico . '.png" alt="" /></a>';

	// return back..
	echo '<a class="mysettings" title="'. $texts[$count] . '" href="' , $scripturl , '?action=admin;area=theme;sa=settings;th=' . $settings['theme_id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">More..</a>';

	echo '
	</div>';

	if($sub == 'menu')
	{
		echo '
<form style="background: none; width: 100%;" id="mysettings_form" method="post" action="' . $scripturl . '?action=mysettings" enctype="multipart/form-data">
	<div class="title_bar">
		<h3 class="titlebg">Custom Menu Manager</h3>
	</div>
	<div style="overflow: hidden; padding: 10px;">
		<div id="admin_menu">
			<ul class="dropmenu">
				<li><a class="firstlevel active" href=""><span class="firstlevel">Index</span></a></li>
				<li><a class="firstlevel" href="' , $scripturl  , '?action=mysettings;sa=addtopitem;'.$context['session_var'].'='.$context['session_id'].'">
				<span class="firstlevel">Add  Custom Top Menu</span></a></li>
			</ul>
		</div><hr style="clear: both;" />
		
	<div style="clear: both; ">
	<table style="width: 100%;">
		<tr>
		<td style="width: 49%; padding: 1% 1% 1% 0; vertical-align: top;">
			<h3 style="padding: 0 5px 5px 5px;">Existing top menus</h3>';
		

		foreach($topmenus as $top)
		{
			$done = false;
			// locate it
			foreach($context['menu_buttons'] as $button => $data)
			{
				if($button == $top)
				{
					$renderbutton = $top;
					$done=true;
				}
			}
			if(!$done)
				continue;
			
			echo '
		<div style="padding: 5px 10px; background: #444; margin: 2px; overflow: hidden;">
			<table style="width: 100%;"><tr>
				<td style="width: 30%; vertical-align: top;"><b>'. $topmenutexts[$renderbutton] . '</b>
				' , isset($context['menu_buttons'][$top]['extra']) ? '
					<a href="' . $scripturl . '?action=mysettings;sa=edittopitem;u='.$top.';'.$context['session_var'].'='.$context['session_id'].'"><img src="'.$settings['images_url'].'/theme/my_edit.png" alt="" /></a>
					<a onclick="return confirm(\'Are you sure you want to delete?\');" href="' . $scripturl . '?action=mysettings;sa=removetopitem;u='.$top.';'.$context['session_var'].'='.$context['session_id'].'"><img src="'.$settings['images_url'].'/theme/my_delete.png" alt="" /></a>
					' : '' , '
				</td>
				<td style=" vertical-align: top; ">
				</td>
				<td style=" vertical-align: top;width: 30%;">
				<input type="checkbox" value="1" id="remove_'.$top.'" name="remove_'.$top.'" > Remove?
				</td>
			</tr></table>
				';

			// any childs then?
			foreach($childmenus as $child => $parent)
			{
				if($parent == $top)
				{
					if(empty($topmenutexts[$child]))
						continue;
					
					echo '		
				<div style="padding: 5px 0 ; border-top: solid 1px #eee; margin: 2px; overflow: hidden;">
					<table style="width: 100%;"><tr>
						<td style="width: 30%; vertical-align: top;">&nbsp;&nbsp;-> '. $topmenutexts[$child] . '	
						' , isset($context['menu_buttons'][$child]['extra']) ? '
					<a href="' . $scripturl . '?action=mysettings;sa=edittopitem;u='.$child.';'.$context['session_var'].'='.$context['session_id'].'"><img src="'.$settings['images_url'].'/theme/my_edit.png" alt="" /></a>
					<a onclick="return confirm(\'Are you sure you want to delete?\');" href="' . $scripturl . '?action=mysettings;sa=removetopitem;u='.$child.';'.$context['session_var'].'='.$context['session_id'].'"><img src="'.$settings['images_url'].'/theme/my_delete.png" alt="" /></a>
						' : '' , '
						</td>
						<td style=" vertical-align: top; ">Move 
							<select id="move2_'.$child.'" name="move2_'.$child.'">
								<option value="">- none -</option>';
					if(count($topmenus)>0)
					{
						foreach($topmenus as $tops)
						{
							if($tops != $parent)
								echo '<option value="'.$tops.'">..under ' . $topmenutexts[$tops] . '</option>';
						}
					}
					echo '			
							</select>
						</td>
						<td style=" vertical-align: top;width: 30%;">
						
						<input type="checkbox" value="1" id="removechild_'.$child.'" name="removechild_'.$child.'" > Remove?
						</td>
					</tr></table>
				</div>';
				}

			}
			echo '
		</div>';
		}

		echo '
		</td>
		<td style="width: 49%; padding: 1% 0 1% 1%; vertical-align: top;">
			<h3 style="padding: 0 5px 0px 5px;">Available items</h3>
			<div class="plainbox">All items below will appear under the home menu by default if they are not defined as either top items or child items of a top menu item. NB! Requires at least <b>one</b> top menu to be defined, otherwise the menu is shown the default SMF way.</em></div>';
		
		foreach($context['menu_buttons'] as $renderbutton => $data)
		{
			$exists = false;
			if(in_array($renderbutton, $topmenus))
				$exists = true;
			
			foreach($childmenus as $child => $parent)
			{
				if($renderbutton == $child)
					$exists = true;
			}
			
			if($exists)
				continue;

			echo '
		<div style="padding: 5px 10px; background: #444; margin: 1px 2px; overflow: hidden;">
			<table style="width: 100%;"><tr>
				<td style="width: 25%;">'. $topmenutexts[$renderbutton] . '	
				' , isset($context['menu_buttons'][$renderbutton]['extra']) ? '
					<a href="' . $scripturl . '?action=mysettings;sa=edittopitem;u='.$renderbutton.';'.$context['session_var'].'='.$context['session_id'].'"><img src="'.$settings['images_url'].'/theme/my_edit.png" alt="" /></a>
					<a onclick="return confirm(\'Are you sure you want to delete?\');" href="' . $scripturl . '?action=mysettings;sa=removetopitem;u='.$renderbutton.';'.$context['session_var'].'='.$context['session_id'].'"><img src="'.$settings['images_url'].'/theme/my_delete.png" alt="" /></a>
						' : '' , '
				</td>
				<td>Move 
					<select id="move4_'.$renderbutton.'" name="move4_'.$renderbutton.'">
						<option value="">- none -</option>';
			if(count($topmenus)>0)
			{
				foreach($topmenus as $tops)
					echo '<option value="'.$tops.'">..under ' . $topmenutexts[$tops] . '</option>';
			}
			echo '			
					</select>
				</td>
				<td>
				
				<input type="checkbox" value="1" id="addtop_'.$renderbutton.'" name="addtop_'.$renderbutton.'" > Set as top item?
				</td>
			</tr></table>
		</div>';
		}
		echo '
		</td>
	</tr>
	</table>
	<div style="padding: 1em; text-align: center;">
		<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
		<hr /><input type="submit" name="mysettings_submit" id="mysettings_submit" value="Submit" />
	</div>
	</div>
	</div>

</form>';
	}
	elseif($sub=='addtopitem')
	{
		checksession('get');
		echo '
<form style="background: none; width: 100%;" id="mysettings_form" method="post" action="' . $scripturl . '?action=mysettings" enctype="multipart/form-data">
	<div class="title_bar">
		<h3 class="titlebg">Custom Menu Manager - Add menu item</h3>
	</div>
	<div style="overflow: hidden; padding: 10px;">
		<div id="admin_menu">
			<ul class="dropmenu">
				<li><a class="firstlevel" href="' , $scripturl  , '?action=mysettings;sa=menu"><span class="firstlevel">Index</span></a></li>
				<li><a class="firstlevel active" href=""><span class="firstlevel">Add  Custom Top Menu</span></a></li>
			</ul>
		</div><hr style="clear: both;" />
		<div class="windowbg2">
			 <span style="width: 7%; text-align: right; margin: 4px 1% 0 0; display: block; float: left;">ID </span><input type="text" id="new_id" name="new_id" style="width: 90%; margin: 4px 0;" /><br>
			<span style="width: 7%; text-align: right; margin: 4px 1% 0 0;display: block; float: left;">Title</span><input type="text" id="new_title" name="new_title" style="width: 90%; margin: 4px 0;" /> <br>
			<span style="width: 7%; text-align: right; margin: 4px 1% 0 0;display: block; float: left;">URL</span><input type="text" id="new_href" name="new_href" style="width: 90%; margin: 4px 0;" /><br>
		</div>
		<div style="padding: 1em; text-align: center;">
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			<hr /><input type="submit" name="mysettings_submit" id="mysettings_submit" value="Submit" />
		</div>
	</div>
</form>';
	
	}
	elseif($sub=='edittopitem')
	{
		if(empty($_GET['u']))
			redirectexit('action=mysettings;sa=menu');

		$what = $_GET['u'];
		
		checksession('get');
		$item = $extramenus[$what];

		echo '
<form style="background: none; width: 100%;" id="mysettings_form" method="post" action="' . $scripturl . '?action=mysettings" enctype="multipart/form-data">
	<div class="title_bar">
		<h3 class="titlebg">Custom Menu Manager - Add menu item</h3>
	</div>
	<div style="overflow: hidden; padding: 10px;">
		<div id="admin_menu">
			<ul class="dropmenu">
				<li><a class="firstlevel" href="' , $scripturl  , '?action=mysettings;sa=menu"><span class="firstlevel">Index</span></a></li>
				<li><a class="firstlevel" href="' , $scripturl  , '?action=mysettings;sa=addtopitem;'.$context['session_var'].'='.$context['session_id'].'">
				<span class="firstlevel">Add  Custom Top Menu</span></a></li>
				<li><a class="firstlevel active" href=""><span class="firstlevel">Edit Custom Top Menu</span></a></li>
			</ul>
		</div><hr style="clear: both;" />
		<div class="windowbg2">
			 <span style="width: 7%; text-align: right; margin: 4px 1% 0 0; display: block; float: left;">ID </span><input type="text" id="old_id'.$item['id'].'" name="old_id'.$item['id'].'" value="'.$item['id'].'" style="width: 90%; margin: 4px 0;" /><br>
			<span style="width: 7%; text-align: right; margin: 4px 1% 0 0;display: block; float: left;">Title</span><input type="text" id="old_title'.$item['id'].'" name="old_title'.$item['id'].'" value="'.$item['title'].'"  style="width: 90%; margin: 4px 0;" /> <br>
			<span style="width: 7%; text-align: right; margin: 4px 1% 0 0;display: block; float: left;">URL</span><input type="text" id="old_href'.$item['id'].'" name="old_href'.$item['id'].'" value="'.$item['href'].'" style="width: 90%; margin: 4px 0;" /><br>
		</div>
		<div style="padding: 1em; text-align: center;">
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			<hr /><input type="submit" name="mysettings_submit" id="mysettings_submit" value="Submit" />
		</div>
	</div>
</form>';
	
	}
	elseif($sub=='removetopitem')
	{
		checksession('get');
		if(!empty($_GET['u']))
		{
			$what = $_GET['u'];
			foreach($extramenus as $renderbutton => $data)
			{
				if($what == $renderbutton)
				{
					// chek if its a child too
					if(isset($childmenus[$renderbutton]))
					{
						unset($childmenus[$renderbutton]);
						updateMySettings('childmenus',serialize($childmenus),'');
					}
					unset($extramenus[$renderbutton]);
					updateMySettings('extramenus',serialize($extramenus),'action=mysettings;sa=menu');
				}
			}
			
		}
	}
	elseif($sub=='removechilditem')
	{
		checksession('get');
		if(!empty($_GET['u']))
		{
			$what = $_GET['u'];
			foreach($childmenus as $renderbutton )
			{
				if($what == $renderbutton)
				{
					unset($childmenus[$renderbutton]);
					updateMySettings('childmenus',serialize($childmenus),'action=mysettings;sa=menu');
				}
			}	
		}
	}
	elseif($sub=='blog')
	{
		echo '
<form style="background: none; width: 100%;" id="mysettings_form" method="post" action="' . $scripturl . '?action=mysettings" enctype="multipart/form-data">
	<div class="title_bar">
		<h3 class="titlebg">Setting boards as "blog boards"</h3>
	</div>
	<div style="overflow: hidden; padding: 10px;">

		<div class="windowbg2">';

		$result_boards = $smcFunc['db_query']('', 'SELECT id_board, name FROM {db_prefix}boards WHERE 1');
		$count=1;
		while ($row = $smcFunc['db_fetch_assoc']($result_boards))
		{
			echo '<div class="floatleft" style="padding: 4px; width: 18%; border: solid 1px #f0f0f0; margin: 2px;">
			<input type="checkbox" id="blogboard'.$count.'" name="blogboard'.$count.'" value="'.$row['id_board'].'"' , in_array($row['id_board'],$blogboards) ? ' checked="checked"' : '' , '><span' , in_array($row['id_board'],$galleryboards) ? ' class="error" title="This board is also set as galleryboard - it will NOT show as blog then!"' : '' ,'> '. $row['name'].'</span>
			</div>';
			$count++;
		}

		echo '
		<p class="error smalltext" style="padding: 1em;clear: both; font-size: 0.8em;">* This board is set as a galleryboard</p>
		</div>
		<div style="padding: 1em; text-align: center; clear: both;">
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			<input type="hidden" name="bboard" id="bboard" value="1" />
			<hr /><input type="submit" name="mysettings_submit2" id="mysettings_submit2" value="Submit" />
		</div>
	</div>
</form>';
	
	}
	elseif($sub=='gallery')
	{
		echo '
<form style="background: none; width: 100%;" id="mysettings_form" method="post" action="' . $scripturl . '?action=mysettings" enctype="multipart/form-data">
	<div class="title_bar">
		<h3 class="titlebg">Setting boards as "gallery boards"</h3>
	</div>
	<div style="overflow: hidden; padding: 10px;">
		<div class="windowbg2">';

		$result_boards = $smcFunc['db_query']('', 'SELECT id_board, name FROM {db_prefix}boards WHERE 1');
		$count=1;
		while ($row = $smcFunc['db_fetch_assoc']($result_boards))
		{
			echo '<div class="floatleft" style="padding: 4px; width: 18%; border: solid 1px #f0f0f0; margin: 2px;">
			<input type="checkbox" id="galleryboard'.$count.'" name="galleryboard'.$count.'" value="'.$row['id_board'].'"' , in_array($row['id_board'],$galleryboards) ? ' checked="checked"' : '' , '><span' , in_array($row['id_board'],$blogboards) ? ' class="error" title="This board is also set as blogboard - but it will show as galley."' : '' ,'> '. $row['name'].'</span>
			</div>';
			$count++;
		}

		echo '
		<p class="error smalltext" style="padding: 1em; font-size: 0.8em; clear: both;">* This board is set as a blogboard.</p>
		</div>
		<div style="padding: 1em; text-align: center; clear: both;">
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			<input type="hidden" name="gboard" id="gboard" value="1" />
			<hr /><input type="submit" name="mysettings_submit2" id="mysettings_submit2" value="Submit" />
		</div>
	</div>
</form>';
	}
	elseif($sub == 'about')
	{
		$what = file_get_contents($settings['theme_dir'].'/pages/about.template.php');
		$content = explode("<!--split-->",$what);
		
		echo '
<form style="background: none; width: 100%;" id="mysettings_form" method="post" action="' . $scripturl . '?action=mysettings" enctype="multipart/form-data">
	<div class="title_bar">
		<h3 class="titlebg">Editing the About page</h3>
	</div>
	<div style="overflow: hidden; ">
		<div class="windowbg2" style="overflow: hidden; padding: 1em;">
			<textarea id="aboutbox" name="aboutbox" style="width: 100%; height: 500px;">' , $content[1] , '</textarea>
		</div>
		<div style="padding: 1em; text-align: center; clear: both;">
			<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			<input type="hidden" name="aboutme" id="aboutme" value="1" />
			<hr /><input type="submit" name="mysettings_submit3" id="mysettings_submit3" value="Submit" />
		</div>
	</div>
</form>';
	}
}
	
function updateMySettings($var, $data, $action, $theme_id = 1)
{
	global $context, $settings, $options, $scripturl, $txt, $smcFunc;

	$inserts = array(0, $theme_id, $var, $data);		
	$smcFunc['db_insert']('replace',
			'{db_prefix}themes',
			array('id_member' => 'int', 'id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),
			$inserts,
			array('id_member', 'id_theme', 'variable')
		);

	cache_put_data('theme_settings-'.$theme_id, null, 90);

	// Invalidate the cache.
	updateSettings(array('settings_updated' => time()));
	if(empty($action))
		return;

	redirectexit($action);
}




?>