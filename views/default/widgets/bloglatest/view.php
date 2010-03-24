<?php
    
/**
 * Latest blog entries
 * 
 * @package BlogLatest
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Alistair Young <alistair@codebrane.com>
 * @copyright codeBrane 2010
 * @link http://codebrane.com/blog/
 */
	 
	global $CONFIG;
	require_once($CONFIG->pluginspath . "bloglatest/util/utils.php");
?>

<div class="contentWrapper">
	<? echo get_widget_top_text($vars['entity']->username); ?>
</div>

<?
$blogs = get_blog_posts($vars['entity']->username, $vars['entity']->tags, $vars['entity']->no_of_posts);
foreach ($blogs as $username_and_icon => $blog) {
	$user_parts = explode(",", $username_and_icon);
	$description = get_excerpt($blog->description);
	?>
	<div class="contentWrapper">
	<?
	if ($vars['entity']->username == "") {
		echo "<a href=\"{$CONFIG->wwwroot}pg/profile/{$user_parts[1]}\">";
		echo "<img src=\"{$user_parts[2]}\" title=\"{$user_parts[0]}\" alt=\"{$user_parts[0]}\"/>";
		echo "{$user_parts[0]}</a><br />";
	}
	echo "<a href=\"{$CONFIG->wwwroot}/pg/blog/admin/read/{$blog->guid}\">{$blog->title}</a><br />{$description} ...";
	?>
	</div>
	<?
}
?>

