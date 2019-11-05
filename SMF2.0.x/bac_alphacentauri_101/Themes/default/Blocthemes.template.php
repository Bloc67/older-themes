<?php

/**
 * @package Blocthemes Admin
 * @version 1.0
 * @theme default
 * @author Blocthemes - http://www.blocthemes.net
 * Copyright (C) 2014 - Blocthemes
 *
 */

function template_btnews()
{
	global $context, $txt, $scripturl;
	
	echo '
	<div class="bwgrid">';
	
	$alt = true;
	foreach($context['btfeed']['channel']['item'] as $u => $s)
	{
		echo '
	<h3 class="titlebg"><a href="' , $s['href'] , '" target="_blank">' , $s['title'] , '</a></h3>
	<div class="windowbg' , $alt ? '2' : '' , '" style="margin-bottom: 4px;">
		' , $s['description'] , '<hr>
		<span class="smalltext">Added: ' , $s['pubDate'] , '</span>
	</div>';
		
		$alt = !$alt;
	}
	echo '
	</div>';
}

function template_btusersites()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="bwtable"><div class="bwrow">';
	
	$items = 0;
	foreach($context['btfeed']['channel']['item'] as $u => $s)
	{
		echo '
	<div class="bwcol w33"><div style="padding: 0 1em 1em 0;">
		<h3 class="">' , $s['title'] , '</h3>
		<p>' , $s['description'] , '</p><hr>
		<span class="smalltext">Added: ' , $s['pubDate'] , '</span>
	</div></div>';
		$items++;
		if($items>2)
		{
			$items= 0;
			echo '</div><div class="bwrow">';
		}
	}
	echo '
	</div></div>';
}

function template_btshowcase()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="bwgrid" id="btshowcase">';
	
	foreach($context['btfeed']['channel']['item'] as $u => $s)
	{
		echo '
	<div class="bwcell33"><div style="padding: 0 2em 1em 0; overflow: hidden;">
		<h3><a href="' , $s['link'] , '">' , $s['title'] , '</a></h3>
		<p>' , $s['description'] , '</p><hr>
		<a href="' , $s['link'] , '">Read more..</a>
	</div></div>';
	}
	echo '
	</div>';
}
function show_btsettings($opts)
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=blocthemes;sa=current;th=', $settings['theme_id'], '" method="post" enctype="multipart/form-data" accept-charset="', $context['character_set'], '">
			<div class="windowbg">
				<div class="content">
					<dl class="settings flow_auto">';

	foreach ($opts as $setting)
	{
		// 
		if(isset($settings[$setting['id']]))
			$setting['value'] = $settings[$setting['id']];
		
		// Is this a separator?
		if (empty($setting))
		{
			echo '
					</dl>
					<hr class="hrcolor" />
					<dl class="settings flow_auto">';
		}
		// A checkbox?
		elseif ($setting['type'] == 'checkbox')
		{
			echo '
						<dt>
							<label for="', $setting['id'], '">', $setting['label'], '</label>:';

			if (isset($setting['description']))
				echo '<br />
							<em class="smalltext">', $setting['description'], '</em>';

			echo '
						</dt>
						<dd>
							<input type="hidden" name="', !empty($setting['default']) ? 'default_' : '', 'options[', $setting['id'], ']" value="0" />
							<input type="checkbox" name="', !empty($setting['default']) ? 'default_' : '', 'options[', $setting['id'], ']" id="', $setting['id'], '"', !empty($setting['value']) ? ' checked="checked"' : '', ' value="1" class="input_check" />
						</dd>';
		}
		// A list with options?
		elseif ($setting['type'] == 'list')
		{
			echo '
						<dt>
							<label for="', $setting['id'], '">', $setting['label'], '</label>:';

			if (isset($setting['description']))
				echo '<br />
							<em class="smalltext">', $setting['description'], '</em>';

			echo '
						</dt>
						<dd>
							<select name="', !empty($setting['default']) ? 'default_' : '', 'options[', $setting['id'], ']" id="', $setting['id'], '">';

			foreach ($setting['options'] as $value => $label)
				echo '
							<option value="', $value, '"', $value == $setting['value'] ? ' selected="selected"' : '', '>', $label, '</option>';

			echo '
							</select>
						</dd>';
		}
		// A list with options?
		elseif ($setting['type'] == 'radio')
		{
			echo '
						<dt>
							<label for="', $setting['id'], '">', $setting['label'], '</label>:';

			if (isset($setting['description']))
				echo '<br />
							<em class="smalltext">&nbsp;', $setting['description'], '</em>';

			echo '
						</dt>
						<dd>';

			foreach ($setting['options'] as $value => $label)
				echo '
							<input type="radio" name="', !empty($setting['default']) ? 'default_' : '', 'options[', $setting['id'], ']" id="', $setting['id'], '" value="', $value, '"', $value == $setting['value'] ? ' checked="checked"' : '', '>', $label, ' <br>';

			echo '
						</dd>';
		}
		elseif ($setting['type'] == 'upload')
		{
			echo '
						<dt>
							<label for="', $setting['id'], '">', $setting['label'], '</label>:';

			if (isset($setting['description']))
				echo '<br />
							<em class="smalltext">', $setting['description'], '</em>';

			echo '
						</dt>
						<dd>
							<input type="file" name="', $setting['id'], '" id="', $setting['id'], '" class="input_text" />
						</dd>';
		}
		elseif ($setting['type'] == 'colorselect')
		{
			echo '
						<dt>
							<label for="', $setting['id'], '">', $setting['label'], '</label>:';

			if (isset($setting['description']))
				echo '<br />
							<em class="smalltext">', $setting['description'], '</em>';

			echo '
						</dt>
						<dd>
							<div id="slider_' . $setting['id'] . '" style="border: solid 1px #888; background: #fff; display: inline-block; width: 25px; height: 60px;"></div>
							<div id="picker_' . $setting['id'] . '" style="border: solid 1px #888; background: #fff; display: inline-block; width: 100px; height: 60px;"></div>
							<div id="show_' . $setting['id'] . '" style="background: ' . $setting['value'] . '; display: inline-block; width: 200px; height: 60px;"></div>
   <script type="text/javascript">
      ColorPicker(
        document.getElementById(\'slider_' . $setting['id'] . '\'),
        document.getElementById(\'picker_' . $setting['id'] . '\'),
        function(hex, hsv, rgb) {
          document.getElementById(\'' . $setting['id'] . '\').value = hex;   
          document.getElementById(\'show_' . $setting['id'] . '\').style.backgroundColor = hex;   
        });
    </script>
							<br><input type="text" name="options[', $setting['id'], ']" id="', $setting['id'], '" value="' , $setting['value'] , '" class="input_text" />
						</dd>';
		}
		// A regular input box, then?
		else
		{
			echo '
						<dt>
							<label for="', $setting['id'], '">', $setting['label'], '</label>:';

			if (isset($setting['description']))
				echo '<br />
							<em class="smalltext">', $setting['description'], '</em>';

			echo '
						</dt>
						<dd>
							<input type="text" name="', !empty($setting['default']) ? 'default_' : '', 'options[', $setting['id'], ']" id="', $setting['id'], '" value="', $setting['value'], '"', $setting['type'] == 'number' ? ' size="5"' : (empty($setting['size']) ? ' size="40"' : ' size="' . $setting['size'] . '"'), ' class="input_text" />';

			if (!empty($setting['value']) && isset($setting['image']))
				echo '<br />
							<img src="', $setting['value'], '" style="margin: 0.5em 0;' , $setting['image']!= 1 ? ' height: '.$setting['image'].'px;' : ''  , '" alt="" />';
			
			echo '
						</dd>';
		}
	}

	echo '
					</dl>
					<div class="righttext">
						<input type="submit" name="submit" value="', $txt['save'], '" class="button_submit" />
					</div>
				</div>
			</div>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			<input type="hidden" name="bt_theme_id" value="', $context['theme_id'], '" />
		</form>
	</div>
	<br class="clear" />';
}

?>