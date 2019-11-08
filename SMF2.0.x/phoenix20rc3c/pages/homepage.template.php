<?php

// a simple homepage
function template_main()
{
	global $context, $settings, $modSettings, $boarddir;


	echo '
	<h3 class="titlebg"><span class="left"></span>Latest Topics</h3>
	<div class="windowbg" style="overflow: hidden; padding: 1em;">
		';
	if(!function_exists('ssi_recentTopics'))
		require_once($boarddir.'/SSI.php');

	// latest posts
	ssi_boardNews();
	echo '
	</div>';	

}
?>