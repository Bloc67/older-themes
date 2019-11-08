<?php

// a simple homepage
function template_main()
{
	global $context, $settings, $modSettings;


	echo '
	<div class="clearfix" style="width: 100%;">
		<div class="windowbg" style="border: solid 1px #888; float: right; padding: 1em; margin: 2em 1em 1em 1em; width: 30%;">
			<p>';
	
	$what=ssi_recentTopics(6, NULL,  'array');
	// Output the topics
	echo '<h3>Recent Topics:</h3>
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
			</p><h3>News</h3>';
	
	// latest news
	ssi_news();
	echo '
		</div>
		<div style="width: 60%;">';

	// latest posts
	ssi_boardNews();
	echo '
		</div>
	</div>';	

}
?>