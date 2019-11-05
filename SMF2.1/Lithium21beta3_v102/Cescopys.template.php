<?php

// Cesium 1.0
// a rewrite theme

function ces_copys()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $smcFunc;

	if(!empty($_POST['copys_theme']) && !empty($_POST['copys_submit']))
	{
		$id_from = is_numeric($_POST['copys_theme']) ? $_POST['copys_theme'] : 0;
		if(!empty($id_from))
		{
			$s = 'AND (variable="use_' . (implode('" OR variable="use_', $settings['ces_boardtypes'])) . '" OR variable="' . (implode('_boards" OR variable="', $settings['ces_boardtypes'])) . '_boards" OR variable="ces_fullmenus" OR variable="ces_fullmenus_keep")';
			$id_current = $settings['theme_id'];
			// first, release all boardtypes for current theme
			$smcFunc['db_query']('substring', '
			DELETE FROM {db_prefix}themes 
			WHERE id_theme = '. $id_current . '
			' .$s . '	
			');
			
			$request = $smcFunc['db_query']('substring', '
			SELECT variable, value FROM {db_prefix}themes
			WHERE id_theme = '. $id_from . '
			' .$s . '	
				');
			$new = array();
			if($smcFunc['db_num_rows']($request)>0) 
			{	
				while($row = $smcFunc['db_fetch_assoc']($request))
					$new[$row['variable']] = $row['value'];

				$smcFunc['db_free_result']($request);
				// insert new ones
				foreach($new as $v => $val)
					$request = $smcFunc['db_query']('substring', '
			REPLACE INTO {db_prefix}themes (id_member,id_theme,variable,value) VALUES("0","' . $id_current . '","' . $v . '","' . $val . '")');

			
			}
		}
	}
}

function subtemplate_aside()
{

}

function template_main()
{
	global $scripturl, $context, $txt, $settings;

	echo '
	<p><b>' , $txt['ces_copys'] , '</b></p>
	<form action="' . $scripturl . '?action=copys" name="copys_form" method="POST">';

	$a = cesthemes($settings['theme_id']);
	foreach ($a as $value => $label)
		echo '
		<input type="radio" name="copys_theme" value="', $value, '"> ', $label, '<br>';

	echo '<br>
		<hr>
		<input type="submit" name="copys_submit" value="' . $txt['ces_send'] . '" />
	</form>';
}


?>