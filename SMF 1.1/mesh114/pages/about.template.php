<?php

function template_main()
{
	global $context, $settings, $modSettings;
?>

<div class="clearfix">

<div style="text-align: center; font-size: x-small; float: right; padding: 10px;"><img style="border: solid 1px #888;" src="<?php echo $settings['images_url'] ?>/escher.jpg" alt="" />
<br />M.C. Escher<br />"Ascending and descending"</div>
<h2>Introduction..</h2>
<p>
This page illustrates one way of using the template system in SMF for adding custom pages. All you need is to make template files(PHP files really) and place 
it into a folder of the actual Novelty theme folder called "pages". Then you go to Admin section, 
"Themes and Settings" and look for "current theme settings".
</p>
<p>
There you can add the entries for the custom files. First its real name(minus "template.php") and the 
title you like to use. For example, this custom page is the file "about.template.php" and has "Custom" as the title. Therefore we insert "about,Custom" for this one. To add more, do the same, 
but put a "|" inbetween, like: "about,Custom|my,My Page!"
</p>

<h3>Content of custom pages</h3>
<p>..can be anything really, since its PHP pages, you can even add your own PHP routines in there. 
Note that its indeed still a part of SMF, so any values you might want to use from the forum, is available. 
If you look at the other custom page called "homepage.template.php" you will see its using a lot of SSI functions 
to show a simple "news" page. It can also mix PHP and HTML freely, as this template shows.
</p></div>

<?php } ?>
