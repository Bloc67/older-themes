<?php

global $txt,$context, $scripturl, $settings, $boardidr, $boardurl;

$context['linktree'][] = array(
					'url' => $scripturl . '?action=bwblog',
					'name' => $txt['bwblog'],
			);
$context['page_title_html_safe'] = $txt['bwblog'];

?>