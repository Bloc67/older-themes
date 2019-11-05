<?php

global $txt,$context, $scripturl, $settings, $boardidr, $boardurl;

$context['linktree'][] = array(
					'url' => $scripturl . '?action=bwgallery',
					'name' => $txt['bloc_gallery'],
			);

$context['page_title_html_safe'] = $txt['bloc_gallery'];

?>