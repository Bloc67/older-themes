<?php

global $context, $scripturl, $settings, $boardidr, $boardurl;

$context['linktree'][] = array(
					'url' => $scripturl . '?action=bwblog',
					'name' => 'All blogs',
			);
$settings['bwrelated'] = '<a href="index.php?action=about">About</a>';

?>