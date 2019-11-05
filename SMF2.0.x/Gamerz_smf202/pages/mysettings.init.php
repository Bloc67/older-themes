<?php

global $txt,$context, $scripturl, $settings, $boardidr, $boardurl;

$context['linktree'][] = array(
					'url' => $scripturl . '?action=mysettings',
					'name' => 'My theme settings',
			);

$context['page_title_html_safe'] = 'My theme settings';

// a subpage defined?
$actions = array('menu','blog','gallery','about','addtopitem','edittopitem');
$texts = array(
	'menu' => 'Custom Menu Editor',
	'blog' => 'Setting Blogboards',
	'gallery' => 'Setting Galleryboards',
	'about' => 'Editing "About" page',
	'addtopitem' => 'Adding Top menu',
	'edittopitem' => 'Editing Top Menu',
	);

if(isset($_GET['sa']) && in_array($_GET['sa'], $actions))
{
	$context['linktree'][] = array(
					'url' => $scripturl . '?action=mysettings;sa='.$_GET['sa'],
					'name' => $texts[$_GET['sa']],
			);
	$context['page_title_html_safe'] .= ' - ' .$texts[$_GET['sa']];

}

?>