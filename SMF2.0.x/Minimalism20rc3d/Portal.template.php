<?php
// Version: 2.3.2; Portal

function template_portal_above()
{
	global $context, $modSettings;

	if (empty($modSettings['sp_disable_side_collapse']) && ((!empty($modSettings['showleft']) && !empty($context['SPortal']['blocks'][1])) || (!empty($modSettings['showright']) && !empty($context['SPortal']['blocks'][4]))))
	{
		echo '
	<div class="sp_right sp_fullwidth">';

		if (!empty($modSettings['showleft']) && !empty($context['SPortal']['blocks'][1]))
			echo '
		<a href="#side" onclick="return sp_collapseSide(1)">', sp_embed_image($context['SPortal']['sides'][1]['collapsed'] ? 'expand' : 'collapse', '', null, null, true, 'sp_collapse_side1'), '</a>';

		if (!empty($modSettings['showright']) && !empty($context['SPortal']['blocks'][4]))
			echo '
		<a href="#side" onclick="return sp_collapseSide(4)">', sp_embed_image($context['SPortal']['sides'][4]['collapsed'] ? 'expand' : 'collapse', '', null, null, true, 'sp_collapse_side4'), '</a>';

		echo '
	</div>';
	}

	echo '
	<table id="sp_main" style="margin: 0;">
		<tr>';

	if (!empty($modSettings['showleft']) && !empty($context['SPortal']['blocks'][1]))
	{
		echo '
			<td id="sp_left" style="background: #f8f8f8; width: 200px; padding: 4px 15px; "', $context['SPortal']['sides'][1]['collapsed'] && empty($modSettings['sp_disable_side_collapse']) ? ' style="display: none;"' : '', '>';

		foreach ($context['SPortal']['blocks'][1] as $block)
			template_block_left($block);

		echo '
			</td>';
	}
	if (!empty($modSettings['showright']) && !empty($context['SPortal']['blocks'][4]))
	{
		echo '
			<td id="sp_right" style="background: #f4f4f4; width: 200px; padding: 4px 15px; "', !empty($modSettings['rightwidth']) ? ' width="' . $modSettings['rightwidth'] . '"' : '', $context['SPortal']['sides'][4]['collapsed'] && empty($modSettings['sp_disable_side_collapse']) ? ' style="display: none;"' : '', '>';

		foreach ($context['SPortal']['blocks'][4] as $block)
			template_block_left($block);

		echo '
			</td>';
	}

	echo '
			<td id="sp_center">';

	if (!empty($context['SPortal']['blocks'][2]))
		foreach ($context['SPortal']['blocks'][2] as $block)
			template_block($block);
}

function template_portal_below()
{
	global $context, $modSettings;

	echo '
				<br />';

	if (!empty($context['SPortal']['blocks'][3]))
		foreach ($context['SPortal']['blocks'][3] as $block)
			template_block($block);

	echo '
			</td>';

	echo '
		</tr>
	</table>';
}

function template_block($block)
{
	global $context;

	if ($block['type'] == 'sp_boardNews')
	{
		$block['type']($block['parameters'], $block['id']);

		return;
	}

	if ($context['SPortal']['core_compat'])
		template_block_core($block);
	else
		template_block_curve($block);
}

function template_block_core($block)
{
	global $settings;

	echo '
				<div class="', !empty($block['style']['no_body']) ? '' : ' tborder', '">
					<table class="sp_block">';

	if (empty($block['style']['no_title']))
	{
		echo '
						<tr>
							<td class="sp_block_padding ', $block['style']['title']['class'], '"', !empty($block['style']['title']['style']) ? ' style="' . $block['style']['title']['style'] . '"' : '', '>';

		if (empty($block['force_view']))
			echo '
								<a class="sp_float_right" href="javascript:void(0);" onclick="sp_collapseBlock(\'', $block['id'], '\')"><img id="sp_collapse_', $block['id'], '" src="', $settings['images_url'], $block['collapsed'] ? '/expand.gif' : '/collapse.gif', '" alt="*" /></a>';

		echo '
								', parse_bbc($block['label']), '
							</td>
						</tr>';
	}

	echo '
						<tr', (empty($block['force_view']) ? ' id="sp_block_' . $block['id'] . '"' : '') , $block['collapsed'] && empty($block['force_view']) ? ' style="display: none;"' : '', '>
							<td class="sp_block_padding', ($block['type'] == 'sp_menu') ? '' : ' sp_block', empty($block['style']['body']['class']) ? '' : ' ' . $block['style']['body']['class'], '"', !empty($block['style']['body']['style']) ? ' style="' . $block['style']['body']['style'] . '"' : '', '>';

	$block['type']($block['parameters'], $block['id']);

	echo '
							</td>
						</tr>
					</table>
				</div>
				<br />';
}

function template_block_curve($block)
{
	global $settings;

	if (empty($block['style']['no_title']))
	{
		echo '
	<h3 class="', $block['style']['title']['class'], '"', !empty($block['style']['title']['style']) ? ' style="' . $block['style']['title']['style'] . '"' : '', '><span class="left"></span>';

		if (empty($block['force_view']))
			echo '
		<a class="sp_float_right" style="padding-top: 7px;" href="javascript:void(0);" onclick="sp_collapseBlock(\'', $block['id'], '\')"><img id="sp_collapse_', $block['id'], '" src="', $settings['images_url'], $block['collapsed'] ? '/expand.gif' : '/collapse.gif', '" alt="*" /></a>';

		echo '
		', parse_bbc($block['label']), '
	</h3>';
	}

	echo '
	<div id="sp_block_' . $block['id'] . '"', $block['collapsed'] && empty($block['force_view']) ? ' style="display: none;"' : '', '>';

	if (strpos($block['style']['body']['class'], 'roundframe') !== false)
	{
		echo '
		<span class="upperframe"><span></span></span>';
	}

	echo '
		<div', empty($block['style']['body']['class']) ? '' : ' class="' . $block['style']['body']['class'] . '">';

	if (empty($block['style']['no_body']))
	{
		echo '
			<span class="topslice"><span></span></span>';
	}

	echo '
			<div class="', $block['type'] != 'sp_menu' ? 'sp_block' : 'sp_content_padding', '"', !empty($block['style']['body']['style']) ? ' style="' . $block['style']['body']['style'] . '"' : '', '>';

	$block['type']($block['parameters'], $block['id']);

	echo '
			</div>';

	if (empty($block['style']['no_body']))
	{
		echo '
			<span class="botslice"><span></span></span>';
	}

	echo '
		</div>';

	if (strpos($block['style']['body']['class'], 'roundframe') !== false)
	{
		echo '
		<span class="lowerframe"><span></span></span>';
	}

	echo '
	</div>
	<br />';
}

function template_block_left($block)
{
	global $settings;

	if (empty($block['style']['no_title']))
	{
		$name = '';
		if (empty($block['force_view']))
			$name = '
		<a class="sp_float_right" href="javascript:void(0);" onclick="sp_collapseBlock(\'' . $block['id']. '\')"><img id="sp_collapse_'. $block['id']. '" src="'. $settings['images_url']. ($block['collapsed'] ? '/expand.gif' : '/collapse.gif'). '" alt="*" /></a>';
		$name .= parse_bbc($block['label']);
		mini_leftblock_def($name, 'start');
	}

	echo '
	<div id="sp_block_' . $block['id'] . '"', $block['collapsed'] && empty($block['force_view']) ? ' style="display: none;"' : '', '>';

	$block['type']($block['parameters'], $block['id']);

	echo '
	</div>';
	if (empty($block['style']['no_title']))
	{
		mini_leftblock_def('', 'end');
	}


}

?>