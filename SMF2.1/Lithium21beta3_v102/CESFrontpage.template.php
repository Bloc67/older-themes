<?php

function template_ces_frontpage()
{
	global $scripturl, $context, $txt, $settings;

	if(empty($settings['use_news']))
	{
		echo '
	<div class="cat_bar">
		<h3 class="catbg">' , $txt['news'] , '</h3>
	</div>
	<div class="up_contain">
		<div class="padding">' , $context['random_news_line'] , '</div>	
	</div>';
		return;
	}

	ces_topics('news');

	if(empty($context['posts']))
	{	
		echo '
		<div class="toppadding">', $txt['no_matches'] , '</div>';
		return;
	}
	$pattern = array(
		16,0,
		6,10,0,
		10,6,0,
		8,8,0,
		16,0,
		8,8,0,
		6,10,0,
		10,6,0,
	);
	echo '
<div style="background: #f6f6f6; margin-bottom: 1rem; overflow: hidden;">
	<div class="bwgrid">';	
	$count =0;
	foreach ($context['posts'] as $w => $post)
	{
		if($pattern[$count]==0)
		{
			echo '
	</div><div class="bwgrid">';
			$count++;
			if($count>19)
				$count = 0;
		}
		echo '
		<div class="bwcell' . $pattern[$count] . '"><div class="bwgutter_news">
			<a href="' , $post['href'] , '" class="blog_attachment b' . $pattern[$count] . '" style="background-image: url(' , !empty($context['loaded_attachments'][$post['id']]) ? $context['loaded_attachments'][$post['id']] : $settings['images_url'].'/newsbg.jpg' , ');">&nbsp;</a>
			' , $post['num_replies'] > 0 ? '<a href="' . $scripturl . '?topic=' . $post['topic'] . '#comments"><span class="amt_bigger floatright"><span class="icon-bubble" style="vertical-align: -15%;margin-right: 3px;"></span>' . $post['num_replies'] . '</span></a>' : '', '
			<h3 class="h3_news_' . $pattern[$count] . '">
				' , !empty($post['cesparams']['slogan']) ? '<span style="font-size: 70%; font-weight: bold; display: block; padding-bottom: 0.5rem;">'.$post['cesparams']['slogan'].'</span>' : '' , '
				<a href="' , $post['href'] , '">
				' , $post['subject'] , '
				</a>
			</h3>
			<div class="news_' . $pattern[$count] . '">
				' , !empty($post['cesparams']['ingress']) ? $post['cesparams']['ingress'] : '' , '
			</div>
		</div></div>
			';
		$count++;
	}
	echo '
	</div>
</div>';

}


?>
