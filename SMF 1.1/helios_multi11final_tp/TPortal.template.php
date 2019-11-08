<?php
// Version: 0.9; TPortal

// TPortal template - for frontpage.

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

        $topbox='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 16px; width: 22px;"><img src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topleft.gif" style="height: 16px; width: 22px; border: 0px; padding: 0px; margin: 0px;" alt="gfx" /></td><td style="height: 16px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topmid.gif); "></td><td style="height: 16px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topright.gif" style="height: 16px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr><tr><td style="width: 22px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midleft.gif); "></td><td width="100%" valign="top">';
        $botbox='</td><td style="width: 27px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-midright.gif); "></td></tr><tr><td style="height: 14px; width: 22px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botleft.gif" style="height: 14px; width: 22px; border: 0px; padding: 0px; margin: 0px;" /></td><td style="height: 14px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botmid.gif); "></td><td style="height: 14px; width: 27px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-botright.gif" style="height: 14px; width: 27px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        $leftbox='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="height: 54px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-lefttop.gif" style="height: 54px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td nowrap="nowrap" width="100%" style="height: 54px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-midtop.gif); ">';
        $rightbox='</td><td style="height: 54px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-topright.gif" style="height: 54px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';


        $leftboxbot='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-left: 5px; height: 78px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-botleft.gif" style="height: 78px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td valign="top" nowrap="nowrap" style="text-align: center; height: 78px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-botmid.gif); ">';
        $rightboxbot='</td><td style="height: 78px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-board-botright.gif" style="height: 78px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

        $leftboxbot2='<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-left: 4px; height: 36px; width: 72px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topleft-40.gif" style="height: 36px; width: 72px; border: 0px; padding: 0px; margin: 0px;" /></td><td valign="top" nowrap="nowrap" style="text-align: center; height: 36px; background-image: url('.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topmid-41.gif); ">';
        $rightboxbot2='</td><td style="padding-right: 0px; height: 36px; width: 79px;"><img alt="gfx" src="'.$settings['images_url'].'/pod/'.$options['theme_color'].'/pod-info-topright-42.gif" style="height: 36px; width: 79px; border: 0px; padding: 0px; margin: 0px;" /></td></tr></table>';

	 echo $leftbox;
	// show the linktree
	if((isset($context['TPortal']['boardnews'][0]['options']['linktree']) && $context['TPortal']['front_type']!='frontblock') || $context['TPortal']['front_type']=='frontblock')
	       theme_linktree();
	echo $rightbox.$topbox;

	// if frontblocks are chosen, render the frontblocks. Or if frontblocks+news is chosen
	if(($context['TPortal']['front_type']=='frontblock' || $context['TPortal']['frontblock_type']=='first') && $context['TPortal']['frontbar']){
		echo '
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>' , TPortal_sidebar('front') , '</td>
		</tr>
	</table>';
	}

	if($context['TPortal']['front_type']!='frontblock')
	{
		if(isset($context['TPortal']['show_catlist']) && $context['TPortal']['show_catlist'] && !$context['TPortal']['use_old_admin']){
			echo '
	<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 10px;">
		<tr>
			<td class="mirrortab_first">&nbsp;</td>';
			// get back to homepage as well
			echo '
			<td valign="top" class="mirrortab_back"><a href="', $scripturl, '">' , $txt[103] , '</a></td>';

			foreach($context['TPortal']['clist'] as $cats){
				echo $cats['selected'] ? '
			<td class="mirrortab_active_first">&nbsp;</td>' : '' , '
			<td valign="top" class="mirrortab_' , $cats['selected'] ? 'active_back' : 'back' , '">
				<a href="', $scripturl, '?cat=',$cats['id'],'">' , $cats['name'] , '</a>
			</td>' , $cats['selected'] ? '<td class="mirrortab_active_last">&nbsp;</td>' : '';
			}
			echo '
			<td class="mirrortab_last">&nbsp;</td>
		</tr>
	</table>';
		}
		if(isset($context['TPortal']['boardnews']) && sizeof($context['TPortal']['boardnews'])>0){
			// some init values
			$start = 0;
			$end = $context['TPortal']['frontpage_limit'];
			$count = 1;

			// layout option 1 - straight down
			if($context['TPortal']['frontpage_layout']=='0'){
				echo '
	<table cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed;">
		<tr>
			<td valign="top">';
			$first=false;
			$second=false;
			$half=0;
			$finish=false;
			}
			// layout option 2 - 1 on top, 2 columns after
			elseif($context['TPortal']['frontpage_layout']=='1'){
				echo '
	<table cellpadding="0" cellspacing="0" border="0" width="100%">';
				$first=true;
				$second=false;
				$half=floor($end/2);
				$finish=false;
			}
			// layout option 3 - single left column, multiple right column
			elseif($context['TPortal']['frontpage_layout']=='2'){
				echo '
	<table cellpadding="0" cellspacing="0" border="0" width="100%">';
				$first=true;
				$second=false;
			}
			// layout option 4 - double columns
			elseif($context['TPortal']['frontpage_layout']=='3'){
				echo '
	<table cellpadding="0" cellspacing="0" border="0" width="100%">';
				$first=true;
				$second=false;
				$half=floor($end/2);
				$finish=false;
			}

			foreach($context['TPortal']['boardnews'] as $story)
			{
				if($count<($context['TPortal']['frontpage_limit']+1)){
					// option 2
					if($context['TPortal']['frontpage_layout']=='1'){
						if($first==true){
							echo '
		<tr>
			<td colspan="2" valign="top" width="100%">';
							$first=false;
							$second=true;
						}
						elseif($second==true){
							echo '
			</td>
		</tr>
		<tr>
			<td valign="top" valign="top" width="50%">';
							$first=false;
							$second=false;
						}
						elseif($first==false && $second==false && $count>($half+1) && $finish==false){
							echo '
			</td>
			<td valign="top" width="50%">';
							$finish=true;
						}
					}

					// option 3
					elseif($context['TPortal']['frontpage_layout']=='2'){
						if($first){
							echo '
		<tr>
			<td valign="top" width="50%" style="padding-right: 10px;">';
							$first=false;
							$second=true;
						}
						elseif($second==true){
							echo '
			</td>
			<td valign="top" width="50%">';
							$first=false;
							$second=false;
						}
					}

					// option 4
					elseif($context['TPortal']['frontpage_layout']=='3'){
						if($first==true && $second==false && $finish==false){
							echo '
		<tr>
			<td valign="top" width="50%" style="padding-right: 10px;">';
							$first=false;
						}
						elseif($first==false && $second==false && $finish==true){
							echo '
			</td>
			<td valign="top" width="50%">';
							$second=true;
						}
						elseif($first==false && $second==false && $finish==false && $count>($half-1)){
								$finish=true;
								$second=false;
							}
					}

					echo '
				<div' , ($story['is_boardnews'] || $story['frame']=='theme') ? ' class="tborder"' : '' , ' style="margin-bottom: 2px; ">';
					if($story['is_boardnews'] || isset($story['options']['title']) || $context['TPortal']['mycat']!=''){
						if($story['frame']=='theme' || $story['frame']=='title')
				 			echo '
					<div style="padding: 3px 6px 3px 6px;" class="' , $story['is_boardnews'] ? 'catbg' : 'titlebg' , '">' , $story['icon'], ' <a href="' , $story['href'] , '">' , $story['subject'] , '</a></div>';
						else
				 			echo '
					<div style="padding: 0px 6px 0px 6px;">' , $story['icon'], ' <a href="' , $story['href'] , '">' , $story['subject'] , '</a></div>';
					}

					echo '
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" ' , $story['frame']=='theme' ? ' class="windowbg2"' : '' , ' style="padding: 4px;">';


					// any options for author and date?
					if(isset($story['options']['author']) || $story['is_boardnews'])
						echo '<span class="smalltext">' , $txt['tp-by'] , ' <b>' , $story['poster']['link'] , '</b></span>';
					if(isset($story['options']['date']) || $story['is_boardnews'])
						echo '<span class="smalltext"> ' , $txt[30] , ' ' , $story['time'] , '</span>';

					// ..and for views and ratings , even comments?
					echo '<br />';
					$opts=array();
					if(isset($story['options']['views']) || $story['is_boardnews'])
						$opts[] = '<span class="smalltext">' . $story['views'] . ' ' . $txt['tp-views'] . '</span>';
					if(isset($story['options']['comments']) || $story['is_boardnews']){
						if($story['is_boardnews'] && ($story['comments']>1 || $story['comments']==0))
							$what=$txt[110];
						elseif($story['is_boardnews'] && $story['comments']==1)
							$what=$txt[146];
						else
							$what='<a href="'.$scripturl.'?page='.$story['id'].'#tp-comment">'.$txt['tp-comments'].'</a>';

						$opts[] = '<span class="smalltext">' . $story['comments'] . ' ' . $what . '</span>';
					}
					if(isset($story['options']['rating']))
						$opts[] = '<span class="smalltext">' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="'.$settings['images_url'].'/tpblue.gif" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $story['rating_average'])) : $story['rating_average']) . ' (' . $story['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ') </span>';

                     echo implode("&nbsp;|&nbsp;",$opts);

					// render the text
					echo '
								<div style="_height: 1%; overflow: auto; ' , (isset($story['options']['author']) || isset($story['options']['rating']) || isset($story['options']['date']) || $story['is_boardnews']) ? ' margin-top: 1ex;' : '' , '">';

					// any category icon?
					if(!empty($story['category']))
						echo $story['category'];

                 	// set some height to articles too...
					echo '
									<div style="line-height: 1.3em; ">';
					// use a intro when not single page mode?
					if($context['TPortal']['front_type']!='singlepage')
					{
						// use intro!
						if($story['use_intro']=='1' && !isset($context['TPortal']['show_single_page']))
						{
							if($story['is_php'])
							{
								eval($story['body']);
							}
							else
							{
								echo $story['introtext'];
								echo '
										<div class="smalltext" style="margin-top: 1ex; font-weight: bold;"><a href="' .$story['href']. '">'.$txt['tp-readmore'].'</a></div>';
							}
						}
						else
						{
							if($story['is_php'])
								eval($story['body']);
							else
								echo $story['body'];
						}
					}
					else
					{
						if($story['is_php'])
							eval($story['body']);
						else
							echo $story['body'];
					}

					echo '
									</div>
					     		</div>
							</td>';
				$context['TPortal']['bothcols']=false;
				// any extra articles to show?
					if(isset($story['options']['category']) && !empty($context['TPortal']['morearticles']) && isset($story['options']['catlist']) && !empty($context['TPortal']['categories']))
					{
						$context['TPortal']['bothcols']=true;
						echo '
							<td nowrap valign="top" ' , $story['frame']=='theme' ? ' class="windowbg2"' : '' , '>';
						// do we show the categoy list as well?
						if(isset($story['options']['catlist']) && sizeof($context['TPortal']['categories'])>0)
						{
							echo '
								<div id="articlelist_container" style="padding-bottom: 0; margin: 1ex 1ex 0 0;">
									<div style="padding: 8px; padding-bottom: 1ex">';
							echo '
										<ul id="catlist">';

							foreach($context['TPortal']['categories'] as $cat)
							{
								echo '
											<li' , $story['ID_CAT'] == $cat['id'] ? 'class="chosen"' : '' , '><a href="' . $scripturl . '?cat=' . $cat['id'] . '">' , $cat['name'] , '</a>';

								if($story['ID_CAT'] == $cat['id'])
								{
									echo '
												<ul id="articlelist">';
									foreach($context['TPortal']['morearticles'] as $alink)
									{
										echo '
													<li' , $story['id'] == $alink['id'] ? ' class="chosen"' : '' , '><a href="' . $scripturl . '?page=' . $alink['id'] . '">' , $alink['subject'] , '</a></li>';
									}
									echo '
												</ul>';
								}
								echo '
											</li>';
							}
							echo '
										</ul>
									</div>
								</div>
							</td>';
						}
						else
						{
							echo '
								<div id="articlelist_container" style="padding-bottom: 0; margin: 1ex 1ex 0 0;">
									<div style="padding: 8px;">
										<ul id="articlelist" >';
							foreach($context['TPortal']['morearticles'] as $alink)
							{
								echo '
													<li' , $story['id'] == $alink['id'] ? ' class="chosen"' : '' , '><a href="' . $scripturl . '?page=' . $alink['id'] . '">' , $alink['subject'] , '</a></li>';
							}
							echo '
										</ul>
									</div>
								</div>
							</td>';
						}
					}
					// guess not, just the articles then
					elseif(isset($story['options']['category']) && !empty($context['TPortal']['morearticles']) && !isset($story['options']['catlist']))
					{
						$context['TPortal']['bothcols']=true;
						echo '
							<td nowrap valign="top" ' , $story['frame']=='theme' ? ' class="windowbg2"' : '' , '>';
						echo '
								<div id="articlelist_container" style="padding-bottom: 0; margin: 1ex 1ex 0 0;">
									<div style="padding: 8px;">
										<ul id="articlelist">';

						foreach($context['TPortal']['morearticles'] as $alink)
						{
							echo '
													<li' , $story['id'] == $alink['id'] ? ' class="chosen"' : '' , '><a href="' . $scripturl . '?page=' . $alink['id'] . '">' , $alink['subject'] , '</a></li>';
						}
						echo '
										</ul>
						     		</div>
								</div>
							</td>';
					}

					echo '
						</tr>
						<tr>
							<td ' , $context['TPortal']['bothcols'] ? 'colspan="2"' : '' , ' ' , $story['frame']=='theme' ? ' class="windowbg2"' : '' , '>';

					// can we rate it?
					if(isset($context['TPortal']['show_single_page']) && isset($story['options']['ratingallow'])  && $context['user']['is_logged']){
						if($story['can_rate']){
							echo '
								<hr />
								<div class="smalltext" style="padding: 1ex;">', $txt['tp-ratearticle'] ,'
									<form name="tp_article_rating" action="',$scripturl,'?action=tpmod;sa=rate_article" method="post">
										<select size="1" name="tp_article_rating">';
							for($u=$context['TPortal']['maxstars'] ; $u>0 ; $u--){
								echo '
											<option value="'.$u.'">'.$u.'</option>';
							}
							echo '
										</select>
										<input type="submit" name="tp_article_rating_submit" value="',$txt['tp_rate'],'">
										<input name="tp_article_type" type="hidden" value="article_rating">
										<input name="tp_article_id" type="hidden" value="'.$story['id'].'">
										<input type="hidden" name="sc" value="', $context['session_id'], '" />
									</form>
								</div>';
						}
						else
							echo '
								<hr /><i>'.$txt['tp-haverated'].'</i>';
					}

					// give 'em a edit link? :)
					if(allowedTo('tp_articles') && !$story['is_boardnews'] && $context['TPortal']['hide_editarticle_link']=='0')
               			echo '
         						<div class="smalltext" style="margin: 2ex 1ex 1ex 1ex;"><a href="' , $scripturl , '?action=tpadmin;sa=editarticle' , $story['id'] , '">[' , $txt['tp-editarticle'] , ']</a>
         							<a href="' , $scripturl , '?action=tpadmin;sa=editarticle' , $story['id'] , ';visualopts">[' , $txt['tp-editarticle2'] , ']</a>
         						</div>';
					// their own article?
					elseif(!$story['is_boardnews'] && !allowedTo('tp_articles') && $story['poster']['id']==$context['user']['id'] && $context['TPortal']['hide_editarticle_link']=='0')
               			echo '
         						<div class="smalltext" style="margin: 2ex 1ex 1ex 1ex;"><a href="' , $scripturl , '?action=tpmod;sa=editarticle' , $story['id'] , '">[' , $txt['tp-editarticle'] , ']</a></div>';
					elseif($story['is_boardnews'])
               			echo '
								<div class="smalltext" style="margin: 2ex 1ex 1ex 1ex;">'.$story['link'].' | '.$story['new_comment'].'</div>';

					// any comments then?
					if(isset($context['TPortal']['show_single_page']) && isset($story['options']['comments'])){
						if(isset($context['TPortal']['article_comments']) && sizeof($context['TPortal']['article_comments'])>0){
							echo '
								<p><a name="tp-comment"></a></p>
								<div class="titlebg" style="padding: 6px;">
									<a href="#tp-comment" onclick="swapOptionsArticle(\'tp_article_comment\')">
									<img id="articleUpshrink" name="articleUpshrink" src="'.$settings['images_url'].'/' , isset($story['options']['commentupshrink']) ? 'upshrink' : 'upshrink2' , '.gif" style="margin: 0; border: 0;" alt="" /> '.$txt['tp-comments'].':</a></div>';

							// show number of comments, and if new ones
							if(isset($context['TPortal']['article_comments_count'])){
								echo '
									<div class="smalltext" style="padding-left: 1ex;">('.$context['TPortal']['article_comments_count'].' '.$txt['tp-comments'];
								if(isset($context['TPortal']['article_comments_new']))
									echo ' , '.$context['TPortal']['article_comments_new'].' '.$txt['tp-arenew'];
								echo ')
									</div>';
							}


							echo '
								</div>
								<div id="tp_article_comment" ' , isset($story['options']['commentupshrink']) ? '' : 'style="display: none;"' , '>';

                             $cc=1;
							foreach($context['TPortal']['article_comments'] as $comment){
								echo '
									<div style="padding: 8px; margin-top: 5px;" ', ($context['user']['is_admin'] || $context['user']['id']==$comment['posterID']) ? ' class="windowbg"' : '' , '>
											'.$cc.'
												'.$comment['subject'].'
								', ($comment['is_new'] && $context['user']['is_logged']) ? '<img src="'.$settings['images_url'].'/'.$context['user']['language'].'/new.gif" alt="" />' : '' ,'
												<span class="smalltext"> '.$txt['tp-by'].' <a href="'.$scripturl.'?action=profile;u='.$comment['posterID'].'">'.$comment['poster'].'</a>
												'.$txt[30].' '.$comment['date'].'</span>
								   		<div class="smalltext" style="margin-top: 1ex;">'.$comment['text'];
								// can we edit the comment or are the owner of it?
								if(allowedTo('tp_articles') || $comment['posterID'] == $context['user']['id'])
									echo '
											<div style="margin-top: 1ex;">
												<a href="' , $scripturl , '?action=tpmod;sa=killcomment' , $comment['id'] , '" onclick="javascript:return confirm(\''.$txt['tp-confirmdelete'].'\')">[' , $txt['tp-delete'] , ']</a>
											</div>';

								echo '
								     	</div>
								   </div>';
								$cc++;
							}
						}
						if(isset($story['options']['commentallow']) && $context['user']['is_logged'])
							echo '<br />
									<form name="tp_article_comment" action="'.$scripturl.'?action=tpmod;sa=comment" method="post" style="margin: 1ex;">
										<input name="tp_article_comment_title" type="text" value="Re: '. strip_tags($story['subject']) .'"> <br />
										<textarea name="tp_article_bodytext" rows="6" cols="20" style="width: 90%;" wrap="on"></textarea>
										<br /><input id="tp_article_comment_submit" type="submit" value="'.$txt['tp-submit'].'">
										<input name="tp_article_type" type="hidden" value="article_comment">
										<input name="tp_article_id" type="hidden" value="'.$story['id'].'">
										<input type="hidden" name="sc" value="', $context['session_id'], '" />
									</form>';
						else
							echo '
									<div style="padding: 1ex;" class="smalltext"><em>'.$txt['tp-cannotcomment'].'</em></div>';

						echo '
						     	</div>';
					}

					echo '</td>
						</tr>
					</table></div>';
				$count++;
				}
			}
				echo '</td>
		</tr>
	</table>';
		}
			if(!empty($context['TPortal']['pageindex']))
				echo '
	<div style="padding-left: 1ex;" class="middletext">'.$context['TPortal']['pageindex'].'</div>';
	}
	// are frontblocks shown at the end?
	if($context['TPortal']['frontblock_type']=='last' && $context['TPortal']['frontbar']){
		echo '
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>' , TPortal_sidebar('front') , '</td>
		</tr>
	</table>';
	}
    echo $botbox;

}




?>