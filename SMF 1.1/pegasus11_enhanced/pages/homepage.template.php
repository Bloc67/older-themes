<?php

// a simple homepage
function template_main()
{
	global $context, $settings, $modSettings, $boarddir;


	echo '
	<div style="overflow: hidden; padding: 1em;">
		<div style="border-left: solid 3px #aaa; float: right; padding: 1em 1em 1em 2em; margin: 2em 1em 1em 1em; width: 45%;">
			<p>';
	if(!function_exists('ssi_recentTopics'))
		require_once($boarddir.'/SSI.php');

	$what=ssi_recentTopics(6, NULL,  'array');
	// Output the topics
	echo '<h2>Recent Topics:</h2>
			<ul style="padding: 0 1em; margin: 0;">';
	foreach($what as $mine){
		echo '
				<li><a href="'.$mine['href'].'">'.$mine['subject'].'</a> ';
		if(!$mine['new'])
			echo ' <a href="'.$mine['href'].'"><img border="0" src="Themes/default/images/english/new.gif" alt="new" /></a> ';

		echo ' on ', $mine['time'] , ' by ' ,$mine['poster']['link'], '</li> ';
	}
	echo '</ul>';
	echo '
			</p><br /><h2>News</h2>';
	
	// latest news
	ssi_news();
	echo '
		</div>
		<div style="width: 45%;">';

	// latest posts
	ssi_boardNews();
	echo '
		</div>
	</div>';	

}
?>