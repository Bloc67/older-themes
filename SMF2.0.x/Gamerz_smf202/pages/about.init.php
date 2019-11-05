<?php

global $settings, $scripturl, $txt, $context;

$context['page_title_html_safe'] = $txt['bloc_about'];
$context['linktree'][] = array(
					'url' => $scripturl . '?action=about',
					'name' => $txt['bloc_about'],
			);

?>